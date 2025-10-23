<?php 
$title = "Llistat d'Estudiants";
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="page-header">
    <h2>📋 Llistat d'Estudiants</h2>
    <a href="/students/create" class="btn btn-primary">➕ Afegir Estudiant</a>
</div>

<?php if (empty($students)): ?>
    <div class="alert alert-info">
        <p>No hi ha estudiants registrats. <a href="/students/create">Crea'n un de nou!</a></p>
    </div>
<?php else: ?>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Edat</th>
                    <th>Curs</th>
                    <th>Data de Creació</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= htmlspecialchars($student['id']) ?></td>
                        <td><?= htmlspecialchars($student['name']) ?></td>
                        <td><?= htmlspecialchars($student['email']) ?></td>
                        <td><?= htmlspecialchars($student['age']) ?> anys</td>
                        <td>
                            <span class="badge">
                                <?= htmlspecialchars($student['course_code'] ?? 'Sense curs') ?>
                            </span>
                            <?php if (!empty($student['course_name'])): ?>
                                <br><small style="color: #666;"><?= htmlspecialchars($student['course_name']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y', strtotime($student['created_at'])) ?></td>
                        <td class="actions">
                            <a href="/students/edit/<?= $student['id'] ?>" class="btn btn-small btn-secondary">✏️ Editar</a>
                            <a href="/students/delete/<?= $student['id'] ?>" 
                               class="btn btn-small btn-danger"
                               onclick="return confirm('Estàs segur que vols eliminar aquest estudiant?')">
                               🗑️ Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="stats">
        <p>Total d'estudiants: <strong><?= count($students) ?></strong></p>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
