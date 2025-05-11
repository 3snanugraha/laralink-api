<?php
/**
 * Contact Controller
 * 
 * Handles contact information
 */
class ContactController
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
     * Handle API requests to the contacts endpoint
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
                    $this->getContact($id);
                } else {
                    $this->getAllContacts();
                }
                break;

            default:
                http_response_code(RESPONSE_METHOD_NOT_ALLOWED);
                echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
                break;
        }
    }

    /**
     * Get all contact information
     */
    private function getAllContacts()
    {
        $query = "SELECT * FROM contact_info WHERE is_active = 1 ORDER BY contact_type";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(RESPONSE_OK);
        echo json_encode([
            'status' => 'success',
            'data' => $contacts
        ]);
    }

    /**
     * Get a specific contact by ID
     * 
     * @param int $id Contact ID
     */
    private function getContact($id)
    {
        $query = "SELECT * FROM contact_info WHERE contact_id = ? AND is_active = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            $contact = $stmt->fetch(PDO::FETCH_ASSOC);

            http_response_code(RESPONSE_OK);
            echo json_encode([
                'status' => 'success',
                'data' => $contact
            ]);
        } else {
            http_response_code(RESPONSE_NOT_FOUND);
            echo json_encode(['status' => 'error', 'message' => 'Contact not found']);
        }
    }
}
