<?php
/**
 * API Key Middleware
 * 
 * Validates API keys for all requests
 */
class ApiKeyMiddleware
{
    private $db;
    private $logger;

    public function __construct()
    {
        // Get database connection
        require_once 'config/database.php';
        $database = new Database();
        $this->db = $database->getConnection();

        // Initialize logger
        require_once 'utils/Logger.php';
        $this->logger = new Logger();
    }

    /**
     * Validate API key from request headers
     * 
     * @return bool True if API key is valid, false otherwise
     */
    public function validate()
    {
        // Get API key from header
        $headers = getallheaders();

        // Debug: Log semua header yang diterima
        error_log('All headers received: ' . print_r($headers, true));

        // Coba berbagai format header API key (case-insensitive)
        $apiKey = null;
        foreach ($headers as $key => $value) {
            error_log("Header: $key = $value");
            if (strtolower($key) === 'x-api-key') {
                $apiKey = $value;
                break;
            }
        }

        // Fallback ke cara lama jika tidak ditemukan
        if (!$apiKey) {
            $apiKey = $headers['X-API-KEY'] ?? $headers['x-api-key'] ?? $_SERVER['HTTP_X_API_KEY'] ?? null;
        }

        if (!$apiKey) {
            $this->logger->logRequest('', 'Invalid API key - Missing key', RESPONSE_UNAUTHORIZED);
            error_log('API Key not found in headers');
            return false;
        }

        // Debug: Log API key yang ditemukan
        error_log('API Key found: ' . $apiKey);

        // Check if API key exists and is active
        $query = "SELECT * FROM api_keys WHERE api_key = ? AND is_active = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$apiKey]);

        if ($stmt->rowCount() > 0) {
            // Update last used timestamp
            $updateQuery = "UPDATE api_keys SET last_used_at = NOW() WHERE api_key = ?";
            $updateStmt = $this->db->prepare($updateQuery);
            $updateStmt->execute([$apiKey]);

            // Log successful API key validation
            $apiKeyData = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->logger->logRequest($apiKey, 'Valid API key - ' . $apiKeyData['name'], RESPONSE_OK);
            error_log('API Key validation successful');

            return true;
        }

        // Log invalid API key
        $this->logger->logRequest($apiKey, 'Invalid API key', RESPONSE_UNAUTHORIZED);
        error_log('API Key validation failed: Key not found in database or not active');
        return false;
    }


    /**
     * Get API key details if valid
     * 
     * @return array|null API key details or null if invalid
     */
    public function getApiKeyDetails()
    {
        // Get API key from header
        $headers = getallheaders();
        $apiKey = $headers['X-API-KEY'] ?? null;

        if (!$apiKey) {
            return null;
        }

        // Get API key details
        $query = "SELECT * FROM api_keys WHERE api_key = ? AND is_active = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$apiKey]);

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return null;
    }
}
