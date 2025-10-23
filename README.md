// filepath: /Users/joan/Documents/Documents - INS Montsià/Repos/Cursos/25-26/mvc-example/README.md
# 🎓 MVC amb PHP — Guia per a Estudiants

Objectiu: entendre el patró MVC i com s’organitza el codi d’aquest projecte perquè el puguis llegir, modificar i ampliar amb seguretat.

---

## 📚 Què és el patró MVC?

El patró **Model–Vista–Controlador (MVC)** separa l’aplicació en tres peces clarament diferenciades:

```
┌──────────────┐      ┌──────────────┐      ┌──────────────┐
│    MODEL     │◄─────│ CONTROLLER   │─────►│     VIEW     │
│ (Dades)      │      │ (Lògica)     │      │ (Interfície) │
└──────────────┘      └──────────────┘      └──────────────┘
       ▲                     ▲                      ▲
       │                     │                      │
   Base de Dades        Gestiona peticions       Mostra informació
```

- Model: codi d’accés a dades i lògica de negoci (no sap res d’HTML).
- Controller: rep la petició, valida dades i demana al Model; després passa dades a la Vista.
- View: HTML/CSS/JS per mostrar dades (no fa consultes a la BD ni valida negoci).

En aquest projecte també hi ha un component clau: el Router.

```
Navegador → .htaccess/router → index.php (Front Controller)
          → Router (core/Router.php) + rutes (routes/web.php)
          → Controller → Model → View (layouts + vista)
          → Resposta HTML
```

---

## 🗂️ Estructura del projecte

```
mvc-example/
│
├── index.php                    # 🚪 Punt d'entrada (Front Controller)
├── .htaccess                    # ⚙️ Reescriptura d’URLs (si uses Apache)
│
├── core/
│   └── Router.php               # 🔀 Sistema de rutes (matching i dispatch)
│
├── routes/
│   └── web.php                  # 📍 Definició CENTRALITZADA de rutes
│
├── config/
│   └── Database.php             # 🧱 Connexió a BD (PDO, Singleton)
│
├── models/
│   ├── Student.php              # 📊 Model d'Estudiants (relació 1:N amb Courses)
│   ├── Teacher.php              # 📊 Model de Professors (relació N:M amb Courses)
│   ├── Course.php               # 📊 Model de Cursos (1:N amb Students, N:M amb Teachers)
│   └── TeachingTeam.php         # 📊 Model d'Equips Docents (taula intermèdia N:M)
│
├── controllers/
│   ├── StudentController.php    # 🎮 Lògica d'Estudiants
│   ├── TeacherController.php    # 🎮 Lògica de Professors
│   ├── CourseController.php     # 🎮 Lògica de Cursos
│   └── TeachingTeamController.php # 🎮 Lògica d'Equips Docents (assignacions N:M)
│
├── views/
│   ├── layouts/
│   │   ├── header.php           # 🎨 Capçalera (HTML compartit)
│   │   └── footer.php           # 🎨 Peu de pàgina (HTML compartit)
│   ├── students/
│   │   ├── index.php            # 👁️ Llistat d'estudiants
│   │   ├── create.php           # 👁️ Formulari de creació
│   │   └── edit.php             # 👁️ Formulari d'edició
│   ├── teachers/
│   │   ├── index.php            # 👁️ Llistat de professors
│   │   ├── create.php           # 👁️ Formulari de creació
│   │   └── edit.php             # 👁️ Formulari d'edició
│   ├── courses/
│   │   ├── index.php            # 👁️ Llistat de cursos
│   │   ├── create.php           # 👁️ Formulari de creació
│   │   └── edit.php             # 👁️ Formulari d'edició
│   └── teaching-teams/
│       ├── index.php            # 👁️ Llistat d'equips docents
│       ├── show.php             # 👁️ Detall d'un equip docent
│       ├── assign.php           # 👁️ Formulari d'assignació
│       └── by-teacher.php       # 👁️ Cursos d'un professor
│
├── public/
│   └── css/
│       └── style.css            # 🎨 Estils
│
└── database/
    ├── init.php                 # 🔧 Inicialitza la BD (SQLite)
    ├── init.sql                 # 📝 Esquema + dades de mostra
    └── students.db              # 💾 Fitxer SQLite (generat)
```

