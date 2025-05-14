<?php
/**
 * Material Controller
 * 
 * Handles educational materials
 */
class MaterialController
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
     * Handle API requests to the materials endpoint
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
                    $this->getMaterial($id);
                } else {
                    $this->getAllMaterials();
                }
                break;

            case 'POST':
                if ($id && $subresource === 'image') {
                    $this->uploadMaterialImage($id);
                } else {
                    $this->createMaterial();
                }
                break;

            case 'PUT':
                if ($id) {
                    $this->updateMaterial($id);
                } else {
                    http_response_code(RESPONSE_BAD_REQUEST);
                    echo json_encode(['status' => 'error', 'message' => 'Material ID is required']);
                }
                break;

            case 'DELETE':
                if ($id) {
                    $this->deleteMaterial($id);
                } else {
                    http_response_code(RESPONSE_BAD_REQUEST);
                    echo json_encode(['status' => 'error', 'message' => 'Material ID is required']);
                }
                break;

            default:
                http_response_code(RESPONSE_METHOD_NOT_ALLOWED);
                echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
                break;
        }
    }

    /**
     * Get all educational materials
     * Can be filtered by violence_type_id
     */
    private function getAllMaterials()
    {
        // Get query parameters
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
        $violenceTypeId = isset($_GET['violence_type_id']) ? $_GET['violence_type_id'] : null;

        // Calculate offset
        $offset = ($page - 1) * $limit;

        // Build query
        $query = "SELECT m.*, vt.type_name 
                  FROM violence_materials m
                  LEFT JOIN violence_types vt ON m.violence_type_id = vt.violence_type_id";

        // Add filter if violence_type_id is provided
        if ($violenceTypeId) {
            $query .= " WHERE m.violence_type_id = ?";
        }

        // Add pagination
        $query .= " ORDER BY m.created_at DESC LIMIT ?, ?";

        // Prepare statement
        $stmt = $this->db->prepare($query);

        // Bind parameters
        if ($violenceTypeId) {
            $stmt->bindParam(1, $violenceTypeId, PDO::PARAM_INT);
            $stmt->bindParam(2, $offset, PDO::PARAM_INT);
            $stmt->bindParam(3, $limit, PDO::PARAM_INT);
        } else {
            $stmt->bindParam(1, $offset, PDO::PARAM_INT);
            $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        }

        // Execute query
        $stmt->execute();

        // Get total count for pagination
        $countQuery = "SELECT COUNT(*) as total FROM violence_materials";
        if ($violenceTypeId) {
            $countQuery .= " WHERE violence_type_id = ?";
            $countStmt = $this->db->prepare($countQuery);
            $countStmt->execute([$violenceTypeId]);
        } else {
            $countStmt = $this->db->prepare($countQuery);
            $countStmt->execute();
        }
        $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        $totalPages = ceil($totalCount / $limit);

        // Fetch materials
        $materials = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(RESPONSE_OK);
        echo json_encode([
            'status' => 'success',
            'data' => [
                'materials' => $materials,
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
     * Get a specific educational material by ID
     * 
     * @param int $id Material ID
     */
    private function getMaterial($id)
    {
        $query = "SELECT m.*, vt.type_name 
                  FROM violence_materials m
                  LEFT JOIN violence_types vt ON m.violence_type_id = vt.violence_type_id
                  WHERE m.material_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            $material = $stmt->fetch(PDO::FETCH_ASSOC);
            http_response_code(RESPONSE_OK);
            echo json_encode(['status' => 'success', 'data' => $material]);
        } else {
            http_response_code(RESPONSE_NOT_FOUND);
            echo json_encode(['status' => 'error', 'message' => 'Material not found']);
        }
    }

    /**
     * Create a new educational material
     */
    private function createMaterial()
    {
        // Get posted data
        $data = json_decode(file_get_contents("php://input"));

        // Validate input
        if (
            !isset($data->title) || empty($data->title) ||
            !isset($data->content) || empty($data->content) ||
            !isset($data->violence_type_id) || empty($data->violence_type_id)
        ) {
            http_response_code(RESPONSE_BAD_REQUEST);
            echo json_encode(['status' => 'error', 'message' => 'Title, content, and violence type are required']);
            return;
        }

        // Sanitize input
        $title = htmlspecialchars(strip_tags($data->title));
        $content = htmlspecialchars(strip_tags($data->content));
        $violence_type_id = htmlspecialchars(strip_tags($data->violence_type_id));

        // Handle image_url if provided
        $image_url = null;
        if (isset($data->image_url) && !empty($data->image_url)) {
            $image_url = htmlspecialchars(strip_tags($data->image_url));
        }

        // Insert material
        $query = "INSERT INTO violence_materials (title, content, violence_type_id, image_url) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);

        if ($stmt->execute([$title, $content, $violence_type_id, $image_url])) {
            $materialId = $this->db->lastInsertId();

            http_response_code(RESPONSE_CREATED);
            echo json_encode([
                'status' => 'success',
                'message' => 'Material created successfully',
                'data' => [
                    'material_id' => $materialId,
                    'title' => $title,
                    'content' => $content,
                    'violence_type_id' => $violence_type_id,
                    'image_url' => $image_url
                ]
            ]);
        } else {
            http_response_code(RESPONSE_INTERNAL_ERROR);
            echo json_encode(['status' => 'error', 'message' => 'Unable to create material']);
        }
    }

    /**
     * Update an existing educational material
     * 
     * @param int $id Material ID
     */
    private function updateMaterial($id)
    {
        // Get posted data
        $data = json_decode(file_get_contents("php://input"));

        // Check if material exists
        $checkQuery = "SELECT * FROM violence_materials WHERE material_id = ?";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute([$id]);

        if ($checkStmt->rowCount() === 0) {
            http_response_code(RESPONSE_NOT_FOUND);
            echo json_encode(['status' => 'error', 'message' => 'Material not found']);
            return;
        }

        // Build update query
        $updateFields = [];
        $params = [];

        if (isset($data->title) && !empty($data->title)) {
            $updateFields[] = "title = ?";
            $params[] = htmlspecialchars(strip_tags($data->title));
        }

        if (isset($data->content) && !empty($data->content)) {
            $updateFields[] = "content = ?";
            $params[] = htmlspecialchars(strip_tags($data->content));
        }

        if (isset($data->violence_type_id) && !empty($data->violence_type_id)) {
            $updateFields[] = "violence_type_id = ?";
            $params[] = htmlspecialchars(strip_tags($data->violence_type_id));
        }

        // Handle image_url update
        if (isset($data->image_url)) {
            $updateFields[] = "image_url = ?";
            $params[] = !empty($data->image_url) ? htmlspecialchars(strip_tags($data->image_url)) : null;
        }

        if (empty($updateFields)) {
            http_response_code(RESPONSE_BAD_REQUEST);
            echo json_encode(['status' => 'error', 'message' => 'No fields to update']);
            return;
        }

        // Add updated_at timestamp
        $updateFields[] = "updated_at = NOW()";

        // Add material_id to params
        $params[] = $id;

        $query = "UPDATE violence_materials SET " . implode(", ", $updateFields) . " WHERE material_id = ?";
        $stmt = $this->db->prepare($query);

        if ($stmt->execute($params)) {
            // Get updated material
            $materialQuery = "SELECT m.*, vt.type_name 
                          FROM violence_materials m
                          LEFT JOIN violence_types vt ON m.violence_type_id = vt.violence_type_id
                          WHERE m.material_id = ?";
            $materialStmt = $this->db->prepare($materialQuery);
            $materialStmt->execute([$id]);
            $material = $materialStmt->fetch(PDO::FETCH_ASSOC);

            http_response_code(RESPONSE_OK);
            echo json_encode([
                'status' => 'success',
                'message' => 'Material updated successfully',
                'data' => $material
            ]);
        } else {
            http_response_code(RESPONSE_INTERNAL_ERROR);
            echo json_encode(['status' => 'error', 'message' => 'Unable to update material']);
        }
    }

    /**
     * Delete an educational material
     * 
     * @param int $id Material ID
     */
    private function deleteMaterial($id)
    {
        // Check if material exists
        $checkQuery = "SELECT * FROM violence_materials WHERE material_id = ?";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute([$id]);

        if ($checkStmt->rowCount() === 0) {
            http_response_code(RESPONSE_NOT_FOUND);
            echo json_encode(['status' => 'error', 'message' => 'Material not found']);
            return;
        }

        // Delete material
        $query = "DELETE FROM violence_materials WHERE material_id = ?";
        $stmt = $this->db->prepare($query);

        if ($stmt->execute([$id])) {
            http_response_code(RESPONSE_OK);
            echo json_encode([
                'status' => 'success',
                'message' => 'Material deleted successfully'
            ]);
        } else {
            http_response_code(RESPONSE_INTERNAL_ERROR);
            echo json_encode(['status' => 'error', 'message' => 'Unable to delete material']);
        }
    }

    /**
     * Upload image for a material
     * 
     * @param int $id Material ID
     */
    private function uploadMaterialImage($id)
    {
        // Check if material exists
        $checkQuery = "SELECT * FROM violence_materials WHERE material_id = ?";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute([$id]);

        if ($checkStmt->rowCount() === 0) {
            http_response_code(RESPONSE_NOT_FOUND);
            echo json_encode(['status' => 'error', 'message' => 'Material not found']);
            return;
        }

        // Check if file was uploaded
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(RESPONSE_BAD_REQUEST);
            echo json_encode(['status' => 'error', 'message' => 'No image file uploaded or upload error']);
            return;
        }

        // Define upload directory
        $uploadDir = 'uploads/materials/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate unique filename
        $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $newFilename = uniqid() . '.' . $fileExtension;
        $targetPath = $uploadDir . $newFilename;

        // Check file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            http_response_code(RESPONSE_BAD_REQUEST);
            echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed']);
            return;
        }

        // Move uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            // Update material with image URL
            $query = "UPDATE violence_materials SET image_url = ?, updated_at = NOW() WHERE material_id = ?";
            $stmt = $this->db->prepare($query);

            $imageUrl = $targetPath; // Store the relative path

            if ($stmt->execute([$imageUrl, $id])) {
                http_response_code(RESPONSE_OK);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Image uploaded successfully',
                    'data' => [
                        'image_url' => $imageUrl
                    ]
                ]);
            } else {
                // Delete the uploaded file if database update fails
                unlink($targetPath);
                http_response_code(RESPONSE_INTERNAL_ERROR);
                echo json_encode(['status' => 'error', 'message' => 'Unable to update material with image URL']);
            }
        } else {
            http_response_code(RESPONSE_INTERNAL_ERROR);
            echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file']);
        }
    }
}

