<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Gestió d\'Estudiants' ?></title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <h1 class="logo">🎓 Gestió d'Estudiants (exemple de CRUD seguint el patró MVC)</h1>
            <ul class="nav-links">
                <li><a href="index.php">Llistat</a></li>
                <li><a href="index.php?action=create" class="btn-primary">➕ Nou Estudiant</a></li>
            </ul>
        </div>
    </nav>
    
    <main class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
