<?php
/**
 * Application Constants
 * 
 * This file contains constants used throughout the LaraLink+ API.
 */

// API Version
define('API_VERSION', '1.0.0');

// Base paths
define('BASE_PATH', dirname(__DIR__));
define('UPLOAD_PATH', BASE_PATH . '/uploads');
define('REPORTS_UPLOAD_PATH', UPLOAD_PATH . '/reports');
define('MATERIALS_UPLOAD_PATH', UPLOAD_PATH . '/materials');

// File upload limits (in bytes)
define('MAX_IMAGE_SIZE', 5 * 1024 * 1024); // 5MB
define('MAX_VIDEO_SIZE', 50 * 1024 * 1024); // 50MB

// Allowed file types
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
define('ALLOWED_VIDEO_TYPES', ['video/mp4', 'video/quicktime', 'video/x-msvideo']);

// Report status values (matching the ENUM in the database)
define('REPORT_STATUS_SUBMITTED', 'submitted');
define('REPORT_STATUS_PROCESSING', 'processing');
define('REPORT_STATUS_INVESTIGATING', 'investigating');
define('REPORT_STATUS_RESOLVED', 'resolved');
define('REPORT_STATUS_CLOSED', 'closed');

// User roles
define('USER_ROLE_USER', 'user');
define('USER_ROLE_ADMIN', 'admin');

// User status
define('USER_STATUS_ACTIVE', 'active');
define('USER_STATUS_INACTIVE', 'inactive');
define('USER_STATUS_BLOCKED', 'blocked');

// Media types
define('MEDIA_TYPE_IMAGE', 'image');
define('MEDIA_TYPE_VIDEO', 'video');

// API response codes
define('RESPONSE_OK', 200);
define('RESPONSE_CREATED', 201);
define('RESPONSE_BAD_REQUEST', 400);
define('RESPONSE_UNAUTHORIZED', 401);
define('RESPONSE_FORBIDDEN', 403);
define('RESPONSE_NOT_FOUND', 404);
define('RESPONSE_METHOD_NOT_ALLOWED', 405);
define('RESPONSE_INTERNAL_ERROR', 500);

// Pagination defaults
define('DEFAULT_PAGE_SIZE', 10);
define('MAX_PAGE_SIZE', 50);
