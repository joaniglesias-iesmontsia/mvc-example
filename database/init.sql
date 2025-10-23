-- Script d'inicialització de la base de dades
-- Crea les taules de cursos, estudiants i professors amb relacions

DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS teachers;
DROP TABLE IF EXISTS courses;

-- Taula de cursos (1:N amb students)
CREATE TABLE courses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Taula d'estudiants
CREATE TABLE students (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    age INTEGER NOT NULL,
    course_id INTEGER NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE RESTRICT
);

-- Taula de professors
CREATE TABLE teachers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    phone TEXT NOT NULL,
    department TEXT NOT NULL,
    specialty TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Dades d'exemple de cursos
INSERT INTO courses (code, name, description) VALUES 
    ('DAW', 'Desenvolupament d''Aplicacions Web', 'CFGS de programació web amb PHP, JavaScript i frameworks moderns'),
    ('DAM', 'Desenvolupament d''Aplicacions Multiplataforma', 'CFGS de programació d''aplicacions per a diferents plataformes'),
    ('ASIX', 'Administració de Sistemes Informàtics en Xarxa', 'CFGS d''administració de xarxes i sistemes'),
    ('SMX', 'Sistemes Microinformàtics i Xarxes', 'CFGM de muntatge i manteniment d''equips');

-- Dades d'exemple d'estudiants (amb course_id)
INSERT INTO students (name, email, age, course_id) VALUES 
    ('Maria García', 'maria.garcia@example.com', 20, 1),
    ('Joan Martínez', 'joan.martinez@example.com', 21, 1),
    ('Laura Sánchez', 'laura.sanchez@example.com', 19, 3),
    ('Pere Rodríguez', 'pere.rodriguez@example.com', 22, 2);

-- Dades d'exemple de professors
INSERT INTO teachers (name, email, phone, department, specialty) VALUES 
    ('Anna Soler Martí', 'anna.soler@iesmontsia.cat', '977501234', 'Informàtica', 'Desenvolupament Web'),
    ('Josep Ferrando Valls', 'josep.ferrando@iesmontsia.cat', '977501235', 'Informàtica', 'Bases de Dades'),
    ('Marta Riba Costa', 'marta.riba@iesmontsia.cat', '977501236', 'Informàtica', 'Programació'),
    ('Carles Pons Serra', 'carles.pons@iesmontsia.cat', '977501237', 'FOL', 'Formació i Orientació Laboral');
