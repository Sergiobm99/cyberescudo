<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle = 'OP: HEADERS — CyberEscudo';
require __DIR__ . '/../templates/header.php';
?>

<main class="content-page">
    <div class="briefing-container" style="border-top: 4px solid var(--cyan);">
        <div class="classification">NIVEL: INTERMEDIO</div>
        <h1 class="mission-title">OP: HEADERS</h1>
        
        <div class="intel-block">
            <strong>[ ANÁLISIS DE RESPUESTA HTTP ]</strong><br><br>
            Las cabeceras de un servidor pueden decir mucho sobre su configuración. En ocasiones, configuraciones personalizadas o de depuración revelan más de la cuenta.<br><br>
            <strong>Objetivo:</strong> Analiza las cabeceras de respuesta (Response Headers) que envía el servidor al cargar la Home. Busca una cabecera no estándar llamada "X-Cyber-Access".
        </div>

        <div style="margin-top: 2rem; text-align: center; font-family: var(--mono);">
            <p style="color: #666; font-size: 0.8rem;">Usa las DevTools (Network) o curl -I</p>
            <code style="color: var(--cyan);">submit OP-HEADERS FLAG{...}</code>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>