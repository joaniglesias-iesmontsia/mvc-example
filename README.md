// filepath: /Users/joan/Documents/Documents - INS Montsià/Repos/Cursos/25-26/mvc-example/README.md
# 🎓 Exemple Pràctic del Patró MVC amb PHP

## 📚 Què és el Patró MVC?

El patró **Model-Vista-Controlador (MVC)** és un patró de disseny arquitectònic que organitza el codi d'una aplicació en **tres components principals** que treballen junts però de forma independent:

```
┌──────────────┐      ┌──────────────┐      ┌──────────────┐
│   MODEL      │◄─────│ CONTROLLER   │─────►│    VIEW      │
│  (Dades)     │      │  (Lògica)    │      │(Interfície)  │
└──────────────┘      └──────────────┘      └──────────────┘
       ▲                     ▲                      ▲
       │                     │                      │
    Base de                Gestiona              Mostra
     Dades               Peticions             Informació
```

### 🔹 Components del MVC

#### 1. **MODEL** - La Capa de Dades
- **Responsabilitat**: Gestionar les dades i la lògica de negoci
- **No sap res sobre**: Com es mostren les dades (Vistes) o com arriben les peticions (Controladors)
- **En aquest projecte**: `models/Student.php`, `models/Teacher.php`

#### 2. **VIEW** - La Capa de Presentació
- **Responsabilitat**: Mostrar la informació a l'usuari (HTML, CSS)
- **No sap res sobre**: D'on venen les dades o com es processen
- **En aquest projecte**: Tots els fitxers dins `views/`

#### 3. **CONTROLLER** - La Capa de Coordinació
- **Responsabilitat**: Rebre peticions, coordinar Model i Vista
- **És l'intermediari**: Entre l'usuari i les dades
- **En aquest projecte**: `controllers/StudentController.php`, `controllers/TeacherController.php`

#### 4. **ROUTER** - El Sistema de Rutes ✨ NOU
- **Responsabilitat**: Analitzar la URL i cridar al controlador adequat
- **Centralitza**: Totes les rutes de l'aplicació en un sol fitxer
- **En aquest projecte**: `core/Router.php` i `routes/web.php`

---

## 🗂️ Estructura del Projecte

```
mvc-example/
│
├── index.php                    # 🚪 Front Controller ÚNIC (punt d'entrada)
│
├── .htaccess                    # ⚙️ Configuració Apache per URLs netes
│
├── core/
│   └── Router.php              # 🔀 Sistema de gestió de rutes
│
├── routes/
│   └── web.php                 # 📍 DEFINICIÓ DE TOTES LES RUTES
│
├── config/
│   └── Database.php            # ⚙️ Configuració de la connexió a BD
│
├── models/
│   ├── Student.php             # 📊 MODEL - Gestió de dades d'estudiants
│   └── Teacher.php             # 📊 MODEL - Gestió de dades de professors
│
├── controllers/
│   ├── StudentController.php   # 🎮 CONTROLLER - Lògica d'estudiants
│   └── TeacherController.php   # 🎮 CONTROLLER - Lògica de professors
│
├── views/
│   ├── layouts/
│   │   ├── header.php         # 🎨 Capçalera HTML compartida
│   │   └── footer.php         # 🎨 Peu de pàgina compartit
│   ├── students/
│   │   ├── index.php          # 👁️ VISTA - Llistat d'estudiants
│   │   ├── create.php         # 👁️ VISTA - Formulari de creació
│   │   └── edit.php           # 👁️ VISTA - Formulari d'edició
│   └── teachers/
│       ├── index.php          # 👁️ VISTA - Llistat de professors
│       ├── create.php         # 👁️ VISTA - Formulari de creació
│       └── edit.php           # 👁️ VISTA - Formulari d'edició
│
├── public/
│   └── css/
│       └── style.css          # 🎨 Estils CSS
│
└── database/
    ├── init.php               # 🔧 Script d'inicialització de BD
    ├── init.sql               # 📝 Estructura de la BD
    └── students.db            # 💾 Base de dades SQLite
```

