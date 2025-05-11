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
}
