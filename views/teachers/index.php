<?php 
$title = "Llistat de Professors";
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="page-header">
    <h2>👨‍🏫 Llistat de Professors</h2>
    <a href="/teachers/create" class="btn btn-primary">➕ Afegir Professor</a>
</div>

<?php if (empty($teachers)): ?>
    <div class="alert alert-info">
        <p>No hi ha professors registrats. <a href="/teachers/create">Crea'n un de nou!</a></p>
    </div>
<?php else: ?>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Telèfon</th>
                    <th>Departament</th>
                    <th>Especialitat</th>
                    <th>Data de Creació</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teachers as $teacher): ?>
                    <tr>
                        <td><?= htmlspecialchars($teacher['id']) ?></td>
                        <td><?= htmlspecialchars($teacher['name']) ?></td>
                        <td><?= htmlspecialchars($teacher['email']) ?></td>
                        <td><?= htmlspecialchars($teacher['phone']) ?></td>
                        <td><span class="badge"><?= htmlspecialchars($teacher['department']) ?></span></td>
                        <td><?= htmlspecialchars($teacher['specialty']) ?></td>
                        <td><?= date('d/m/Y', strtotime($teacher['created_at'])) ?></td>
                        <td class="actions">
                            <a href="/teachers/edit/<?= $teacher['id'] ?>" class="btn btn-small btn-secondary">✏️ Editar</a>
                            <a href="/teachers/delete/<?= $teacher['id'] ?>" 
                               class="btn btn-small btn-danger"
                               onclick="return confirm('Estàs segur que vols eliminar aquest professor?')">
                               🗑️ Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="stats">
        <p>Total de professors: <strong><?= count($teachers) ?></strong></p>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
