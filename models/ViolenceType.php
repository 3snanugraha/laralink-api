<?php
/**
 * Violence Type Model
 * 
 * Handles violence type data operations
 */
class ViolenceType
{
    // Database connection and table name
    private $conn;
    private $table_name = "violence_types";

    // Object properties
    public $violence_type_id;
    public $type_name;
    public $description;
    public $icon_url;

    // Constructor with database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Read all violence types
     * 
     * @return PDOStatement Result set
     */
    public function read()
    {
        // Query to read all records
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY type_name";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $stmt->execute();

        return $stmt;
    }

    /**
     * Read a single violence type
     * 
     * @return bool True if violence type found, false otherwise
     */
    public function readOne()
    {
        // Query to read single record
        $query = "SELECT * FROM " . $this->table_name . " WHERE violence_type_id = ?";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(1, $this->violence_type_id);

        // Execute query
        $stmt->execute();

        // Get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // If violence type exists, set properties
        if ($row) {
            $this->violence_type_id = $row['violence_type_id'];
            $this->type_name = $row['type_name'];
            $this->description = $row['description'];
            $this->icon_url = $row['icon_url'];

            return true;
        }

        return false;
    }
}