---

## � Sistema de Rutes (Routing)

Aquest projecte utilitza un **sistema de rutes centralitzat**, similar al que usen frameworks moderns com Laravel, Symfony o Slim.

### 📍 Com Funcionen les Rutes?

**Abans (sense router):**
```
URL: index.php?action=edit&id=5
Problema: URLs llargues, poc semàntiques, difícils d'escalar
```

**Ara (amb router):**
```
URL: /students/edit/5
Avantatge: URLs netes, semàntiques, fàcils de mantenir
```

### 🎯 Definició de Rutes (`routes/web.php`)

```php
// Llistat d'estudiants
$router->get('/students', 'StudentController@index');

// Crear estudiant
$router->get('/students/create', 'StudentController@create');
$router->post('/students/store', 'StudentController@store');

// Editar estudiant (amb paràmetre dinàmic :id)
$router->get('/students/edit/:id', 'StudentController@edit');
$router->post('/students/update', 'StudentController@update');

// Eliminar estudiant
$router->get('/students/delete/:id', 'StudentController@delete');
```

### 🔧 Com Funciona Internament?

#### 1️⃣ **L'usuari accedeix a una URL**
```
GET /students/edit/5
```

#### 2️⃣ **El Router analitza la URL**
```php
// core/Router.php
public function dispatch() {
    $method = 'GET';
    $uri = '/students/edit/5';
    
    // Busca la ruta corresponent
    foreach ($this->routes['GET'] as $route => $action) {
        if ($this->match('/students/edit/:id', '/students/edit/5')) {
            // Coincidència! Extreu el paràmetre: id = 5
            $this->callAction('StudentController@edit', [5]);
        }
    }
}
```

#### 3️⃣ **El Router crida al Controlador**
```php
// Crea instància: $controller = new StudentController();
// Crida mètode: $controller->edit(5);
```

#### 4️⃣ **El Controlador processa i mostra la Vista**
```php
public function edit($id) {
    $student = $this->studentModel->getById($id);
    require_once 'views/students/edit.php';
}
```

### ✨ Avantatges del Sistema de Rutes

#### ✅ **Centralització**
Totes les rutes en un sol fitxer (`routes/web.php`), fàcil de veure què fa l'aplicació.

#### ✅ **URLs Netes i Semàntiques**
```
❌ ABANS: index.php?action=edit&id=5
✅ ARA:   /students/edit/5
```

#### ✅ **Escalabilitat**
Afegir nous models és molt més fàcil:
```php
// Només cal afegir aquestes línies a routes/web.php
$router->get('/courses', 'CourseController@index');
$router->get('/courses/create', 'CourseController@create');
// etc.
```

#### ✅ **Preparació per a Frameworks Professionals**
Laravel, Symfony, i altres frameworks usen exactament aquest patró.

#### ✅ **Paràmetres Dinàmics**
```php
$router->get('/students/:id', 'StudentController@show');
$router->get('/posts/:slug', 'PostController@show');
$router->get('/users/:username/posts/:id', 'PostController@userPost');
```

### 🎓 Com Afegir Noves Rutes?

**Exemple: Crear un CRUD de Cursos**

#### 1. Defineix les rutes (`routes/web.php`):
```php
$router->get('/courses', 'CourseController@index');
$router->get('/courses/create', 'CourseController@create');
$router->post('/courses/store', 'CourseController@store');
$router->get('/courses/edit/:id', 'CourseController@edit');
$router->post('/courses/update', 'CourseController@update');
$router->get('/courses/delete/:id', 'CourseController@delete');
```

#### 2. Crea el Controlador (`controllers/CourseController.php`):
```php
class CourseController {
    public function index() { ... }
    public function create() { ... }
    public function store() { ... }
    public function edit($id) { ... }
    public function update() { ... }
    public function delete($id) { ... }
}
```

#### 3. Crea el Model i les Vistes
- Model: `models/Course.php`
- Vistes: `views/courses/index.php`, `create.php`, `edit.php`

