<?php
/**
 * File Uploader Utility
 * 
 * Handles file uploads for the API
 */
class FileUploader
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
     * Upload media for a report
     * 
     * @param int $reportId Report ID
     * @return array Upload result with status and message
     */
    public function uploadReportMedia($reportId)
    {
        // Check if files were uploaded
        if (!isset($_FILES['media']) || empty($_FILES['media']['name'])) {
            return [
                'status' => 'error',
                'message' => 'No file uploaded'
            ];
        }

        // Get media type
        $mediaType = isset($_POST['media_type']) ? $_POST['media_type'] : 'image';

        // Validate media type
        if (!in_array($mediaType, [MEDIA_TYPE_IMAGE, MEDIA_TYPE_VIDEO])) {
            return [
                'status' => 'error',
                'message' => 'Invalid media type. Must be "image" or "video"'
            ];
        }

        // Validate file
        $validationResult = $this->validator->validateFileUpload($_FILES['media'], $mediaType);
        if (!$validationResult['status']) {
            return [
                'status' => 'error',
                'message' => $validationResult['message']
            ];
        }

        // Create upload directory if it doesn't exist
        $uploadDir = REPORTS_UPLOAD_PATH . '/' . $reportId;
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename
        $fileExtension = pathinfo($_FILES['media']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $fileExtension;
        $filePath = $uploadDir . '/' . $fileName;

        // Move uploaded file
        if (move_uploaded_file($_FILES['media']['tmp_name'], $filePath)) {
            // Save file info to database
            $relativePath = 'uploads/reports/' . $reportId . '/' . $fileName;
            $query = "INSERT INTO report_media (report_id, media_type, file_path) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($query);

            if ($stmt->execute([$reportId, $mediaType, $relativePath])) {
                $mediaId = $this->db->lastInsertId();

                return [
                    'status' => 'success',
                    'message' => 'File uploaded successfully',
                    'data' => [
                        'media_id' => $mediaId,
                        'media_type' => $mediaType,
                        'file_path' => $relativePath
                    ]
                ];
            } else {
                // Delete file if database insert fails
                unlink($filePath);

                return [
                    'status' => 'error',
                    'message' => 'Failed to save file information to database'
                ];
            }
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to move uploaded file'
            ];
        }
    }

    /**
     * Upload image for educational material
     * 
     * @param int $materialId Material ID
     * @return array Upload result with status and message
     */
    public function uploadMaterialImage($materialId)
    {
        // Check if files were uploaded
        if (!isset($_FILES['image']) || empty($_FILES['image']['name'])) {
            return [
                'status' => 'error',
                'message' => 'No file uploaded'
            ];
        }

        // Validate file
        $validationResult = $this->validator->validateFileUpload($_FILES['image'], 'image');
        if (!$validationResult['status']) {
            return [
                'status' => 'error',
                'message' => $validationResult['message']
            ];
        }

        // Create upload directory if it doesn't exist
        $uploadDir = MATERIALS_UPLOAD_PATH;
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename
        $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $fileName = 'material_' . $materialId . '_' . uniqid() . '.' . $fileExtension;
        $filePath = $uploadDir . '/' . $fileName;

        // Move uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
            // Update material with image URL
            $relativePath = 'uploads/materials/' . $fileName;
            $query = "UPDATE violence_materials SET image_url = ? WHERE material_id = ?";
            $stmt = $this->db->prepare($query);

            if ($stmt->execute([$relativePath, $materialId])) {
                return [
                    'status' => 'success',
                    'message' => 'Image uploaded successfully',
                    'data' => [
                        'image_url' => $relativePath
                    ]
                ];
            } else {
                // Delete file if database update fails
                unlink($filePath);

                return [
                    'status' => 'error',
                    'message' => 'Failed to update material with image URL'
                ];
            }
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to move uploaded file'
            ];
        }
    }
}
