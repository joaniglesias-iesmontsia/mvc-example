# 🔀 Sistema de Rutes Implementat

## 📋 Resum de Canvis

S'ha implementat un **sistema de rutes centralitzat** similar a Laravel, que millora significativament l'organització i escalabilitat del projecte.

---

## ✅ Fitxers Nous Creats

### 1. **Sistema de Rutes** (`core/Router.php`)
Classe que gestiona tot el sistema de routing:
- Suport per GET i POST
- Paràmetres dinàmics (:id, :slug, etc.)
- Matching de rutes amb expressions regulars
- Gestió d'errors 404
- Mètode `Router::redirect()` per redireccions netes

### 2. **Definició de Rutes** (`routes/web.php`)
Fitxer centralitzat amb **totes les rutes** de l'aplicació:
- Rutes d'estudiants (6 rutes)
- Rutes de professors (6 rutes)
- Comentaris educatius explicant com afegir noves rutes
- Notes sobre l'ordre de les rutes

### 3. **Configuració Apache** (`.htaccess`)
Redirigeix totes les peticions a index.php:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

---

## 🔄 Fitxers Modificats

### 1. **index.php** - Nou Front Controller
**ABANS:**
```php
$controller = new StudentController();
$action = $_GET['action'] ?? 'index';
switch ($action) { ... }
```

**ARA:**
```php
$router = new Router();
require_once 'routes/web.php';
$router->dispatch();
```

### 2. **StudentController.php**
- Afegit `require_once Router.php`
- Mètodes actualitzats per usar `Router::redirect()`
- Suport per paràmetres de ruta: `edit($id)`, `delete($id)`

### 3. **TeacherController.php**
- Mateix canvis que StudentController
- Totes les redireccions ara usen `Router::redirect()`

### 4. **Totes les Vistes** (students/* i teachers/*)
**ABANS:**
```html
<a href="index.php?action=edit&id=5">Editar</a>
<form action="index.php?action=store" method="POST">
```

**ARA:**
```html
<a href="/students/edit/5">Editar</a>
<form action="/students/store" method="POST">
```

### 5. **views/layouts/header.php**
Navegació actualitzada amb URLs netes:
```html
<a href="/students">📚 Estudiants</a>
<a href="/teachers">👨‍🏫 Professors</a>
```

### 6. **README.md**
- Nova secció "Sistema de Rutes" amb explicació completa
- Diagrames actualitzats incloent el Router
- Exemples de com afegir noves rutes
- Notes sobre l'ordre de les rutes

---

## 🎯 Avantatges del Canvi

### ✅ URLs Netes i Semàntiques
```
❌ ABANS: index.php?action=edit&id=5
✅ ARA:   /students/edit/5
```

### ✅ Centralització de Rutes
Totes les rutes en un sol fitxer (`routes/web.php`), fàcil de:
- Veure totes les URLs de l'aplicació
- Mantenir i actualitzar
- Entendre l'estructura

### ✅ Escalabilitat
Afegir un nou CRUD és molt més simple:
```php
// Només cal afegir 6 línies a routes/web.php
$router->get('/courses', 'CourseController@index');
$router->get('/courses/create', 'CourseController@create');
// etc.
```

### ✅ Preparació per a Frameworks Professionals
Els estudiants aprenen el mateix patró que usen:
- Laravel
- Symfony
- Slim
- CodeIgniter 4

### ✅ Paràmetres Dinàmics
```php
$router->get('/students/edit/:id', 'StudentController@edit');
// La URL /students/edit/5 passa automàticament 5 com a paràmetre
```

---

## 📊 Comparativa: Abans vs Ara

| Aspecte | Abans | Ara |
|---------|-------|-----|
| **Punt d'entrada** | `index.php`, `teachers.php` | Només `index.php` |
| **Rutes** | Disperses en cada fitxer | Centralitzades en `routes/web.php` |
| **URLs** | `?action=edit&id=5` | `/students/edit/5` |
| **Redireccions** | `header('Location: ...')` | `Router::redirect('/...')` |
| **Afegir CRUD** | Crear nou fitxer + switch | Afegir línies a routes |
| **Llegibilitat** | ⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Escalabilitat** | ⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Professional** | ⭐⭐ | ⭐⭐⭐⭐⭐ |

---

## 🎓 Per als Estudiants

### Com Afegir un Nou CRUD (exemple: Cursos)

#### 1️⃣ Afegeix les rutes (`routes/web.php`):
```php
$router->get('/courses', 'CourseController@index');
$router->get('/courses/create', 'CourseController@create');
$router->post('/courses/store', 'CourseController@store');
$router->get('/courses/edit/:id', 'CourseController@edit');
$router->post('/courses/update', 'CourseController@update');
$router->get('/courses/delete/:id', 'CourseController@delete');
```

#### 2️⃣ Crea el Model (`models/Course.php`)
Copia `Student.php` i adapta els camps

#### 3️⃣ Crea el Controller (`controllers/CourseController.php`)
Copia `StudentController.php` i adapta

#### 4️⃣ Crea les Vistes (`views/courses/`)
- `index.php` - Llistat
- `create.php` - Formulari creació
- `edit.php` - Formulari edició

**I ja està!** El router ho gestiona tot automàticament. 🚀

---

## 🔧 Com Funciona Internament

### Exemple: GET /students/edit/5

1. **Petició HTTP**: L'usuari accedeix a `/students/edit/5`
2. **.htaccess**: Redirigeix a `index.php`
3. **index.php**: Carrega el Router i les rutes
4. **Router::dispatch()**: 
   - Analitza la URL: `/students/edit/5`
   - Busca la ruta: `/students/edit/:id`
   - Extreu paràmetres: `$id = 5`
   - Crida: `StudentController@edit` amb paràmetre `5`
5. **Controller**: `$controller->edit(5)`
6. **Model**: Obté l'estudiant amb ID 5
7. **Vista**: Mostra el formulari d'edició

---

## ⚠️ Notes Importants

### Ordre de les Rutes
```php
✅ CORRECTE:
$router->get('/students/create', 'StudentController@create');
$router->get('/students/:id', 'StudentController@show');

❌ MALAMENT:
$router->get('/students/:id', 'StudentController@show');
$router->get('/students/create', 'StudentController@create');
// "create" seria capturat com a ID!
```

### Compatibilitat
- Funciona amb Apache (mitjançant .htaccess)
- Funciona amb el servidor integrat de PHP (php -S)
- No requereix Nginx ni configuracions complexes

---

## 📚 Recursos per Aprendre Més

- **Laravel Routing**: https://laravel.com/docs/routing
- **PHP The Right Way - Routing**: https://phptherightway.com/#routing
- **RESTful URLs**: https://restfulapi.net/resource-naming/

---

**Data**: 23 d'octubre de 2025  
**Versió**: 2.0 - Sistema de Rutes Implementat  
**Autor**: INS Montsià - CFGS DAW
