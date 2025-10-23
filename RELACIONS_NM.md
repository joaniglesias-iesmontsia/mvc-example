# 👥 Implementació de Relacions N:M - Equips Docents

## 🎯 Objectiu Educatiu

Aquest exemple demostra com implementar relacions **N:M (molts a molts)** en una aplicació MVC amb base de dades relacional. Concretament:

- **1 Curs** pot tenir **N Professors**
- **1 Professor** pot estar a **M Cursos**

Això es coneix com a **relació N:M** (many-to-many).

---

## 🗄️ Estructura de la Base de Dades

### Taula Intermèdia: `teaching_teams`

Per implementar una relació N:M **sempre necessitem una taula intermèdia** que connecta les dues entitats principals.

```sql
CREATE TABLE teaching_teams (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    course_id INTEGER NOT NULL,           -- FK → courses(id)
    teacher_id INTEGER NOT NULL,          -- FK → teachers(id)
    assigned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    -- Claus foranes cap a les dues taules
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE,
    
    -- Constraint d'unicitat: evita duplicats
    UNIQUE(course_id, teacher_id)
);
```

### 🔑 Conceptes Clau

1. **Taula Intermèdia (Junction Table)**:
   - No és una entitat del domini del problema
   - Existeix només per gestionar la relació
   - Conté dues claus foranes (una per cada entitat)

2. **UNIQUE(course_id, teacher_id)**:
   - Evita que un professor s'assigni dues vegades al mateix curs
   - Garanteix la integritat de les dades

3. **ON DELETE CASCADE**:
   - Si s'elimina un curs, s'eliminen automàticament les seves assignacions
   - Si s'elimina un professor, s'eliminen automàticament les seves assignacions
   - Les entitats principals (courses, teachers) no s'afecten entre elles

---

## 📊 Diagrama de la Relació N:M

```
┌──────────────────┐         ┌──────────────────────┐         ┌──────────────────┐
│     courses      │ 1     N │   teaching_teams     │ M     1 │    teachers      │
├──────────────────┤◄────────┤──────────────────────┤────────►├──────────────────┤
│ id (PK)          │         │ id (PK)              │         │ id (PK)          │
│ code             │         │ course_id (FK)       │         │ name             │
│ name             │         │ teacher_id (FK)      │         │ email            │
│ description      │         │ assigned_at          │         │ phone            │
│ created_at       │         │ UNIQUE(c_id, t_id)   │         │ department       │
└──────────────────┘         └──────────────────────┘         │ specialty        │
                                                               │ created_at       │
                                                               └──────────────────┘

Legend:
  PK = Primary Key
  FK = Foreign Key
  N:M = Many-to-Many Relationship
```

---

## 🏗️ Estructura de Fitxers Implementats

### Model
- **`models/TeachingTeam.php`**: Gestiona la relació N:M
  - `getTeachersByCourse($courseId)`: Obtenir professors d'un curs (JOIN)
  - `getCoursesByTeacher($teacherId)`: Obtenir cursos d'un professor (JOIN invers)
  - `getAllTeamsWithCount()`: Resum amb nombres de professors per curs
  - `getAvailableTeachers($courseId)`: Professors NO assignats a un curs
  - `assignTeacher($courseId, $teacherId)`: Crear assignació
  - `removeTeacher($courseId, $teacherId)`: Eliminar assignació
  - `isTeacherAssigned()`: Validar si ja existeix l'assignació

### Controller
- **`controllers/TeachingTeamController.php`**: Gestiona les peticions HTTP
  - `index()`: Llistat de tots els equips docents
  - `show($courseId)`: Veure professors d'un curs específic
  - `assign($courseId)`: Formulari per afegir professor
  - `store()`: Processar assignació
  - `remove()`: Desassignar professor
  - `byTeacher($teacherId)`: Veure cursos d'un professor

### Vistes
- **`views/teaching-teams/index.php`**: Llistat general amb estadístiques
- **`views/teaching-teams/show.php`**: Detall de l'equip docent d'un curs
- **`views/teaching-teams/assign.php`**: Formulari d'assignació
- **`views/teaching-teams/by-teacher.php`**: Cursos d'un professor

