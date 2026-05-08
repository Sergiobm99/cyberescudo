<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle = 'OP: EXIF-DATA — CyberEscudo';
require __DIR__ . '/../templates/header.php';
?>
<main class="content-page">
    <div class="briefing-container" style="border-top: 4px solid #ffcc00;">
        <div class="classification" style="color:#ffcc00; border-color:#ffcc00;"><?= $lang === 'es' ? 'NIVEL: INTERMEDIO' : 'LEVEL: INTERMEDIATE' ?></div>
        <h1 class="mission-title">OP: EXIF-DATA</h1>
        <div class="intel-block" style="border-color:#ffcc00; background:rgba(255,204,0,0.02);">
            <strong style="color:#ffcc00;"><?= $lang === 'es' ? '[ ANÁLISIS DE METADATOS ]' : '[ METADATA ANALYSIS ]' ?></strong><br><br>
            <?= $lang === 'es' ? 'Las imágenes guardan información invisible llamada EXIF (cámara, ubicación geográfica, autor). A veces, esconden secretos.' : 'Images store invisible information called EXIF (camera, geolocation, author). Sometimes, they hide secrets.' ?><br><br>
            <strong><?= $lang === 'es' ? 'Objetivo:' : 'Objective:' ?></strong> <?= $lang === 'es' ? 'Descarga la imagen objetivo y extrae sus metadatos usando herramientas forenses.' : 'Download the target image and extract its metadata using forensic tools.' ?>
        </div>
        <div style="text-align: center; margin: 2rem 0; padding: 1.5rem; background: #050505; border: 1px dashed #333;">
            <a href="<?= e(BASE_URL) ?>/assets/challenges/ctf-11-imagen.jpg" download class="btn-deploy" style="border-color:#ffcc00; color:#ffcc00;">[ <?= $lang === 'es' ? 'DESCARGAR EVIDENCIA' : 'DOWNLOAD EVIDENCE' ?> ]</a>
        </div>
        <div style="margin-top: 2rem; text-align: center; font-family: var(--mono);">
            <p style="color: #666; font-size: 0.8rem;"><?= $lang === 'es' ? 'Pista: exiftool o comandos básicos como "strings".' : 'Hint: exiftool or basic commands like "strings".' ?></p>
            <code style="color: #ffcc00;">submit OP-EXIF-DATA FLAG{...}</code>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../templates/footer.php'; ?>