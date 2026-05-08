<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle = 'OP: DOUBLE-CIPHER — CyberEscudo';
require __DIR__ . '/../templates/header.php';
?>

<main class="content-page">
    <div class="briefing-container" style="border-top: 4px solid var(--cyan);">
        <div class="classification"><?= $lang === 'es' ? 'NIVEL: PRINCIPIANTE' : 'LEVEL: BEGINNER' ?></div>
        <h1 class="mission-title">OP: DOUBLE-CIPHER</h1>
        
        <div class="intel-block">
            <strong><?= $lang === 'es' ? '[ CRIPTOGRAFÍA MÚLTIPLE ]' : '[ MULTIPLE CRYPTOGRAPHY ]' ?></strong><br><br>
            <?= $lang === 'es' ? 'En análisis de malware es muy común encontrar datos multi-codificados (encadenados) para dificultar el trabajo del Blue Team.' : 'In malware analysis, it is very common to find multi-encoded (chained) data to hinder the Blue Team\'s work.' ?><br><br>
            <strong><?= $lang === 'es' ? 'Objetivo:' : 'Objective:' ?></strong> <?= $lang === 'es' ? 'Decodifica la siguiente cadena. Primero aplica Base64 y al resultado aplícale ROT13.' : 'Decode the following string. First apply Base64, and then apply ROT13 to the result.' ?>
        </div>

        <div style="text-align: center; margin: 2rem 0; padding: 1.5rem; background: #050505; border: 1px dashed #333;">
            <code style="color: var(--cyan); font-size: 1.2rem; word-break: break-all;">U1lOVHtxMGhveTNfcDFjdTNlfQ==</code>
        </div>

        <div style="margin-top: 2rem; text-align: center; font-family: var(--mono);">
            <p style="color: #666; font-size: 0.8rem;"><?= $lang === 'es' ? 'Pista: Utiliza la herramienta de decodificación de tu panel superior.' : 'Hint: Use the decoding tool from your top panel.' ?></p>
            <strong style="color: var(--cyan);">submit OP-DOUBLE-CIPHER FLAG{...}</strong>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>