**I ja està! El router s'encarrega de la resta.** 🚀

### ⚠️ Important: Ordre de les Rutes

Les rutes més específiques han d'anar **ABANS** que les genèriques:

```php
✅ CORRECTE:
$router->get('/students/create', 'StudentController@create');
$router->get('/students/:id', 'StudentController@show');

❌ MALAMENT:
$router->get('/students/:id', 'StudentController@show');
$router->get('/students/create', 'StudentController@create');
// ↑ Mai s'executarà! :id captura "create" com a paràmetre
```

---

## �🔄 Flux d'Execució: Com Funciona el MVC

### Exemple 1: Llistar Estudiants

```
👤 Usuari                🚪 Front             🎮 Controller        📊 Model           👁️ View
   │                    Controller              │                   │                  │
   │                       │                    │                   │                  │
   │  1. Accedeix          │                    │                   │                  │
   │  index.php            │                    │                   │                  │
   ├──────────────────────►│                    │                   │                  │
   │                       │                    │                   │                  │
   │                       │  2. Crida index()  │                   │                  │
   │                       ├───────────────────►│                   │                  │
   │                       │                    │                   │                  │
   │                       │                    │  3. getAll()      │                  │
   │                       │                    ├──────────────────►│                  │
   │                       │                    │                   │                  │
   │                       │                    │  4. Retorna       │                  │
   │                       │                    │     estudiants    │                  │
   │                       │                    │◄──────────────────┤                  │
   │                       │                    │                   │                  │
   │                       │                    │  5. Carrega       │                  │
   │                       │                    │     index.php     │                  │
   │                       │                    ├──────────────────────────────────────►│
   │                       │                    │                   │                  │
   │  6. Mostra HTML       │                    │                   │                  │
   │◄──────────────────────┴────────────────────┴───────────────────┴──────────────────┤
```

### 📝 Codi Real del Projecte:

#### 1️⃣ **Front Controller** (`index.php`)
```php
// Punt d'entrada de l'aplicació
$controller = new StudentController();
$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $controller->index();  // Crida al mètode index del controlador
        break;
}
```

#### 2️⃣ **Controller** (`controllers/StudentController.php`)
```php
public function index() {
    // Demana les dades al Model
    $students = $this->studentModel->getAll();
    
    // Passa les dades a la Vista
    require_once __DIR__ . '/../views/students/index.php';
}
```

#### 3️⃣ **Model** (`models/Student.php`)
```php
public function getAll() {
    // Consulta SQL per obtenir tots els estudiants
    $query = "SELECT * FROM students ORDER BY name ASC";
    $stmt = $this->db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll();  // Retorna un array d'estudiants
}
```

#### 4️⃣ **View** (`views/students/index.php`)
```php
<?php foreach ($students as $student): ?>
    <tr>
        <td><?= htmlspecialchars($student['name']) ?></td>
        <td><?= htmlspecialchars($student['email']) ?></td>
        <td><?= htmlspecialchars($student['age']) ?></td>
    </tr>
<?php endforeach; ?>
```

---

## 📖 Exemple 2: Crear un Nou Estudiant

### Pas a Pas amb Codi Real:

### **Pas 1: L'usuari clica "Afegir Estudiant"**
```html
<!-- Enllaç a la Vista -->
<a href="index.php?action=create">➕ Afegir Estudiant</a>
```

### **Pas 2: El Front Controller rep la petició**
```php
// index.php
$action = $_GET['action'] ?? 'index';  // action = 'create'

switch ($action) {
    case 'create':
        $controller->create();  // Mostra el formulari
        break;
}
```

### **Pas 3: El Controller mostra el formulari**
```php
// controllers/StudentController.php
public function create() {
    // Simplement carrega la vista del formulari
    require_once __DIR__ . '/../views/students/create.php';
}
```

