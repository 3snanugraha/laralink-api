<?php
/**
 * Report Controller
 * 
 * Handles report creation and management
 */
class ReportController
{
    private $db;
    private $validator;
    private $fileUploader;

    public function __construct()
    {
        // Get database connection
        require_once 'config/database.php';
        $database = new Database();
        $this->db = $database->getConnection();

        // Initialize validator and file uploader
        require_once 'utils/Validator.php';
        require_once 'utils/FileUploader.php';
        $this->validator = new Validator();
        $this->fileUploader = new FileUploader();
    }

    /**
     * Handle API requests to the report endpoint
     * 
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string|null $id Resource ID
     * @param string|null $subresource Sub-resource name
     */
    public function handleRequest($method, $id = null, $subresource = null)
    {
        switch ($method) {
            case 'GET':
                if ($id) {
                    $this->getReport($id);
                } else {
                    // Check if we're getting user reports or all reports
                    if (isset($_GET['user_id'])) {
                        $this->getUserReports();
                    } else {
                        $this->getAllReports(); // Admin function to get all reports
                    }
                }
                break;

            case 'POST':
                if ($id && $subresource === 'media') {
                    $this->uploadReportMedia($id);
                } else {
                    $this->createReport();
                }
                break;

            case 'PUT':
                if ($id && $subresource === 'status') {
                    $this->updateReportStatus($id); // Admin function to update report status
                } else {
                    http_response_code(RESPONSE_METHOD_NOT_ALLOWED);
                    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
                }
                break;

            case 'DELETE':
                if ($id) {
                    $this->deleteReport($id); // Admin function to delete a report
                } else {
                    http_response_code(RESPONSE_BAD_REQUEST);
                    echo json_encode(['status' => 'error', 'message' => 'Report ID is required']);
                }
                break;

            default:
                http_response_code(RESPONSE_METHOD_NOT_ALLOWED);
                echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
                break;
        }
    }


    /**
     * Create a new report
     */
    private function createReport()
    {
        // Get posted data
        $data = json_decode(file_get_contents("php://input"));

        // Validate input
        if (!$this->validator->validateReportCreation($data)) {
            return;
        }

        // Sanitize input
        $user_id = htmlspecialchars(strip_tags($data->user_id));
        $violence_type_id = htmlspecialchars(strip_tags($data->violence_type_id));
        $perpetrator = htmlspecialchars(strip_tags($data->perpetrator));
        $incident_date = htmlspecialchars(strip_tags($data->incident_date));
        $incident_location_lat = htmlspecialchars(strip_tags($data->incident_location_lat));
        $incident_location_lng = htmlspecialchars(strip_tags($data->incident_location_lng));
        $location_address = isset($data->location_address) ? htmlspecialchars(strip_tags($data->location_address)) : null;
        $description = htmlspecialchars(strip_tags($data->description));

        // Fix for is_anonymous - ensure it's a proper boolean value (0 or 1)
        $is_anonymous = isset($data->is_anonymous) ? ($data->is_anonymous ? 1 : 0) : 0;

        // Insert report
        $query = "INSERT INTO reports 
              (user_id, violence_type_id, perpetrator, incident_date, incident_location_lat, 
               incident_location_lng, location_address, description, is_anonymous) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);

