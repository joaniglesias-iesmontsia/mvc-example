#!/bin/bash
# Test script per verificar la implementació de les relacions N:M

echo "🧪 Tests de la Implementació de Relacions N:M"
echo "=============================================="
echo ""

# Colors per a output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
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
TABLES=$(sqlite3 database/students.db ".tables")

for table in "courses" "students" "teachers" "teaching_teams"; do
    if echo "$TABLES" | grep -q "$table"; then
        echo -e "${GREEN}✅${NC} Taula '$table' existeix"
    else
        echo -e "${RED}❌${NC} Taula '$table' no existeix"
    fi
done

echo ""
echo "🔑 3. Verificant claus foranes de teaching_teams..."
FOREIGN_KEYS=$(sqlite3 database/students.db "PRAGMA foreign_key_list(teaching_teams);")

if echo "$FOREIGN_KEYS" | grep -q "courses"; then
    echo -e "${GREEN}✅ Clau forana course_id → courses(id) configurada${NC}"
else
    echo -e "${RED}❌ Clau forana course_id no trobada${NC}"
fi

if echo "$FOREIGN_KEYS" | grep -q "teachers"; then
    echo -e "${GREEN}✅ Clau forana teacher_id → teachers(id) configurada${NC}"
else
    echo -e "${RED}❌ Clau forana teacher_id no trobada${NC}"
fi

echo ""
echo "🔍 4. Verificant constraint UNIQUE..."
SCHEMA=$(sqlite3 database/students.db ".schema teaching_teams")
if echo "$SCHEMA" | grep -q "UNIQUE"; then
    echo -e "${GREEN}✅ Constraint UNIQUE(course_id, teacher_id) trobat${NC}"
else
    echo -e "${YELLOW}⚠️  Constraint UNIQUE no trobat explícitament${NC}"
fi

echo ""
echo "👨‍🏫 5. Comprovant assignacions N:M..."
ASSIGNMENT_COUNT=$(sqlite3 database/students.db "SELECT COUNT(*) FROM teaching_teams;")
echo -e "   Assignacions trobades: ${YELLOW}$ASSIGNMENT_COUNT${NC}"

if [ "$ASSIGNMENT_COUNT" -gt 0 ]; then
    echo -e "${GREEN}✅ Hi ha assignacions a la base de dades${NC}"
    echo ""
    echo "   Distribució d'assignacions per curs:"
    sqlite3 database/students.db "
        SELECT 
            c.code,
            c.name,
            COUNT(tt.teacher_id) as num_professors
        FROM courses c
        LEFT JOIN teaching_teams tt ON c.id = tt.course_id
        GROUP BY c.id, c.code, c.name
        ORDER BY num_professors DESC
    " | while read line; do
        echo -e "      ${BLUE}•${NC} $line"
    done
    
    echo ""
    echo "   Distribució d'assignacions per professor:"
    sqlite3 database/students.db "
        SELECT 
            t.name,
            COUNT(tt.course_id) as num_cursos
        FROM teachers t
        LEFT JOIN teaching_teams tt ON t.id = tt.teacher_id
        GROUP BY t.id, t.name
        ORDER BY num_cursos DESC
    " | while read line; do
        echo -e "      ${BLUE}•${NC} $line"
    done
else
    echo -e "${RED}❌ No hi ha assignacions a la base de dades${NC}"
fi

echo ""
echo "📁 6. Verificant fitxers del sistema..."
FILES=(
    "models/TeachingTeam.php"
    "controllers/TeachingTeamController.php"
    "views/teaching-teams/index.php"
    "views/teaching-teams/show.php"
    "views/teaching-teams/assign.php"
    "views/teaching-teams/by-teacher.php"
    "RELACIONS_NM.md"
)

for file in "${FILES[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✅${NC} $file"
    else
        echo -e "${RED}❌${NC} $file ${RED}(no trobat)${NC}"
    fi
done

