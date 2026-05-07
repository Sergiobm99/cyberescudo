<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle = $lang === 'es' ? 'Centro de Misiones — CyberEscudo' : 'Mission Center — CyberEscudo';
require __DIR__ . '/../templates/header.php';
?>

<style>
    :root {
        --danger: #ff2a2a;
        --cyan: #00ffff;
        --terminal-green: #00ff41;
    }
body {
        background-color: #050505 !important;
        background-image: 
            linear-gradient(rgba(0, 255, 255, 0.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(0, 255, 255, 0.03) 1px, transparent 1px),
            radial-gradient(circle at center, #0a1a1a 0%, #050505 100%) !important;
        background-size: 40px 40px, 40px 40px, 100% 100% !important;
        background-attachment: fixed !important; /* Esto hace que el fondo no se corte al hacer scroll hacia abajo */
    }
    .mission-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }

    .mission-card {
        background: rgba(10, 10, 10, 0.8);
        border: 1px solid #222;
        padding: 20px;
        position: relative;
        overflow: hidden;
        transition: all 0.4s;
        cursor: crosshair;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .mission-card:hover {
        border-color: var(--danger);
        box-shadow: 0 0 20px rgba(255, 42, 42, 0.2);
        transform: scale(1.02);
    }

    /* Efecto de línea de escaneo */
    .mission-card::after {
        content: "";
        position: absolute;
        top: -100%;
        left: 0;
        width: 100%;
        height: 2px;
        background: var(--danger);
        opacity: 0.1;
        animation: scan 4s linear infinite;
    }

    @keyframes scan {
        0% { top: -100%; }
        100% { top: 200%; }
    }

    .status-badge {
        font-family: var(--mono);
        font-size: 0.7rem;
        padding: 2px 8px;
        border-radius: 3px;
        text-transform: uppercase;
        float: right;
    }

    .status-critical { background: rgba(255, 42, 42, 0.2); color: var(--danger); border: 1px solid var(--danger); }
    
    .mission-title {
        font-family: var(--mono);
        color: #fff;
        margin: 15px 0 10px 0;
        letter-spacing: 1px;
    }

    .mission-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #222;
        padding-top: 15px;
        margin-top: 15px;
        font-size: 0.8rem;
        color: #666;
        font-family: var(--mono);
    }

    .btn-deploy {
        background: transparent;
        border: 1px solid var(--cyan);
        color: var(--cyan);
        padding: 6px 12px;
        cursor: pointer;
        font-family: var(--mono);
        font-size: 0.75rem;
        transition: 0.3s;
        text-transform: uppercase;
        text-decoration: none; /* Para que el enlace no tenga subrayado */
        display: inline-block;
        text-align: center;
    }

    .btn-deploy:hover {
        background: var(--cyan);
        color: #000;
        box-shadow: 0 0 10px rgba(0, 255, 255, 0.3);
    }

    /* Insignia dinámica de completado (oculta por defecto, gestionada por JS) */
    .badge-completed {
        position: absolute;
        top: -15px;
        right: -35px;
        background: var(--terminal-green);
        color: #000;
        font-family: var(--mono);
        font-size: 0.7rem;
        font-weight: bold;
        padding: 20px 30px 5px 30px;
        transform: rotate(45deg);
        z-index: 10;
        display: none; /* Se activa mediante JavaScript */
        box-shadow: 0 0 10px var(--terminal-green);
    }
    /* Hacer el navbar y el footer sólidos para ocultar la rejilla de fondo */
    #navbar, .navbar, footer {
        background-color: #050505 !important;
        background-image: none !important;
        position: relative;
        z-index: 100; /* Asegura que estén siempre por encima del fondo */
    }
    /* 1. ANIMACIÓN DE FONDO: Reducimos la opacidad para que sea muy sutil */
    body::before {
        content: "";
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        /* Creamos una línea horizontal de luz cian */
        background: linear-gradient(0deg, 
            transparent 0%, 
            rgba(0, 255, 255, 0.02) 45%, 
            rgba(0, 255, 255, 0.08) 50%, 
            rgba(0, 255, 255, 0.02) 55%, 
            transparent 100%);
        background-size: 100% 200px; /* Tamaño de la onda */
        z-index: -1; /* Detrás de todo */
        animation: scanline 10s linear infinite; /* 10 segundos por vuelta */
        opacity: 0.6; /* Un extra de sutilidad */
    }

    @keyframes scanline {
        0% { background-position: 0 -200px; }
        100% { background-position: 0 100vh; }
    }

    /* 2. EFECTO GLITCH PARA EL TÍTULO (Solo al cargar) */
    .glitch-title {
        position: relative;
        animation: glitch-reveal 0.5s ease-out forwards;
        opacity: 0;
    }

    @keyframes glitch-reveal {
        0% { opacity: 0; transform: translateX(-10px); filter: blur(5px); }
        80% { opacity: 1; transform: translateX(2px); filter: blur(0px); }
        85% { opacity: 1; transform: translateX(-2px) skewX(5deg); color: #fff; }
        90% { opacity: 1; transform: translateX(2px) skewX(-5deg); color: var(--danger); }
        100% { opacity: 1; transform: translateX(0) skewX(0deg); color: #fff; }
    }
</style>

<main class="content-page">
    <div class="md-container" style="padding-top: 5rem; padding-bottom: 6rem;">
        
        <header class="ops-header" style="position: relative; overflow: hidden; border-left: 4px solid var(--danger); padding-left: 20px; margin-bottom: 40px; background: rgba(255, 42, 42, 0.05); padding: 1.5rem;">
            <div style="position: relative; z-index: 1;">
                <h1 class="glitch-title" style="font-family: var(--mono); font-size: 2.5rem; text-transform: uppercase;">
                    <?= $lang === 'es' ? 'Centro de Operaciones: Black Ops' : 'Black Ops: Mission Center' ?>
                </h1>
                <p style="color: #888; font-family: var(--mono);">
                    <?= $lang === 'es' ? '[ ACCESO AUTORIZADO ] — Selecciona un objetivo para iniciar el despliegue.' : '[ ACCESS GRANTED ] — Select a target to initiate deployment.' ?>
                </p>
                
                <div style="margin-top: 20px; background: rgba(0,0,0,0.8); border: 1px solid #333; padding: 15px; border-radius: 4px; max-width: 400px; box-shadow: inset 0 0 10px rgba(0,0,0,1);">
                    <div style="display: flex; justify-content: space-between; font-family: var(--mono); font-size: 0.85rem; margin-bottom: 8px;">
                        <span style="color: var(--cyan);">USER_XP: <span id="user-xp">0</span></span>
                        <span style="color: #888;">RANK: <span id="user-rank" style="color: #fff;">RECRUIT</span></span>
                    </div>
                    <div style="width: 100%; height: 8px; background: #111; border-radius: 4px; overflow: hidden;">
                        <div id="xp-bar" style="height: 100%; width: 0%; background: var(--cyan); box-shadow: 0 0 10px var(--cyan); transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1);"></div>
                    </div>
                    <div style="text-align: right; font-family: var(--mono); font-size: 0.7rem; color: #555; margin-top: 5px;">
                        <span id="missions-count">0</span>/3 MISSIONS COMPLETED
                    </div>
                </div>
            </div>
        </header>

        <div class="mission-grid">
            <div class="mission-card" id="card-OP-GHOST-TRAFFIC">
                <div class="badge-completed">CLEARED</div>
                <div>
                    <span class="status-badge status-critical">Hardcore</span>
                    <div style="color: var(--danger); font-family: var(--mono); font-size: 0.8rem;">OP: GHOST_TRAFFIC</div>
                    <h3 class="mission-title"><?= $lang === 'es' ? 'Análisis de Exfiltración' : 'Exfiltration Analysis' ?></h3>
                    <p style="color: #888; font-size: 0.9rem;">
                        <?= $lang === 'es' ? 'Un servidor interno ha sido comprometido. Analiza el volcado .pcap y encuentra el túnel DNS oculto.' : 'An internal server has been compromised. Analyze the .pcap dump and find the hidden DNS tunnel.' ?>
                    </p>
                </div>
                <div class="mission-footer">
                    <span>TIPO: Forensics</span>
                    <a href="ghost-traffic.php" class="btn-deploy"><?= $lang === 'es' ? 'VER INFORME' : 'VIEW BRIEFING' ?></a>
                </div>
            </div>

            <div class="mission-card" id="card-OP-SECURE-DEV">
                <div class="badge-completed">CLEARED</div>
                <div>
                    <span class="status-badge" style="color: #aaa; border: 1px solid #444;">Intermediate</span>
                    <div style="color: var(--cyan); font-family: var(--mono); font-size: 0.8rem;">OP: SECURE_DEV</div>
                    <h3 class="mission-title">Audit: Logic Bomb</h3>
                    <p style="color: #888; font-size: 0.9rem;">
                        <?= $lang === 'es' ? 'Revisa este fragmento de código en Python y localiza la vulnerabilidad de ejecución remota de comandos.' : 'Review this Python code snippet and locate the remote command execution vulnerability.' ?>
                    </p>
                </div>
                <div class="mission-footer">
                    <span>TIPO: Source Audit</span>
                    <a href="logic-bomb.php" class="btn-deploy" style="border-color: var(--cyan); color: var(--cyan);"><?= $lang === 'es' ? 'VER CÓDIGO' : 'VIEW SOURCE' ?></a>
                </div>
            </div>

            <div class="mission-card" id="card-OP-DEEP-STATE">
                <div class="badge-completed">CLEARED</div>
                <div>
                    <span class="status-badge" style="background: rgba(170, 0, 255, 0.2); color: #aa00ff; border: 1px solid #aa00ff;">Advanced</span>
                    <div style="color: #aa00ff; font-family: var(--mono); font-size: 0.8rem;">OP: DEEP_STATE</div>
                    <h3 class="mission-title"><?= $lang === 'es' ? 'Mensajes en la Sombra' : 'Shadow Messages' ?></h3>
                    <p style="color: #888; font-size: 0.9rem;">
                        <?= $lang === 'es' ? 'Un informante subió una imagen a un foro público antes de desaparecer. Extrae el mensaje oculto.' : 'An informant uploaded an image to a public forum before going dark. Extract the hidden message.' ?>
                    </p>
                </div>
                <div class="mission-footer">
                    <span>TIPO: Steganography</span>
                    <a href="shadow-messages.php" class="btn-deploy" style="border-color: #aa00ff; color: #aa00ff;"><?= $lang === 'es' ? 'VER INFORME' : 'VIEW BRIEFING' ?></a>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    // Pequeño script exclusivo para esta página que comprueba qué misiones
    // están completadas y muestra la insignia "CLEARED" en la esquina de la tarjeta.
    document.addEventListener('DOMContentLoaded', () => {
        let completedMissions = JSON.parse(localStorage.getItem('cyber_missions')) || [];
        completedMissions.forEach(missionId => {
            let card = document.getElementById('card-' + missionId);
            if (card) {
                // Hacer visible la etiqueta "CLEARED"
                let badge = card.querySelector('.badge-completed');
                if (badge) badge.style.display = 'block';
                
                // Efecto visual: oscurecer ligeramente la misión completada
                card.style.opacity = '0.7';
                card.style.borderColor = 'var(--terminal-green)';
            }
        });
    });
</script>

<?php require __DIR__ . '/../templates/footer.php'; ?>