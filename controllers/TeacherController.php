<?php
/**
 * CONTROLLER: TeacherController
 * 
 * Gestiona les peticions HTTP relacionades amb els professors
 * Fa de pont entre el Model (Teacher) i les Vistes
 */

require_once __DIR__ . '/../models/Teacher.php';
require_once __DIR__ . '/../core/Router.php';

class TeacherController {
    private $teacherModel;
    
    public function __construct() {
        $this->teacherModel = new Teacher();
    }
    
    /**
     * Mostra el llistat de professors
     */
    public function index() {
        $teachers = $this->teacherModel->getAll();
        require_once __DIR__ . '/../views/teachers/index.php';
    }
    
    /**
     * Mostra el formulari per crear un nou professor
     */
    public function create() {
        require_once __DIR__ . '/../views/teachers/create.php';
    }
    
    /**
     * Processa la creació d'un nou professor
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Router::redirect('/teachers');
        }
        
        $errors = $this->validateTeacher($_POST);
        
        if (empty($errors)) {
            $result = $this->teacherModel->create($_POST);
            
            if ($result) {
                $_SESSION['success'] = "Professor creat correctament!";
                Router::redirect('/teachers');
            } else {
                $_SESSION['error'] = "Error al crear el professor";
            }
        } else {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
        }
        
        Router::redirect('/teachers/create');
    }
    
    /**
     * Mostra el formulari per editar un professor
     */
    public function edit($id = null) {
        // Si no es passa l'ID com a paràmetre, prova d'obtenir-lo de GET
        if ($id === null) {
            $id = $_GET['id'] ?? null;
        }
        
        if (!$id) {
            Router::redirect('/teachers');
        }
        
        $teacher = $this->teacherModel->getById($id);
        
        if (!$teacher) {
            $_SESSION['error'] = "Professor no trobat";
            Router::redirect('/teachers');
        }
        
        require_once __DIR__ . '/../views/teachers/edit.php';
    }
    
    /**
     * Processa l'actualització d'un professor
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Router::redirect('/teachers');
        }
        
        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            Router::redirect('/teachers');
        }
        
        $errors = $this->validateTeacher($_POST, $id);
        
        if (empty($errors)) {
            $result = $this->teacherModel->update($id, $_POST);
            
            if ($result) {
                $_SESSION['success'] = "Professor actualitzat correctament!";
                Router::redirect('/teachers');
            } else {
                $_SESSION['error'] = "Error al actualitzar el professor";
            }
        } else {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
        }
        
        Router::redirect('/teachers/edit/' . $id);
    }
    
    /**
     * Elimina un professor
     */
    public function delete($id = null) {
        // Si no es passa l'ID com a paràmetre, prova d'obtenir-lo de GET
        if ($id === null) {
            $id = $_GET['id'] ?? null;
        }
        
        if (!$id) {
            Router::redirect('/teachers');
        }
        
        $result = $this->teacherModel->delete($id);
        
        if ($result) {
            $_SESSION['success'] = "Professor eliminat correctament!";
        } else {
            $_SESSION['error'] = "Error al eliminar el professor";
        }
        
        Router::redirect('/teachers');
    }
    
    /**
     * Valida les dades d'un professor
     */
    private function validateTeacher($data, $id = null) {
        $errors = [];
        
        // Validar nom
        if (empty($data['name']) || strlen(trim($data['name'])) < 3) {
            $errors['name'] = "El nom ha de tenir almenys 3 caràcters";
        }
        
        // Validar email
        if (empty($data['email'])) {
            $errors['email'] = "L'email és obligatori";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "L'email no és vàlid";
        } elseif ($this->teacherModel->emailExists($data['email'], $id)) {
            $errors['email'] = "Aquest email ja està registrat";
        }
        
        // Validar telèfon
        if (empty($data['phone'])) {
            $errors['phone'] = "El telèfon és obligatori";
        } elseif (!preg_match('/^[0-9]{9}$/', $data['phone'])) {
            $errors['phone'] = "El telèfon ha de tenir 9 dígits";
        }
        
        // Validar departament
        if (empty($data['department']) || strlen(trim($data['department'])) < 2) {
            $errors['department'] = "El departament és obligatori";
        }
        
        // Validar especialitat
        if (empty($data['specialty']) || strlen(trim($data['specialty'])) < 2) {
            $errors['specialty'] = "L'especialitat és obligatòria";
        }
        
        return $errors;
    }
}
