<?php
/**
 * Configuració de la base de dades
 * Proporciona la connexió PDO a SQLite
 */

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        $dbPath = __DIR__ . '/../database/students.db';
        
        // Comprova si la base de dades existeix
        if (!file_exists($dbPath)) {
            die("Error: La base de dades no existeix. Executa primer 'php database/init.php'");
        }
        
        try {
            $this->connection = new PDO('sqlite:' . $dbPath);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error de connexió: " . $e->getMessage());
        }
    }
    
    /**
     * Patró Singleton per tenir una única instància de connexió
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Retorna la connexió PDO
     */
    public function getConnection() {
        return $this->connection;
    }
    
    // Evita clonar la instància
    private function __clone() {}
    
    // Evita deserialitzar la instància
    public function __wakeup() {
        throw new Exception("No es pot deserialitzar un singleton");
    }
}