### Modificacions a Models Existents
- **`models/Course.php`**: Afegit `teacher_count` al recompte
- **`models/Teacher.php`**: Afegit `getAllWithCourseCount()` per N:M
- **`controllers/TeacherController.php`**: Usa el nou mètode amb recompte

### Modificacions a Vistes Existents
- **`views/courses/index.php`**: Mostra nombre de professors i link "Veure equip"
- **`views/teachers/index.php`**: Mostra nombre de cursos i link "Veure cursos"

---

## 🔄 Exemples de Consultes N:M

### 1. Obtenir professors d'un curs (amb INNER JOIN)

```php
// Model: TeachingTeam::getTeachersByCourse($courseId)
SELECT 
    t.*,
    tt.assigned_at,
    tt.id as assignment_id
FROM teachers t
INNER JOIN teaching_teams tt ON t.id = tt.teacher_id
WHERE tt.course_id = ?
ORDER BY t.name ASC
```

**Explicació:**
- `INNER JOIN` només retorna professors que tenen assignació
- Obtenim dades del professor (`t.*`) més dades de l'assignació (`tt.assigned_at`)
- Filtrem per un curs concret (`WHERE tt.course_id = ?`)

### 2. Obtenir cursos d'un professor (perspectiva inversa)

```php
// Model: TeachingTeam::getCoursesByTeacher($teacherId)
SELECT 
    c.*,
    tt.assigned_at,
    tt.id as assignment_id
FROM courses c
INNER JOIN teaching_teams tt ON c.id = tt.course_id
WHERE tt.teacher_id = ?
ORDER BY c.code ASC
```

**Explicació:**
- És la **mateixa relació N:M** però des de l'altra perspectiva
- Útil per mostrar els cursos on està assignat un professor

### 3. Resum de tots els equips amb recompte

```php
// Model: TeachingTeam::getAllTeamsWithCount()
SELECT 
    c.id,
    c.code,
    c.name,
    c.description,
    COUNT(tt.teacher_id) as teacher_count
FROM courses c
LEFT JOIN teaching_teams tt ON c.id = tt.course_id
GROUP BY c.id
ORDER BY c.code ASC
```

**Explicació:**
- `LEFT JOIN` inclou cursos encara que no tinguin professors
- `COUNT()` amb `GROUP BY` dona el nombre de professors per curs
- Útil per vistes de resum

### 4. Obtenir professors disponibles (NO assignats)

```php
// Model: TeachingTeam::getAvailableTeachers($courseId)
SELECT t.*
FROM teachers t
WHERE t.id NOT IN (
    SELECT teacher_id 
    FROM teaching_teams 
    WHERE course_id = ?
)
ORDER BY t.name ASC
```

**Explicació:**
- Subconsulta per obtenir IDs de professors ja assignats
- `NOT IN` filtra per excloure'ls
- Útil per formularis de selecció

---

## ✅ Beneficis de les Relacions N:M

### 1. **Flexibilitat**
- ✅ Afegir/eliminar assignacions sense afectar les entitats principals
- ✅ Un professor pot estar a qualsevol nombre de cursos
- ✅ Un curs pot tenir qualsevol nombre de professors

