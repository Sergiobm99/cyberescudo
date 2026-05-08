<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle = 'OP: JWT-TOKEN — CyberEscudo';
require __DIR__ . '/../templates/header.php';
?>

<main class="content-page">
    <div class="briefing-container" style="border-top: 4px solid #aa00ff;">
        <div class="classification" style="color: #aa00ff; border-color: #aa00ff;"><?= $lang === 'es' ? 'NIVEL: PRINCIPIANTE' : 'LEVEL: BEGINNER' ?></div>
        <h1 class="mission-title" style="color: #fff;">OP: JWT-TOKEN</h1>
        
        <div class="intel-block" style="border-left-color: #aa00ff; background: rgba(170, 0, 255, 0.02);">
            <strong style="color: #aa00ff;"><?= $lang === 'es' ? '[ AUDITORÍA DE API ]' : '[ API AUDIT ]' ?></strong><br><br>
            <?= $lang === 'es' ? 'Los tokens JWT se usan para mantener sesiones en aplicaciones modernas. Suelen constar de 3 partes separadas por puntos. La parte central (payload) está en Base64 y contiene los datos del usuario.' : 'JWT tokens are used to maintain sessions in modern apps. They usually consist of 3 parts separated by dots. The middle part (payload) is in Base64 and contains user data.' ?><br><br>
            <strong><?= $lang === 'es' ? 'Objetivo:' : 'Objective:' ?></strong> <?= $lang === 'es' ? 'Extrae y decodifica el payload (la parte del medio) de este token interceptado para robar la flag.' : 'Extract and decode the payload (the middle part) of this intercepted token to steal the flag.' ?>
        </div>

        <div style="text-align: center; margin: 2rem 0; padding: 1.5rem; background: #050505; border: 1px dashed #333;">
            <code style="color: #aa00ff; font-size: 0.85rem; word-break: break-all;">eyJhbGciOiJub25lIiwidHlwIjoiSldUIn0.eyJzdWIiOiIxMjM0NTY3ODkwIiwicm9sZSI6InVzZXIiLCJmbGFnIjoiRkxBR3tqd3RfcDR5bDA0ZF8zeHAwczNkfSIsImlhdCI6MTUxNjIzOTAyMn0.</code>
        </div>

        <div style="margin-top: 2rem; text-align: center; font-family: var(--mono);">
            <p style="color: #666; font-size: 0.8rem; margin-bottom: 1rem;">
                <?= $lang === 'es' ? 'Pista: Utiliza la herramienta de decodificación JWT de tu panel superior.' : 'Hint: Use the JWT decoding tool from your top panel.' ?>
            </p>
            <strong style="color: #aa00ff;">submit OP-JWT-TOKEN FLAG{...}</strong>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>