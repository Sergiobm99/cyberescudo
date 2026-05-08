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
        <div class="classification">NIVEL: INICIACIÓN</div>
        <h1 style="color: #fff; font-family: var(--mono); margin: 1rem 0;">OP: FOOTPRINT</h1>
        
        <div class="intel-block">
            <strong>[ ANÁLISIS DE INCIDENTES ]</strong><br><br>
            Hemos detectado una intrusión en un servidor de desarrollo. El atacante intentó borrar su rastro usando `history -c`, pero nuestro sistema de auditoría recuperó los comandos antes de que se perdieran.<br><br>
            Tu misión: Analiza el historial de comandos, identifica qué archivos intentó robar el atacante y encuentra la flag que dejó expuesta accidentalmente.
        </div>

        <div style="text-align: center; background: #050505; padding: 2rem; border: 1px dashed #333;">
            <p style="color: #888; font-family: var(--mono);">Archivo: attacker_history.txt</p>
            <a href="<?= e(BASE_URL) ?>/assets/challenges/attacker_history.txt" class="btn-download" download>
                DESCARGAR HISTORIAL
            </a>
        </div>

        <div style="margin-top: 2rem; text-align: center; font-family: var(--mono); color: #555;">
            Usa `grep`, `cat` o un editor de texto para inspeccionar el archivo.<br>
            <strong style="color: #ffcc00;">submit OP-FOOTPRINT FLAG{...}</strong>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>