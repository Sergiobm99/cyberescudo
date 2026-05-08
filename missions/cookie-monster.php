<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle = 'OP: COOKIE_MONSTER — CyberEscudo';
require __DIR__ . '/../templates/header.php';
?>

<main class="content-page">
    <div class="briefing-container" style="border-top: 4px solid #aa00ff;">
        <div class="classification" style="color: #aa00ff; border-color: #aa00ff;"><?= $lang === 'es' ? 'NIVEL: PRINCIPIANTE' : 'LEVEL: BEGINNER' ?></div>
        <h1 class="mission-title" style="color: #fff;">OP: COOKIE_MONSTER</h1>
        
        <div class="intel-block" style="border-left-color: #aa00ff; background: rgba(170, 0, 255, 0.02);">
            <strong style="color: #aa00ff;"><?= $lang === 'es' ? '[ AUDITORÍA DE SESIONES ]' : '[ SESSION AUDIT ]' ?></strong><br><br>
            <?= $lang === 'es' ? 'Las <strong>cookies HTTP</strong> almacenan información vital en el navegador del usuario. A veces, por una mala práctica, contienen datos ofuscados que no deberían ser accesibles al cliente, como roles de administración o credenciales.' : '<strong>HTTP cookies</strong> store vital information in the user\'s browser. Sometimes, due to bad practices, they contain obfuscated data that should not be accessible to the client, such as admin roles or credentials.' ?><br><br>
            <strong><?= $lang === 'es' ? 'Objetivo:' : 'Objective:' ?></strong> <?= $lang === 'es' ? 'Hemos detectado un panel de usuario de pruebas. Visita el objetivo, inspecciona las cookies que te asigna el servidor y decodifica su contenido.' : 'We have detected a test user panel. Visit the target, inspect the cookies assigned by the server, and decode their content.' ?>
        </div>

        <div style="text-align: center; margin: 2rem 0; padding: 1.5rem; background: #050505; border: 1px dashed #333;">
            <p style="color: #888; font-family: var(--mono); margin-bottom: 1rem;"><?= $lang === 'es' ? 'Ruta objetivo (Target):' : 'Target path:' ?></p>
            <a href="user-panel.php" target="_blank" class="btn-deploy" style="border-color: #aa00ff; color: #aa00ff;">
                [ <?= $lang === 'es' ? 'ACCEDER AL PANEL DE USUARIO' : 'ACCESS USER PANEL' ?> ↗ ]
            </a>
        </div>

        <div style="margin-top: 2rem; text-align: center; font-family: var(--mono);">
            <p style="color: #666; font-size: 0.8rem;"><?= $lang === 'es' ? 'Pista: Abre las DevTools (F12) > Pestaña \'Aplicación\' > Cookies' : 'Hint: Open DevTools (F12) > \'Application\' Tab > Cookies' ?></p>
            <strong style="color: #aa00ff;">submit OP-COOKIE-MONSTER FLAG{...}</strong>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>