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
        error_log("validateAdmin: Starting admin validation for user ID: " . $userId);

        if (!$userId) {
            error_log("validateAdmin: No user ID provided");
            return false;
        }

        try {
            // Check if user exists, is active, and has admin role
            $query = "SELECT * FROM users WHERE user_id = ? AND status = ? AND role = ?";
            error_log("validateAdmin: Query: " . $query);
            error_log("validateAdmin: Parameters: " . $userId . ", " . USER_STATUS_ACTIVE . ", " . USER_ROLE_ADMIN);

            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId, USER_STATUS_ACTIVE, USER_ROLE_ADMIN]);

            $result = $stmt->rowCount() > 0;
            error_log("validateAdmin: Query result: " . ($result ? "true" : "false"));

            if ($result) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                error_log("validateAdmin: User data: " . json_encode($user));
            }

            return $result;
        } catch (Exception $e) {
            error_log("validateAdmin: Database error: " . $e->getMessage());
            return false;
        }
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

    /**
     * Validate admin access for protected endpoints
     * 
     * @return bool True if valid admin, false otherwise
     */
    public function validateAdminAccess()
    {
        error_log("validateAdminAccess: Starting admin access validation");

        // Get user ID from request
        $userId = null;
        $headers = getallheaders();

        error_log("validateAdminAccess: Headers: " . json_encode($headers));

        // Check in Authorization header
        if (isset($headers['Authorization']) || isset($headers['authorization'])) {
            $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : $headers['authorization'];
            error_log("validateAdminAccess: Authorization header: " . $authHeader);

            if (strpos($authHeader, 'Bearer ') === 0) {
                $userId = substr($authHeader, 7);
                error_log("validateAdminAccess: Found user ID in Authorization header: " . $userId);
            }
        }

        // Check in GET parameters if not found in header
        if (!$userId && isset($_GET['admin_id'])) {
            $userId = $_GET['admin_id'];
            error_log("validateAdminAccess: Found user ID in GET parameter: " . $userId);
        }

        // Check in POST/PUT data if not found in GET
        if (!$userId) {
            $data = json_decode(file_get_contents("php://input"), true);
            if (isset($data['admin_id'])) {
                $userId = $data['admin_id'];
                error_log("validateAdminAccess: Found user ID in request body: " . $userId);
            }
        }

        if (!$userId) {
            error_log("validateAdminAccess: No user ID found in request");
            return false;
        }

        // Validate that the user is an admin
        $isAdmin = $this->validateAdmin($userId);
        error_log("validateAdminAccess: User ID " . $userId . " is admin: " . ($isAdmin ? "true" : "false"));
        return $isAdmin;
    }



    /**
     * Apply admin authentication middleware
     * 
     * This method checks if the request is from an admin and returns appropriate response if not
     * 
     * @return bool True if admin authenticated, false otherwise
     */
    public function applyAdminAuth()
    {
        error_log("applyAdminAuth: Starting admin authentication");

        $isAdmin = $this->validateAdminAccess();

        if (!$isAdmin) {
            error_log("applyAdminAuth: Admin authentication failed");
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'Admin authentication required'
            ]);
            return false;
        }

        error_log("applyAdminAuth: Admin authentication successful");
        return true;
    }
}
