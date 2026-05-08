<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle = 'OP: SOURCE — CyberEscudo';
require __DIR__ . '/../templates/header.php';
?>

<main class="content-page">
    <div class="briefing-container" style="border-top: 4px solid var(--cyan);">
        <div class="classification"><?= $lang === 'es' ? 'NIVEL: PRINCIPIANTE' : 'LEVEL: BEGINNER' ?></div>
        <h1 class="mission-title">OP: SOURCE</h1>
        
        <div class="intel-block">
            <strong><?= $lang === 'es' ? '[ INSPECCIÓN DE ELEMENTOS ]' : '[ ELEMENT INSPECTION ]' ?></strong><br><br>
            <?= $lang === 'es' ? 'A veces, las prisas hacen que los desarrolladores dejen comentarios con información sensible, rutas de prueba o incluso credenciales olvidadas en el HTML.' : 'Sometimes, rushing causes developers to leave comments with sensitive information, test paths, or even forgotten credentials in the HTML.' ?><br><br>
            <strong><?= $lang === 'es' ? 'Objetivo:' : 'Objective:' ?></strong> <?= $lang === 'es' ? 'Mira "detrás de las cortinas" de la página de inicio de CyberEscudo. ¿Eres capaz de ver lo que no se muestra a simple vista?' : 'Look "behind the curtains" of the CyberEscudo home page. Can you see what is not visible to the naked eye?' ?>
        </div>

        <div style="margin-top: 2rem; text-align: center; font-family: var(--mono);">
            <p style="color: #666; font-size: 0.8rem;"><?= $lang === 'es' ? 'Pista: Ctrl+U o Inspeccionar Elemento' : 'Hint: Ctrl+U or Inspect Element' ?></p>
            <code style="color: var(--cyan);">submit OP-SOURCE FLAG{...}</code>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>