### 2. **Escalabilitat**
- ✅ La taula intermèdia creix independentment
- ✅ Fàcil afegir metadades (data d'assignació, rol, etc.)

### 3. **Integritat**
- ✅ Les claus foranes garanteixen que només existeixen relacions vàlides
- ✅ `UNIQUE` constraint evita duplicats
- ✅ `ON DELETE CASCADE` manté la consistència

### 4. **Consultes Eficients**
- ✅ Els JOIN permeten obtenir dades combinades en una sola consulta
- ✅ Els índexs sobre les claus foranes milloren el rendiment

---

## 🎓 Comparació: 1:N vs N:M

### Relació 1:N (Students ↔ Courses)
```
students table:
  course_id INTEGER  ← Clau forana directa a la taula

✅ Un estudiant només pot estar en 1 curs
✅ No cal taula intermèdia
✅ Més senzill d'implementar
```

### Relació N:M (Courses ↔ Teachers)
```
teaching_teams table:
  course_id INTEGER   ← Clau forana a courses
  teacher_id INTEGER  ← Clau forana a teachers

✅ Un professor pot estar a molts cursos
✅ Un curs pot tenir molts professors
✅ Cal taula intermèdia
✅ Més flexible però més complex
```

---

## 🧪 Com Provar-ho

1. **Accedir a l'aplicació**: http://localhost:8000

2. **Navegar a "Equips Docents"**:
   - Veure llistat de cursos amb nombre de professors
   - Accedir a un equip docent específic

3. **Assignar professors a cursos**:
   - Click "Afegir Professor" a un curs
   - Seleccionar un professor disponible
   - Veure com apareix a la llista

4. **Veure des de la perspectiva del professor**:
   - Anar a "Professors"
   - Click "Veure cursos" d'un professor
   - Veure tots els cursos on està assignat

5. **Desassignar**:
   - Click "Desassignar" a un professor
   - Veure com desapareix de l'equip docent
   - Les entitats (curs i professor) no s'afecten

---

## 📝 Rutes Implementades

```php
// Llistat general
GET  /teaching-teams              → TeachingTeamController@index

// Veure equip d'un curs
GET  /teaching-teams/show?course_id=:id    → TeachingTeamController@show

// Assignar professor
GET  /teaching-teams/assign?course_id=:id  → TeachingTeamController@assign
POST /teaching-teams/store                 → TeachingTeamController@store

// Desassignar professor
GET  /teaching-teams/remove?course_id=:id&teacher_id=:id → remove

// Veure cursos d'un professor
GET  /teaching-teams/by-teacher?teacher_id=:id → byTeacher
```

---

## 💡 Conceptes Clau per als Estudiants

### 1. **Taula Intermèdia (Junction Table)**
- Existeix només per gestionar la relació
- Conté les claus foranes de les dues entitats
- Pot contenir metadades addicionals (data, estat, rol, etc.)

### 2. **Bidireccionalitat**
- La relació es pot consultar des de qualsevol direcció
- `getTeachersByCourse()` i `getCoursesByTeacher()` són la mateixa relació

### 3. **Integritat Referencial**
- Les claus foranes garanteixen que només existeixen relacions vàlides
- `ON DELETE CASCADE` elimina automàticament les assignacions òrfenes

### 4. **UNIQUE Constraint**
- Evita duplicats a la taula intermèdia
- `UNIQUE(course_id, teacher_id)` garanteix que un professor només s'assigna una vegada

### 5. **JOIN Types**
- `INNER JOIN`: Només retorna registres amb coincidències
- `LEFT JOIN`: Inclou tots els registres de l'esquerra, amb o sense coincidències

---

## 🚀 Activitats d'Ampliació per als Estudiants

1. **Afegir metadades**:
   - Camp `role` a `teaching_teams` (titular, substitut, etc.)
   - Camp `hours_per_week` per hores lectives

2. **Implementar una tercera relació N:M**:
   - Assignatures ↔ Estudiants (matrícules)
   - Taula `enrollments` amb `student_id` i `subject_id`

3. **Afegir validacions**:
   - Limitar el nombre màxim de professors per curs
   - Limitar el nombre màxim de cursos per professor

4. **Estadístiques avançades**:
   - Professor amb més cursos assignats
   - Curs amb més professors
   - Gràfics de distribució

5. **Històric d'assignacions**:
   - En lloc d'eliminar, marcar com `inactive`
   - Mantenir històric d'assignacions passades

---

## 📚 Recursos Addicionals

### Documentació SQL
- [SQLite Foreign Keys](https://www.sqlite.org/foreignkeys.html)
- [SQLite UNIQUE Constraints](https://www.sqlite.org/lang_createtable.html#uniqueconst)

### Patrons de Disseny
- [Junction Table Pattern](https://en.wikipedia.org/wiki/Associative_entity)
- [Many-to-Many Relationships](https://en.wikipedia.org/wiki/Many-to-many_(data_model))

---

**Data d'implementació**: Octubre 2024  
**Autor**: INS Montsià - DAW  
**Propòsit**: Material educatiu per ensenyar relacions N:M en bases de dades relacionals
