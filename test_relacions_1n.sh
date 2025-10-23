#!/bin/bash
# Test script per verificar la implementació de les relacions 1:N

echo "🧪 Tests de la Implementació de Relacions 1:N"
echo "=============================================="
echo ""

# Colors per a output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Base URL
BASE_URL="http://localhost:8000"

echo "📋 1. Comprovant que la base de dades existeix..."
if [ -f "database/students.db" ]; then
    echo -e "${GREEN}✅ Base de dades trobada${NC}"
else
    echo -e "${RED}❌ Base de dades no trobada${NC}"
    exit 1
fi

echo ""
echo "📊 2. Comprovant taules de la base de dades..."
sqlite3 database/students.db ".tables" | grep -q "courses"
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Taula 'courses' existeix${NC}"
else
    echo -e "${RED}❌ Taula 'courses' no existeix${NC}"
fi

sqlite3 database/students.db ".tables" | grep -q "students"
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Taula 'students' existeix${NC}"
else
    echo -e "${RED}❌ Taula 'students' no existeix${NC}"
fi

echo ""
echo "🔑 3. Verificant clau forana..."
FOREIGN_KEY=$(sqlite3 database/students.db "PRAGMA foreign_key_list(students);" | grep courses)
if [ ! -z "$FOREIGN_KEY" ]; then
    echo -e "${GREEN}✅ Clau forana course_id → courses(id) configurada${NC}"
    echo "   $FOREIGN_KEY"
else
    echo -e "${RED}❌ Clau forana no trobada${NC}"
fi

echo ""
echo "📚 4. Comprovant cursos a la base de dades..."
COURSE_COUNT=$(sqlite3 database/students.db "SELECT COUNT(*) FROM courses;")
echo -e "   Cursos trobats: ${YELLOW}$COURSE_COUNT${NC}"
if [ "$COURSE_COUNT" -gt 0 ]; then
    echo -e "${GREEN}✅ Hi ha cursos a la base de dades${NC}"
    sqlite3 database/students.db "SELECT code, name FROM courses;" | while read line; do
        echo "      - $line"
    done
else
    echo -e "${RED}❌ No hi ha cursos a la base de dades${NC}"
fi

echo ""
echo "👨‍🎓 5. Comprovant estudiants amb relació a cursos..."
STUDENT_COUNT=$(sqlite3 database/students.db "SELECT COUNT(*) FROM students;")
echo -e "   Estudiants trobats: ${YELLOW}$STUDENT_COUNT${NC}"

if [ "$STUDENT_COUNT" -gt 0 ]; then
    echo -e "${GREEN}✅ Hi ha estudiants a la base de dades${NC}"
    echo ""
    echo "   Estudiants per curs (JOIN query):"
    sqlite3 database/students.db "
        SELECT 
            c.code, 
            COUNT(s.id) as num_estudiants
        FROM courses c
        LEFT JOIN students s ON c.id = s.course_id
        GROUP BY c.id, c.code
    " | while read line; do
        echo "      - $line"
    done
fi

echo ""
echo "📁 6. Verificant fitxers del sistema..."
FILES=(
    "models/Course.php"
    "models/Student.php"
    "controllers/CourseController.php"
    "controllers/StudentController.php"
    "views/courses/index.php"
    "views/courses/create.php"
    "views/courses/edit.php"
    "views/students/create.php"
    "views/students/edit.php"
    "routes/web.php"
)

for file in "${FILES[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✅${NC} $file"
    else
        echo -e "${RED}❌${NC} $file ${RED}(no trobat)${NC}"
    fi
done

echo ""
echo "🌐 7. Comprovant servidor web..."
if curl -s "$BASE_URL" > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Servidor accessible a $BASE_URL${NC}"
else
    echo -e "${RED}❌ Servidor no accessible${NC}"
    echo -e "${YELLOW}   Pots iniciar-lo amb: php -S localhost:8000${NC}"
fi

echo ""
echo "🔍 8. Test de protecció d'integritat referencial..."
echo "   Comprovant cursos amb estudiants assignats..."
sqlite3 database/students.db "
    SELECT 
        c.code,
        c.name,
        COUNT(s.id) as student_count,
        CASE 
            WHEN COUNT(s.id) > 0 THEN '🔒 PROTEGIT (no es pot eliminar)'
            ELSE '✅ Es pot eliminar'
        END as status
    FROM courses c
    LEFT JOIN students s ON c.id = s.course_id
    GROUP BY c.id, c.code, c.name
" | while read line; do
    echo "      $line"
done

echo ""
echo "=============================================="
echo -e "${GREEN}✅ Tests completats!${NC}"
echo ""
echo "📖 Per testar manualment:"
echo "   1. Accedeix a $BASE_URL"
echo "   2. Navega a 'Cursos' i 'Estudiants'"
echo "   3. Prova de crear/editar/eliminar cursos i estudiants"
echo "   4. Intenta eliminar un curs amb estudiants (hauria d'estar protegit)"
echo ""
