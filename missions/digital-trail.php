<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle = 'OP: DIGITAL-TRAIL — CyberEscudo';
require __DIR__ . '/../templates/header.php';
?>
<main class="content-page">
    <div class="briefing-container" style="border-top: 4px solid #00ff41;">
        <div class="classification" style="color:#00ff41; border-color:#00ff41;"><?= $lang === 'es' ? 'NIVEL: PRINCIPIANTE' : 'LEVEL: BEGINNER' ?></div>
        <h1 class="mission-title">OP: DIGITAL-TRAIL</h1>
        <div class="intel-block" style="border-color:#00ff41; background:rgba(0,255,65,0.02);">
            <strong style="color:#00ff41;"><?= $lang === 'es' ? '[ INTELIGENCIA DE FUENTES ABIERTAS ]' : '[ OPEN SOURCE INTELLIGENCE ]' ?></strong><br><br>
            <?= $lang === 'es' ? 'A veces la información más sensible no se hackea, simplemente se busca. Los registros de certificados SSL y DNS son públicos.' : 'Sometimes the most sensitive information is not hacked, it is simply searched. SSL and DNS certificate records are public.' ?><br><br>
            <strong><?= $lang === 'es' ? 'Objetivo:' : 'Objective:' ?></strong> <?= $lang === 'es' ? 'Hemos volcado una base de datos de subdominios. Descarga el JSON y encuentra el subdominio que contiene la flag.' : 'We have dumped a database of subdomains. Download the JSON and find the subdomain containing the flag.' ?>
        </div>
        <div style="text-align: center; margin: 2rem 0; padding: 1.5rem; background: #050505; border: 1px dashed #333;">
            <p style="color: #888; font-family: var(--mono);"><?= $lang === 'es' ? 'Archivo interceptado:' : 'Intercepted file:' ?></p>
            <a href="<?= e(BASE_URL) ?>/assets/challenges/ssl_dump.json" download class="btn-deploy" style="border-color:#00ff41; color:#00ff41;">[ <?= $lang === 'es' ? 'DESCARGAR JSON' : 'DOWNLOAD JSON' ?> ]</a>
        </div>
        <div style="margin-top: 2rem; text-align: center; font-family: var(--mono);">
            <code style="color: #00ff41;">submit OP-DIGITAL-TRAIL FLAG{...}</code>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../templates/footer.php'; ?>