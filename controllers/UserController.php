<?php
/**
 * User Controller
 * 
 * Handles user registration and authentication
 */
class UserController
{
    private $db;
    private $validator;

    public function __construct()
    {
        // Get database connection
        require_once 'config/database.php';
        $database = new Database();
        $this->db = $database->getConnection();

        // Initialize validator
        require_once 'utils/Validator.php';
        $this->validator = new Validator();
    }

    /**
     * Handle API requests to the user endpoint
     *
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string|null $id Resource ID
     * @param string|null $subresource Sub-resource name
     */
    public function handleRequest($method, $id = null, $subresource = null)
    {
        // Special case for admin login
        if ($method === 'POST' && $subresource === 'login') {
            $this->adminLogin();
            return;
        }

        // Handle regular user endpoints
        switch ($method) {
            case 'GET':
                if ($id) {
                    $this->getUser($id);
                } else {
                    $this->getAllUsers();
                }
                break;
            case 'POST':
                if ($subresource === 'register') {
                    $this->registerUser();
                } elseif ($subresource === 'admin') {
                    $this->createAdminUser();
                } else {
                    $this->registerUser(); // Default to regular registration
                }
                break;
            case 'PUT':
                if ($id) {
                    if ($subresource === 'status') {
                        $this->updateUserStatus($id);
                    } elseif ($subresource === 'role') {
                        $this->updateUserRole($id);
                    } else {
                        $this->updateUser($id);
                    }
                } else {
                    http_response_code(RESPONSE_BAD_REQUEST);
                    echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
                }
                break;
            case 'DELETE':
                if ($id) {
                    $this->deleteUser($id);
                } else {
                    http_response_code(RESPONSE_BAD_REQUEST);
                    echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
                }
                break;
            default:
                http_response_code(RESPONSE_METHOD_NOT_ALLOWED);
                echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
                break;
        }
    }

    /**
     * Register a new user or retrieve existing user
     */
    private function registerUser()
    {
        // Get posted data
        $data = json_decode(file_get_contents("php://input"));

        // Validate input
        if (!$this->validator->validateUserRegistration($data)) {
            return;
        }

        // Sanitize input
        $phone_number = htmlspecialchars(strip_tags($data->phone_number));
        $name = htmlspecialchars(strip_tags($data->name));

        // Check if phone number already exists
        $checkQuery = "SELECT * FROM users WHERE phone_number = ?";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute([$phone_number]);

        if ($checkStmt->rowCount() > 0) {
            // User already exists, return user data
            $user = $checkStmt->fetch(PDO::FETCH_ASSOC);
            http_response_code(RESPONSE_OK);
            echo json_encode([
                'status' => 'success',
                'message' => 'User already registered',
                'data' => [
                    'user_id' => $user['user_id'],
                    'name' => $user['name'],
                    'phone_number' => $user['phone_number'],
                    'role' => $user['role'],
                    'status' => $user['status']
                ]
            ]);
            return;
        }

        // Insert new user
        $query = "INSERT INTO users (phone_number, name) VALUES (?, ?)";
        $stmt = $this->db->prepare($query);

        if ($stmt->execute([$phone_number, $name])) {
            $userId = $this->db->lastInsertId();

            http_response_code(RESPONSE_CREATED);
            echo json_encode([
                'status' => 'success',
                'message' => 'User registered successfully',
                'data' => [
                    'user_id' => $userId,
                    'name' => $name,
                    'phone_number' => $phone_number,
                    'role' => USER_ROLE_USER,
                    'status' => USER_STATUS_ACTIVE
                ]
            ]);
        } else {
            http_response_code(RESPONSE_INTERNAL_ERROR);
            echo json_encode(['status' => 'error', 'message' => 'Unable to register user']);
        }
    }


