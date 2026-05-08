<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle = 'OP: ROBOTS — CyberEscudo';
require __DIR__ . '/../templates/header.php';
?>

<main class="content-page">
    <div class="briefing-container" style="border-top: 4px solid var(--cyan);">
        <div class="classification"><?= $lang === 'es' ? 'NIVEL: INTERMEDIO' : 'LEVEL: INTERMEDIATE' ?></div>
        <h1 class="mission-title">OP: ROBOTS</h1>
        
        <div class="intel-block">
            <strong><?= $lang === 'es' ? '[ RECONOCIMIENTO PASIVO ]' : '[ PASSIVE RECON ]' ?></strong><br><br>
            <?= $lang === 'es' ? 'Los administradores web a menudo indican a los buscadores qué rutas no deben indexar. A veces, por error, dejan pistas sobre archivos internos en el archivo estándar de exclusión.' : 'Web administrators often tell search engines which paths not to index. Sometimes, by mistake, they leave clues about internal files in the standard exclusion file.' ?><br><br>
            <strong><?= $lang === 'es' ? 'Objetivo:' : 'Objective:' ?></strong> <?= $lang === 'es' ? 'Revisa la raíz del dominio y encuentra el archivo que controla a los bots de Google.' : 'Check the root of the domain and find the file that controls Google bots.' ?>
        </div>

        <div style="margin-top: 2rem; text-align: center; font-family: var(--mono);">
            <code style="color: var(--cyan);">submit OP-ROBOTS FLAG{...}</code>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>