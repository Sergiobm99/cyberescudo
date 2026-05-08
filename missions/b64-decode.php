<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle = 'OP: B64_DECODE — CyberEscudo';
require __DIR__ . '/../templates/header.php';
?>

<main class="content-page">
    <div class="briefing-container" style="border-top: 4px solid var(--cyan);">
        <div class="classification">NIVEL: PRINCIPIANTE</div>
        <h1 class="mission-title">OP: B64-DECODE</h1>
        
        <div class="intel-block">
            <strong>[ CRIPTOGRAFÍA BÁSICA ]</strong><br><br>
            La codificación no es cifrado. Muchos sistemas usan Base64 para pasar datos binarios de forma segura en URLs o Cookies, pero cualquiera puede revertirlo en un segundo.<br><br>
            <strong>Objetivo:</strong> Decodifica la siguiente cadena para recuperar la flag:<br>
            <span style="color: var(--cyan); word-break: break-all;">RkxBR3tiNHM2NF8xc19ub3RfZW5jcnlwNzEwbn0=</span>
        </div>

        <div style="margin-top: 2rem; text-align: center; font-family: var(--mono);">
            <code style="color: var(--cyan);">submit OP-B64-DECODE FLAG{...}</code>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>