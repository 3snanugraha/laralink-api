<?php
/**
 * Violence Type Controller
 * 
 * Handles violence types data
 */
class ViolenceTypeController
{
    private $db;

    public function __construct()
    {
        // Get database connection
        require_once 'config/database.php';
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Handle API requests to the violence-types endpoint
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
                    $this->getViolenceType($id);
                } else {
                    $this->getAllViolenceTypes();
                }
                break;

            case 'POST':
                $this->createViolenceType();
                break;

            case 'PUT':
                if ($id) {
                    $this->updateViolenceType($id);
                } else {
                    http_response_code(RESPONSE_BAD_REQUEST);
                    echo json_encode(['status' => 'error', 'message' => 'Violence type ID is required']);
                }
                break;

            case 'DELETE':
                if ($id) {
                    $this->deleteViolenceType($id);
                } else {
                    http_response_code(RESPONSE_BAD_REQUEST);
                    echo json_encode(['status' => 'error', 'message' => 'Violence type ID is required']);
                }
                break;

            default:
                http_response_code(RESPONSE_METHOD_NOT_ALLOWED);
                echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
                break;
        }
    }


    /**
     * Get all violence types
     */
    private function getAllViolenceTypes()
    {
        $query = "SELECT * FROM violence_types ORDER BY type_name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $violenceTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(RESPONSE_OK);
        echo json_encode([
            'status' => 'success',
            'data' => $violenceTypes
        ]);
    }

    /**
     * Get a specific violence type by ID
     * 
     * @param int $id Violence type ID
     */
    private function getViolenceType($id)
    {
        $query = "SELECT * FROM violence_types WHERE violence_type_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            $violenceType = $stmt->fetch(PDO::FETCH_ASSOC);

            http_response_code(RESPONSE_OK);
            echo json_encode([
                'status' => 'success',
                'data' => $violenceType
            ]);
        } else {
            http_response_code(RESPONSE_NOT_FOUND);
            echo json_encode(['status' => 'error', 'message' => 'Violence type not found']);
        }
    }


    /**
     * Create a new violence type
     */
    private function createViolenceType()
    {
        // Get posted data
        $data = json_decode(file_get_contents("php://input"));

        // Validate input
        if (!isset($data->type_name) || empty($data->type_name)) {
            http_response_code(RESPONSE_BAD_REQUEST);
            echo json_encode(['status' => 'error', 'message' => 'Type name is required']);
            return;
        }

        // Sanitize input
        $type_name = htmlspecialchars(strip_tags($data->type_name));
        $description = isset($data->description) ? htmlspecialchars(strip_tags($data->description)) : null;
        $icon_url = isset($data->icon_url) ? htmlspecialchars(strip_tags($data->icon_url)) : null;

        // Insert violence type
        $query = "INSERT INTO violence_types (type_name, description, icon_url) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);

        if ($stmt->execute([$type_name, $description, $icon_url])) {
            $typeId = $this->db->lastInsertId();

            http_response_code(RESPONSE_CREATED);
            echo json_encode([
                'status' => 'success',
                'message' => 'Violence type created successfully',
                'data' => [
                    'violence_type_id' => $typeId,
                    'type_name' => $type_name,
                    'description' => $description,
                    'icon_url' => $icon_url
                ]
            ]);
        } else {
            http_response_code(RESPONSE_INTERNAL_ERROR);
            echo json_encode(['status' => 'error', 'message' => 'Unable to create violence type']);
        }
    }

    /**
     * Update a violence type
     * 
     * @param int $id Violence type ID
     */
    private function updateViolenceType($id)
    {
        // Get posted data
        $data = json_decode(file_get_contents("php://input"));

        // Check if violence type exists
        $checkQuery = "SELECT * FROM violence_types WHERE violence_type_id = ?";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute([$id]);

        if ($checkStmt->rowCount() === 0) {
            http_response_code(RESPONSE_NOT_FOUND);
            echo json_encode(['status' => 'error', 'message' => 'Violence type not found']);
            return;
        }

        // Build update query
        $updateFields = [];
        $params = [];

        if (isset($data->type_name) && !empty($data->type_name)) {
            $updateFields[] = "type_name = ?";
            $params[] = htmlspecialchars(strip_tags($data->type_name));
        }

        if (isset($data->description)) {
            $updateFields[] = "description = ?";
            $params[] = htmlspecialchars(strip_tags($data->description));
        }

        if (isset($data->icon_url)) {
            $updateFields[] = "icon_url = ?";
            $params[] = htmlspecialchars(strip_tags($data->icon_url));
        }

        if (empty($updateFields)) {
            http_response_code(RESPONSE_BAD_REQUEST);
            echo json_encode(['status' => 'error', 'message' => 'No fields to update']);
            return;
        }

        // Add violence_type_id to params
        $params[] = $id;

        $query = "UPDATE violence_types SET " . implode(", ", $updateFields) . " WHERE violence_type_id = ?";
        $stmt = $this->db->prepare($query);

        if ($stmt->execute($params)) {
            // Get updated violence type
            $typeQuery = "SELECT * FROM violence_types WHERE violence_type_id = ?";
            $typeStmt = $this->db->prepare($typeQuery);
            $typeStmt->execute([$id]);
            $type = $typeStmt->fetch(PDO::FETCH_ASSOC);

            http_response_code(RESPONSE_OK);
            echo json_encode([
                'status' => 'success',
                'message' => 'Violence type updated successfully',
                'data' => $type
            ]);
        } else {
            http_response_code(RESPONSE_INTERNAL_ERROR);
            echo json_encode(['status' => 'error', 'message' => 'Unable to update violence type']);
        }
    }

    /**
     * Delete a violence type
     * 
     * @param int $id Violence type ID
     */
    private function deleteViolenceType($id)
    {
        // Check if violence type exists
        $checkQuery = "SELECT * FROM violence_types WHERE violence_type_id = ?";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute([$id]);

        if ($checkStmt->rowCount() === 0) {
            http_response_code(RESPONSE_NOT_FOUND);
            echo json_encode(['status' => 'error', 'message' => 'Violence type not found']);
            return;
        }

        // Check if there are any materials or reports using this violence type
        $usageQuery = "SELECT 
                    (SELECT COUNT(*) FROM violence_materials WHERE violence_type_id = ?) as materials_count,
                    (SELECT COUNT(*) FROM reports WHERE violence_type_id = ?) as reports_count";
        $usageStmt = $this->db->prepare($usageQuery);
        $usageStmt->execute([$id, $id]);
        $usage = $usageStmt->fetch(PDO::FETCH_ASSOC);

        if ($usage['materials_count'] > 0 || $usage['reports_count'] > 0) {
            http_response_code(RESPONSE_BAD_REQUEST);
            echo json_encode([
                'status' => 'error',
                'message' => 'Cannot delete violence type that is in use',
                'data' => [
                    'materials_count' => $usage['materials_count'],
                    'reports_count' => $usage['reports_count']
                ]
            ]);
            return;
        }

        // Delete violence type
        $query = "DELETE FROM violence_types WHERE violence_type_id = ?";
        $stmt = $this->db->prepare($query);

        if ($stmt->execute([$id])) {
            http_response_code(RESPONSE_OK);
            echo json_encode([
                'status' => 'success',
                'message' => 'Violence type deleted successfully'
            ]);
        } else {
            http_response_code(RESPONSE_INTERNAL_ERROR);
            echo json_encode(['status' => 'error', 'message' => 'Unable to delete violence type']);
        }
    }
}
