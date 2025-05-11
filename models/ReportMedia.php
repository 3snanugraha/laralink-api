<?php
/**
 * Report Media Model
 * 
 * Handles report media data operations
 */
class ReportMedia
{
    // Database connection and table name
    private $conn;
    private $table_name = "report_media";

    // Object properties
    public $media_id;
    public $report_id;
    public $media_type;
    public $file_path;
    public $uploaded_at;

    // Constructor with database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Create a new media record
     * 
     * @return bool True if created successfully, false otherwise
     */
    public function create()
    {
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . " 
                  (report_id, media_type, file_path) 
                  VALUES (?, ?, ?)";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->report_id = htmlspecialchars(strip_tags($this->report_id));
        $this->media_type = htmlspecialchars(strip_tags($this->media_type));
        $this->file_path = htmlspecialchars(strip_tags($this->file_path));

        // Bind values
        $stmt->bindParam(1, $this->report_id);
        $stmt->bindParam(2, $this->media_type);
        $stmt->bindParam(3, $this->file_path);

        // Execute query
        if ($stmt->execute()) {
            $this->media_id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Read all media for a report
     * 
     * @param int $reportId Report ID
     * @return array Media array
     */
    public function readByReport($reportId)
    {
        // Query to get media
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE report_id = ? 
                  ORDER BY uploaded_at DESC";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(1, $reportId);

        // Execute query
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete a media record
     * 
     * @return bool True if deleted successfully, false otherwise
     */
    public function delete()
    {
        // First, get the file path to delete the file
        $query = "SELECT file_path FROM " . $this->table_name . " WHERE media_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->media_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $filePath = BASE_PATH . '/' . $row['file_path'];

            // Delete the file if it exists
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Now delete the record
            $query = "DELETE FROM " . $this->table_name . " WHERE media_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->media_id);

            if ($stmt->execute()) {
                return true;
            }
        }

        return false;
    }
}

