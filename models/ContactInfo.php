<?php
/**
 * Contact Info Model
 * 
 * Handles contact information data operations
 */
class ContactInfo
{
    // Database connection and table name
    private $conn;
    private $table_name = "contact_info";

    // Object properties
    public $contact_id;
    public $contact_type;
    public $contact_value;
    public $description;
    public $is_active;

    // Constructor with database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Read all active contact information
     * 
     * @return PDOStatement Result set
     */
    public function read()
    {
        // Query to read all active contacts
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE is_active = 1 
                  ORDER BY contact_type";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $stmt->execute();

        return $stmt;
    }

    /**
     * Read a single contact information
     * 
     * @return bool True if contact found, false otherwise
     */
    public function readOne()
    {
        // Query to read single record
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE contact_id = ? AND is_active = 1";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(1, $this->contact_id);

        // Execute query
        $stmt->execute();

        // Get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // If contact exists, set properties
        if ($row) {
            $this->contact_id = $row['contact_id'];
            $this->contact_type = $row['contact_type'];
            $this->contact_value = $row['contact_value'];
            $this->description = $row['description'];
            $this->is_active = $row['is_active'];

            return true;
        }

        return false;
    }

    /**
     * Read contacts by type
     * 
     * @param string $type Contact type
     * @return array Contacts array
     */
    public function readByType($type)
    {
        // Query to read contacts by type
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE contact_type = ? AND is_active = 1";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(1, $type);

        // Execute query
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
