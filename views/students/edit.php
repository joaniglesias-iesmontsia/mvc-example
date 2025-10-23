<?php 
$title = "Editar Estudiant";
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="page-header">
    <h2>✏️ Editar Estudiant</h2>
    <a href="/students" class="btn btn-secondary">⬅️ Tornar</a>
</div>

<div class="form-container">
    <form action="/students/update" method="POST" class="form">
        <input type="hidden" name="id" value="<?= htmlspecialchars($student['id']) ?>">
        
        <div class="form-group">
            <label for="name">Nom complet *</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="<?= htmlspecialchars($_SESSION['old']['name'] ?? $student['name']) ?>"
                required
                placeholder="Ex: Maria García Pérez"
            >
            <?php if (isset($_SESSION['errors']['name'])): ?>
                <span class="error"><?= $_SESSION['errors']['name'] ?></span>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="email">Correu electrònic *</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="<?= htmlspecialchars($_SESSION['old']['email'] ?? $student['email']) ?>"
                required
                placeholder="Ex: maria.garcia@example.com"
            >
            <?php if (isset($_SESSION['errors']['email'])): ?>
                <span class="error"><?= $_SESSION['errors']['email'] ?></span>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="age">Edat *</label>
            <input 
                type="number" 
                id="age" 
                name="age" 
                value="<?= htmlspecialchars($_SESSION['old']['age'] ?? $student['age']) ?>"
                min="16" 
                max="99"
                required
                placeholder="Ex: 20"
            >
            <?php if (isset($_SESSION['errors']['age'])): ?>
                <span class="error"><?= $_SESSION['errors']['age'] ?></span>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="course_id">Curs * (Relació 1:N)</label>
            <select id="course_id" name="course_id" required>
                <option value="">Selecciona un curs...</option>
                <?php 
                // Clau forana: course_id referencia courses(id)
                $currentCourseId = $_SESSION['old']['course_id'] ?? $student['course_id'];
                foreach ($courses as $course):
                ?>
                    <option 
                        value="<?= $course['id'] ?>"
                        <?= ($currentCourseId == $course['id']) ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($course['code']) ?> - <?= htmlspecialchars($course['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($_SESSION['errors']['course_id'])): ?>
                <span class="error"><?= $_SESSION['errors']['course_id'] ?></span>
            <?php endif; ?>
            <small style="color: #666; font-size: 0.875rem; display: block; margin-top: 4px;">
                💡 Els cursos es carreguen dinàmicament de la base de dades (taula <code>courses</code>)
            </small>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Actualitzar</button>
            <a href="/students" class="btn btn-secondary">❌ Cancel·lar</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
