<?php
/**
 * MODEL: Student
 * 
 * Representa un estudiant i gestiona totes les operacions CRUD
 * amb la base de dades (Create, Read, Update, Delete)
 */

require_once __DIR__ . '/../config/Database.php';

class Student {
    private $db;
    private $table = 'students';
    
    // Propietats de l'estudiant
    public $id;
    public $name;
    public $email;
    public $age;
    public $course;
    public $created_at;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtenir tots els estudiants
     */
    public function getAll() {
        $query = "SELECT * FROM {$this->table} ORDER BY name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obtenir un estudiant per ID
     */
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Crear un nou estudiant
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} (name, email, age, course) 
                  VALUES (:name, :email, :age, :course)";
        
        $stmt = $this->db->prepare($query);
        
        // Neteja les dades
        $data['name'] = htmlspecialchars(strip_tags($data['name']));
        $data['email'] = htmlspecialchars(strip_tags($data['email']));
        $data['age'] = intval($data['age']);
        $data['course'] = htmlspecialchars(strip_tags($data['course']));
        
        // Vincula els paràmetres
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':age', $data['age']);
        $stmt->bindParam(':course', $data['course']);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Actualitzar un estudiant existent
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table} 
                  SET name = :name, email = :email, age = :age, course = :course 
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        
        // Neteja les dades
        $data['name'] = htmlspecialchars(strip_tags($data['name']));
        $data['email'] = htmlspecialchars(strip_tags($data['email']));
        $data['age'] = intval($data['age']);
        $data['course'] = htmlspecialchars(strip_tags($data['course']));
        
        // Vincula els paràmetres
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':age', $data['age']);
        $stmt->bindParam(':course', $data['course']);
        
        return $stmt->execute();
    }
    
    /**
     * Eliminar un estudiant
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    /**
     * Comprovar si un email ja existeix
     */
    public function emailExists($email, $excludeId = null) {
        $query = "SELECT COUNT(*) FROM {$this->table} WHERE email = :email";
        
        if ($excludeId !== null) {
            $query .= " AND id != :id";
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        
        if ($excludeId !== null) {
            $stmt->bindParam(':id', $excludeId);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}
