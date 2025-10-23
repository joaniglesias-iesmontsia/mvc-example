-- Script d'inicialització de la base de dades
-- Crea la taula d'estudiants

DROP TABLE IF EXISTS students;

CREATE TABLE students (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    age INTEGER NOT NULL,
    course TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Dades d'exemple
INSERT INTO students (name, email, age, course) VALUES 
    ('Maria García', 'maria.garcia@example.com', 20, 'DAW'),
    ('Joan Martínez', 'joan.martinez@example.com', 21, 'DAW'),
    ('Laura Sánchez', 'laura.sanchez@example.com', 19, 'ASIX'),
    ('Pere Rodríguez', 'pere.rodriguez@example.com', 22, 'DAM');