---

## 🔀 Sistema de rutes (Routing)

Les rutes estan definides a `routes/web.php` i són gestionades per `core/Router.php`. Això permet tenir **URLs netes** i tota la navegació en un **únic lloc**.

Exemple de rutes dels estudiants:

```php
// Llistat
$router->get('/students', 'StudentController@index');

// Crear
$router->get('/students/create', 'StudentController@create');
$router->post('/students/store', 'StudentController@store');

// Editar
$router->get('/students/edit/:id', 'StudentController@edit');
$router->post('/students/update', 'StudentController@update');

// Eliminar
$router->get('/students/delete/:id', 'StudentController@delete');
```

Per professors es segueix el mateix patró (`/teachers`, ...).

Notes importants:
- Les rutes específiques han d’anar abans de les genèriques (`/students/create` abans que `/students/:id`).
- Els paràmetres dinàmics es defineixen com `:id`, `:slug`, etc.

---

## 🔄 Flux complet d’una petició (exemple: llistar estudiants)

```
👤 Usuari         🌐 GET /students
   │
   ▼
🧭 .htaccess / Router → 🚪 index.php (Front Controller)
                         │
                         ├─ 🔀 Router: troba la ruta '/students'
                         │        ⇒ StudentController@index
                         │
                         ├─ 🎮 Controller: demana dades al Model
                         │        ⇒ Student::getAll()
                         │
                         ├─ 📊 Model: consulta BD i retorna dades
                         │
                         └─ 👁️ View: carrega layouts/header.php + views/students/index.php + layouts/footer.php
                                     ⇒ Resposta HTML al navegador
```

Mini codi real que reflecteix aquest flux:

```php
// routes/web.php
$router->get('/students', 'StudentController@index');

// controllers/StudentController.php
public function index() {
    $students = $this->studentModel->getAll();
    require __DIR__ . '/../views/students/index.php';
}

// models/Student.php
public function getAll() {
    $stmt = $this->db->prepare('SELECT * FROM students ORDER BY name ASC');
    $stmt->execute();
    return $stmt->fetchAll();
}
```

### 📊 Diagrama de seqüència detallat

```
👤 Usuari          .htaccess       🚪 index.php      🔀 Router        🎮 Controller      📊 Model          💾 BD           👁️ View
   │                   │                │                │                 │                 │               │              │
   │  GET /students    │                │                │                 │                 │               │              │
   ├──────────────────►│                │                │                 │                 │               │              │
   │                   │                │                │                 │                 │               │              │
   │                   │ Redirigeix a   │                │                 │                 │               │              │
   │                   │   index.php    │                │                 │                 │               │              │
   │                   ├───────────────►│                │                 │                 │               │              │
   │                   │                │                │                 │                 │               │              │
   │                   │                │ Carrega Router │                 │                 │               │              │
   │                   │                │ i routes/web   │                 │                 │               │              │
   │                   │                ├───────────────►│                 │                 │               │              │
   │                   │                │                │                 │                 │               │              │
   │                   │                │                │ Troba ruta      │                 │               │              │
   │                   │                │                │ '/students'     │                 │               │              │
   │                   │                │                │ ⇒ Crida         │                 │               │              │
   │                   │                │                │ StudentController│                │               │              │
   │                   │                │                │ @index          │                 │               │              │
   │                   │                │                ├────────────────►│                 │               │              │
   │                   │                │                │                 │                 │               │              │
   │                   │                │                │                 │ getAll()        │               │              │
   │                   │                │                │                 ├────────────────►│               │              │
   │                   │                │                │                 │                 │               │              │
   │                   │                │                │                 │                 │ SELECT * FROM │              │
   │                   │                │                │                 │                 │   students    │              │
   │                   │                │                │                 │                 ├──────────────►│              │
   │                   │                │                │                 │                 │               │              │
   │                   │                │                │                 │                 │ Retorna files │              │
   │                   │                │                │                 │                 │◄──────────────┤              │
   │                   │                │                │                 │                 │               │              │
   │                   │                │                │                 │ Array estudiants│               │              │
   │                   │                │                │                 │◄────────────────┤               │              │
   │                   │                │                │                 │                 │               │              │
   │                   │                │                │                 │ require          │               │              │
   │                   │                │                │                 │ views/students/  │               │              │
   │                   │                │                │                 │ index.php        │               │              │
   │                   │                │                │                 ├─────────────────────────────────────────────────►│
   │                   │                │                │                 │                 │               │              │
   │                   │                │                │                 │                 │               │ Genera HTML  │
   │                   │                │                │                 │                 │               │ amb foreach  │
   │                   │                │                │                 │                 │               │              │
   │  Mostra pàgina HTML                │                │                 │                 │               │              │
   │◄────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
```

