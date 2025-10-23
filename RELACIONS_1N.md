# 📚 Implementació de Relacions 1:N - Cursos i Estudiants

## 🎯 Objectiu Educatiu

Aquest exemple demostra com implementar relacions **1:N (un a molts)** en una aplicació MVC amb base de dades relacional. Concretament:

- **1 Curs** pot tenir **N Estudiants**
- **1 Estudiant** pertany a **1 Curs**

Això es coneix com a **relació 1:N** (one-to-many).

---

## 🗄️ Estructura de la Base de Dades

### Taula `courses` (1)
```sql
CREATE TABLE courses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL UNIQUE,           -- Codi únic del curs (DAW, DAM, etc.)
    name TEXT NOT NULL,                  -- Nom complet del curs
    description TEXT,                    -- Descripció opcional
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### Taula `students` (N)
```sql
CREATE TABLE students (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    age INTEGER NOT NULL,
    course_id INTEGER,                   -- CLAU FORANA! Referencia courses(id)
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    -- Constraint de clau forana amb ON DELETE RESTRICT
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE RESTRICT
);
```

### 🔑 Clau Forana (Foreign Key)

La columna `course_id` a la taula `students` és una **clau forana** que referencia `id` de la taula `courses`.

**Beneficis:**
- ✅ **Integritat referencial**: No es poden assignar cursos que no existeixen
- ✅ **Protecció de dades**: No es poden eliminar cursos amb estudiants assignats (`ON DELETE RESTRICT`)
- ✅ **Consistència**: Les dades estan sempre coherents

---

## 🏗️ Estructura de Fitxers Implementats

### Models
- **`models/Course.php`**: CRUD complet per gestionar cursos
  - `getAll()`: Obtenir tots els cursos
  - `getById($id)`: Obtenir un curs per ID
  - `getWithStudentCount()`: Obtenir cursos amb el nombre d'estudiants (**JOIN**)
  - `create($data)`: Crear un nou curs
  - `update($id, $data)`: Actualitzar un curs
  - `delete($id)`: Eliminar un curs (només si no té estudiants)
  - `codeExists($code, $excludeId)`: Validar unicitat del codi
  - `hasStudents($id)`: Comprovar si un curs té estudiants assignats

- **`models/Student.php`** (modificat):
  - Tots els mètodes adaptats per utilitzar `course_id` en lloc de `course` text
  - `getAll()` i `getById()` utilitzen **LEFT JOIN** per obtenir dades del curs

### Controllers
- **`controllers/CourseController.php`**: Gestiona les peticions HTTP per cursos
  - Inclou validació de codi únic
  - Impedeix eliminar cursos amb estudiants assignats
  - Mostra missatges educatius sobre la protecció de dades

- **`controllers/StudentController.php`** (modificat):
  - Validació de `course_id` com a clau forana
  - Injecta la llista de cursos a les vistes `create` i `edit`

### Views
- **`views/courses/index.php`**: Llistat de cursos amb:
  - Columna de nombre d'estudiants per curs (visualització de la relació 1:N)
  - Botó d'eliminar desactivat per cursos amb estudiants
  - Missatge educatiu sobre relacions 1:N

- **`views/courses/create.php`**: Formulari per crear cursos
  - Camps: code (únic), name (obligatori), description (opcional)
  
- **`views/courses/edit.php`**: Formulari per editar cursos

- **`views/students/create.php`** i **`views/students/edit.php`** (modificats):
  - Dropdown dinàmic de cursos carregat des de la base de dades
  - Mostra `code - name` per cada curs
  - Utilitza `course_id` com a valor en lloc de text

---

## 🔄 Exemple de Consulta JOIN

### Model Student::getAll()
```php
$stmt = $this->db->prepare("
    SELECT 
        s.*, 
        c.code AS course_code,
        c.name AS course_name
    FROM students s
    LEFT JOIN courses c ON s.course_id = c.id
    ORDER BY s.id DESC
");
```

Aquesta consulta demostra:
- ✅ **LEFT JOIN**: Inclou estudiants encara que no tinguin curs assignat
- ✅ **Alias de columnes**: `c.code AS course_code` evita conflictes de noms
- ✅ **Relació 1:N**: Cada estudiant obté les dades del seu curs

---

## 🚫 Protecció d'Integritat Referencial

### Model Course::hasStudents()
```php
public function hasStudents($id) {
    $stmt = $this->db->prepare("
        SELECT COUNT(*) as count 
        FROM students 
        WHERE course_id = ?
    ");
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] > 0;
}
```

### Controller CourseController::delete()
```php
if ($this->courseModel->hasStudents($id)) {
    $_SESSION['error'] = "No es pot eliminar aquest curs perquè té estudiants assignats. 
                          Primer reassigna els estudiants a un altre curs.";
    Router::redirect('/courses');
}
```

**Resultat:**
- ✅ No es poden eliminar cursos amb estudiants
- ✅ Missatge clar per a l'usuari
- ✅ Dades protegides contra eliminacions accidentals

---

## 🎨 Visualització de la Relació 1:N

### Vista courses/index.php
Mostra una columna amb el **nombre d'estudiants per curs**:

```php
<td>
    <span class="badge badge-info">
        <?= $course['student_count'] ?> estudiants
    </span>
</td>
```

Botó d'eliminar condicional:
```php
<?php if ($course['student_count'] > 0): ?>
    <button class="btn btn-small btn-danger" disabled title="Aquest curs té estudiants assignats">
        🔒 Protegit
    </button>
<?php else: ?>
    <a href="/courses/delete/<?= $course['id'] ?>" 
       onclick="return confirm('Segur que vols eliminar aquest curs?')">
        🗑️ Eliminar
    </a>
<?php endif; ?>
```

---

## 📝 Rutes Afegides

A `routes/web.php`:

```php
// RUTES DE CURSOS (Relació 1:N amb Estudiants)
$router->get('/courses', 'CourseController@index');
$router->get('/courses/create', 'CourseController@create');
$router->post('/courses/store', 'CourseController@store');
$router->get('/courses/edit/:id', 'CourseController@edit');
$router->post('/courses/update', 'CourseController@update');
$router->get('/courses/delete/:id', 'CourseController@delete');
```

---

## 🧪 Com Provar-ho

1. **Reinicialitzar la base de dades** (ja fet):
   ```bash
   php database/init.php
   ```

2. **Iniciar el servidor**:
   ```bash
   php -S localhost:8000
   ```

3. **Provar les funcionalitats**:
   - Accedir a http://localhost:8000/courses
   - Crear un nou curs
   - Crear estudiants i assignar-los a cursos
   - Intentar eliminar un curs amb estudiants (veure protecció)
   - Editar un estudiant i canviar el seu curs

---

## 💡 Conceptes Clau per als Estudiants

### 1. **Relació 1:N (One-to-Many)**
- Un registre de la taula "1" pot estar relacionat amb molts registres de la taula "N"
- Exemple: Un curs (1) té molts estudiants (N)

### 2. **Clau Forana (Foreign Key)**
- Columna que referencia la clau primària d'una altra taula
- Garanteix que només es poden inserir valors que existeixen a la taula referenciada

### 3. **ON DELETE RESTRICT**
- Impedeix eliminar un registre si té referències a altres taules
- Protegeix la integritat de les dades

### 4. **JOIN en SQL**
- Permet combinar dades de múltiples taules en una sola consulta
- `LEFT JOIN` inclou tots els registres de l'esquerra, encara que no tinguin coincidències

### 5. **Integritat Referencial**
- Conjunt de regles que garanteixen que les relacions entre taules siguin vàlides
- Preveu dades òrfenes (referències a registres que no existeixen)

---

## 📊 Diagrama de Relació

```
┌──────────────────┐         ┌──────────────────┐
│     courses      │ 1     N │    students      │
├──────────────────┤◄────────┤──────────────────┤
│ id (PK)          │         │ id (PK)          │
│ code (UNIQUE)    │         │ name             │
│ name             │         │ email (UNIQUE)   │
│ description      │         │ age              │
│ created_at       │         │ course_id (FK)   │
└──────────────────┘         │ created_at       │
                             └──────────────────┘

Legend:
  PK = Primary Key (Clau Primària)
  FK = Foreign Key (Clau Forana)
  1:N = One-to-Many Relationship
```

---

## ✅ Resum de Modificacions

### Base de dades
- ✅ Nova taula `courses` amb 4 cursos d'exemple (DAW, DAM, ASIX, SMX)
- ✅ Taula `students` modificada amb `course_id` i `FOREIGN KEY`

### Models
- ✅ `Course.php` creat amb tots els mètodes CRUD
- ✅ `Student.php` adaptat per utilitzar relacions

### Controllers
- ✅ `CourseController.php` creat amb protecció d'integritat
- ✅ `StudentController.php` adaptat per validar `course_id`

### Views
- ✅ `views/courses/` (index, create, edit)
- ✅ `views/students/` (create, edit) actualitzats amb dropdown dinàmic

### Routing
- ✅ 6 noves rutes per gestionar cursos
- ✅ Link a "Cursos" afegit al menú de navegació

---

## 🎓 Per a l'Alumnat: Activitats d'Ampliació

1. **Afegir més camps al curs**: durada (hores), tipus (CFGS/CFGM)
2. **Crear una relació N:M**: Assignatures ↔ Estudiants (amb taula intermèdia)
3. **Afegir gràfics**: Mostrar estadístiques d'estudiants per curs
4. **Implementar cerca**: Filtrar estudiants per curs
5. **Afegir paginació**: Dividir els llistats en pàgines

---

**Data d'implementació**: Octubre 2024  
**Autor**: INS Montsià - DAW  
**Propòsit**: Material educatiu per ensenyar relacions en bases de dades
