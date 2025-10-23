<?php 
$title = "Nou Professor";
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="page-header">
    <h2>➕ Crear Nou Professor</h2>
    <a href="/teachers" class="btn btn-secondary">⬅️ Tornar</a>
</div>

<div class="form-container">
    <form action="/teachers/store" method="POST" class="form">
        <div class="form-group">
            <label for="name">Nom complet *</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="<?= htmlspecialchars($_SESSION['old']['name'] ?? '') ?>"
                required
                placeholder="Ex: Anna Soler Martí"
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
                placeholder="Ex: anna.soler@iesmontsia.cat"
            >
            <?php if (isset($_SESSION['errors']['email'])): ?>
                <span class="error"><?= $_SESSION['errors']['email'] ?></span>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="phone">Telèfon *</label>
            <input 
                type="tel" 
                id="phone" 
                name="phone" 
                value="<?= htmlspecialchars($_SESSION['old']['phone'] ?? '') ?>"
                required
                pattern="[0-9]{9}"
                placeholder="Ex: 977123456"
            >
            <?php if (isset($_SESSION['errors']['phone'])): ?>
                <span class="error"><?= $_SESSION['errors']['phone'] ?></span>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="department">Departament *</label>
            <select id="department" name="department" required>
                <option value="">Selecciona un departament...</option>
                <option value="Informàtica" <?= (($_SESSION['old']['department'] ?? '') === 'Informàtica') ? 'selected' : '' ?>>Informàtica</option>
                <option value="Matemàtiques" <?= (($_SESSION['old']['department'] ?? '') === 'Matemàtiques') ? 'selected' : '' ?>>Matemàtiques</option>
                <option value="Llengües" <?= (($_SESSION['old']['department'] ?? '') === 'Llengües') ? 'selected' : '' ?>>Llengües</option>
                <option value="Ciències" <?= (($_SESSION['old']['department'] ?? '') === 'Ciències') ? 'selected' : '' ?>>Ciències</option>
                <option value="FOL" <?= (($_SESSION['old']['department'] ?? '') === 'FOL') ? 'selected' : '' ?>>FOL</option>
            </select>
            <?php if (isset($_SESSION['errors']['department'])): ?>
                <span class="error"><?= $_SESSION['errors']['department'] ?></span>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="specialty">Especialitat *</label>
            <input 
                type="text" 
                id="specialty" 
                name="specialty" 
                value="<?= htmlspecialchars($_SESSION['old']['specialty'] ?? '') ?>"
                required
                placeholder="Ex: Desenvolupament Web, Bases de Dades, etc."
            >
            <?php if (isset($_SESSION['errors']['specialty'])): ?>
                <span class="error"><?= $_SESSION['errors']['specialty'] ?></span>
            <?php endif; ?>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Crear Professor</button>
            <a href="/teachers" class="btn btn-secondary">❌ Cancel·lar</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
