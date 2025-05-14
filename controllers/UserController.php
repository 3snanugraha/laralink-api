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
                if ($subresource === 'register' || $subresource === null) {
                    $this->registerUser();
                } else {
                    http_response_code(RESPONSE_NOT_FOUND);
                    echo json_encode(['status' => 'error', 'message' => 'Endpoint not found']);
                }
                break;
            case 'PUT':
                if ($id) {
                    $this->updateUser($id);
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

        // Validate input - check for either username or phone_number
        $phone_number = null;
        if (isset($data->phone_number) && !empty($data->phone_number)) {
            $phone_number = htmlspecialchars(strip_tags($data->phone_number));
        } else if (isset($data->username) && !empty($data->username)) {
            $phone_number = htmlspecialchars(strip_tags($data->username));
        } else {
            http_response_code(RESPONSE_BAD_REQUEST);
            echo json_encode([
                'status' => 'error',
                'message' => 'Phone number/username is required'
            ]);
            return;
        }

        if (!isset($data->password) || empty($data->password)) {
            http_response_code(RESPONSE_BAD_REQUEST);
            echo json_encode([
                'status' => 'error',
                'message' => 'Password is required'
            ]);
            return;
        }

        $password = $data->password; // Don't sanitize password before verification

        // Create user model instance
        $user = new User($this->db);

        // Attempt to authenticate admin
        $result = $user->authenticateAdmin($phone_number, $password);

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

}