echo ""
echo "🔄 7. Verificant consultes N:M amb JOIN..."
echo "   Provant query: Professors del curs DAW"
DAW_TEACHERS=$(sqlite3 database/students.db "
    SELECT t.name
    FROM teachers t
    INNER JOIN teaching_teams tt ON t.id = tt.teacher_id
    INNER JOIN courses c ON c.id = tt.course_id
    WHERE c.code = 'DAW'
" | wc -l)
echo -e "      ${YELLOW}$DAW_TEACHERS${NC} professors assignats al curs DAW"

echo ""
echo "   Provant query inversa: Cursos del primer professor"
TEACHER_COURSES=$(sqlite3 database/students.db "
    SELECT c.code
    FROM courses c
    INNER JOIN teaching_teams tt ON c.id = tt.course_id
    WHERE tt.teacher_id = 1
" | wc -l)
echo -e "      ${YELLOW}$TEACHER_COURSES${NC} cursos assignats al primer professor"

echo ""
echo "🌐 8. Comprovant servidor web..."
if curl -s "$BASE_URL" > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Servidor accessible a $BASE_URL${NC}"
else
    echo -e "${RED}❌ Servidor no accessible${NC}"
    echo -e "${YELLOW}   Pots iniciar-lo amb: php -S localhost:8000${NC}"
fi

echo ""
echo "📈 9. Estadístiques globals N:M..."
STATS=$(sqlite3 database/students.db "
    SELECT 
        COUNT(DISTINCT course_id) as total_courses_with_teachers,
        COUNT(DISTINCT teacher_id) as total_teachers_assigned,
        COUNT(*) as total_assignments,
        ROUND(CAST(COUNT(*) AS FLOAT) / COUNT(DISTINCT course_id), 2) as avg_teachers_per_course
    FROM teaching_teams
")
echo "$STATS" | awk -F'|' '{
    print "   • Cursos amb professors: " $1
    print "   • Professors assignats: " $2
    print "   • Total assignacions: " $3
    print "   • Mitjana professors/curs: " $4
}'

echo ""
echo "✨ 10. Verificant casos especials..."

# Comprovar professors sense cursos
TEACHERS_WITHOUT_COURSES=$(sqlite3 database/students.db "
    SELECT COUNT(*)
    FROM teachers t
    LEFT JOIN teaching_teams tt ON t.id = tt.teacher_id
    WHERE tt.teacher_id IS NULL
")
if [ "$TEACHERS_WITHOUT_COURSES" -gt 0 ]; then
    echo -e "   ${YELLOW}⚠️  Hi ha $TEACHERS_WITHOUT_COURSES professor(s) sense cursos assignats${NC}"
else
    echo -e "   ${GREEN}✅ Tots els professors tenen almenys un curs assignat${NC}"
fi

# Comprovar cursos sense professors
COURSES_WITHOUT_TEACHERS=$(sqlite3 database/students.db "
    SELECT COUNT(*)
    FROM courses c
    LEFT JOIN teaching_teams tt ON c.id = tt.course_id
    WHERE tt.course_id IS NULL
")
if [ "$COURSES_WITHOUT_TEACHERS" -gt 0 ]; then
    echo -e "   ${YELLOW}⚠️  Hi ha $COURSES_WITHOUT_TEACHERS curs(os) sense professors assignats${NC}"
else
    echo -e "   ${GREEN}✅ Tots els cursos tenen almenys un professor assignat${NC}"
fi

echo ""
echo "=============================================="
echo -e "${GREEN}✅ Tests completats!${NC}"
echo ""
echo "📖 Per testar manualment:"
echo "   1. Accedeix a $BASE_URL"
echo "   2. Navega a 'Equips Docents' (👥)"
echo "   3. Prova d'assignar/desassignar professors a cursos"
echo "   4. Veure cursos des de la perspectiva del professor"
echo "   5. Comprova que no es poden crear duplicats"
echo ""
echo "📚 Documentació:"
echo "   • README.md - Visió general del projecte"
echo "   • RELACIONS_1N.md - Explicació de relacions 1:N"
echo "   • RELACIONS_NM.md - Explicació de relacions N:M"
echo ""
