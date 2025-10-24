<?php 
$title = "Editar Aula";
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="page-header">
    <h2>✏️ Editar Aula</h2>
    <a href="/courses" class="btn btn-secondary">⬅️ Tornar</a>
</div>

<div class="form-container">
    <form action="/classrooms/update" method="POST" class="form">
        <input type="hidden" name="id" value="<?= htmlspecialchars($classroom['id']) ?>">
        
        <div class="form-group">
            <label for="name">Nom complet del aula *</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="<?= htmlspecialchars($_SESSION['old']['name'] ?? $classroom['name']) ?>"
                required
                placeholder="Ex: A21"
            >
            <?php if (isset($_SESSION['errors']['name'])): ?>
                <span class="error"><?= $_SESSION['errors']['name'] ?></span>
            <?php endif; ?>
        </div>
                
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Actualitzar</button>
            <a href="/courses" class="btn btn-secondary">❌ Cancel·lar</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
