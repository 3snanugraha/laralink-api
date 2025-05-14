<?php
/**
 * Validator Utility
 * 
 * Validates input data for API endpoints
 */
class Validator
{
    /**
     * Validate user registration data
     * 
     * @param object $data User registration data
     * @return bool True if valid, false otherwise
     */
    public function validateUserRegistration($data)
    {
        $errors = [];

        // Check required fields
        if (!isset($data->phone_number) || empty($data->phone_number)) {
            $errors['phone_number'] = 'Phone number is required';
        } elseif (!preg_match('/^[0-9]{10,15}$/', $data->phone_number)) {
            $errors['phone_number'] = 'Phone number must be 10-15 digits';
        }

        if (!isset($data->name) || empty($data->name)) {
            $errors['name'] = 'Name is required';
        } elseif (strlen($data->name) < 2 || strlen($data->name) > 100) {
            $errors['name'] = 'Name must be between 2 and 100 characters';
        }

        // If there are errors, send validation error response
        if (!empty($errors)) {
            ApiResponse::validationError($errors);
            return false;
        }

        return true;
    }

    /**
     * Validate admin login data
     * 
     * @param object $data Admin login data
     * @return bool True if valid, false otherwise
     */
    public function validateAdminLogin($data)
    {
        $errors = [];

        // Check for phone_number/username
        $hasPhoneNumber = false;
        if (isset($data->phone_number) && !empty($data->phone_number)) {
            $hasPhoneNumber = true;
        } else if (isset($data->username) && !empty($data->username)) {
            $hasPhoneNumber = true;
        } else {
            $errors['phone_number'] = 'Phone number/username is required';
        }

        // Check for password
        if (!isset($data->password) || empty($data->password)) {
            $errors['password'] = 'Password is required';
        }

        // If there are errors, send validation error response
        if (!empty($errors)) {
            ApiResponse::validationError($errors);
            return false;
        }

        return true;
    }

    /**
     * Validate report creation data
     * 
     * @param object $data Report creation data
     * @return bool True if valid, false otherwise
     */
    public function validateReportCreation($data)
    {
        $errors = [];

        // Check required fields
        if (!isset($data->user_id) || empty($data->user_id)) {
            $errors['user_id'] = 'User ID is required';
        }

        if (!isset($data->violence_type_id) || empty($data->violence_type_id)) {
            $errors['violence_type_id'] = 'Violence type is required';
        }

        if (!isset($data->perpetrator) || empty($data->perpetrator)) {
            $errors['perpetrator'] = 'Perpetrator information is required';
        }

        if (!isset($data->incident_date) || empty($data->incident_date)) {
            $errors['incident_date'] = 'Incident date is required';
        } elseif (!$this->isValidDate($data->incident_date)) {
            $errors['incident_date'] = 'Invalid date format. Use YYYY-MM-DD HH:MM:SS';
        }

        if (!isset($data->incident_location_lat) || !is_numeric($data->incident_location_lat)) {
            $errors['incident_location_lat'] = 'Valid latitude is required';
        }

        if (!isset($data->incident_location_lng) || !is_numeric($data->incident_location_lng)) {
            $errors['incident_location_lng'] = 'Valid longitude is required';
        }

        if (!isset($data->description) || empty($data->description)) {
            $errors['description'] = 'Description is required';
        }

        // If there are errors, send validation error response
        if (!empty($errors)) {
            ApiResponse::validationError($errors);
            return false;
        }

        return true;
    }

    /**
     * Validate file upload
     * 
     * @param array $file File data from $_FILES
     * @param string $type Expected file type ('image' or 'video')
     * @return array Validation result with status and message
     */
    public function validateFileUpload($file, $type = 'image')
    {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return [
                'status' => false,
                'message' => 'No file uploaded or upload error occurred'
            ];
        }

        // Check file size
        if ($type === 'image' && $file['size'] > MAX_IMAGE_SIZE) {
            return [
                'status' => false,
                'message' => 'Image size exceeds the maximum allowed size (' . (MAX_IMAGE_SIZE / 1024 / 1024) . 'MB)'
            ];
        } elseif ($type === 'video' && $file['size'] > MAX_VIDEO_SIZE) {
            return [
                'status' => false,
                'message' => 'Video size exceeds the maximum allowed size (' . (MAX_VIDEO_SIZE / 1024 / 1024) . 'MB)'
            ];
        }

