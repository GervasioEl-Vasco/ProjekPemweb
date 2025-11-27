<?php
class Game {
    private $conn;
    private $table_name = "games";

    public $id;
    public $title;
    public $description;
    public $genre;
    public $release_date;
    public $price;
    public $image_url;
    public $created_by;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Function 1: Create game
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET title=:title, description=:description, genre=:genre, 
                     release_date=:release_date, price=:price, image_url=:image_url, created_by=:created_by";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->genre = htmlspecialchars(strip_tags($this->genre));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));
        
        // Bind data
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":genre", $this->genre);
        $stmt->bindParam(":release_date", $this->release_date);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":image_url", $this->image_url);
        $stmt->bindParam(":created_by", $this->created_by);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Function 2: Read all games
    public function read() {
        $query = "SELECT g.*, u.username as creator 
                 FROM " . $this->table_name . " g 
                 LEFT JOIN users u ON g.created_by = u.id 
                 ORDER BY g.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Function 3: Read one game
    public function readOne() {
        $query = "SELECT g.*, u.username as creator 
                 FROM " . $this->table_name . " g 
                 LEFT JOIN users u ON g.created_by = u.id 
                 WHERE g.id = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->genre = $row['genre'];
            $this->release_date = $row['release_date'];
            $this->price = $row['price'];
            $this->image_url = $row['image_url'];
            $this->created_by = $row['created_by'];
            $this->created_at = $row['created_at'];
        }
    }

    // Function 4: Update game
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                 SET title=:title, description=:description, genre=:genre, 
                     release_date=:release_date, price=:price, image_url=:image_url 
                 WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->genre = htmlspecialchars(strip_tags($this->genre));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Bind data
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":genre", $this->genre);
        $stmt->bindParam(":release_date", $this->release_date);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":image_url", $this->image_url);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Function 5: Delete game
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Function 6: Search games
    public function search($keywords) {
        $query = "SELECT g.*, u.username as creator 
                 FROM " . $this->table_name . " g 
                 LEFT JOIN users u ON g.created_by = u.id 
                 WHERE g.title LIKE ? OR g.description LIKE ? OR g.genre LIKE ? 
                 ORDER BY g.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        
        $keywords = htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%";
        
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);
        
        $stmt->execute();
        return $stmt;
    }
}
?>