<?php
/**
 * MODEL: Teacher
 * 
 * Representa un professor i gestiona totes les operacions CRUD
 * amb la base de dades (Create, Read, Update, Delete)
 */

require_once __DIR__ . '/../config/Database.php';

class Teacher {
    private $db;
    private $table = 'teachers';
    
    // Propietats del professor
    public $id;
    public $name;
    public $email;
    public $phone;
    public $department;
    public $specialty;
    public $created_at;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtenir tots els professors
     */
    public function getAll() {
        $query = "SELECT * FROM {$this->table} ORDER BY name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obtenir tots els professors amb el nombre de cursos assignats (N:M)
     */
    public function getAllWithCourseCount() {
        $query = "SELECT t.*, COUNT(DISTINCT tt.course_id) as course_count
                  FROM {$this->table} t
                  LEFT JOIN teaching_teams tt ON tt.teacher_id = t.id
                  GROUP BY t.id
                  ORDER BY t.name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obtenir un professor per ID
     */
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Crear un nou professor
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} (name, email, phone, department, specialty) 
                  VALUES (:name, :email, :phone, :department, :specialty)";
        
        $stmt = $this->db->prepare($query);
        
        // Neteja les dades
        $data['name'] = htmlspecialchars(strip_tags($data['name']));
        $data['email'] = htmlspecialchars(strip_tags($data['email']));
        $data['phone'] = htmlspecialchars(strip_tags($data['phone']));
        $data['department'] = htmlspecialchars(strip_tags($data['department']));
        $data['specialty'] = htmlspecialchars(strip_tags($data['specialty']));
        
        // Vincula els paràmetres
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':department', $data['department']);
        $stmt->bindParam(':specialty', $data['specialty']);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Actualitzar un professor existent
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table} 
                  SET name = :name, email = :email, phone = :phone, 
                      department = :department, specialty = :specialty 
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        
        // Neteja les dades
        $data['name'] = htmlspecialchars(strip_tags($data['name']));
        $data['email'] = htmlspecialchars(strip_tags($data['email']));
        $data['phone'] = htmlspecialchars(strip_tags($data['phone']));
        $data['department'] = htmlspecialchars(strip_tags($data['department']));
        $data['specialty'] = htmlspecialchars(strip_tags($data['specialty']));
        
        // Vincula els paràmetres
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':department', $data['department']);
        $stmt->bindParam(':specialty', $data['specialty']);
        
        return $stmt->execute();
    }
    
    /**
     * Eliminar un professor
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
