<?php
/**
 * CONTROLLER: Classroom controller
 * Gestiona les peticions HTTP relacionades amb les aules
 */

require_once __DIR__ . '/../models/Classroom.php';
require_once __DIR__ . '/../core/Router.php';

class ClassroomController {
    private $ClassroomModel;
    
    public function __construct() {
        $this->ClassroomModel = new Classroom();
    }
    
    /**
     * Mostra el llistat de aulaos amb el nombre d'estudiants assignats
     */
    public function index() {
        $classrooms = $this->ClassroomModel->getAll();
        require_once __DIR__ . '/../views/classrooms/index.php';
    }
    
    /**
     * Mostra el formulari per crear un nou aula
     */
    public function create() {
        require_once __DIR__ . '/../views/classrooms/create.php';
    }
    
    /**
     * Processa la creació d'un nou aula
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Router::redirect('/classrooms');
        }
        
        $errors = $this->validateClassroom($_POST);
        
        if (empty($errors)) {
            $result = $this->ClassroomModel->create($_POST);
            
            if ($result) {
                $_SESSION['success'] = "aula creat correctament!";
                Router::redirect('/classrooms');
            } else {
                $_SESSION['error'] = "Error al crear el aula";
            }
        } else {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
        }
        
        Router::redirect('/classrooms/create');
    }
    
    /**
     * Mostra el formulari per editar un aula
     */
    public function edit($id = null) {
        if ($id === null) {
            $id = $_GET['id'] ?? null;
        }
        
        if (!$id) {
            Router::redirect('/classrooms');
        }
        
        $classroom = $this->ClassroomModel->getById($id);
        
        if (!$classroom) {
            $_SESSION['error'] = "aula no trobat";
            Router::redirect('/classrooms');
        }
        
        require_once __DIR__ . '/../views/classrooms/edit.php';
    }
    
    /**
     * Processa l'actualització d'un aula
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Router::redirect('/classrooms');
        }
        
        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            Router::redirect('/classrooms');
        }
        
        $errors = $this->validateClassroom($_POST, $id);
        
        if (empty($errors)) {
            $result = $this->ClassroomModel->update($id, $_POST);
            
            if ($result) {
                $_SESSION['success'] = "aula actualitzat correctament!";
                Router::redirect('/classrooms');
            } else {
                $_SESSION['error'] = "Error al actualitzar el aula";
            }
        } else {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
        }

        Router::redirect('/classrooms/edit/' . $id);
    }
    
    /**
     * Elimina un aula
     * NOTA: Si té estudiants assignats, no es podrà eliminar (FOREIGN KEY RESTRICT)
     */
    public function delete($id = null) {
        if ($id === null) {
            $id = $_GET['id'] ?? null;
        }
        
        if (!$id) {
            Router::redirect('/classrooms');
        }
        
        $result = $this->ClassroomModel->delete($id);
        
        if ($result) {
            $_SESSION['success'] = "aula eliminat correctament!";
        } else {
            $_SESSION['error'] = "Error al eliminar el aula";
        }
        
        Router::redirect('/classrooms');
    }
    
    /**
     * Valida les dades d'un aula
     */
    private function validateClassroom($data, $id = null) {
        $errors = [];
        
        // Validar nom
        if (empty($data['name']) || strlen(trim($data['name'])) < 3) {
            $errors['name'] = "El nom ha de tenir almenys 3 caràcters";
        }
        
        return $errors;
    }
}
