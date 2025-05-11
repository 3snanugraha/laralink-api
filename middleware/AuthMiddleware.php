<?php
/**
 * Authentication Middleware
 * 
 * Validates user authentication for protected endpoints
 */
class AuthMiddleware
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
     * Validate user authentication
     * 
     * @param int $userId User ID to validate
     * @return bool True if user is valid and active, false otherwise
     */
    public function validateUser($userId)
    {
        if (!$userId) {
            return false;
        }

        // Check if user exists and is active
        $query = "SELECT * FROM users WHERE user_id = ? AND status = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId, USER_STATUS_ACTIVE]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Validate admin authentication
     * 
     * @param int $userId User ID to validate
     * @return bool True if user is a valid admin, false otherwise
     */
    public function validateAdmin($userId)
    {
        if (!$userId) {
            return false;
        }

        // Check if user exists, is active, and has admin role
        $query = "SELECT * FROM users WHERE user_id = ? AND status = ? AND role = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId, USER_STATUS_ACTIVE, USER_ROLE_ADMIN]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Validate user ownership of a resource
     * 
     * @param int $userId User ID to validate
     * @param string $resourceType Type of resource (e.g., 'reports')
     * @param int $resourceId ID of the resource
     * @return bool True if user owns the resource, false otherwise
     */
    public function validateOwnership($userId, $resourceType, $resourceId)
    {
        if (!$userId || !$resourceType || !$resourceId) {
            return false;
        }

        $query = "";

        // Determine query based on resource type
        switch ($resourceType) {
            case 'reports':
                $query = "SELECT * FROM reports WHERE report_id = ? AND user_id = ?";
                break;

            // Add other resource types as needed

            default:
                return false;
        }

        // Execute query
        $stmt = $this->db->prepare($query);
        $stmt->execute([$resourceId, $userId]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Get user from request
     * 
     * @return array|null User data or null if not found/valid
     */
    public function getUserFromRequest()
    {
        // Get user ID from request
        $userId = null;

        // Check in GET parameters
        if (isset($_GET['user_id'])) {
            $userId = $_GET['user_id'];
        }

        // Check in POST/PUT data
        if (!$userId) {
            $data = json_decode(file_get_contents("php://input"), true);
            if (isset($data['user_id'])) {
                $userId = $data['user_id'];
            }
        }

        if (!$userId) {
            return null;
        }

        // Get user data
        $query = "SELECT * FROM users WHERE user_id = ? AND status = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId, USER_STATUS_ACTIVE]);

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return null;
    }
}
