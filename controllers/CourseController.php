<?php
/**
 * CONTROLLER: CourseController
 * 
 * Gestiona les peticions HTTP relacionades amb els cursos
 * Demostra la relació 1:N (un curs té molts estudiants)
 */

require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../core/Router.php';

class CourseController {
    private $courseModel;
    
    public function __construct() {
        $this->courseModel = new Course();
    }
    
    /**
     * Mostra el llistat de cursos amb el nombre d'estudiants assignats
     */
    public function index() {
        $courses = $this->courseModel->getWithStudentCount();
        require_once __DIR__ . '/../views/courses/index.php';
    }
    
    /**
     * Mostra el formulari per crear un nou curs
     */
    public function create() {
        require_once __DIR__ . '/../views/courses/create.php';
    }
    
    /**
     * Processa la creació d'un nou curs
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Router::redirect('/courses');
        }
        
        $errors = $this->validateCourse($_POST);
        
        if (empty($errors)) {
            $result = $this->courseModel->create($_POST);
            
            if ($result) {
                $_SESSION['success'] = "Curs creat correctament!";
                Router::redirect('/courses');
            } else {
                $_SESSION['error'] = "Error al crear el curs";
            }
        } else {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
        }
        
        Router::redirect('/courses/create');
    }
    
    /**
     * Mostra el formulari per editar un curs
     */
    public function edit($id = null) {
        if ($id === null) {
            $id = $_GET['id'] ?? null;
        }
        
        if (!$id) {
            Router::redirect('/courses');
        }
        
        $course = $this->courseModel->getById($id);
        
        if (!$course) {
            $_SESSION['error'] = "Curs no trobat";
            Router::redirect('/courses');
        }
        
        require_once __DIR__ . '/../views/courses/edit.php';
    }
    
    /**
     * Processa l'actualització d'un curs
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Router::redirect('/courses');
        }
        
        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            Router::redirect('/courses');
        }
        
        $errors = $this->validateCourse($_POST, $id);
        
        if (empty($errors)) {
            $result = $this->courseModel->update($id, $_POST);
            
            if ($result) {
                $_SESSION['success'] = "Curs actualitzat correctament!";
                Router::redirect('/courses');
            } else {
                $_SESSION['error'] = "Error al actualitzar el curs";
            }
        } else {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
        }
        
        Router::redirect('/courses/edit/' . $id);
    }
    
    /**
     * Elimina un curs
     * NOTA: Si té estudiants assignats, no es podrà eliminar (FOREIGN KEY RESTRICT)
     */
    public function delete($id = null) {
        if ($id === null) {
            $id = $_GET['id'] ?? null;
        }
        
        if (!$id) {
            Router::redirect('/courses');
        }
        
        // Comprova si té estudiants assignats
        if ($this->courseModel->hasStudents($id)) {
            $_SESSION['error'] = "No es pot eliminar el curs perquè té estudiants assignats. Reassigna'ls primer.";
            Router::redirect('/courses');
        }
        
        $result = $this->courseModel->delete($id);
        
        if ($result) {
            $_SESSION['success'] = "Curs eliminat correctament!";
        } else {
            $_SESSION['error'] = "Error al eliminar el curs";
        }
        
        Router::redirect('/courses');
    }
    
    /**
     * Valida les dades d'un curs
     */
    private function validateCourse($data, $id = null) {
        $errors = [];
        
        // Validar codi
        if (empty($data['code']) || strlen(trim($data['code'])) < 2) {
            $errors['code'] = "El codi ha de tenir almenys 2 caràcters";
        } elseif ($this->courseModel->codeExists($data['code'], $id)) {
            $errors['code'] = "Aquest codi de curs ja existeix";
        }
        
        // Validar nom
        if (empty($data['name']) || strlen(trim($data['name'])) < 3) {
            $errors['name'] = "El nom ha de tenir almenys 3 caràcters";
        }
        
        // La descripció és opcional
        
        return $errors;
    }
}
