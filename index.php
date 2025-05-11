<?php
// Enable error reporting during development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set headers for API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-KEY');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Include configuration files
require_once 'config/database.php';
require_once 'config/constants.php';

// Include utility classes
require_once 'utils/ApiResponse.php';
require_once 'utils/Validator.php';
require_once 'utils/Logger.php';

// Include middleware
require_once 'middleware/ApiKeyMiddleware.php';

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Validate API key for all requests
// Make sure ApiKeyMiddleware constructor accepts a database connection
$apiKeyMiddleware = new ApiKeyMiddleware();
if (!$apiKeyMiddleware->validate()) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid API key'
    ]);
    exit;
}

// Parse the URL
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// Remove empty segments
$uri = array_filter($uri);
$uri = array_values($uri);

// Get the base folder name (e.g., 'laralink-api')
$baseFolder = isset($uri[count($uri) - 2]) ? $uri[count($uri) - 2] : '';

// Get the resource name (e.g., 'users', 'reports')
$resource = isset($uri[count($uri) - 1]) ? $uri[count($uri) - 1] : '';

// Get ID if present
$id = isset($uri[count($uri)]) ? $uri[count($uri)] : null;

// Get subresource if present
$subresource = isset($uri[count($uri) + 1]) ? $uri[count($uri) + 1] : null;

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// Get request body for POST and PUT requests
$requestBody = null;
if ($method === 'POST' || $method === 'PUT') {
    $requestBody = json_decode(file_get_contents('php://input'), true);
}

// Log the API request
// Make sure Logger constructor accepts a database connection
$logger = new Logger();
$logger->logRequest();

// Route to appropriate controller based on resource
switch ($resource) {
    case 'users':
        require_once 'controllers/UserController.php';
        require_once 'models/User.php';
        // Make sure UserController constructor accepts a database connection
        $controller = new UserController();
        break;

    case 'reports':
        require_once 'controllers/ReportController.php';
        require_once 'models/Report.php';
        require_once 'models/ReportMedia.php';
        // Make sure ReportController constructor accepts a database connection
        $controller = new ReportController();
        break;

    case 'violence-types':
        require_once 'controllers/ViolenceTypeController.php';
        require_once 'models/ViolenceType.php';
        // Make sure ViolenceTypeController constructor accepts a database connection
        $controller = new ViolenceTypeController();
        break;

    case 'materials':
        require_once 'controllers/MaterialController.php';
        require_once 'models/ViolenceMaterial.php';
        // Make sure MaterialController constructor accepts a database connection
        $controller = new MaterialController();
        break;

    case 'contacts':
        require_once 'controllers/ContactController.php';
        require_once 'models/ContactInfo.php';
        // Make sure ContactController constructor accepts a database connection
        $controller = new ContactController();
        break;

    default:
        // If not accessing a specific resource, return API information
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => 'LaraLink+ API',
            'version' => '1.0.0',
            'documentation' => 'See README.md for API documentation'
        ]);
        exit;
}

// Call the appropriate controller method based on HTTP method and parameters
if (isset($controller)) {
    // Make sure handleRequest method accepts the correct number of parameters
    $controller->handleRequest($method, $id, $subresource);
} else {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => 'Resource not found'
    ]);
}
