-- Script d'inicialització de la base de dades
-- Crea les taules d'estudiants i professors

DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS teachers;

-- Taula d'estudiants
CREATE TABLE students (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    age INTEGER NOT NULL,
    course TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
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

-- Dades d'exemple d'estudiants
INSERT INTO students (name, email, age, course) VALUES 
    ('Maria García', 'maria.garcia@example.com', 20, 'DAW'),
    ('Joan Martínez', 'joan.martinez@example.com', 21, 'DAW'),
    ('Laura Sánchez', 'laura.sanchez@example.com', 19, 'ASIX'),
    ('Pere Rodríguez', 'pere.rodriguez@example.com', 22, 'DAM');

-- Dades d'exemple de professors
INSERT INTO teachers (name, email, phone, department, specialty) VALUES 
    ('Anna Soler Martí', 'anna.soler@iesmontsia.cat', '977501234', 'Informàtica', 'Desenvolupament Web'),
    ('Josep Ferrando Valls', 'josep.ferrando@iesmontsia.cat', '977501235', 'Informàtica', 'Bases de Dades'),
    ('Marta Riba Costa', 'marta.riba@iesmontsia.cat', '977501236', 'Informàtica', 'Programació'),
    ('Carles Pons Serra', 'carles.pons@iesmontsia.cat', '977501237', 'FOL', 'Formació i Orientació Laboral');
