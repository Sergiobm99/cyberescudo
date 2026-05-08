<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle = 'OP: IDOR-ACCESS — CyberEscudo';
require __DIR__ . '/../templates/header.php';
?>
<main class="content-page">
    <div class="briefing-container" style="border-top: 4px solid #ffcc00;">
        <div class="classification" style="color:#ffcc00; border-color:#ffcc00;"><?= $lang === 'es' ? 'NIVEL: INTERMEDIO' : 'LEVEL: INTERMEDIATE' ?></div>
        <h1 class="mission-title">OP: IDOR-ACCESS</h1>
        <div class="intel-block" style="border-color:#ffcc00; background:rgba(255,204,0,0.02);">
            <strong style="color:#ffcc00;"><?= $lang === 'es' ? '[ INYECCIÓN DE PARÁMETROS ]' : '[ PARAMETER INJECTION ]' ?></strong><br><br>
            <?= $lang === 'es' ? 'El IDOR (Insecure Direct Object Reference) ocurre cuando una API confía ciegamente en el ID que le envía el usuario en la URL sin comprobar sus permisos.' : 'IDOR occurs when an API blindly trusts the ID sent by the user in the URL without checking their permissions.' ?><br><br>
            <strong><?= $lang === 'es' ? 'Objetivo:' : 'Objective:' ?></strong> <?= $lang === 'es' ? 'Entra en el endpoint de la API. Tu ID de usuario actual es 42. Cambia los parámetros de la URL para encontrar el perfil del Administrador.' : 'Enter the API endpoint. Your current user ID is 42. Change the URL parameters to find the Administrator profile.' ?>
        </div>
        <div style="text-align: center; margin: 2rem 0; padding: 1.5rem; background: #050505; border: 1px dashed #333;">
            <a href="user-api.php?user_id=42" target="_blank" class="btn-deploy" style="border-color:#ffcc00; color:#ffcc00;">[ <?= $lang === 'es' ? 'ABRIR ENDPOINT API ↗' : 'OPEN API ENDPOINT ↗' ?> ]</a>
        </div>
        <div style="margin-top: 2rem; text-align: center; font-family: var(--mono);">
            <code style="color: #ffcc00;">submit OP-IDOR-ACCESS FLAG{...}</code>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../templates/footer.php'; ?>