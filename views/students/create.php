<?php 
$title = "Nou Estudiant";
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="page-header">
    <h2>➕ Crear Nou Estudiant</h2>
    <a href="index.php" class="btn btn-secondary">⬅️ Tornar</a>
</div>

<div class="form-container">
    <form action="index.php?action=store" method="POST" class="form">
        <div class="form-group">
            <label for="name">Nom complet *</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="<?= htmlspecialchars($_SESSION['old']['name'] ?? '') ?>"
                required
                placeholder="Ex: Maria García Pérez"
            >
            <?php if (isset($_SESSION['errors']['name'])): ?>
                <span class="error"><?= $_SESSION['errors']['name'] ?></span>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="email">Correu electrònic *</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>"
                required
                placeholder="Ex: maria.garcia@example.com"
            >
            <?php if (isset($_SESSION['errors']['email'])): ?>
                <span class="error"><?= $_SESSION['errors']['email'] ?></span>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="age">Edat *</label>
            <input 
                type="number" 
                id="age" 
                name="age" 
                value="<?= htmlspecialchars($_SESSION['old']['age'] ?? '') ?>"
                min="16" 
                max="99"
                required
                placeholder="Ex: 20"
            >
            <?php if (isset($_SESSION['errors']['age'])): ?>
                <span class="error"><?= $_SESSION['errors']['age'] ?></span>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="course">Curs *</label>
            <select id="course" name="course" required>
                <option value="">Selecciona un curs...</option>
                <option value="DAW" <?= (($_SESSION['old']['course'] ?? '') === 'DAW') ? 'selected' : '' ?>>DAW - Desenvolupament d'Aplicacions Web</option>
                <option value="DAM" <?= (($_SESSION['old']['course'] ?? '') === 'DAM') ? 'selected' : '' ?>>DAM - Desenvolupament d'Aplicacions Multiplataforma</option>
                <option value="ASIX" <?= (($_SESSION['old']['course'] ?? '') === 'ASIX') ? 'selected' : '' ?>>ASIX - Sistemes Informàtics i Xarxes</option>
                <option value="SMX" <?= (($_SESSION['old']['course'] ?? '') === 'SMX') ? 'selected' : '' ?>>SMX - Sistemes Microinformàtics i Xarxes</option>
            </select>
            <?php if (isset($_SESSION['errors']['course'])): ?>
                <span class="error"><?= $_SESSION['errors']['course'] ?></span>
            <?php endif; ?>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Crear Estudiant</button>
            <a href="index.php" class="btn btn-secondary">❌ Cancel·lar</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