### **Pas 4: L'usuari omple i envia el formulari**
```html
<!-- views/students/create.php -->
<form action="index.php?action=store" method="POST">
    <input type="text" name="name" required>
    <input type="email" name="email" required>
    <input type="number" name="age" required>
    <select name="course" required>
        <option value="DAW">DAW</option>
        <option value="DAM">DAM</option>
    </select>
    <button type="submit">💾 Crear Estudiant</button>
</form>
```

### **Pas 5: El Controller processa les dades**
```php
// controllers/StudentController.php
public function store() {
    // 1. Valida les dades
    $errors = $this->validateStudent($_POST);
    
    if (empty($errors)) {
        // 2. Crida al Model per guardar
        $result = $this->studentModel->create($_POST);
        
        if ($result) {
            $_SESSION['success'] = "Estudiant creat correctament!";
            header('Location: index.php');  // Redirigeix al llistat
            exit;
        }
    }
}
```

### **Pas 6: El Model guarda a la Base de Dades**
```php
// models/Student.php
public function create($data) {
    // SQL preparada per evitar injeccions
    $query = "INSERT INTO students (name, email, age, course) 
              VALUES (:name, :email, :age, :course)";
    
    $stmt = $this->db->prepare($query);
    
    // Neteja i vincula les dades
    $stmt->bindParam(':name', $data['name']);
    $stmt->bindParam(':email', $data['email']);
    $stmt->bindParam(':age', $data['age']);
    $stmt->bindParam(':course', $data['course']);
    
    return $stmt->execute();  // Retorna true si tot va bé
}
```

---

## 📖 Exemple 3: CRUD de Professors - Extensió del Patró

Aquest projecte també inclou un **CRUD complet de professors** que segueix exactament el mateix patró MVC que els estudiants. Això demostra la **reutilització i escalabilitat** del patró.

### 🔄 Comparativa: Estudiants vs Professors

| Component | Estudiants | Professors |
|-----------|-----------|------------|
| **Model** | `Student.php` | `Teacher.php` |
| **Controller** | `StudentController.php` | `TeacherController.php` |
| **Vistes** | `views/students/` | `views/teachers/` |
| **Front Controller** | `index.php` | `teachers.php` |
| **Taula BD** | `students` | `teachers` |

### 📊 Camps del Model Teacher

```php
// models/Teacher.php
class Teacher {
    public $id;
    public $name;          // Nom del professor
    public $email;         // Email (únic)
    public $phone;         // Telèfon (9 dígits)
    public $department;    // Departament (Informàtica, Matemàtiques, etc.)
    public $specialty;     // Especialitat (Desenvolupament Web, etc.)
    public $created_at;    // Data de creació
}
```

### 🎓 Lliçó Important: **Patró com a Plantilla Reutilitzable**

Quan tens un patró ben definit:
1. **Crear un nou CRUD és ràpid**: Només cal copiar l'estructura i adaptar els camps
2. **Menys errors**: Segueixes sempre la mateixa lògica
3. **Fàcil de mantenir**: Tots els CRUDs funcionen igual
4. **Escalable**: Pots afegir tants models com necessitis (cursos, assignatures, notes, etc.)

### 💡 Exemple: Si vols afegir un CRUD de "Cursos"

1. Crea `models/Course.php` (copiant `Student.php` i adaptant camps)
2. Crea `controllers/CourseController.php` (copiant `StudentController.php`)
3. Crea `views/courses/` amb `index.php`, `create.php`, `edit.php`
4. Crea `courses.php` com a Front Controller
5. Afegeix la taula `courses` a `init.sql`
6. Afegeix l'enllaç al menú de navegació en `header.php`

**I ja tens un CRUD complet de cursos!** 🚀

---

## 🎯 Avantatges del Patró MVC

### ✅ **Separació de Responsabilitats**
Cada component té una funció clara:
- **Model**: Només gestiona dades
- **Vista**: Només mostra informació
- **Controller**: Només coordina

### ✅ **Reutilització de Codi**
- Els layouts (`header.php`, `footer.php`) es reutilitzen en totes les vistes
- El mateix Model pot ser usat per diferents Controladors

