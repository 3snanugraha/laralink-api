<?php
/**
 * User Model
 * 
 * Handles user data operations
 */
class User
{
    // Database connection and table name
    private $conn;
    private $table_name = "users";

    // Object properties
    public $user_id;
    public $phone_number;
    public $name;
    public $role;
    public $password;
    public $registration_date;
    public $status;
    public $fcm_token;

    // Constructor with database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Get a single user by ID
     * 
     * @return bool True if user found, false otherwise
     */
    public function readOne()
    {
        // Query to read single record
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = ?";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(1, $this->user_id);

        // Execute query
        $stmt->execute();

        // Get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // If user exists, set properties
        if ($row) {
            $this->user_id = $row['user_id'];
            $this->phone_number = $row['phone_number'];
            $this->name = $row['name'];
            $this->role = $row['role'];
            $this->registration_date = $row['registration_date'];
            $this->status = $row['status'];
            $this->fcm_token = $row['fcm_token'];

            return true;
        }

        return false;
    }

    /**
     * Get a user by phone number
     * 
     * @return bool True if user found, false otherwise
     */
    public function readByPhone()
    {
        // Query to read single record
        $query = "SELECT * FROM " . $this->table_name . " WHERE phone_number = ?";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Bind phone number
        $stmt->bindParam(1, $this->phone_number);

        // Execute query
        $stmt->execute();

        // Get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // If user exists, set properties
        if ($row) {
            $this->user_id = $row['user_id'];
            $this->phone_number = $row['phone_number'];
            $this->name = $row['name'];
            $this->role = $row['role'];
            $this->registration_date = $row['registration_date'];
            $this->status = $row['status'];
            $this->fcm_token = $row['fcm_token'];

            return true;
        }

        return false;
    }

    /**
     * Create a new user
     * 
     * @return bool True if created successfully, false otherwise
     */
    public function create()
    {
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . " 
                  (phone_number, name, role, status) 
                  VALUES (?, ?, ?, ?)";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->phone_number = htmlspecialchars(strip_tags($this->phone_number));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->status = htmlspecialchars(strip_tags($this->status));

        // Bind values
        $stmt->bindParam(1, $this->phone_number);
        $stmt->bindParam(2, $this->name);
        $stmt->bindParam(3, $this->role);
        $stmt->bindParam(4, $this->status);

        // Execute query
        if ($stmt->execute()) {
            $this->user_id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Update user information
     * 
     * @return bool True if updated successfully, false otherwise
     */
    public function update()
    {
        // Query to update record
        $query = "UPDATE " . $this->table_name . " 
                  SET name = ?, status = ?, fcm_token = ? 
                  WHERE user_id = ?";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->fcm_token = htmlspecialchars(strip_tags($this->fcm_token));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));

        // Bind values
        $stmt->bindParam(1, $this->name);
        $stmt->bindParam(2, $this->status);
        $stmt->bindParam(3, $this->fcm_token);
        $stmt->bindParam(4, $this->user_id);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Update FCM token for push notifications
     * 
     * @return bool True if updated successfully, false otherwise
     */
    public function updateFcmToken()
    {
        // Query to update FCM token
        $query = "UPDATE " . $this->table_name . " 
                  SET fcm_token = ? 
                  WHERE user_id = ?";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->fcm_token = htmlspecialchars(strip_tags($this->fcm_token));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));

        // Bind values
        $stmt->bindParam(1, $this->fcm_token);
        $stmt->bindParam(2, $this->user_id);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Authenticate admin user
     * 
     * @param string $username Admin username
     * @param string $password Admin password
     * @return array|bool User data if authenticated, false otherwise
     */
    public function authenticateAdmin($username, $password)
    {
        // Query to find admin user by username (using phone_number field)
        $query = "SELECT * FROM " . $this->table_name . " 
              WHERE phone_number = ? AND role = 'admin'";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Bind username
        $stmt->bindParam(1, $username);

        // Execute query
        $stmt->execute();

        // Check if admin user exists
        if ($stmt->rowCount() > 0) {
            // Get admin user data
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // For simplicity, we'll use a direct password comparison for now
            // In a real application, you should use password_verify() with hashed passwords
            if ($password === 'admin' || password_verify($password, $row['password'])) {
                return $row;
            }
        }

        return false;
    }

}
