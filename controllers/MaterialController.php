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
        $violenceTypeId = isset($_GET['violence_type_id']) ? htmlspecialchars(strip_tags($_GET['violence_type_id'])) : null;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;

        // Count total records for pagination
        $countQuery = "SELECT COUNT(*) as total FROM violence_materials";
        if ($violenceTypeId) {
            $countQuery .= " WHERE violence_type_id = ?";
        }

        $countStmt = $this->db->prepare($countQuery);
        if ($violenceTypeId) {
            $countStmt->execute([$violenceTypeId]);
        } else {
            $countStmt->execute();
        }

        $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        $totalPages = ceil($totalRecords / $limit);

        // Base query
        $query = "SELECT m.*, vt.type_name 
                  FROM violence_materials m
                  LEFT JOIN violence_types vt ON m.violence_type_id = vt.violence_type_id";

        // Add filter if violence_type_id is provided
        if ($violenceTypeId) {
            $query .= " WHERE m.violence_type_id = ?";
        }

        $query .= " ORDER BY m.created_at DESC LIMIT $offset, $limit";

        $stmt = $this->db->prepare($query);

        if ($violenceTypeId) {
            $stmt->execute([$violenceTypeId]);
        } else {
            $stmt->execute();
        }

        $materials = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(RESPONSE_OK);
        echo json_encode([
            'status' => 'success',
            'data' => [
                'materials' => $materials,
                'pagination' => [
                    'total_records' => $totalRecords,
                    'total_pages' => $totalPages,
                    'current_page' => $page,
                    'limit' => $limit
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
            echo json_encode([
                'status' => 'success',
                'data' => $material
            ]);
        } else {
            http_response_code(RESPONSE_NOT_FOUND);
            echo json_encode(['status' => 'error', 'message' => 'Material not found']);
        }
    }
}
