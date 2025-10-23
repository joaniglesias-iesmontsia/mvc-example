<?php 
$title = "Editar Curs";
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="page-header">
    <h2>✏️ Editar Curs</h2>
    <a href="/courses" class="btn btn-secondary">⬅️ Tornar</a>
</div>

<div class="form-container">
    <form action="/courses/update" method="POST" class="form">
        <input type="hidden" name="id" value="<?= htmlspecialchars($course['id']) ?>">
        
        <div class="form-group">
            <label for="code">Codi del curs *</label>
            <input 
                type="text" 
                id="code" 
                name="code" 
                value="<?= htmlspecialchars($_SESSION['old']['code'] ?? $course['code']) ?>"
                required
                maxlength="10"
                placeholder="Ex: DAW"
                style="text-transform: uppercase;"
            >
            <?php if (isset($_SESSION['errors']['code'])): ?>
                <span class="error"><?= $_SESSION['errors']['code'] ?></span>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="name">Nom complet del curs *</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="<?= htmlspecialchars($_SESSION['old']['name'] ?? $course['name']) ?>"
                required
                placeholder="Ex: Desenvolupament d'Aplicacions Web"
            >
            <?php if (isset($_SESSION['errors']['name'])): ?>
                <span class="error"><?= $_SESSION['errors']['name'] ?></span>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="description">Descripció (opcional)</label>
            <input 
                type="text" 
                id="description" 
                name="description" 
                value="<?= htmlspecialchars($_SESSION['old']['description'] ?? $course['description']) ?>"
                placeholder="Ex: CFGS de programació web amb PHP, JavaScript i frameworks moderns"
            >
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Actualitzar</button>
            <a href="/courses" class="btn btn-secondary">❌ Cancel·lar</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
