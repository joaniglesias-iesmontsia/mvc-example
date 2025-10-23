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
     * Obtenir tots els estudiants amb el nom del curs (JOIN 1:N)
     */
    public function getAll() {
        $query = "SELECT s.*, c.code as course_code, c.name as course_name 
                  FROM {$this->table} s 
                  LEFT JOIN courses c ON s.course_id = c.id 
                  ORDER BY s.name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obtenir un estudiant per ID amb el nom del curs (JOIN 1:N)
     */
    public function getById($id) {
        $query = "SELECT s.*, c.code as course_code, c.name as course_name 
                  FROM {$this->table} s 
                  LEFT JOIN courses c ON s.course_id = c.id 
                  WHERE s.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Crear un nou estudiant (amb course_id en relació 1:N)
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} (name, email, age, course_id) 
                  VALUES (:name, :email, :age, :course_id)";
        
        $stmt = $this->db->prepare($query);
        
        // Neteja les dades
        $data['name'] = htmlspecialchars(strip_tags($data['name']));
        $data['email'] = htmlspecialchars(strip_tags($data['email']));
        $data['age'] = intval($data['age']);
        $data['course_id'] = intval($data['course_id']);
        
        // Vincula els paràmetres
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':age', $data['age']);
        $stmt->bindParam(':course_id', $data['course_id']);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Actualitzar un estudiant existent (amb course_id en relació 1:N)
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table} 
                  SET name = :name, email = :email, age = :age, course_id = :course_id 
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        
        // Neteja les dades
        $data['name'] = htmlspecialchars(strip_tags($data['name']));
        $data['email'] = htmlspecialchars(strip_tags($data['email']));
        $data['age'] = intval($data['age']);
        $data['course_id'] = intval($data['course_id']);
        
        // Vincula els paràmetres
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':age', $data['age']);
        $stmt->bindParam(':course_id', $data['course_id']);
        
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
