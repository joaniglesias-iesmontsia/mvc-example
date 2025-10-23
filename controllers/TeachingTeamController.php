<?php
/**
 * CONTROLLER: TeachingTeamController
 * 
 * Gestiona les operacions sobre els Equips Docents (relació N:M)
 * Permet assignar i desassignar professors a cursos
 */

require_once __DIR__ . '/../models/TeachingTeam.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Teacher.php';
require_once __DIR__ . '/../core/Router.php';

class TeachingTeamController {
    private $teamModel;
    private $courseModel;
    private $teacherModel;
    
    public function __construct() {
        $this->teamModel = new TeachingTeam();
        $this->courseModel = new Course();
        $this->teacherModel = new Teacher();
    }
    
    /**
     * Mostra el llistat de tots els equips docents
     * Amb resum de professors per curs
     */
    public function index() {
        $teams = $this->teamModel->getAllTeamsWithCount();
        $statistics = $this->teamModel->getStatistics();
        require_once __DIR__ . '/../views/teaching-teams/index.php';
    }
    
    /**
     * Mostra els professors assignats a un curs concret
     * i permet gestionar l'equip docent
     */
    public function show($courseId = null) {
        if ($courseId === null) {
            $courseId = $_GET['course_id'] ?? null;
        }
        
        if (!$courseId) {
            $_SESSION['error'] = "Cal especificar un curs";
            Router::redirect('/teaching-teams');
        }
        
        $course = $this->courseModel->getById($courseId);
        if (!$course) {
            $_SESSION['error'] = "Curs no trobat";
            Router::redirect('/teaching-teams');
        }
        
        $teachers = $this->teamModel->getTeachersByCourse($courseId);
        $availableTeachers = $this->teamModel->getAvailableTeachers($courseId);
        
        require_once __DIR__ . '/../views/teaching-teams/show.php';
    }
    
    /**
     * Mostra el formulari per assignar un professor a un curs
     */
    public function assign($courseId = null) {
        if ($courseId === null) {
            $courseId = $_GET['course_id'] ?? null;
        }
        
        if (!$courseId) {
            $_SESSION['error'] = "Cal especificar un curs";
            Router::redirect('/teaching-teams');
        }
        
        $course = $this->courseModel->getById($courseId);
        if (!$course) {
            $_SESSION['error'] = "Curs no trobat";
            Router::redirect('/teaching-teams');
        }
        
        $availableTeachers = $this->teamModel->getAvailableTeachers($courseId);
        
        require_once __DIR__ . '/../views/teaching-teams/assign.php';
    }
    
    /**
     * Processa l'assignació d'un professor a un curs
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Router::redirect('/teaching-teams');
        }
        
        $courseId = $_POST['course_id'] ?? null;
        $teacherId = $_POST['teacher_id'] ?? null;
        
        if (!$courseId || !$teacherId) {
            $_SESSION['error'] = "Dades incompletes";
            Router::redirect('/teaching-teams');
        }
        
        // Validar que el curs existeix
        $course = $this->courseModel->getById($courseId);
        if (!$course) {
            $_SESSION['error'] = "Curs no trobat";
            Router::redirect('/teaching-teams');
        }
        
        // Validar que el professor existeix
        $teacher = $this->teacherModel->getById($teacherId);
        if (!$teacher) {
            $_SESSION['error'] = "Professor no trobat";
            Router::redirect('/teaching-teams');
        }
        
        // Comprovar si ja està assignat
        if ($this->teamModel->isTeacherAssigned($courseId, $teacherId)) {
            $_SESSION['error'] = "Aquest professor ja està assignat a aquest curs";
            Router::redirect('/teaching-teams/show?course_id=' . $courseId);
        }
        
        // Assignar professor al curs
        $result = $this->teamModel->assignTeacher($courseId, $teacherId);
        
        if ($result) {
            $_SESSION['success'] = "Professor assignat correctament a l'equip docent!";
        } else {
            $_SESSION['error'] = "Error al assignar el professor";
        }
        
        Router::redirect('/teaching-teams/show?course_id=' . $courseId);
    }
    
    /**
     * Desassigna un professor d'un curs
     */
    public function remove($courseId = null, $teacherId = null) {
        // Intentar obtenir des dels paràmetres de la URL o GET
        if ($courseId === null) {
            $courseId = $_GET['course_id'] ?? null;
        }
        if ($teacherId === null) {
            $teacherId = $_GET['teacher_id'] ?? null;
        }
        
        if (!$courseId || !$teacherId) {
            $_SESSION['error'] = "Dades incompletes per desassignar";
            Router::redirect('/teaching-teams');
        }
        
        $result = $this->teamModel->removeTeacher($courseId, $teacherId);
        
        if ($result) {
            $_SESSION['success'] = "Professor desassignat de l'equip docent correctament!";
        } else {
            $_SESSION['error'] = "Error al desassignar el professor";
        }
        
        Router::redirect('/teaching-teams/show?course_id=' . $courseId);
    }
    
    /**
     * Mostra la vista de professors amb els seus cursos assignats
     */
    public function byTeacher($teacherId = null) {
        if ($teacherId === null) {
            $teacherId = $_GET['teacher_id'] ?? null;
        }
        
        if (!$teacherId) {
            $_SESSION['error'] = "Cal especificar un professor";
            Router::redirect('/teachers');
        }
        
        $teacher = $this->teacherModel->getById($teacherId);
        if (!$teacher) {
            $_SESSION['error'] = "Professor no trobat";
            Router::redirect('/teachers');
        }
        
        $courses = $this->teamModel->getCoursesByTeacher($teacherId);
        
        require_once __DIR__ . '/../views/teaching-teams/by-teacher.php';
    }
}
