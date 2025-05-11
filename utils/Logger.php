<?php
/**
 * Logger Utility
 * 
 * Logs API requests and activities
 */
class Logger
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
     * Log an API request
     * 
     * @param string $apiKey API key used for the request
     * @param string $status Status message
     * @param int $statusCode HTTP status code
     */
    public function logRequest($apiKey = '', $status = 'success', $statusCode = 200)
    {
        // Get request information
        $endpoint = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];
        $ipAddress = $this->getClientIP();

        // If API key is not provided, try to get it from headers
        if (empty($apiKey)) {
            $headers = getallheaders();
            $apiKey = $headers['X-API-KEY'] ?? 'unknown';
        }

        // Truncate status to ensure it fits in the database column (assuming 20 chars max)
        $status = substr($status, 0, 20);

        // Insert log entry
        $query = "INSERT INTO api_logs (endpoint, method, ip_address, api_key, status) 
                  VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$endpoint, $method, $ipAddress, $apiKey, $status]);
    }


    /**
     * Get client IP address
     * 
     * @return string Client IP address
     */
    private function getClientIP()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            // IP from shared internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // IP passed from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            // Remote IP address
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    /**
     * Log a specific activity
     * 
     * @param int $userId User ID
     * @param string $action Action performed
     * @param string $resourceType Resource type
     * @param int $resourceId Resource ID
     * @param string $details Additional details
     */
    public function logActivity($userId, $action, $resourceType, $resourceId = null, $details = '')
    {
        // This method can be expanded to log user activities to a separate table
        // For now, we'll just log to the server error log
        $logMessage = sprintf(
            "User ID: %d | Action: %s | Resource: %s | Resource ID: %s | Details: %s",
            $userId,
            $action,
            $resourceType,
            $resourceId ?? 'N/A',
            $details
        );

        error_log($logMessage);
    }
}

