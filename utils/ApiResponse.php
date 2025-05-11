<?php
/**
 * API Response Utility
 * 
 * Provides standardized API response format
 */
class ApiResponse
{
    /**
     * Send a success response
     * 
     * @param mixed $data Response data
     * @param string $message Success message
     * @param int $statusCode HTTP status code
     */
    public static function success($data = null, $message = 'Success', $statusCode = RESPONSE_OK)
    {
        http_response_code($statusCode);
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    /**
     * Send an error response
     * 
     * @param string $message Error message
     * @param int $statusCode HTTP status code
     * @param array $errors Detailed error information
     */
    public static function error($message = 'Error', $statusCode = RESPONSE_BAD_REQUEST, $errors = null)
    {
        http_response_code($statusCode);
        $response = [
            'status' => 'error',
            'message' => $message
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        echo json_encode($response);
        exit;
    }

    /**
     * Send a validation error response
     * 
     * @param array $errors Validation errors
     * @param string $message Error message
     */
    public static function validationError($errors, $message = 'Validation failed')
    {
        self::error($message, RESPONSE_BAD_REQUEST, $errors);
    }

    /**
     * Send a not found response
     * 
     * @param string $message Not found message
     */
    public static function notFound($message = 'Resource not found')
    {
        self::error($message, RESPONSE_NOT_FOUND);
    }

    /**
     * Send an unauthorized response
     * 
     * @param string $message Unauthorized message
     */
    public static function unauthorized($message = 'Unauthorized access')
    {
        self::error($message, RESPONSE_UNAUTHORIZED);
    }

    /**
     * Send a forbidden response
     * 
     * @param string $message Forbidden message
     */
    public static function forbidden($message = 'Access forbidden')
    {
        self::error($message, RESPONSE_FORBIDDEN);
    }
}
