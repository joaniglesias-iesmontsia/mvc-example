<?php
/**
 * MODEL: Classroom
 * 
 * Representa una aula i gestiona totes les operacions CRUD
 * amb la base de dades (Create, Read, Update, Delete)
 * 
 * Relació 1:N amb Students (una aula té molts estudiants)
 */

require_once __DIR__ . '/../config/Database.php';

class Classroom {
    private $db;
    private $table = 'classrooms';
    
    // Propietats de l'aula
    public $id;
    public $name;
    public $created_at;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtenir totes les aules
     */
    public function getAll() {
        $query = "SELECT * FROM {$this->table} ORDER BY name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obtenir una aula per ID
     */
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Crear una nova aula
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} (name) 
                  VALUES (:name)";
        
        $stmt = $this->db->prepare($query);
        
        // Neteja les dades
        $data['name'] = htmlspecialchars(strip_tags($data['name']));
        
        // Vincula els paràmetres
        $stmt->bindParam(':name', $data['name']);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Actualitzar una aula existent
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table} 
                  SET name = :name
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        
        // Neteja les dades
        $data['name'] = htmlspecialchars(strip_tags($data['name']));

        // Vincula els paràmetres
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $data['name']);
        
        return $stmt->execute();
    }
    
    /**
     * Eliminar una aula
     * NOTA: Si hi ha estudiants assignats, l'eliminació fallarà per la clau forana
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
}
