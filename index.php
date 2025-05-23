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

// Find the index of 'laralink-api' to determine where the actual API path starts
$apiBaseIndex = array_search('laralink-api', $uri);
if ($apiBaseIndex !== false) {
    // Remove everything before and including 'laralink-api'
    $uri = array_slice($uri, $apiBaseIndex + 1);
}

// Initialize variables
$resource = '';
$id = null;
$subresource = null;

// Special case for admin/login
if (count($uri) >= 2 && $uri[0] === 'admin' && $uri[1] === 'login') {
    $resource = 'admin';
    $subresource = 'login';
    $id = null;
}
// Regular URL parsing for other routes
else if (count($uri) > 0) {
    // Get the total number of segments
    $uriCount = count($uri);

    // Extract resource, ID, and subresource from URI
    // Check if we have at least one segment
    $lastIndex = $uriCount - 1;

    // The last segment could be either the resource name or an ID
    if (is_numeric($uri[$lastIndex])) {
        // If the last segment is numeric, it's an ID
        $id = $uri[$lastIndex];
        // The second-to-last segment is the resource
        if ($lastIndex > 0) {
            $resource = $uri[$lastIndex - 1];
        }
        // Check if there's a subresource (for routes like /reports/1/media)
        if ($lastIndex < count($uri) - 1) {
            $subresource = $uri[$lastIndex + 1];
        }
    } else {
        // If the last segment is not numeric, it's the resource name or a subresource
        // Check if the second-to-last segment is numeric (for routes like /reports/1/media)
        if ($lastIndex > 0 && is_numeric($uri[$lastIndex - 1])) {
            $subresource = $uri[$lastIndex];
            $id = $uri[$lastIndex - 1];
            if ($lastIndex > 1) {
                $resource = $uri[$lastIndex - 2];
            }
        } else {
            // It's just a resource name
            $resource = $uri[$lastIndex];
        }
    }
}

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// Get request body for POST and PUT requests
$requestBody = null;
if ($method === 'POST' || $method === 'PUT') {
    $requestBody = file_get_contents('php://input');
    $requestBody = json_decode($requestBody, true);
}

// Log the API request
$logger = new Logger();
$logger->logRequest();

// Check if this is an admin route that needs protection
$isAdminRoute = false;
$adminProtectedRoutes = [
    'users' => ['DELETE'],
    'reports' => ['PUT', 'DELETE'],
    'violence-types' => ['POST', 'PUT', 'DELETE'],
    'materials' => ['POST', 'PUT', 'DELETE'],
    'contacts' => ['POST', 'PUT', 'DELETE']
];

// Special case for admin/login which doesn't need admin auth
$isAdminLoginRoute = ($resource === 'admin' && $subresource === 'login');

// Check if current route needs admin authentication
if (
    (isset($adminProtectedRoutes[$resource]) && in_array($method, $adminProtectedRoutes[$resource])) ||
    (isset($adminProtectedRoutes["$resource/$subresource"]) && in_array($method, $adminProtectedRoutes["$resource/$subresource"])) &&
    !$isAdminLoginRoute
) {
    $isAdminRoute = true;
}


// Apply admin authentication for protected routes
if ($isAdminRoute) {
    require_once 'middleware/AuthMiddleware.php';
    $authMiddleware = new AuthMiddleware();

    if (!$authMiddleware->applyAdminAuth()) {
        exit; // Stop execution if admin auth fails
    }
}

// Route to appropriate controller based on resource
switch ($resource) {
    case 'users':
        require_once 'controllers/UserController.php';
        require_once 'models/User.php';
        $controller = new UserController();
        break;

    // In the admin case, add:
    case 'admin':
        error_log("Admin route detected. Subresource: " . $subresource);
        require_once 'controllers/UserController.php';
        require_once 'models/User.php';
        $controller = new UserController();
        // Don't overwrite the subresource parameter
        break;


    case 'reports':
        require_once 'controllers/ReportController.php';
        require_once 'models/Report.php';
        require_once 'models/ReportMedia.php';
        $controller = new ReportController();
        break;

    case 'violence-types':
        require_once 'controllers/ViolenceTypeController.php';
        require_once 'models/ViolenceType.php';
        $controller = new ViolenceTypeController();
        break;

    case 'materials':
        require_once 'controllers/MaterialController.php';
        require_once 'models/ViolenceMaterial.php';
        $controller = new MaterialController();
        break;

    case 'contacts':
        require_once 'controllers/ContactController.php';
        require_once 'models/ContactInfo.php';
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
    $controller->handleRequest($method, $id, $subresource);
} else {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => 'Resource not found'
    ]);
}
