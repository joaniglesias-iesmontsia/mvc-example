<?php
/**
 * ROUTER - Sistema de Gestió de Rutes
 * 
 * Aquesta classe gestiona totes les rutes de l'aplicació de forma centralitzada.
 * Similar al sistema de rutes de Laravel, però simplificat per a l'aprenentatge.
 * 
 * Conceptes clau:
 * - Separació de rutes per mètode HTTP (GET, POST)
 * - Rutes amb paràmetres dinàmics (:id, :slug, etc.)
 * - Controlador@mètode per definir qui gestiona cada ruta
 */

class Router {
    private $routes = [
        'GET' => [],
        'POST' => []
    ];
    
    /**
     * Registra una ruta GET
     * 
     * @param string $uri - La URI de la ruta (ex: '/students' o '/students/:id')
     * @param string $action - El controlador i mètode (ex: 'StudentController@index')
     */
    public function get($uri, $action) {
        $this->routes['GET'][$uri] = $action;
    }
    
    /**
     * Registra una ruta POST
     * 
     * @param string $uri - La URI de la ruta
     * @param string $action - El controlador i mètode
     */
    public function post($uri, $action) {
        $this->routes['POST'][$uri] = $action;
    }
    
    /**
     * Executa la ruta corresponent a la petició actual
     */
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $this->getUri();
        
        // Busca la ruta corresponent
        foreach ($this->routes[$method] as $route => $action) {
            $params = $this->match($route, $uri);
            
            if ($params !== false) {
                return $this->callAction($action, $params);
            }
        }
        
        // Si no es troba la ruta, error 404
        $this->notFound();
    }
    
    /**
     * Obté la URI actual neta (sense query string)
     */
    private function getUri() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Elimina l'slash final si existeix (excepte per a la ruta arrel)
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }
        
        return $uri;
    }
    
    /**
     * Comprova si la URI coincideix amb la ruta i extreu els paràmetres
     * 
     * @param string $route - La ruta definida (ex: '/students/:id')
     * @param string $uri - La URI actual (ex: '/students/5')
     * @return array|false - Els paràmetres extrets o false si no coincideix
     */
    private function match($route, $uri) {
        // Converteix la ruta en una expressió regular
        // :id, :slug, :any esdevenen grups de captura
        $pattern = preg_replace('/:[a-zA-Z]+/', '([^/]+)', $route);
        $pattern = '#^' . $pattern . '$#';
        
        if (preg_match($pattern, $uri, $matches)) {
            // Elimina la coincidència completa, només deixa els paràmetres
            array_shift($matches);
            return $matches;
        }
        
        return false;
    }
    
    /**
     * Executa l'acció del controlador
     * 
     * @param string $action - Format: 'Controller@method'
     * @param array $params - Paràmetres extrets de la URL
     */
    private function callAction($action, $params = []) {
        // Separa el controlador i el mètode
        list($controller, $method) = explode('@', $action);
        
        // Carrega el fitxer del controlador
        $controllerFile = __DIR__ . '/../controllers/' . $controller . '.php';
        
        if (!file_exists($controllerFile)) {
            die("Error: El controlador {$controller} no existeix.");
        }
        
        require_once $controllerFile;
        
        // Crea una instància del controlador
        $controllerInstance = new $controller();
        
        // Comprova si el mètode existeix
        if (!method_exists($controllerInstance, $method)) {
            die("Error: El mètode {$method} no existeix en {$controller}.");
        }
        
        // Crida al mètode passant els paràmetres
        return call_user_func_array([$controllerInstance, $method], $params);
    }
    
    /**
     * Gestiona errors 404
     */
    private function notFound() {
        http_response_code(404);
        echo "<!DOCTYPE html>
        <html lang='ca'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>404 - Pàgina no trobada</title>
            <link rel='stylesheet' href='/public/css/style.css'>
        </head>
        <body>
            <div class='container' style='text-align: center; padding: 4rem 0;'>
                <h1 style='font-size: 6rem; color: #e74c3c;'>404</h1>
                <h2>Pàgina no trobada</h2>
                <p>La pàgina que cerques no existeix.</p>
                <a href='/' class='btn btn-primary'>Tornar a l'inici</a>
            </div>
        </body>
        </html>";
        exit;
    }
    
    /**
     * Redirigeix a una URL
     * 
     * @param string $url - La URL de destinació
     */
    public static function redirect($url) {
        header("Location: {$url}");
        exit;
    }
}
