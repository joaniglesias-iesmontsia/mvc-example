<?php
/**
 * ROUTES - Definició de Totes les Rutes de l'Aplicació
 * 
 * Aquest fitxer centralitza totes les rutes de l'aplicació.
 * Cada ruta associa una URL amb un controlador i un mètode.
 * 
 * Format: $router->METHOD('url', 'Controlador@metode');
 * 
 * Exemple:
 *   $router->get('/students', 'StudentController@index');
 *   Significa: Quan es fa GET a /students, crida StudentController::index()
 * 
 * Paràmetres dinàmics:
 *   :id, :slug, etc. → Es capturen i es passen com a paràmetres al controlador
 *   Exemple: /students/:id → StudentController::show($id)
 */

// ============================================
// RUTES D'ESTUDIANTS
// ============================================

// Llistat de tots els estudiants
$router->get('/', 'StudentController@index');
$router->get('/students', 'StudentController@index');

// Formulari per crear un nou estudiant
$router->get('/students/create', 'StudentController@create');

// Processa la creació d'un estudiant (POST)
$router->post('/students/store', 'StudentController@store');

// Formulari per editar un estudiant
$router->get('/students/edit/:id', 'StudentController@edit');

// Processa l'actualització d'un estudiant (POST)
$router->post('/students/update', 'StudentController@update');

// Elimina un estudiant
$router->get('/students/delete/:id', 'StudentController@delete');


// ============================================
// RUTES DE PROFESSORS
// ============================================

// Llistat de tots els professors
$router->get('/teachers', 'TeacherController@index');

// Formulari per crear un nou professor
$router->get('/teachers/create', 'TeacherController@create');

// Processa la creació d'un professor (POST)
$router->post('/teachers/store', 'TeacherController@store');

// Formulari per editar un professor
$router->get('/teachers/edit/:id', 'TeacherController@edit');

// Processa l'actualització d'un professor (POST)
$router->post('/teachers/update', 'TeacherController@update');

// Elimina un professor
$router->get('/teachers/delete/:id', 'TeacherController@delete');


// ============================================
// RUTES DE CURSOS (Relació 1:N amb Estudiants)
// ============================================

// Llistat de tots els cursos
$router->get('/courses', 'CourseController@index');

// Formulari per crear un nou curs
$router->get('/courses/create', 'CourseController@create');

// Processa la creació d'un curs (POST)
$router->post('/courses/store', 'CourseController@store');

// Formulari per editar un curs
$router->get('/courses/edit/:id', 'CourseController@edit');

// Processa l'actualització d'un curs (POST)
$router->post('/courses/update', 'CourseController@update');

// Elimina un curs (només si no té estudiants assignats)
$router->get('/courses/delete/:id', 'CourseController@delete');


// ============================================
// RUTES D'EQUIPS DOCENTS (Relació N:M entre Courses i Teachers)
// ============================================

// Llistat de tots els equips docents
$router->get('/teaching-teams', 'TeachingTeamController@index');

// Veure l'equip docent d'un curs concret
$router->get('/teaching-teams/show', 'TeachingTeamController@show');

// Formulari per assignar un professor a un curs
$router->get('/teaching-teams/assign', 'TeachingTeamController@assign');

// Processa l'assignació d'un professor a un curs (POST)
$router->post('/teaching-teams/store', 'TeachingTeamController@store');

// Desassigna un professor d'un curs
$router->get('/teaching-teams/remove', 'TeachingTeamController@remove');

// Veure cursos d'un professor concret
$router->get('/teaching-teams/by-teacher', 'TeachingTeamController@byTeacher');

// ============================================
// RUTES DE AULES
// ============================================

// Llistat de tots els aules
$router->get('/classrooms', 'ClassroomController@index');

// Formulari per crear un nou aula
$router->get('/classrooms/create', 'ClassroomController@create');

// Processa la creació d'un aula (POST)
$router->post('/classrooms/store', 'ClassroomController@store');

// Formulari per editar un aula
$router->get('/classrooms/edit/:id', 'ClassroomController@edit');

// Processa l'actualització d'un aula (POST)
$router->post('/classrooms/update', 'ClassroomController@update');

// Elimina un aula
$router->get('/classrooms/delete/:id', 'ClassroomController@delete');



// ============================================
// NOTES PER ALS ESTUDIANTS
// ============================================

/**
 * 💡 Com afegir noves rutes?
 * 
 * 1. Defineix la ruta aquí seguint el patró:
 *    $router->get('/cursos', 'CourseController@index');
 * 
 * 2. Crea el controlador corresponent:
 *    controllers/CourseController.php
 * 
 * 3. Crea els mètodes necessaris al controlador:
 *    public function index() { ... }
 * 
 * 4. Crea les vistes corresponents:
 *    views/courses/index.php
 * 
 * ✨ Ordre important: Les rutes més específiques primer!
 *    ❌ MALAMENT:
 *       $router->get('/students/:id', ...);
 *       $router->get('/students/create', ...);  ← Mai s'executarà!
 * 
 *    ✅ CORRECTE:
 *       $router->get('/students/create', ...);
 *       $router->get('/students/:id', ...);
 */
