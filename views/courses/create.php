<?php 
$title = "Nou Curs";
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="page-header">
    <h2>➕ Crear Nou Curs</h2>
    <a href="/courses" class="btn btn-secondary">⬅️ Tornar</a>
</div>

<div class="form-container">
    <form action="/courses/store" method="POST" class="form">
        <div class="form-group">
            <label for="code">Codi del curs * (ex: DAW, DAM, ASIX)</label>
            <input 
                type="text" 
                id="code" 
                name="code" 
                value="<?= htmlspecialchars($_SESSION['old']['code'] ?? '') ?>"
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
                value="<?= htmlspecialchars($_SESSION['old']['name'] ?? '') ?>"
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
                value="<?= htmlspecialchars($_SESSION['old']['description'] ?? '') ?>"
                placeholder="Ex: CFGS de programació web amb PHP, JavaScript i frameworks moderns"
            >
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Crear Curs</button>
            <a href="/courses" class="btn btn-secondary">❌ Cancel·lar</a>
        </div>
    </form>
</div>

<div class="alert alert-info" style="margin-top: 2rem;">
    <p><strong>💡 Relació 1:N:</strong> Un cop creat el curs, podràs assignar-li estudiants. 
    Un curs pot tenir molts estudiants, però cada estudiant només pot estar en un curs.</p>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
