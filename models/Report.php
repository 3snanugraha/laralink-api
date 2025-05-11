<?php
/**
 * Report Model
 * 
 * Handles report data operations
 */
class Report
{
    // Database connection and table name
    private $conn;
    private $table_name = "reports";

    // Object properties
    public $report_id;
    public $user_id;
    public $violence_type_id;
    public $perpetrator;
    public $incident_date;
    public $incident_location_lat;
    public $incident_location_lng;
    public $location_address;
    public $description;
    public $report_date;
    public $status;
    public $is_anonymous;

    // Constructor with database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Create a new report
     * 
     * @return bool True if created successfully, false otherwise
     */
    public function create()
    {
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . " 
                  (user_id, violence_type_id, perpetrator, incident_date, 
                   incident_location_lat, incident_location_lng, location_address, 
                   description, status, is_anonymous) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->violence_type_id = htmlspecialchars(strip_tags($this->violence_type_id));
        $this->perpetrator = htmlspecialchars(strip_tags($this->perpetrator));
        $this->incident_date = htmlspecialchars(strip_tags($this->incident_date));
        $this->incident_location_lat = htmlspecialchars(strip_tags($this->incident_location_lat));
        $this->incident_location_lng = htmlspecialchars(strip_tags($this->incident_location_lng));
        $this->location_address = htmlspecialchars(strip_tags($this->location_address));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->is_anonymous = $this->is_anonymous ? 1 : 0;

        // Bind values
        $stmt->bindParam(1, $this->user_id);
        $stmt->bindParam(2, $this->violence_type_id);
        $stmt->bindParam(3, $this->perpetrator);
        $stmt->bindParam(4, $this->incident_date);
        $stmt->bindParam(5, $this->incident_location_lat);
        $stmt->bindParam(6, $this->incident_location_lng);
        $stmt->bindParam(7, $this->location_address);
        $stmt->bindParam(8, $this->description);
        $stmt->bindParam(9, $this->status);
        $stmt->bindParam(10, $this->is_anonymous);

        // Execute query
        if ($stmt->execute()) {
            $this->report_id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Read a single report
     * 
     * @return bool True if report found, false otherwise
     */
    public function readOne()
    {
        // Query to read single record
        $query = "SELECT r.*, vt.type_name, vt.description as violence_description, vt.icon_url
                  FROM " . $this->table_name . " r
                  JOIN violence_types vt ON r.violence_type_id = vt.violence_type_id
                  WHERE r.report_id = ?";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(1, $this->report_id);

        // Execute query
        $stmt->execute();

        // Get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // If report exists, set properties
        if ($row) {
            $this->report_id = $row['report_id'];
            $this->user_id = $row['user_id'];
            $this->violence_type_id = $row['violence_type_id'];
            $this->perpetrator = $row['perpetrator'];
            $this->incident_date = $row['incident_date'];
            $this->incident_location_lat = $row['incident_location_lat'];
            $this->incident_location_lng = $row['incident_location_lng'];
            $this->location_address = $row['location_address'];
            $this->description = $row['description'];
            $this->report_date = $row['report_date'];
            $this->status = $row['status'];
            $this->is_anonymous = $row['is_anonymous'];

            return true;
        }

        return false;
    }

    /**
     * Read all reports for a user
     * 
     * @param int $userId User ID
     * @param int $page Page number
     * @param int $limit Records per page
     * @return array Reports array
     */
    public function readByUser($userId, $page = 1, $limit = 10)
    {
        // Calculate offset
        $offset = ($page - 1) * $limit;

        // Query to get reports
        $query = "SELECT r.*, vt.type_name 
                  FROM " . $this->table_name . " r
                  JOIN violence_types vt ON r.violence_type_id = vt.violence_type_id
                  WHERE r.user_id = ?
                  ORDER BY r.report_date DESC
                  LIMIT ?, ?";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(1, $userId);
        $stmt->bindParam(2, $offset, PDO::PARAM_INT);
        $stmt->bindParam(3, $limit, PDO::PARAM_INT);

        // Execute query
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count total reports for a user
     * 
     * @param int $userId User ID
     * @return int Total reports count
     */
    public function countByUser($userId)
    {
        // Query to count reports
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE user_id = ?";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(1, $userId);

        // Execute query
        $stmt->execute();

        // Get result
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) $row['total'];
    }

    /**
     * Update report status
     * 
     * @return bool True if updated successfully, false otherwise
     */
    public function updateStatus()
    {
        // Query to update status
        $query = "UPDATE " . $this->table_name . " SET status = ? WHERE report_id = ?";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->report_id = htmlspecialchars(strip_tags($this->report_id));

        // Bind parameters
        $stmt->bindParam(1, $this->status);
        $stmt->bindParam(2, $this->report_id);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