### 📝 Pas a pas de la seqüència:

1️⃣ **Usuari** fa GET `/students` al navegador  
2️⃣ **.htaccess** captura la petició i redirigeix a `index.php`  
3️⃣ **index.php** (Front Controller) carrega el Router i les rutes definides a `routes/web.php`  
4️⃣ **Router** analitza la URL, troba la ruta `/students` i crida `StudentController@index`  
5️⃣ **Controller** demana les dades cridant `$this->studentModel->getAll()`  
6️⃣ **Model** prepara i executa la consulta SQL a la base de dades  
7️⃣ **Base de Dades** retorna les files de la taula `students`  
8️⃣ **Model** retorna un array d'estudiants al Controller  
9️⃣ **Controller** carrega la vista `views/students/index.php` passant-li les dades  
🔟 **View** genera l'HTML iterant sobre l'array d'estudiants  
1️⃣1️⃣ **Usuari** rep la pàgina HTML completa al navegador

---

## ✍️ Flux de creació (exemple: crear estudiant)

1) GET `/students/create` → mostra formulari.

```php
// routes/web.php
$router->get('/students/create', 'StudentController@create');

// controllers/StudentController.php
public function create() {
    require __DIR__ . '/../views/students/create.php';
}
```

2) POST `/students/store` → valida i guarda, després redirigeix.

```php
// routes/web.php
$router->post('/students/store', 'StudentController@store');

// controllers/StudentController.php
public function store() {
    $data = $_POST;
    // (Validacions bàsiques...)
    if ($this->studentModel->create($data)) {
        $_SESSION['success'] = 'Estudiant creat correctament';
        return Router::redirect('/students');
    }
    $_SESSION['error'] = 'No s’ha pogut crear';
    return Router::redirect('/students/create');
}
```

3) Model guarda a la BD amb PDO i consultes preparades.

```php
// models/Student.php
public function create(array $data): bool {
    $sql = 'INSERT INTO students (name, email, age, course) VALUES (:name, :email, :age, :course)';
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':name', $data['name']);
    $stmt->bindValue(':email', $data['email']);
    $stmt->bindValue(':age', $data['age']);
    $stmt->bindValue(':course', $data['course']);
    return $stmt->execute();
}
```

---

## ✅ Bones pràctiques aplicades

- PDO + consultes preparades (seguretat davant injeccions SQL).
- `htmlspecialchars()` a les vistes (evitar XSS).
- Validació al Controller i missatges flash via sessió.
- Layouts compartits (`views/layouts/header.php` i `footer.php`).
- Router centralitzat i redireccions amb `Router::redirect()`.

---

## � Com executar el projecte

1) Inicialitza la base de dades (SQLite):

```bash
php database/init.php
```

2) Engega el servidor de desenvolupament de PHP (recomanat indicar el router):

```bash
php -S localhost:8000 index.php
```

3) Obre al navegador:

- Estudiants: http://localhost:8000/students
- Professors: http://localhost:8000/teachers

Notes:
- Si utilitzes Apache, assegura’t que `.htaccess` estigui actiu (AllowOverride All) per a les URLs netes.
- A les vistes, s’utilitza un camí absolut per al CSS: `/public/css/style.css` (això evita problemes en rutes profundes).

---

## 📋 Rutes disponibles (resum)

Estudiants
- GET  `/students` → index
- GET  `/students/create` → create
- POST `/students/store` → store
- GET  `/students/edit/:id` → edit
- POST `/students/update` → update
- GET  `/students/delete/:id` → delete

