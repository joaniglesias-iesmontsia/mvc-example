-- Script d'inicialització de la base de dades
-- Crea les taules de cursos, estudiants i professors amb relacions
-- Inclou taula intermèdia teaching_teams per gestionar relacions N:M

DROP TABLE IF EXISTS teaching_teams;
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

-- ============================================
-- TAULA INTERMÈDIA per RELACIÓ N:M
-- ============================================
-- Taula teaching_teams (Equips Docents)
-- Implementa la relació MOLTS A MOLTS entre courses i teachers
-- Un curs pot tenir molts professors, i un professor pot estar a molts cursos
CREATE TABLE teaching_teams (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    course_id INTEGER NOT NULL,
    teacher_id INTEGER NOT NULL,
    assigned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    -- Claus foranes cap a courses i teachers
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE,
    -- Evitar duplicats: un professor només pot estar assignat una vegada a cada curs
    UNIQUE(course_id, teacher_id)
);

-- Dades d'exemple d'equips docents (assignacions N:M)
-- DAW: Anna (Web), Josep (BD), Marta (Prog), Carles (FOL)
INSERT INTO teaching_teams (course_id, teacher_id) VALUES 
    (1, 1),  -- DAW - Anna Soler (Desenvolupament Web)
    (1, 2),  -- DAW - Josep Ferrando (Bases de Dades)
    (1, 3),  -- DAW - Marta Riba (Programació)
    (1, 4),  -- DAW - Carles Pons (FOL)
    (2, 2),  -- DAM - Josep Ferrando (Bases de Dades)
    (2, 3),  -- DAM - Marta Riba (Programació)
    (2, 4),  -- DAM - Carles Pons (FOL)
    (3, 2),  -- ASIX - Josep Ferrando (Bases de Dades)
    (3, 4),  -- ASIX - Carles Pons (FOL)
    (4, 4);  -- SMX - Carles Pons (FOL)
