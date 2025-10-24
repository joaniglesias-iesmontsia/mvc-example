<?php 
$title = "Nou aula";
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="page-header">
    <h2>➕ Crear Nou aula</h2>
    <a href="/classrooms" class="btn btn-secondary">⬅️ Tornar</a>
</div>

<div class="form-container">
    <form action="/classrooms/store" method="POST" class="form">
        <div class="form-group">
            <label for="name">Nom complet del aula *</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="<?= htmlspecialchars($_SESSION['old']['name'] ?? '') ?>"
                required
                placeholder="Ex: A21"
            >
            <?php if (isset($_SESSION['errors']['name'])): ?>
                <span class="error"><?= $_SESSION['errors']['name'] ?></span>
            <?php endif; ?>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Crear aula</button>
            <a href="/courses" class="btn btn-secondary">❌ Cancel·lar</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
