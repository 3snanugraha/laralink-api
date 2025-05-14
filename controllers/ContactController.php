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

            case 'POST':
                $this->createContact();
                break;

            case 'PUT':
                if ($id) {
                    $this->updateContact($id);
                } else {
                    http_response_code(RESPONSE_BAD_REQUEST);
                    echo json_encode(['status' => 'error', 'message' => 'Contact ID is required']);
                }
                break;

            case 'DELETE':
                if ($id) {
                    $this->deleteContact($id);
                } else {
                    http_response_code(RESPONSE_BAD_REQUEST);
                    echo json_encode(['status' => 'error', 'message' => 'Contact ID is required']);
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

    /**
     * Create a new contact
     */
    private function createContact()
    {
        // Get posted data
        $data = json_decode(file_get_contents("php://input"));

        // Validate input
        if (
            !isset($data->contact_type) || empty($data->contact_type) ||
            !isset($data->contact_value) || empty($data->contact_value)
        ) {
            http_response_code(RESPONSE_BAD_REQUEST);
            echo json_encode(['status' => 'error', 'message' => 'Contact type and value are required']);
            return;
        }

        // Sanitize input
        $contact_type = htmlspecialchars(strip_tags($data->contact_type));
        $contact_value = htmlspecialchars(strip_tags($data->contact_value));
        $description = isset($data->description) ? htmlspecialchars(strip_tags($data->description)) : null;
        $is_active = isset($data->is_active) ? ($data->is_active ? 1 : 0) : 1;

        // Insert contact
        $query = "INSERT INTO contact_info (contact_type, contact_value, description, is_active) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);

        if ($stmt->execute([$contact_type, $contact_value, $description, $is_active])) {
            $contactId = $this->db->lastInsertId();

            http_response_code(RESPONSE_CREATED);
            echo json_encode([
                'status' => 'success',
                'message' => 'Contact created successfully',
                'data' => [
                    'contact_id' => $contactId,
                    'contact_type' => $contact_type,
                    'contact_value' => $contact_value,
                    'description' => $description,
                    'is_active' => $is_active
                ]
            ]);
        } else {
            http_response_code(RESPONSE_INTERNAL_ERROR);
            echo json_encode(['status' => 'error', 'message' => 'Unable to create contact']);
        }
    }

    /**
     * Update a contact
     * 
     * @param int $id Contact ID
     */
    private function updateContact($id)
    {
        // Get posted data
        $data = json_decode(file_get_contents("php://input"));

        // Check if contact exists
        $checkQuery = "SELECT * FROM contact_info WHERE contact_id = ?";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute([$id]);

        if ($checkStmt->rowCount() === 0) {
            http_response_code(RESPONSE_NOT_FOUND);
            echo json_encode(['status' => 'error', 'message' => 'Contact not found']);
            return;
        }

        // Build update query
        $updateFields = [];
        $params = [];

        if (isset($data->contact_type) && !empty($data->contact_type)) {
            $updateFields[] = "contact_type = ?";
            $params[] = htmlspecialchars(strip_tags($data->contact_type));
        }

        if (isset($data->contact_value) && !empty($data->contact_value)) {
            $updateFields[] = "contact_value = ?";
            $params[] = htmlspecialchars(strip_tags($data->contact_value));
        }

        if (isset($data->description)) {
            $updateFields[] = "description = ?";
            $params[] = htmlspecialchars(strip_tags($data->description));
        }

        if (isset($data->is_active)) {
            $updateFields[] = "is_active = ?";
            $params[] = $data->is_active ? 1 : 0;
        }

        if (empty($updateFields)) {
            http_response_code(RESPONSE_BAD_REQUEST);
            echo json_encode(['status' => 'error', 'message' => 'No fields to update']);
            return;
        }

        // Add contact_id to params
        $params[] = $id;

        $query = "UPDATE contact_info SET " . implode(", ", $updateFields) . " WHERE contact_id = ?";
        $stmt = $this->db->prepare($query);

        if ($stmt->execute($params)) {
            // Get updated contact
            $contactQuery = "SELECT * FROM contact_info WHERE contact_id = ?";
            $contactStmt = $this->db->prepare($contactQuery);
            $contactStmt->execute([$id]);
            $contact = $contactStmt->fetch(PDO::FETCH_ASSOC);

            http_response_code(RESPONSE_OK);
            echo json_encode([
                'status' => 'success',
                'message' => 'Contact updated successfully',
                'data' => $contact
            ]);
        } else {
            http_response_code(RESPONSE_INTERNAL_ERROR);
            echo json_encode(['status' => 'error', 'message' => 'Unable to update contact']);
        }
    }

    /**
     * Delete a contact
     * 
     * @param int $id Contact ID
     */
    private function deleteContact($id)
    {
        // Check if contact exists
        $checkQuery = "SELECT * FROM contact_info WHERE contact_id = ?";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute([$id]);

        if ($checkStmt->rowCount() === 0) {
            http_response_code(RESPONSE_NOT_FOUND);
            echo json_encode(['status' => 'error', 'message' => 'Contact not found']);
            return;
        }

        // Delete contact
        $query = "DELETE FROM contact_info WHERE contact_id = ?";
        $stmt = $this->db->prepare($query);

        if ($stmt->execute([$id])) {
            http_response_code(RESPONSE_OK);
            echo json_encode([
                'status' => 'success',
                'message' => 'Contact deleted successfully'
            ]);
        } else {
            http_response_code(RESPONSE_INTERNAL_ERROR);
            echo json_encode(['status' => 'error', 'message' => 'Unable to delete contact']);
        }
    }
}