    /**
     * Handle admin login
     */
    private function adminLogin()
    {
        // Get posted data
        $data = json_decode(file_get_contents("php://input"));

        // Validate input
        $validator = new Validator();
        if (!$validator->validateAdminLogin($data)) {
            return; // Validation error response already sent by validator
        }

        // Get phone number from either phone_number or username field
        $phone_number = isset($data->phone_number) ? $data->phone_number : $data->username;

        // Create user model instance
        $user = new User($this->db);

        // Attempt to authenticate admin
        $result = $user->authenticateAdmin($phone_number, $data->password);

        if ($result) {
            // Admin authenticated successfully
            http_response_code(RESPONSE_OK);
            echo json_encode([
                'status' => 'success',
                'message' => 'Admin login successful',
                'data' => [
                    'user_id' => $result['user_id'],
                    'name' => $result['name'],
                    'phone_number' => $result['phone_number'],
                    'role' => $result['role'],
                    'status' => $result['status']
                ]
            ]);
        } else {
            // Authentication failed
            http_response_code(RESPONSE_UNAUTHORIZED);
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid phone number/username or password'
            ]);
        }
    }


    /**
     * Update user information
     * 
     * @param int $id User ID
     */
    private function updateUser($id)
    {
        // Get posted data
        $data = json_decode(file_get_contents("php://input"));

        // Validate input
        if (!isset($data->name) || empty($data->name)) {
            http_response_code(RESPONSE_BAD_REQUEST);
            echo json_encode(['status' => 'error', 'message' => 'Name is required']);
            return;
        }

        // Sanitize input
        $name = htmlspecialchars(strip_tags($data->name));
        $fcm_token = isset($data->fcm_token) ? htmlspecialchars(strip_tags($data->fcm_token)) : null;

        // Check if user exists
        $checkQuery = "SELECT * FROM users WHERE user_id = ?";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute([$id]);

        if ($checkStmt->rowCount() === 0) {
            http_response_code(RESPONSE_NOT_FOUND);
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
            return;
        }

        // Update user
        $updateFields = ["name = ?"];
        $params = [$name];

        if ($fcm_token !== null) {
            $updateFields[] = "fcm_token = ?";
            $params[] = $fcm_token;
        }

        $params[] = $id; // Add user_id as the last parameter

        $query = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE user_id = ?";
        $stmt = $this->db->prepare($query);

        if ($stmt->execute($params)) {
            // Get updated user data
            $userQuery = "SELECT user_id, name, phone_number, role, status FROM users WHERE user_id = ?";
            $userStmt = $this->db->prepare($userQuery);
            $userStmt->execute([$id]);
            $user = $userStmt->fetch(PDO::FETCH_ASSOC);

            http_response_code(RESPONSE_OK);
            echo json_encode([
                'status' => 'success',
                'message' => 'User updated successfully',
                'data' => $user
            ]);
        } else {
            http_response_code(RESPONSE_INTERNAL_ERROR);
            echo json_encode(['status' => 'error', 'message' => 'Unable to update user']);
        }
    }

    /**
     * Get user details by ID
     * 
     * @param int $id User ID
     */
    private function getUser($id)
    {
        $query = "SELECT user_id, name, phone_number, role, status FROM users WHERE user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            http_response_code(RESPONSE_OK);
            echo json_encode([
                'status' => 'success',
                'data' => $user
            ]);
        } else {
            http_response_code(RESPONSE_NOT_FOUND);
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
        }
    }

    /**
     * Get all users
     */
    private function getAllUsers()
    {
        $query = "SELECT user_id, name, phone_number, role, status, registration_date FROM users ORDER BY user_id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            http_response_code(RESPONSE_OK);
            echo json_encode([
                'status' => 'success',
                'message' => 'Users retrieved successfully',
                'data' => $users
            ]);
        } else {
            http_response_code(RESPONSE_OK);
            echo json_encode([
                'status' => 'success',
                'message' => 'No users found',
                'data' => []
            ]);
        }
    }

    /**
     * Update user status (admin function)
     * 
     * @param int $id User ID
     */
    private function updateUserStatus($id)
    {
        // Get posted data
        $data = json_decode(file_get_contents("php://input"));

        // Validate input
        if (!isset($data->status) || empty($data->status)) {
            http_response_code(RESPONSE_BAD_REQUEST);
            echo json_encode(['status' => 'error', 'message' => 'Status is required']);
            return;
        }

        // Check if status is valid
        $validStatuses = [USER_STATUS_ACTIVE, USER_STATUS_INACTIVE, USER_STATUS_BLOCKED];
        if (!in_array($data->status, $validStatuses)) {
            http_response_code(RESPONSE_BAD_REQUEST);
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid status. Must be one of: ' . implode(', ', $validStatuses)
            ]);
            return;
        }

        // Check if user exists
        $checkQuery = "SELECT * FROM users WHERE user_id = ?";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute([$id]);

        if ($checkStmt->rowCount() === 0) {
            http_response_code(RESPONSE_NOT_FOUND);
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
            return;
        }

        // Update user status
        $query = "UPDATE users SET status = ? WHERE user_id = ?";
        $stmt = $this->db->prepare($query);

        if ($stmt->execute([$data->status, $id])) {
            // Get updated user
            $userQuery = "SELECT user_id, name, phone_number, role, status FROM users WHERE user_id = ?";
            $userStmt = $this->db->prepare($userQuery);
            $userStmt->execute([$id]);
            $user = $userStmt->fetch(PDO::FETCH_ASSOC);

            http_response_code(RESPONSE_OK);
            echo json_encode([
                'status' => 'success',
                'message' => 'User status updated successfully',
                'data' => $user
            ]);
        } else {
            http_response_code(RESPONSE_INTERNAL_ERROR);
            echo json_encode(['status' => 'error', 'message' => 'Unable to update user status']);
        }
    }

    /**
     * Update user role (admin function)
     * 
     * @param int $id User ID
     */
    private function updateUserRole($id)
    {
        // Get posted data
        $data = json_decode(file_get_contents("php://input"));

        // Validate input
        if (!isset($data->role) || empty($data->role)) {
            http_response_code(RESPONSE_BAD_REQUEST);
            echo json_encode(['status' => 'error', 'message' => 'Role is required']);
            return;
        }

        // Check if role is valid
        $validRoles = [USER_ROLE_USER, USER_ROLE_ADMIN];
        if (!in_array($data->role, $validRoles)) {
            http_response_code(RESPONSE_BAD_REQUEST);
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid role. Must be one of: ' . implode(', ', $validRoles)
            ]);
            return;
        }

        // Check if user exists
        $checkQuery = "SELECT * FROM users WHERE user_id = ?";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute([$id]);

        if ($checkStmt->rowCount() === 0) {
            http_response_code(RESPONSE_NOT_FOUND);
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
            return;
        }

        // Update user role
        $query = "UPDATE users SET role = ? WHERE user_id = ?";
        $stmt = $this->db->prepare($query);

        if ($stmt->execute([$data->role, $id])) {
            // Get updated user
            $userQuery = "SELECT user_id, name, phone_number, role, status FROM users WHERE user_id = ?";
            $userStmt = $this->db->prepare($userQuery);
            $userStmt->execute([$id]);
            $user = $userStmt->fetch(PDO::FETCH_ASSOC);

            http_response_code(RESPONSE_OK);
            echo json_encode([
                'status' => 'success',
                'message' => 'User role updated successfully',
                'data' => $user
            ]);
        } else {
            http_response_code(RESPONSE_INTERNAL_ERROR);
            echo json_encode(['status' => 'error', 'message' => 'Unable to update user role']);
        }
    }

    /**
     * Create a new admin user
     */
    private function createAdminUser()
    {
        // Get posted data
        $data = json_decode(file_get_contents("php://input"));

        // Validate input
        if (
            !isset($data->phone_number) || empty($data->phone_number) ||
            !isset($data->name) || empty($data->name) ||
            !isset($data->password) || empty($data->password)
        ) {
            http_response_code(RESPONSE_BAD_REQUEST);
            echo json_encode(['status' => 'error', 'message' => 'Phone number, name, and password are required']);
            return;
        }

        // Check if phone number already exists
        $checkQuery = "SELECT * FROM users WHERE phone_number = ?";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute([$data->phone_number]);

        if ($checkStmt->rowCount() > 0) {
            http_response_code(RESPONSE_BAD_REQUEST);
            echo json_encode(['status' => 'error', 'message' => 'Phone number already in use']);
            return;
        }

        // Sanitize input
        $phone_number = htmlspecialchars(strip_tags($data->phone_number));
        $name = htmlspecialchars(strip_tags($data->name));

        // Hash password
        $password_hash = password_hash($data->password, PASSWORD_DEFAULT);

        // Insert admin user
        $query = "INSERT INTO users (phone_number, name, password, role, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);

        if ($stmt->execute([$phone_number, $name, $password_hash, USER_ROLE_ADMIN, USER_STATUS_ACTIVE])) {
            $userId = $this->db->lastInsertId();

            http_response_code(RESPONSE_CREATED);
            echo json_encode([
                'status' => 'success',
                'message' => 'Admin user created successfully',
                'data' => [
                    'user_id' => $userId,
                    'name' => $name,
                    'phone_number' => $phone_number,
                    'role' => USER_ROLE_ADMIN,
                    'status' => USER_STATUS_ACTIVE
                ]
            ]);
        } else {
            http_response_code(RESPONSE_INTERNAL_ERROR);
            echo json_encode(['status' => 'error', 'message' => 'Unable to create admin user']);
        }
    }

    /**
     * Delete a user
     * 
     * @param int $id User ID
     */
    private function deleteUser($id)
    {
        // Check if user exists
        $checkQuery = "SELECT * FROM users WHERE user_id = ?";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute([$id]);

        if ($checkStmt->rowCount() === 0) {
            http_response_code(RESPONSE_NOT_FOUND);
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
            return;
        }

        // Check if user has reports
        $reportsQuery = "SELECT COUNT(*) as count FROM reports WHERE user_id = ?";
        $reportsStmt = $this->db->prepare($reportsQuery);
        $reportsStmt->execute([$id]);
        $reportsCount = $reportsStmt->fetch(PDO::FETCH_ASSOC)['count'];

        if ($reportsCount > 0) {
            http_response_code(RESPONSE_BAD_REQUEST);
            echo json_encode([
                'status' => 'error',
                'message' => 'Cannot delete user with reports. Consider deactivating instead.',
                'data' => [
                    'reports_count' => $reportsCount
                ]
            ]);
            return;
        }

        // Delete user
        $query = "DELETE FROM users WHERE user_id = ?";
        $stmt = $this->db->prepare($query);

        if ($stmt->execute([$id])) {
            http_response_code(RESPONSE_OK);
            echo json_encode([
                'status' => 'success',
                'message' => 'User deleted successfully'
            ]);
        } else {
            http_response_code(RESPONSE_INTERNAL_ERROR);
            echo json_encode(['status' => 'error', 'message' => 'Unable to delete user']);
        }
    }

}
