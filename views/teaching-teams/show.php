<?php 
$title = "Equip Docent - " . $course['name'];
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="page-header">
    <h2>👥 Equip Docent: <?= htmlspecialchars($course['code']) ?></h2>
    <div style="display: flex; gap: 10px;">
        <a href="/teaching-teams" class="btn btn-secondary">⬅️ Tornar</a>
        <a href="/teaching-teams/assign?course_id=<?= $course['id'] ?>" class="btn btn-success">
            ➕ Afegir Professor
        </a>
    </div>
</div>

<div class="alert alert-info">
    <strong>📚 Curs:</strong> <?= htmlspecialchars($course['name']) ?> (<?= htmlspecialchars($course['code']) ?>)<br>
    <?php if ($course['description']): ?>
        <strong>Descripció:</strong> <?= htmlspecialchars($course['description']) ?><br>
    <?php endif; ?>
    <strong>Professors assignats:</strong> <?= count($teachers) ?>
</div>

<?php if (empty($teachers)): ?>
    <div class="alert alert-warning">
        <p>⚠️ Aquest curs encara no té professors assignats.</p>
        <p><a href="/teaching-teams/assign?course_id=<?= $course['id'] ?>" class="btn btn-primary">
            ➕ Afegir el primer professor
        </a></p>
    </div>
<?php else: ?>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Professor</th>
                    <th>Email</th>
                    <th>Telèfon</th>
                    <th>Departament</th>
                    <th>Especialitat</th>
                    <th>Data d'assignació</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teachers as $teacher): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($teacher['name']) ?></strong></td>
                        <td><?= htmlspecialchars($teacher['email']) ?></td>
                        <td><?= htmlspecialchars($teacher['phone']) ?></td>
                        <td><span class="badge"><?= htmlspecialchars($teacher['department']) ?></span></td>
                        <td><?= htmlspecialchars($teacher['specialty']) ?></td>
                        <td><?= date('d/m/Y', strtotime($teacher['assigned_at'])) ?></td>
                        <td class="actions">
                            <a href="/teaching-teams/remove?course_id=<?= $course['id'] ?>&teacher_id=<?= $teacher['id'] ?>" 
                               class="btn btn-small btn-danger"
                               onclick="return confirm('Estàs segur que vols desassignar aquest professor del curs?')">
                               🗑️ Desassignar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php if (!empty($availableTeachers)): ?>
    <div class="alert alert-success" style="margin-top: 20px;">
        <strong>✅ Hi ha <?= count($availableTeachers) ?> professor(s) disponible(s) per assignar a aquest curs.</strong>
        <a href="/teaching-teams/assign?course_id=<?= $course['id'] ?>" class="btn btn-small btn-primary" style="margin-left: 10px;">
            ➕ Afegir-ne un
        </a>
    </div>
<?php else: ?>
    <?php if (!empty($teachers)): ?>
        <div class="alert alert-info" style="margin-top: 20px;">
            ℹ️ Tots els professors disponibles ja estan assignats a aquest curs.
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="alert alert-info" style="margin-top: 20px;">
    <strong>💡 Sobre aquesta vista (Relació N:M):</strong><br>
    • Aquesta taula mostra tots els professors assignats a aquest curs específic<br>
    • La consulta utilitza un <code>INNER JOIN</code> entre <code>teachers</code> i <code>teaching_teams</code><br>
    • Pots afegir o eliminar professors sense afectar les dades dels professors ni del curs<br>
    • La taula intermèdia <code>teaching_teams</code> només conté les relacions (assignacions)
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
