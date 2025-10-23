<?php
/**
 * INDEX.PHP - Front Controller
 * 
 * Aquest és el punt d'entrada de l'aplicació MVC
 * Gestiona totes les peticions i les envia al controlador adequat
 */

// Inicia la sessió per gestionar missatges flash i validacions
session_start();

// Inclou el controlador
require_once __DIR__ . '/controllers/StudentController.php';

// Crea una instància del controlador
$controller = new StudentController();

// Obté l'acció de la URL (per defecte: index)
$action = $_GET['action'] ?? 'index';

// Router simple: crida al mètode del controlador segons l'acció
try {
    switch ($action) {
        case 'index':
            $controller->index();
            break;
            
        case 'create':
            $controller->create();
            break;
            
        case 'store':
            $controller->store();
            break;
            
        case 'edit':
            $controller->edit();
            break;
            
        case 'update':
            $controller->update();
            break;
            
        case 'delete':
            $controller->delete();
            break;
            
        default:
            // Si l'acció no existeix, redirigeix a la pàgina principal
            header('Location: index.php');
            exit;
    }
} catch (Exception $e) {
    // Gestió d'errors global
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header('Location: index.php');
    exit;
}