### ✅ **Manteniment Fàcil**
- Vols canviar el disseny? → Modifica només les **Vistes**
- Vols canviar la Base de Dades? → Modifica només el **Model**
- Vols afegir validacions? → Modifica només el **Controller**

### ✅ **Treball en Equip**
- Un desenvolupador pot treballar en les Vistes (frontend)
- Un altre en els Models (backend)
- Un altre en els Controladors (lògica de negoci)

---

## 🧩 Components Addicionals

### 📦 **Database.php** - Patró Singleton
```php
// config/Database.php
class Database {
    private static $instance = null;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
```
**Per què?** Garanteix que només hi hagi UNA connexió a la base de dades durant tota l'execució de l'aplicació.

### 🎨 **Layouts** - Reutilització de Codi HTML
```php
// Totes les vistes inclouen el mateix header i footer
<?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <!-- Contingut específic de cada vista -->
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
```

### 📬 **Missatges Flash** amb Sessions
```php
// Controller: Guarda missatge
$_SESSION['success'] = "Estudiant creat correctament!";

// View: Mostra i elimina
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert"><?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
```

---

## 🚀 Com Executar el Projecte

### 1️⃣ **Inicialitzar la Base de Dades**
```bash
php database/init.php
```
Això crea el fitxer `database/students.db` amb:
- Taula d'**estudiants** amb 4 registres d'exemple
- Taula de **professors** amb 4 registres d'exemple

### 2️⃣ **Iniciar el Servidor PHP**
```bash
php -S localhost:8000
```

### 3️⃣ **Accedir a l'Aplicació**
- **Gestió d'Estudiants**: `http://localhost:8000`
- **Gestió de Professors**: `http://localhost:8000/teachers.php`

---

## 📊 Funcionalitats Implementades

### ✅ **CRUD d'Estudiants**
- ✏️ Crear nous estudiants
- 📋 Llistar tots els estudiants
- ✏️ Editar estudiants existents
- 🗑️ Eliminar estudiants
- ✅ Validació de formularis (nom, email, edat, curs)
- 📧 Comprovació d'emails duplicats

### ✅ **CRUD de Professors**
- ✏️ Crear nous professors
- 📋 Llistar tots els professors
- ✏️ Editar professors existents
- 🗑️ Eliminar professors
- ✅ Validació de formularis (nom, email, telèfon, departament, especialitat)
- 📧 Comprovació d'emails duplicats
- 📱 Validació de format de telèfon (9 dígits)

---

## 🔍 Exercicis per Practicar

### 📝 **Nivell Bàsic**
1. Afegeix un nou camp "població" a la taula d'estudiants
2. Crea una vista per mostrar només estudiants de DAW
3. Afegeix validació per comprovar que l'edat sigui major de 16
4. Afegeix un camp "anys d'experiència" a la taula de professors

### 📝 **Nivell Intermedi**
5. Crea un nou Model `Course` per gestionar els cursos
6. Afegeix paginació al llistat d'estudiants i professors (10 per pàgina)
7. Implementa una cerca per nom o email en ambdues taules
8. Crea una relació entre professors i cursos (un professor pot impartir diversos cursos)

### 📝 **Nivell Avançat**
9. Afegeix autenticació d'usuaris (login/logout)
10. Crea una API REST que retorni JSON en lloc de HTML
11. Implementa el patró Repository per abstraure l'accés a dades
12. Afegeix la possibilitat d'assignar estudiants a professors (relació many-to-many)

---

## 📚 Recursos Addicionals

- [Patró MVC - Wikipedia](https://ca.wikipedia.org/wiki/Model%E2%80%93vista%E2%80%93controlador)
- [PHP: The Right Way](https://phptherightway.com/)
- [PDO i Prepared Statements](https://www.php.net/manual/es/pdo.prepared-statements.php)
- [Sessions en PHP](https://www.php.net/manual/es/book.session.php)

---

## 👨‍💻 Autor

**INS Montsià - CFGS DAW**  
Curs 2025-26

---

## 📄 Llicència

Aquest és un projecte educatiu de lliure ús per a l'aprenentatge del patró MVC.