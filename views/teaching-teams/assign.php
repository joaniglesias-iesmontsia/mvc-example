<?php 
$title = "Afegir Professor a l'Equip Docent";
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="page-header">
    <h2>➕ Afegir Professor a l'Equip Docent</h2>
    <a href="/teaching-teams/show?course_id=<?= $course['id'] ?>" class="btn btn-secondary">⬅️ Tornar</a>
</div>

<div class="alert alert-info">
    <strong>📚 Curs:</strong> <?= htmlspecialchars($course['name']) ?> (<?= htmlspecialchars($course['code']) ?>)
</div>

<?php if (empty($availableTeachers)): ?>
    <div class="alert alert-warning">
        <p>⚠️ No hi ha professors disponibles per assignar a aquest curs.</p>
        <p>Tots els professors ja estan assignats o no hi ha professors registrats al sistema.</p>
        <p><a href="/teachers/create" class="btn btn-primary">➕ Crear un nou professor</a></p>
    </div>
<?php else: ?>
    <div class="form-container">
        <form action="/teaching-teams/store" method="POST" class="form">
            <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
            
            <div class="form-group">
                <label for="teacher_id">Selecciona un professor *</label>
                <select id="teacher_id" name="teacher_id" required>
                    <option value="">Tria un professor...</option>
                    <?php foreach ($availableTeachers as $teacher): ?>
                        <option value="<?= $teacher['id'] ?>">
                            <?= htmlspecialchars($teacher['name']) ?> 
                            - <?= htmlspecialchars($teacher['specialty']) ?>
                            (<?= htmlspecialchars($teacher['department']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <small style="color: #666; font-size: 0.875rem; display: block; margin-top: 4px;">
                    💡 Només es mostren els professors que encara no estan assignats a aquest curs
                </small>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">✅ Assignar Professor</button>
                <a href="/teaching-teams/show?course_id=<?= $course['id'] ?>" class="btn btn-secondary">❌ Cancel·lar</a>
            </div>
        </form>
    </div>
    
    <div class="alert alert-info" style="margin-top: 20px;">
        <strong>💡 Funcionament de la Relació N:M:</strong><br>
        Quan assignes un professor a un curs, es crea un nou registre a la taula <code>teaching_teams</code> amb:<br>
        • <code>course_id</code> = <?= $course['id'] ?> (aquest curs)<br>
        • <code>teacher_id</code> = l'ID del professor seleccionat<br>
        • <code>assigned_at</code> = la data/hora actual<br><br>
        
        Aquesta taula intermèdia permet que:<br>
        ✅ Un professor pugui estar assignat a múltiples cursos<br>
        ✅ Un curs pugui tenir múltiples professors<br>
        ✅ Les assignacions es puguin crear i eliminar sense afectar les entitats principals
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
