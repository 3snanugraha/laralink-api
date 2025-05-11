<?php
/**
 * Violence Material Model
 * 
 * Handles educational materials data operations
 */
class ViolenceMaterial
{
    // Database connection and table name
    private $conn;
    private $table_name = "violence_materials";

    // Object properties
    public $material_id;
    public $title;
    public $content;
    public $violence_type_id;
    public $image_url;
    public $created_at;
    public $updated_at;

    // Constructor with database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Read all educational materials
     * 
     * @param int|null $violenceTypeId Optional violence type ID filter
     * @return PDOStatement Result set
     */
    public function read($violenceTypeId = null)
    {
        // Base query
        $query = "SELECT m.*, vt.type_name 
                  FROM " . $this->table_name . " m
                  LEFT JOIN violence_types vt ON m.violence_type_id = vt.violence_type_id";

        // Add filter if violence_type_id is provided
        if ($violenceTypeId) {
            $query .= " WHERE m.violence_type_id = ?";
        }

        $query .= " ORDER BY m.created_at DESC";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter if needed
        if ($violenceTypeId) {
            $stmt->bindParam(1, $violenceTypeId);
        }

        // Execute query
        $stmt->execute();

        return $stmt;
    }

    /**
     * Read a single educational material
     * 
     * @return bool True if material found, false otherwise
     */
    public function readOne()
    {
        // Query to read single record
        $query = "SELECT m.*, vt.type_name 
                  FROM " . $this->table_name . " m
                  LEFT JOIN violence_types vt ON m.violence_type_id = vt.violence_type_id
                  WHERE m.material_id = ?";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(1, $this->material_id);

        // Execute query
        $stmt->execute();

        // Get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // If material exists, set properties
        if ($row) {
            $this->material_id = $row['material_id'];
            $this->title = $row['title'];
            $this->content = $row['content'];
            $this->violence_type_id = $row['violence_type_id'];
            $this->image_url = $row['image_url'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];

            return true;
        }

        return false;
    }
}
