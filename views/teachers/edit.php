<?php 
$title = "Editar Professor";
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="page-header">
    <h2>✏️ Editar Professor</h2>
    <a href="/teachers" class="btn btn-secondary">⬅️ Tornar</a>
</div>

<div class="form-container">
    <form action="/teachers/update" method="POST" class="form">
        <input type="hidden" name="id" value="<?= htmlspecialchars($teacher['id']) ?>">
        
        <div class="form-group">
            <label for="name">Nom complet *</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="<?= htmlspecialchars($_SESSION['old']['name'] ?? $teacher['name']) ?>"
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
                value="<?= htmlspecialchars($_SESSION['old']['email'] ?? $teacher['email']) ?>"
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
                value="<?= htmlspecialchars($_SESSION['old']['phone'] ?? $teacher['phone']) ?>"
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
                <?php 
                $currentDepartment = $_SESSION['old']['department'] ?? $teacher['department'];
                $departments = [
                    'Informàtica' => 'Informàtica',
                    'Matemàtiques' => 'Matemàtiques',
                    'Llengües' => 'Llengües',
                    'Ciències' => 'Ciències',
                    'FOL' => 'FOL'
                ];
                foreach ($departments as $key => $label):
                ?>
                    <option value="<?= $key ?>" <?= ($currentDepartment === $key) ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
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
                value="<?= htmlspecialchars($_SESSION['old']['specialty'] ?? $teacher['specialty']) ?>"
                required
                placeholder="Ex: Desenvolupament Web, Bases de Dades, etc."
            >
            <?php if (isset($_SESSION['errors']['specialty'])): ?>
                <span class="error"><?= $_SESSION['errors']['specialty'] ?></span>
            <?php endif; ?>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Actualitzar</button>
            <a href="/teachers" class="btn btn-secondary">❌ Cancel·lar</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