        // Check file type
        $mimeType = mime_content_type($file['tmp_name']);

        if ($type === 'image' && !in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
            return [
                'status' => false,
                'message' => 'Invalid image type. Allowed types: ' . implode(', ', ALLOWED_IMAGE_TYPES)
            ];
        } elseif ($type === 'video' && !in_array($mimeType, ALLOWED_VIDEO_TYPES)) {
            return [
                'status' => false,
                'message' => 'Invalid video type. Allowed types: ' . implode(', ', ALLOWED_VIDEO_TYPES)
            ];
        }

        return [
            'status' => true,
            'message' => 'File is valid'
        ];
    }

    /**
     * Check if a date string is valid
     * 
     * @param string $date Date string to validate
     * @return bool True if valid, false otherwise
     */
    private function isValidDate($date)
    {
        $d = DateTime::createFromFormat('Y-m-d H:i:s', $date);
        return $d && $d->format('Y-m-d H:i:s') === $date;
    }

    /**
     * Validate material creation/update data
     * 
     * @param object $data Material data
     * @return bool True if valid, false otherwise
     */
    public function validateMaterial($data)
    {
        $errors = [];

        // Check required fields for creation
        if (isset($data->_method) && $data->_method === 'POST') {
            if (!isset($data->title) || empty($data->title)) {
                $errors['title'] = 'Title is required';
            }

            if (!isset($data->content) || empty($data->content)) {
                $errors['content'] = 'Content is required';
            }

            if (!isset($data->violence_type_id) || empty($data->violence_type_id)) {
                $errors['violence_type_id'] = 'Violence type is required';
            }
        }

        // Validate title length if provided
        if (isset($data->title) && !empty($data->title)) {
            if (strlen($data->title) > 255) {
                $errors['title'] = 'Title must be less than 255 characters';
            }
        }

        // If there are errors, send validation error response
        if (!empty($errors)) {
            ApiResponse::validationError($errors);
            return false;
        }

        return true;
    }

    /**
     * Validate violence type creation/update data
     * 
     * @param object $data Violence type data
     * @return bool True if valid, false otherwise
     */
    public function validateViolenceType($data)
    {
        $errors = [];

        // Check required fields for creation
        if (isset($data->_method) && $data->_method === 'POST') {
            if (!isset($data->type_name) || empty($data->type_name)) {
                $errors['type_name'] = 'Type name is required';
            }
        }

        // Validate type name length if provided
        if (isset($data->type_name) && !empty($data->type_name)) {
            if (strlen($data->type_name) > 100) {
                $errors['type_name'] = 'Type name must be less than 100 characters';
            }
        }

        // If there are errors, send validation error response
        if (!empty($errors)) {
            ApiResponse::validationError($errors);
            return false;
        }

        return true;
    }

    /**
     * Validate contact creation/update data
     * 
     * @param object $data Contact data
     * @return bool True if valid, false otherwise
     */
    public function validateContact($data)
    {
        $errors = [];
        // Check required fields for creation
        if (isset($data->_method) && $data->_method === 'POST') {
            if (!isset($data->contact_type) || empty($data->contact_type)) {
                $errors['contact_type'] = 'Contact type is required';
            }

            if (!isset($data->contact_value) || empty($data->contact_value)) {
                $errors['contact_value'] = 'Contact value is required';
            }
        }

        // Validate contact type length if provided
        if (isset($data->contact_type) && !empty($data->contact_type)) {
            if (strlen($data->contact_type) > 50) {
                $errors['contact_type'] = 'Contact type must be less than 50 characters';
            }
        }

        // Validate contact value length if provided
        if (isset($data->contact_value) && !empty($data->contact_value)) {
            if (strlen($data->contact_value) > 255) {
                $errors['contact_value'] = 'Contact value must be less than 255 characters';
            }
        }

        // If there are errors, send validation error response
        if (!empty($errors)) {
            ApiResponse::validationError($errors);
            return false;
        }

        return true;
    }
}
