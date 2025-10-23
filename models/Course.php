<?php
/**
 * MODEL: Course
 * 
 * Representa un curs i gestiona totes les operacions CRUD
 * amb la base de dades (Create, Read, Update, Delete)
 * 
 * Relació 1:N amb Students (un curs té molts estudiants)
 */

require_once __DIR__ . '/../config/Database.php';

class Course {
    private $db;
    private $table = 'courses';
    
    // Propietats del curs
    public $id;
    public $code;
    public $name;
    public $description;
    public $created_at;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtenir tots els cursos
     */
    public function getAll() {
        $query = "SELECT * FROM {$this->table} ORDER BY name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obtenir un curs per ID
     */
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Obtenir un curs amb el nombre d'estudiants assignats (1:N)
     * i el nombre de professors assignats (N:M)
     */
    public function getWithStudentCount() {
        $query = "SELECT c.*, 
                         COUNT(DISTINCT s.id) as student_count,
                         COUNT(DISTINCT tt.teacher_id) as teacher_count
                  FROM {$this->table} c 
                  LEFT JOIN students s ON s.course_id = c.id 
                  LEFT JOIN teaching_teams tt ON tt.course_id = c.id
                  GROUP BY c.id 
                  ORDER BY c.name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Crear un nou curs
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} (code, name, description) 
                  VALUES (:code, :name, :description)";
        
        $stmt = $this->db->prepare($query);
        
        // Neteja les dades
        $data['code'] = htmlspecialchars(strip_tags($data['code']));
        $data['name'] = htmlspecialchars(strip_tags($data['name']));
        $data['description'] = htmlspecialchars(strip_tags($data['description'] ?? ''));
        
        // Vincula els paràmetres
        $stmt->bindParam(':code', $data['code']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Actualitzar un curs existent
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table} 
                  SET code = :code, name = :name, description = :description 
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        
        // Neteja les dades
        $data['code'] = htmlspecialchars(strip_tags($data['code']));
        $data['name'] = htmlspecialchars(strip_tags($data['name']));
        $data['description'] = htmlspecialchars(strip_tags($data['description'] ?? ''));
        
        // Vincula els paràmetres
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':code', $data['code']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        
        return $stmt->execute();
    }
    
    /**
     * Eliminar un curs
     * NOTA: Si hi ha estudiants assignats, l'eliminació fallarà per la clau forana
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    /**
     * Comprovar si un codi ja existeix
     */
    public function codeExists($code, $excludeId = null) {
        $query = "SELECT COUNT(*) FROM {$this->table} WHERE code = :code";
        
        if ($excludeId !== null) {
            $query .= " AND id != :id";
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':code', $code);
        
        if ($excludeId !== null) {
            $stmt->bindParam(':id', $excludeId);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Comprovar si un curs té estudiants assignats
     * Útil abans d'eliminar
     */
    public function hasStudents($id) {
        $query = "SELECT COUNT(*) FROM students WHERE course_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}
