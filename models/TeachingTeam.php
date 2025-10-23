<?php
/**
 * MODEL: TeachingTeam (Equip Docent)
 * 
 * Gestiona la RELACIÓ N:M (MOLTS A MOLTS) entre courses i teachers
 * 
 * CONCEPTE CLAU: Taula Intermèdia
 * ================================
 * Per implementar una relació N:M necessitem una taula intermèdia que connecta
 * les dues taules principals. En aquest cas:
 * 
 * - Un CURS pot tenir MOLTS PROFESSORS (N)
 * - Un PROFESSOR pot estar a MOLTS CURSOS (M)
 * 
 * Esquema de la relació:
 * 
 *   courses (1) ←→ (N) teaching_teams (M) ←→ (1) teachers
 * 
 * La taula teaching_teams conté:
 * - course_id (FK → courses)
 * - teacher_id (FK → teachers)
 * - assigned_at (timestamp)
 * - UNIQUE(course_id, teacher_id) - Evita duplicats
 * 
 * Això permet:
 * - Assignar múltiples professors a un curs
 * - Veure tots els cursos d'un professor
 * - Eliminar assignacions sense afectar cursos ni professors
 */

require_once __DIR__ . '/../config/Database.php';

class TeachingTeam {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtenir tots els professors assignats a un curs
     * Consulta N:M amb JOIN
     * 
     * @param int $courseId ID del curs
     * @return array Professors assignats amb dades completes
     */
    public function getTeachersByCourse($courseId) {
        $stmt = $this->db->prepare("
            SELECT 
                t.*,
                tt.assigned_at,
                tt.id as assignment_id
            FROM teachers t
            INNER JOIN teaching_teams tt ON t.id = tt.teacher_id
            WHERE tt.course_id = ?
            ORDER BY t.name ASC
        ");
        $stmt->execute([$courseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtenir tots els cursos on està assignat un professor
     * Consulta N:M amb JOIN (perspectiva inversa)
     * 
     * @param int $teacherId ID del professor
     * @return array Cursos assignats amb dades completes
     */
    public function getCoursesByTeacher($teacherId) {
        $stmt = $this->db->prepare("
            SELECT 
                c.*,
                tt.assigned_at,
                tt.id as assignment_id
            FROM courses c
            INNER JOIN teaching_teams tt ON c.id = tt.course_id
            WHERE tt.teacher_id = ?
            ORDER BY c.code ASC
        ");
        $stmt->execute([$teacherId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtenir resum de tots els equips docents
     * Amb nombre de professors per curs
     * 
     * @return array Cursos amb recompte de professors
     */
    public function getAllTeamsWithCount() {
        $stmt = $this->db->prepare("
            SELECT 
                c.id,
                c.code,
                c.name,
                c.description,
                COUNT(tt.teacher_id) as teacher_count
            FROM courses c
            LEFT JOIN teaching_teams tt ON c.id = tt.course_id
            GROUP BY c.id, c.code, c.name, c.description
            ORDER BY c.code ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtenir professors NO assignats a un curs concret
     * Útil per mostrar opcions disponibles al formulari d'assignació
     * 
     * @param int $courseId ID del curs
     * @return array Professors disponibles per assignar
     */
    public function getAvailableTeachers($courseId) {
        $stmt = $this->db->prepare("
            SELECT t.*
            FROM teachers t
            WHERE t.id NOT IN (
                SELECT teacher_id 
                FROM teaching_teams 
                WHERE course_id = ?
            )
            ORDER BY t.name ASC
        ");
        $stmt->execute([$courseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Assignar un professor a un curs
     * Crea la relació N:M
     * 
     * @param int $courseId ID del curs
     * @param int $teacherId ID del professor
     * @return bool True si l'assignació s'ha creat correctament
     */
    public function assignTeacher($courseId, $teacherId) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO teaching_teams (course_id, teacher_id)
                VALUES (?, ?)
            ");
            return $stmt->execute([$courseId, $teacherId]);
        } catch (PDOException $e) {
            // Si hi ha error (p.ex. duplicat per UNIQUE constraint), retornar false
            return false;
        }
    }
    
    /**
     * Desassignar un professor d'un curs
     * Elimina la relació N:M
     * 
     * @param int $courseId ID del curs
     * @param int $teacherId ID del professor
     * @return bool True si l'assignació s'ha eliminat correctament
     */
    public function removeTeacher($courseId, $teacherId) {
        $stmt = $this->db->prepare("
            DELETE FROM teaching_teams
            WHERE course_id = ? AND teacher_id = ?
        ");
        return $stmt->execute([$courseId, $teacherId]);
    }
    
    /**
     * Eliminar totes les assignacions d'un curs
     * Útil quan s'elimina un curs (ON DELETE CASCADE ho fa automàticament)
     * 
     * @param int $courseId ID del curs
     * @return bool True si s'han eliminat les assignacions
     */
    public function removeAllTeachersFromCourse($courseId) {
        $stmt = $this->db->prepare("
            DELETE FROM teaching_teams
            WHERE course_id = ?
        ");
        return $stmt->execute([$courseId]);
    }
    
    /**
     * Comprovar si un professor ja està assignat a un curs
     * Validació per evitar duplicats
     * 
     * @param int $courseId ID del curs
     * @param int $teacherId ID del professor
     * @return bool True si ja està assignat
     */
    public function isTeacherAssigned($courseId, $teacherId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM teaching_teams
            WHERE course_id = ? AND teacher_id = ?
        ");
        $stmt->execute([$courseId, $teacherId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    
    /**
     * Obtenir estadístiques globals dels equips docents
     * Útil per mostrar resums
     * 
     * @return array Estadístiques
     */
    public function getStatistics() {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(DISTINCT course_id) as total_courses_with_teachers,
                COUNT(DISTINCT teacher_id) as total_teachers_assigned,
                COUNT(*) as total_assignments,
                ROUND(CAST(COUNT(*) AS FLOAT) / COUNT(DISTINCT course_id), 2) as avg_teachers_per_course
            FROM teaching_teams
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