Professors
- GET  `/teachers` → index
- GET  `/teachers/create` → create
- POST `/teachers/store` → store
- GET  `/teachers/edit/:id` → edit
- POST `/teachers/update` → update
- GET  `/teachers/delete/:id` → delete

---

## � Com afegir un nou mòdul (ex. Cursos)

1) Crea `models/Course.php` (mètodes: `getAll`, `getById`, `create`, `update`, `delete`).
2) Crea `controllers/CourseController.php` amb accions CRUD.
3) Crea vistes a `views/courses/` (`index.php`, `create.php`, `edit.php`).
4) Afegeix rutes a `routes/web.php`:

```php
$router->get('/courses', 'CourseController@index');
$router->get('/courses/create', 'CourseController@create');
$router->post('/courses/store', 'CourseController@store');
$router->get('/courses/edit/:id', 'CourseController@edit');
$router->post('/courses/update', 'CourseController@update');
$router->get('/courses/delete/:id', 'CourseController@delete');
```

Amb això, tot queda integrat al mateix flux MVC i routing centralitzat.

---

## 🔍 Exercicis recomanats

Bàsic
1) Afegeix el camp “població” a Estudiants i mostra’l al llistat.
2) Valida que l’edat sigui ≥ 16.

Intermedi
3) Cerca per nom o email (students i teachers).
4) Paginació (10 per pàgina) a `/students` i `/teachers`.

Avançat
5) ✅ **Implementat**: Relacions 1:N i N:M amb sistema complet d'equips docents
6) Afegeix login bàsic i protegeix rutes d'edició.

---

## 🔗 Relacions entre Entitats

Aquest projecte demostra **tres tipus de relacions** de bases de dades:

### 1️⃣ Relació 1:N (Un a Molts) - Students ↔ Courses
- **1 Curs** pot tenir **molts Estudiants**
- **1 Estudiant** pertany a **1 Curs**
- Implementació: Columna `course_id` a la taula `students` (clau forana)
- Protecció: No es pot eliminar un curs amb estudiants assignats
- 📖 Veure: [RELACIONS_1N.md](./RELACIONS_1N.md)

### 2️⃣ Relació N:M (Molts a Molts) - Courses ↔ Teachers
- **1 Curs** pot tenir **molts Professors** (equip docent)
- **1 Professor** pot estar a **molts Cursos**
- Implementació: Taula intermèdia `teaching_teams` amb dues claus foranes
- Bidireccional: Es pot consultar des de qualsevol direcció
- 📖 Veure: [RELACIONS_NM.md](./RELACIONS_NM.md)

### 📊 Diagrama de Relacions

```
┌──────────────────┐         ┌──────────────────┐
│     teachers     │ M     N │   teaching_teams │
│                  │◄────────┤   (taula inter.) │
│ id (PK)          │         │ course_id (FK)   │
│ name             │         │ teacher_id (FK)  │
│ email            │         │ UNIQUE(c,t)      │
└──────────────────┘         └──────────────────┘
                                      ▲
                                      │ N
                                      │
                             ┌────────┴─────────┐
                           1 │     courses      │ 1
                             ├──────────────────┤◄────────┐
                             │ id (PK)          │         │
                             │ code (UNIQUE)    │         │
                             │ name             │         │ N
                             └──────────────────┘         │
                                                  ┌───────┴────────┐
                                                  │    students    │
                                                  ├────────────────┤
                                                  │ id (PK)        │
                                                  │ name           │
                                                  │ course_id (FK) │
                                                  └────────────────┘
```

### 🧪 Tests Disponibles

- `./test_relacions_1n.sh` - Verifica relacions 1:N
- `./test_relacions_nm.sh` - Verifica relacions N:M

---

## 📚 Recursos

- Wikipedia (MVC): https://ca.wikipedia.org/wiki/Model%E2%80%93vista%E2%80%93controlador
- PHP: The Right Way: https://phptherightway.com/
- PDO Prepared Statements: https://www.php.net/manual/es/pdo.prepared-statements.php
- Sessions en PHP: https://www.php.net/manual/es/book.session.php

---

## 👨‍🏫 Crèdits

INS Montsià — CFGS DAW (Curs 2025–26)

---

## 📄 Llicència

Projecte educatiu lliure per aprendre MVC amb PHP.