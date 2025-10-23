# 📝 Resum de Canvis: CRUD de Professors

## ✅ Fitxers Creats

### 1. **Model** (`models/Teacher.php`)
- Classe `Teacher` amb propietats: id, name, email, phone, department, specialty, created_at
- Mètodes CRUD: `getAll()`, `getById()`, `create()`, `update()`, `delete()`
- Validació: `emailExists()` per evitar duplicats

### 2. **Controller** (`controllers/TeacherController.php`)
- Classe `TeacherController` amb tots els mètodes CRUD
- Validacions personalitzades per professors:
  - Nom mínim 3 caràcters
  - Email vàlid i únic
  - Telèfon de 9 dígits
  - Departament obligatori
  - Especialitat obligatòria

### 3. **Vistes** (`views/teachers/`)
- `index.php` - Llistat de professors amb taula
- `create.php` - Formulari per crear nou professor
- `edit.php` - Formulari per editar professor existent

### 4. **Front Controller** (`teachers.php`)
- Punt d'entrada per a totes les operacions de professors
- Router amb les accions: index, create, store, edit, update, delete

---

## 🔄 Fitxers Modificats

### 1. **Header** (`views/layouts/header.php`)
- Afegida navegació amb enllaços a Estudiants i Professors
- Botons per crear nous estudiants i professors
- Títol canviat a "Gestió Acadèmica"

### 2. **Base de Dades** (`database/init.sql`)
- Afegida taula `teachers` amb estructura:
  ```sql
  CREATE TABLE teachers (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      name TEXT NOT NULL,
      email TEXT NOT NULL UNIQUE,
      phone TEXT NOT NULL,
      department TEXT NOT NULL,
      specialty TEXT NOT NULL,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP
  );
  ```
- Afegides 4 dades d'exemple de professors

### 3. **README.md**
- Actualitzada estructura del projecte
- Afegida comparativa Estudiants vs Professors
- Explicat el concepte de patró com a plantilla reutilitzable
- Actualitzades les funcionalitats implementades
- Nous exercicis relacionats amb professors

---

## 🎯 Estructura Final del Projecte

```
mvc-example/
├── index.php                      # Estudiants
├── teachers.php                   # Professors ✨ NOU
├── models/
│   ├── Student.php
│   └── Teacher.php                # ✨ NOU
├── controllers/
│   ├── StudentController.php
│   └── TeacherController.php      # ✨ NOU
└── views/
    ├── students/
    │   ├── index.php
    │   ├── create.php
    │   └── edit.php
    └── teachers/                   # ✨ NOU
        ├── index.php
        ├── create.php
        └── edit.php
```

---

## 🚀 Com Provar els Canvis

### 1. Reinicialitzar la Base de Dades
```bash
php database/init.php
```

### 2. Iniciar el Servidor
```bash
php -S localhost:8000
```

### 3. Accedir a l'Aplicació
- **Estudiants**: http://localhost:8000
- **Professors**: http://localhost:8000/teachers.php

---

## 📊 Diferències entre Student i Teacher

| Característica | Student | Teacher |
|---------------|---------|---------|
| Camp principal | `age` (edat) | `phone` (telèfon) |
| Camp secundari | `course` (curs) | `department` (departament) |
| Camp terciari | - | `specialty` (especialitat) |
| Validació edat | 16-99 anys | - |
| Validació telèfon | - | 9 dígits |
| Emoji | 📚 | 👨‍🏫 |

---

## 💡 Lliçons Aprendes

### ✅ **Reutilització del Patró**
El patró MVC és com una plantilla: un cop definit per a estudiants, crear professors és molt ràpid.

### ✅ **Consistència**
Tots els CRUDs funcionen igual, facilitant el manteniment.

### ✅ **Escalabilitat**
Afegir nous models (cursos, assignatures, etc.) és senzill seguint el mateix patró.

### ✅ **Separació de Responsabilitats**
Cada fitxer té una funció clara i no depèn dels altres.

---

## 🎓 Pròxims Passos Suggerits

1. **Relacions entre Models**: Assignar professors a cursos
2. **Cerca i Filtres**: Cercar professors per departament
3. **Paginació**: Mostrar 10 professors per pàgina
4. **Estadístiques**: Comptar professors per departament
5. **Exportació**: Generar PDF o Excel amb el llistat

---

**Data**: 23 d'octubre de 2025  
**Autor**: INS Montsià - CFGS DAW
