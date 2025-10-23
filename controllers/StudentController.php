<?php
/**
 * CONTROLLER: StudentController
 * 
 * Gestiona les peticions HTTP relacionades amb els estudiants
 * Fa de pont entre el Model (Student) i les Vistes
 */

require_once __DIR__ . '/../models/Student.php';

class StudentController {
    private $studentModel;
    
    public function __construct() {
        $this->studentModel = new Student();
    }
    
    /**
     * Mostra el llistat d'estudiants
     */
    public function index() {
        $students = $this->studentModel->getAll();
        require_once __DIR__ . '/../views/students/index.php';
    }
    
    /**
     * Mostra el formulari per crear un nou estudiant
     */
    public function create() {
        require_once __DIR__ . '/../views/students/create.php';
    }
    
    /**
     * Processa la creació d'un nou estudiant
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }
        
        $errors = $this->validateStudent($_POST);
        
        if (empty($errors)) {
            $result = $this->studentModel->create($_POST);
            
            if ($result) {
                $_SESSION['success'] = "Estudiant creat correctament!";
                header('Location: index.php');
                exit;
            } else {
                $_SESSION['error'] = "Error al crear l'estudiant";
            }
        } else {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
        }
        
        header('Location: index.php?action=create');
        exit;
    }
    
    /**
     * Mostra el formulari per editar un estudiant
     */
    public function edit() {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            header('Location: index.php');
            exit;
        }
        
        $student = $this->studentModel->getById($id);
        
        if (!$student) {
            $_SESSION['error'] = "Estudiant no trobat";
            header('Location: index.php');
            exit;
        }
        
        require_once __DIR__ . '/../views/students/edit.php';
    }
    
    /**
     * Processa l'actualització d'un estudiant
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }
        
        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            header('Location: index.php');
            exit;
        }
        
        $errors = $this->validateStudent($_POST, $id);
        
        if (empty($errors)) {
            $result = $this->studentModel->update($id, $_POST);
            
            if ($result) {
                $_SESSION['success'] = "Estudiant actualitzat correctament!";
                header('Location: index.php');
                exit;
            } else {
                $_SESSION['error'] = "Error al actualitzar l'estudiant";
            }
        } else {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
        }
        
        header('Location: index.php?action=edit&id=' . $id);
        exit;
    }
    
    /**
     * Elimina un estudiant
     */
    public function delete() {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            header('Location: index.php');
            exit;
        }
        
        $result = $this->studentModel->delete($id);
        
        if ($result) {
            $_SESSION['success'] = "Estudiant eliminat correctament!";
        } else {
            $_SESSION['error'] = "Error al eliminar l'estudiant";
        }
        
        header('Location: index.php');
        exit;
    }
    
    /**
     * Valida les dades d'un estudiant
     */
    private function validateStudent($data, $id = null) {
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
        } elseif ($this->studentModel->emailExists($data['email'], $id)) {
            $errors['email'] = "Aquest email ja està registrat";
        }
        
        // Validar edat
        if (empty($data['age']) || !is_numeric($data['age'])) {
            $errors['age'] = "L'edat ha de ser un número";
        } elseif ($data['age'] < 16 || $data['age'] > 99) {
            $errors['age'] = "L'edat ha d'estar entre 16 i 99 anys";
        }
        
        // Validar curs
        if (empty($data['course']) || strlen(trim($data['course'])) < 2) {
            $errors['course'] = "El curs és obligatori";
        }
        
        return $errors;
    }
}
