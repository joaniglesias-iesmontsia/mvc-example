<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Gestió d\'Estudiants' ?></title>
        <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <h1 class="logo">🎓 Gestió Acadèmica<br>(exemple de CRUDs seguint el patró MVC)</h1>
            <ul class="nav-links">
                <li><a href="/students">📚 Estudiants</a></li>
                <li><a href="/teachers">👨‍🏫 Professors</a></li>
                <li><a href="/courses">🎯 Cursos</a></li>
                <li><a href="/teaching-teams">👥 Equips Docents</a></li>
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
