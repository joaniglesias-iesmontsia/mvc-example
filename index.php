<?php
/**
 * INDEX.PHP - Front Controller amb Sistema de Rutes
 * 
 * Aquest és el punt d'entrada ÚNIC de l'aplicació MVC.
 * Utilitza un sistema de rutes centralitzat similar a Laravel.
 * 
 * Flux d'execució:
 * 1. Inicia la sessió
 * 2. Carrega el Router
 * 3. Carrega les rutes definides a routes/web.php
 * 4. El router analitza la URL i executa el controlador corresponent
 * 
 * Avantatges:
 * - Totes les rutes en un sol lloc (routes/web.php)
 * - URLs netes i semàntiques (/students/create en lloc de ?action=create)
 * - Fàcil d'escalar i mantenir
 * - Preparació per a frameworks moderns (Laravel, Symfony, etc.)
 */

// Inicia la sessió per gestionar missatges flash i validacions
session_start();

// Carrega el sistema de rutes
require_once __DIR__ . '/core/Router.php';

// Crea una instància del router
$router = new Router();

// Carrega totes les rutes de l'aplicació
require_once __DIR__ . '/routes/web.php';

// Executa la ruta corresponent a la petició actual
try {
    $router->dispatch();
} catch (Exception $e) {
    // Gestió d'errors global
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header('Location: /');
    exit;
}