        if (
            $stmt->execute([
                $user_id,
                $violence_type_id,
                $perpetrator,
                $incident_date,
                $incident_location_lat,
                $incident_location_lng,
                $location_address,
                $description,
                $is_anonymous
            ])
        ) {
            $reportId = $this->db->lastInsertId();

            // Add initial status to history
            $this->addReportStatusHistory($reportId, REPORT_STATUS_SUBMITTED, 'Report submitted', 'System');

            http_response_code(RESPONSE_CREATED);
            echo json_encode([
                'status' => 'success',
                'message' => 'Report created successfully',
                'data' => [
                    'report_id' => $reportId
                ]
            ]);
        } else {
            http_response_code(RESPONSE_INTERNAL_ERROR);
            echo json_encode(['status' => 'error', 'message' => 'Unable to create report']);
        }
    }


    /**
     * Get a specific report by ID
     * 
     * @param int $id Report ID
     */
    private function getReport($id)
    {
        // Get report details
        $query = "SELECT r.*, vt.type_name, vt.description as violence_description, vt.icon_url
                  FROM reports r
                  JOIN violence_types vt ON r.violence_type_id = vt.violence_type_id
                  WHERE r.report_id = ?";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            $report = $stmt->fetch(PDO::FETCH_ASSOC);

            // Get report media
            $mediaQuery = "SELECT * FROM report_media WHERE report_id = ?";
            $mediaStmt = $this->db->prepare($mediaQuery);
            $mediaStmt->execute([$id]);
            $media = $mediaStmt->fetchAll(PDO::FETCH_ASSOC);

            // Get status history
            $historyQuery = "SELECT * FROM report_status_history WHERE report_id = ? ORDER BY changed_at DESC";
            $historyStmt = $this->db->prepare($historyQuery);
            $historyStmt->execute([$id]);
            $history = $historyStmt->fetchAll(PDO::FETCH_ASSOC);

            // Add media and history to report
            $report['media'] = $media;
            $report['status_history'] = $history;

            http_response_code(RESPONSE_OK);
            echo json_encode([
                'status' => 'success',
                'data' => $report
            ]);
        } else {
            http_response_code(RESPONSE_NOT_FOUND);
            echo json_encode(['status' => 'error', 'message' => 'Report not found']);
        }
    }

    /**
     * Get all reports for a user
     */
    private function getUserReports()
    {
        // Check if user_id is provided
        if (!isset($_GET['user_id'])) {
            http_response_code(RESPONSE_BAD_REQUEST);
            echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
            return;
        }

        $user_id = htmlspecialchars(strip_tags($_GET['user_id']));

        // Get pagination parameters
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : DEFAULT_PAGE_SIZE;

        // Validate limit
        if ($limit > MAX_PAGE_SIZE) {
            $limit = MAX_PAGE_SIZE;
        }

        $offset = ($page - 1) * $limit;

        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM reports WHERE user_id = ?";
        $countStmt = $this->db->prepare($countQuery);
        $countStmt->execute([$user_id]);
        $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Get reports
        $query = "SELECT r.*, vt.type_name 
                  FROM reports r
                  JOIN violence_types vt ON r.violence_type_id = vt.violence_type_id
                  WHERE r.user_id = ?
                  ORDER BY r.report_date DESC
                  LIMIT ?, ?";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
        $stmt->bindParam(2, $offset, PDO::PARAM_INT);
        $stmt->bindParam(3, $limit, PDO::PARAM_INT);
        $stmt->execute();

        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate pagination info
        $totalPages = ceil($totalCount / $limit);

        http_response_code(RESPONSE_OK);
        echo json_encode([
            'status' => 'success',
            'data' => [
                'reports' => $reports,
                'pagination' => [
                    'total' => $totalCount,
                    'per_page' => $limit,
                    'current_page' => $page,
                    'total_pages' => $totalPages
                ]
            ]
        ]);
    }

    /**
     * Get all reports (admin function)
     */
    private function getAllReports()
    {
        // Get pagination parameters
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : DEFAULT_PAGE_SIZE;

        // Validate limit
        if ($limit > MAX_PAGE_SIZE) {
            $limit = MAX_PAGE_SIZE;
        }

        $offset = ($page - 1) * $limit;

        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM reports";
        $countStmt = $this->db->prepare($countQuery);
        $countStmt->execute();
        $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Get reports
        $query = "SELECT r.*, vt.type_name, u.name as user_name
                  FROM reports r
                  JOIN violence_types vt ON r.violence_type_id = vt.violence_type_id
                  JOIN users u ON r.user_id = u.user_id
                  ORDER BY r.report_date DESC
                  LIMIT ?, ?";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $offset, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->execute();

        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate pagination info
        $totalPages = ceil($totalCount / $limit);

        http_response_code(RESPONSE_OK);
        echo json_encode([
            'status' => 'success',
            'data' => [
                'reports' => $reports,
                'pagination' => [
                    'total' => $totalCount,
                    'per_page' => $limit,
                    'current_page' => $page,
                    'total_pages' => $totalPages
                ]
            ]
        ]);
    }

    /**
     * Upload media for a report
     * 
     * @param int $id Report ID
     */
    private function uploadReportMedia($id)
    {
        // Check if report exists
        $checkQuery = "SELECT * FROM reports WHERE report_id = ?";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute([$id]);

        if ($checkStmt->rowCount() === 0) {
            http_response_code(RESPONSE_NOT_FOUND);
            echo json_encode(['status' => 'error', 'message' => 'Report not found']);
            return;
        }

        // Handle file upload
        $uploadResult = $this->fileUploader->uploadReportMedia($id);

        if ($uploadResult['status'] === 'success') {
            http_response_code(RESPONSE_OK);
            echo json_encode($uploadResult);
        } else {
            http_response_code(RESPONSE_BAD_REQUEST);
            echo json_encode($uploadResult);
        }
    }

    /**
     * Add a status change to report history
     * 
     * @param int $reportId Report ID
     * @param string $status New status
     * @param string $notes Notes about the status change
     * @param string $changedBy Who changed the status
     * @return bool Success or failure
     */
    private function addReportStatusHistory($reportId, $status, $notes, $changedBy)
    {
        $query = "INSERT INTO report_status_history 
                  (report_id, status, notes, changed_by) 
                  VALUES (?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);
        $result = $stmt->execute([$reportId, $status, $notes, $changedBy]);

        // Also update the status in the reports table
        if ($result) {
            $updateQuery = "UPDATE reports SET status = ? WHERE report_id = ?";
            $updateStmt = $this->db->prepare($updateQuery);
            $updateStmt->execute([$status, $reportId]);
        }

        return $result;
    }


    /**
     * Update report status (admin function)
     * 
     * @param int $id Report ID
     */
    private function updateReportStatus($id)
    {
        // Get posted data
        $data = json_decode(file_get_contents("php://input"));

        // Validate input
        if (
            !isset($data->status) || empty($data->status) ||
            !isset($data->notes) || empty($data->notes) ||
            !isset($data->changed_by) || empty($data->changed_by)
        ) {
            http_response_code(RESPONSE_BAD_REQUEST);
            echo json_encode(['status' => 'error', 'message' => 'Status, notes, and changed_by are required']);
            return;
        }

        // Check if status is valid
        $validStatuses = [
            REPORT_STATUS_SUBMITTED,
            REPORT_STATUS_PROCESSING,
            REPORT_STATUS_INVESTIGATING,
            REPORT_STATUS_RESOLVED,
            REPORT_STATUS_CLOSED
        ];

        if (!in_array($data->status, $validStatuses)) {
            http_response_code(RESPONSE_BAD_REQUEST);
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid status. Must be one of: ' . implode(', ', $validStatuses)
            ]);
            return;
        }

        // Check if report exists
        $checkQuery = "SELECT * FROM reports WHERE report_id = ?";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute([$id]);

        if ($checkStmt->rowCount() === 0) {
            http_response_code(RESPONSE_NOT_FOUND);
            echo json_encode(['status' => 'error', 'message' => 'Report not found']);
            return;
        }

        // Add status history
        $result = $this->addReportStatusHistory(
            $id,
            $data->status,
            htmlspecialchars(strip_tags($data->notes)),
            htmlspecialchars(strip_tags($data->changed_by))
        );

        if ($result) {
            // Get updated report
            $reportQuery = "SELECT r.*, vt.type_name 
                      FROM reports r
                      JOIN violence_types vt ON r.violence_type_id = vt.violence_type_id
                      WHERE r.report_id = ?";
            $reportStmt = $this->db->prepare($reportQuery);
            $reportStmt->execute([$id]);
            $report = $reportStmt->fetch(PDO::FETCH_ASSOC);

            // Get status history
            $historyQuery = "SELECT * FROM report_status_history WHERE report_id = ? ORDER BY changed_at DESC";
            $historyStmt = $this->db->prepare($historyQuery);
            $historyStmt->execute([$id]);
            $history = $historyStmt->fetchAll(PDO::FETCH_ASSOC);

            // Add history to report
            $report['status_history'] = $history;

            http_response_code(RESPONSE_OK);
            echo json_encode([
                'status' => 'success',
                'message' => 'Report status updated successfully',
                'data' => $report
            ]);
        } else {
            http_response_code(RESPONSE_INTERNAL_ERROR);
            echo json_encode(['status' => 'error', 'message' => 'Unable to update report status']);
        }
    }

    /**
     * Delete a report (admin function)
     * 
     * @param int $id Report ID
     */
    private function deleteReport($id)
    {
        // Check if report exists
        $checkQuery = "SELECT * FROM reports WHERE report_id = ?";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute([$id]);

        if ($checkStmt->rowCount() === 0) {
            http_response_code(RESPONSE_NOT_FOUND);
            echo json_encode(['status' => 'error', 'message' => 'Report not found']);
            return;
        }

        // Begin transaction
        $this->db->beginTransaction();

        try {
            // Delete report media files
            $mediaQuery = "SELECT * FROM report_media WHERE report_id = ?";
            $mediaStmt = $this->db->prepare($mediaQuery);
            $mediaStmt->execute([$id]);
            $mediaFiles = $mediaStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($mediaFiles as $media) {
                $filePath = BASE_PATH . '/' . $media['file_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Delete report media records
            $deleteMediaQuery = "DELETE FROM report_media WHERE report_id = ?";
            $deleteMediaStmt = $this->db->prepare($deleteMediaQuery);
            $deleteMediaStmt->execute([$id]);

            // Delete report status history
            $deleteHistoryQuery = "DELETE FROM report_status_history WHERE report_id = ?";
            $deleteHistoryStmt = $this->db->prepare($deleteHistoryQuery);
            $deleteHistoryStmt->execute([$id]);

            // Delete report
            $deleteReportQuery = "DELETE FROM reports WHERE report_id = ?";
            $deleteReportStmt = $this->db->prepare($deleteReportQuery);
            $deleteReportStmt->execute([$id]);

            // Commit transaction
            $this->db->commit();

            http_response_code(RESPONSE_OK);
            echo json_encode([
                'status' => 'success',
                'message' => 'Report and all associated data deleted successfully'
            ]);
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->rollBack();

            http_response_code(RESPONSE_INTERNAL_ERROR);
            echo json_encode([
                'status' => 'error',
                'message' => 'Unable to delete report: ' . $e->getMessage()
            ]);
        }
    }
}
