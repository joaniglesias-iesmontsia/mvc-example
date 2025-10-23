<?php 
$title = "Llistat de Cursos";
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="page-header">
    <h2>📚 Llistat de Cursos  (Relació 1:N)</h2>
    <a href="/courses/create" class="btn btn-primary">➕ Afegir Curs</a>
</div>

<?php if (empty($courses)): ?>
    <div class="alert alert-info">
        <p>No hi ha cursos registrats. <a href="/courses/create">Crea'n un de nou!</a></p>
    </div>
<?php else: ?>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Codi</th>
                    <th>Nom del Curs</th>
                    <th>Descripció</th>
                    <th>👤 Estudiants</th>
                    <th>Data de Creació</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><?= htmlspecialchars($course['id']) ?></td>
                        <td><span class="badge"><?= htmlspecialchars($course['code']) ?></span></td>
                        <td><strong><?= htmlspecialchars($course['name']) ?></strong></td>
                        <td><?= htmlspecialchars($course['description'] ?: '—') ?></td>
                        <td style="text-align: center;">
                            <span class="badge" style="background-color: var(--success-color);">
                                <?= $course['student_count'] ?> estudiants
                            </span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($course['created_at'])) ?></td>
                        <td class="actions">
                            <a href="/courses/edit/<?= $course['id'] ?>" class="btn btn-small btn-secondary">✏️ Editar</a>
                            <?php if ($course['student_count'] == 0): ?>
                                <a href="/courses/delete/<?= $course['id'] ?>" 
                                   class="btn btn-small btn-danger"
                                   onclick="return confirm('Estàs segur que vols eliminar aquest curs?')">
                                   🗑️ Eliminar
                                </a>
                            <?php else: ?>
                                <button class="btn btn-small btn-secondary" disabled title="No es pot eliminar (té estudiants assignats)">
                                    🔒 Protegit
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="stats">
        <p>Total de cursos: <strong><?= count($courses) ?></strong></p>
    </div>
    
    <div class="alert alert-info">
        <p><strong>💡 Nota sobre relacions 1:N:</strong> Cada curs pot tenir molts estudiants. 
        Els cursos amb estudiants assignats no es poden eliminar fins que es reassignin els estudiants a un altre curs.</p>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
