<?php
/**
 * Script per inicialitzar la base de dades SQLite
 * Executa aquest fitxer una vegada per crear la BD i les dades d'exemple
 */

$dbPath = __DIR__ . '/students.db';

// Elimina la BD si ja existeix (per reinicialitzar)
if (file_exists($dbPath)) {
    unlink($dbPath);
    echo "Base de dades anterior eliminada.\n";
}

// Crea la connexió a SQLite
try {
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Base de dades creada correctament.\n";
    
    // Llegeix i executa l'script SQL
    $sql = file_get_contents(__DIR__ . '/init.sql');
    $db->exec($sql);
    
    echo "Taules creades i dades d'exemple insertades correctament.\n";
    echo "Base de dades disponible a: $dbPath\n";
    
} catch (PDOException $e) {
    die("Error inicialitzant la base de dades: " . $e->getMessage());
}
