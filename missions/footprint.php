<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle = 'OP: FOOTPRINT — CyberEscudo';
require __DIR__ . '/../templates/header.php';
?>

<style>
    .briefing-container {
        max-width: 800px;
        margin: 4rem auto;
        background: #0a0a0a;
        border: 1px solid #333;
        border-top: 4px solid #ffcc00;
        padding: 3rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.8);
    }
    .classification {
        color: #ffcc00;
        font-family: var(--mono);
        border: 1px solid #ffcc00;
        padding: 4px 10px;
        font-size: 0.8rem;
        display: inline-block;
    }
    .intel-block {
        background: rgba(255, 204, 0, 0.02);
        border-left: 2px solid #ffcc00;
        padding: 1.5rem;
        margin: 2rem 0;
        font-family: var(--mono);
        color: #ccc;
    }
    .btn-download {
        display: inline-block;
        border: 2px solid #ffcc00;
        color: #ffcc00;
        padding: 12px 30px;
        text-decoration: none;
        font-family: var(--mono);
        transition: 0.3s;
    }
    .btn-download:hover { background: #ffcc00; color: #000; }
</style>

<main class="content-page">
    <div class="briefing-container">
        <div class="classification"><?= $lang === 'es' ? 'NIVEL: PRINCIPIANTE' : 'LEVEL: BEGINNER' ?></div>
        <h1 style="color: #fff; font-family: var(--mono); margin: 1rem 0;">OP: FOOTPRINT</h1>
        
        <div class="intel-block">
            <strong><?= $lang === 'es' ? '[ ANÁLISIS DE INCIDENTES ]' : '[ INCIDENT ANALYSIS ]' ?></strong><br><br>
            <?= $lang === 'es' 
                ? 'Hemos detectado una intrusión en un servidor de desarrollo. El atacante intentó borrar su rastro usando <code>history -c</code>, pero nuestro sistema de auditoría recuperó los comandos antes de que se perdieran.' 
                : 'We detected an intrusion on a development server. The attacker tried to erase their tracks using <code>history -c</code>, but our audit system recovered the commands before they were lost.' 
            ?><br><br>
            <strong><?= $lang === 'es' ? 'Tu misión:' : 'Your mission:' ?></strong> <?= $lang === 'es' 
                ? 'Analiza el historial de comandos, identifica qué archivos intentó robar el atacante y encuentra la flag que dejó expuesta accidentalmente.' 
                : 'Analyze the command history, identify which files the attacker tried to steal, and find the flag they accidentally left exposed.' 
            ?>
        </div>

        <div style="text-align: center; background: #050505; padding: 2rem; border: 1px dashed #333;">
            <p style="color: #888; font-family: var(--mono);"><?= $lang === 'es' ? 'Archivo:' : 'File:' ?> attacker_history.txt</p>
            <a href="<?= e(BASE_URL) ?>/assets/challenges/attacker_history.txt" class="btn-download" download>
                <?= $lang === 'es' ? 'DESCARGAR HISTORIAL' : 'DOWNLOAD HISTORY' ?>
            </a>
        </div>

        <div style="margin-top: 2rem; text-align: center; font-family: var(--mono); color: #555;">
            <?= $lang === 'es' 
                ? 'Usa <code>grep</code>, <code>cat</code> o un editor de texto para inspeccionar el archivo.' 
                : 'Use <code>grep</code>, <code>cat</code>, or a text editor to inspect the file.' 
            ?><br>
            <strong style="color: #ffcc00; display: inline-block; margin-top: 10px;">submit OP-FOOTPRINT FLAG{...}</strong>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>