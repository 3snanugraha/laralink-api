<?php
/**
 * Database Configuration
 * 
 * This file contains the database connection settings for the LaraLink+ API.
 */
class Database
{
    // Database credentials
    private $host = "localhost";
    private $db_name = "laralink_plus";
    private $username = "root"; // Change to your database username
    private $password = ""; // Change to your database password
    private $conn;

    /**
     * Get database connection
     * 
     * @return PDO|null Database connection object
     */
    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch (PDOException $e) {
            // Log the error but don't expose details in production
            error_log("Database Connection Error: " . $e->getMessage());
        }

        return $this->conn;
    }
}
