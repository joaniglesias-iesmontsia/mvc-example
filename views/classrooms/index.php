<?php 
$title = "Llistat de Aules";
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="page-header">
    <h2>📚 Llistat d'Aules</h2>
    <a href="/classrooms/create" class="btn btn-primary">➕ Afegir Aula</a>
</div>

<?php if (empty($classrooms)): ?>
    <div class="alert alert-info">
        <p>No hi ha aules registrades. <a href="/classrooms/create">Crea'n una de nova!</a></p>
    </div>
<?php else: ?>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom de l'Aula</th>
                    <th>Data de Creació</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classrooms as $classroom): ?>
                    <tr>
                        <td><?= htmlspecialchars($classroom['id']) ?></td>
                        <td><strong><?= htmlspecialchars($classroom['name']) ?></strong></td>
                        <td><?= date('d/m/Y', strtotime($classroom['created_at'])) ?></td>
                        <td class="actions">
                            <a href="/classrooms/edit/<?= $classroom['id'] ?>" class="btn btn-small btn-secondary">✏️ Editar</a>
                            <a href="/classrooms/delete/<?= $classroom['id'] ?>" class="btn btn-small btn-danger"
                                   class="btn btn-small btn-danger"
                                   onclick="return confirm('Estàs segur que vols eliminar aquest curs?')">
                                   🗑️ Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="stats">
        <p>Total de aules: <strong><?= count($classrooms) ?></strong></p>
    </div>
    
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
