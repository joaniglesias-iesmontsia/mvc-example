<?php 
$title = "Cursos de " . $teacher['name'];
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="page-header">
    <h2>📚 Cursos Assignats al Professor</h2>
    <a href="/teachers" class="btn btn-secondary">⬅️ Tornar a Professors</a>
</div>

<div class="alert alert-info">
    <strong>👨‍🏫 Professor:</strong> <?= htmlspecialchars($teacher['name']) ?><br>
    <strong>Email:</strong> <?= htmlspecialchars($teacher['email']) ?><br>
    <strong>Especialitat:</strong> <?= htmlspecialchars($teacher['specialty']) ?><br>
    <strong>Departament:</strong> <?= htmlspecialchars($teacher['department']) ?><br>
    <strong>Cursos assignats:</strong> <?= count($courses) ?>
</div>

<?php if (empty($courses)): ?>
    <div class="alert alert-warning">
        <p>⚠️ Aquest professor encara no està assignat a cap curs.</p>
        <p><a href="/teaching-teams" class="btn btn-primary">Gestionar Equips Docents</a></p>
    </div>
<?php else: ?>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Codi</th>
                    <th>Nom del Curs</th>
                    <th>Descripció</th>
                    <th>Data d'assignació</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><span class="badge"><?= htmlspecialchars($course['code']) ?></span></td>
                        <td><strong><?= htmlspecialchars($course['name']) ?></strong></td>
                        <td><?= htmlspecialchars($course['description'] ?? '-') ?></td>
                        <td><?= date('d/m/Y', strtotime($course['assigned_at'])) ?></td>
                        <td class="actions">
                            <a href="/teaching-teams/show?course_id=<?= $course['id'] ?>" class="btn btn-small btn-primary">
                                👁️ Veure Equip Complet
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="stats">
        <p>Total de cursos: <strong><?= count($courses) ?></strong></p>
    </div>
<?php endif; ?>

<div class="alert alert-info" style="margin-top: 20px;">
    <strong>💡 Vista des de la perspectiva del Professor (Relació N:M):</strong><br>
    • Aquesta taula mostra tots els cursos on aquest professor està assignat<br>
    • La consulta utilitza un <code>INNER JOIN</code> entre <code>courses</code> i <code>teaching_teams</code><br>
    • És la mateixa relació N:M, però consultada des de l'altra perspectiva<br>
    • Un professor pot veure fàcilment tots els seus cursos assignats
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
