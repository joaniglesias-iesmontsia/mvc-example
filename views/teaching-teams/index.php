<?php 
$title = "Equips Docents";
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="page-header">
    <h2>👥 Equips Docents (Relació N:M)</h2>
    <p style="color: #666; font-size: 0.9rem; margin-top: 8px;">
        Gestió de les assignacions de professors a cursos (un curs pot tenir molts professors, i un professor pot estar a molts cursos)
    </p>
</div>

<?php if (!empty($statistics)): ?>
    <div class="alert alert-info" style="margin-bottom: 20px;">
        <strong>📊 Estadístiques generals:</strong><br>
        • Cursos amb professors assignats: <strong><?= $statistics['total_courses_with_teachers'] ?></strong><br>
        • Professors assignats: <strong><?= $statistics['total_teachers_assigned'] ?></strong><br>
        • Total d'assignacions: <strong><?= $statistics['total_assignments'] ?></strong><br>
        • Mitjana de professors per curs: <strong><?= $statistics['avg_teachers_per_course'] ?></strong>
    </div>
<?php endif; ?>

<?php if (empty($teams)): ?>
    <div class="alert alert-info">
        <p>No hi ha cursos amb equips docents configurats. <a href="/courses">Gestiona els cursos primer</a></p>
    </div>
<?php else: ?>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Codi</th>
                    <th>Nom del Curs</th>
                    <th>Descripció</th>
                    <th>Professors Assignats</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teams as $team): ?>
                    <tr>
                        <td><span class="badge"><?= htmlspecialchars($team['code']) ?></span></td>
                        <td><strong><?= htmlspecialchars($team['name']) ?></strong></td>
                        <td><?= htmlspecialchars($team['description'] ?? '-') ?></td>
                        <td>
                            <span class="badge badge-info" style="font-size: 1rem;">
                                👨‍🏫 <?= $team['teacher_count'] ?> <?= $team['teacher_count'] == 1 ? 'professor' : 'professors' ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a href="/teaching-teams/show?course_id=<?= $team['id'] ?>" class="btn btn-small btn-primary">
                                👁️ Veure Equip
                            </a>
                            <a href="/teaching-teams/assign?course_id=<?= $team['id'] ?>" class="btn btn-small btn-success">
                                ➕ Afegir Professor
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="alert alert-info" style="margin-top: 20px;">
        <strong>💡 Sobre les relacions N:M (Molts a Molts):</strong><br>
        Aquest exemple demostra com gestionar relacions complexes on:<br>
        • Un <strong>curs</strong> pot tenir <strong>molts professors</strong> (per ensenyar diferents assignatures)<br>
        • Un <strong>professor</strong> pot estar assignat a <strong>molts cursos</strong><br><br>
        
        Això s'implementa amb una <strong>taula intermèdia</strong> (<code>teaching_teams</code>) que conté:<br>
        • <code>course_id</code> (clau forana → courses)<br>
        • <code>teacher_id</code> (clau forana → teachers)<br>
        • <code>UNIQUE(course_id, teacher_id)</code> per evitar duplicats<br><br>
        
        <strong>Beneficis:</strong><br>
        ✅ Flexibilitat: Permet assignacions dinàmiques<br>
        ✅ Escalabilitat: Afegir/eliminar assignacions sense afectar les entitats principals<br>
        ✅ Integritat: Les claus foranes garanteixen que només existeixen relacions vàlides
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
