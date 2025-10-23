<?php 
$title = "Editar Estudiant";
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="page-header">
    <h2>✏️ Editar Estudiant</h2>
    <a href="index.php" class="btn btn-secondary">⬅️ Tornar</a>
</div>

<div class="form-container">
    <form action="index.php?action=update" method="POST" class="form">
        <input type="hidden" name="id" value="<?= htmlspecialchars($student['id']) ?>">
        
        <div class="form-group">
            <label for="name">Nom complet *</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="<?= htmlspecialchars($_SESSION['old']['name'] ?? $student['name']) ?>"
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
                value="<?= htmlspecialchars($_SESSION['old']['email'] ?? $student['email']) ?>"
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
                value="<?= htmlspecialchars($_SESSION['old']['age'] ?? $student['age']) ?>"
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
                <?php 
                $currentCourse = $_SESSION['old']['course'] ?? $student['course'];
                $courses = ['DAW' => 'DAW - Desenvolupament d\'Aplicacions Web', 
                           'DAM' => 'DAM - Desenvolupament d\'Aplicacions Multiplataforma',
                           'ASIX' => 'ASIX - Sistemes Informàtics i Xarxes',
                           'SMX' => 'SMX - Sistemes Microinformàtics i Xarxes'];
                foreach ($courses as $key => $label):
                ?>
                    <option value="<?= $key ?>" <?= ($currentCourse === $key) ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($_SESSION['errors']['course'])): ?>
                <span class="error"><?= $_SESSION['errors']['course'] ?></span>
            <?php endif; ?>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Actualitzar</button>
            <a href="index.php" class="btn btn-secondary">❌ Cancel·lar</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
