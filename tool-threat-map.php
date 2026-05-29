<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $lang === 'es' ? 'Live Threat Map — CyberEscudo' : 'Live Threat Map — CyberEscudo';
require __DIR__ . '/templates/header.php';
?>

<style>
    body {
        /* Fuerza un fondo oscuro puro para que resalte el canvas */
        background: #020508 !important;
    }
    
    .map-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0;
        width: 100%;
        height: calc(100vh - 4rem); /* Ocupa el resto de la pantalla debajo del nav */
        overflow: hidden;
        position: relative;
    }
    
    @media (min-width: 1024px) {
        .map-container {
            grid-template-columns: 3fr 1fr;
        }
    }

    #threat-canvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: block;
        background: radial-gradient(circle at center, #05101a 0%, #000000 100%);
    }

    .live-logs-panel {
        background: rgba(0, 5, 10, 0.9);
        border-left: 1px solid rgba(0, 255, 255, 0.2);
        display: flex;
        flex-direction: column;
        padding: 0;
        z-index: 10;
        box-shadow: -10px 0 30px rgba(0,0,0,0.8);
        height: 100%;
        max-height: calc(100vh - 4rem);
        overflow: hidden;
    }

    .logs-header {
        background: rgba(0, 255, 255, 0.1);
        border-bottom: 1px solid var(--cyan);
        padding: 1rem;
        font-family: var(--mono);
        color: var(--cyan);
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-transform: uppercase;
        font-weight: bold;
        letter-spacing: 2px;
        font-size: 0.85rem;
    }

    .live-pulse {
        display: inline-block;
        width: 10px;
        height: 10px;
        background: #ff2a2a;
        border-radius: 50%;
        box-shadow: 0 0 10px #ff2a2a;
        animation: pulse-live 1s infinite;
    }

    .logs-body {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
        font-family: var(--mono);
        font-size: 0.75rem;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .log-entry {
        background: rgba(255, 255, 255, 0.03);
        border-radius: 4px;
        padding: 8px;
        border-left: 2px solid transparent;
        animation: slide-in 0.3s ease forwards;
        opacity: 0;
        transform: translateX(20px);
    }
    
    @keyframes slide-in {
        to { opacity: 1; transform: translateX(0); }
    }

    .log-entry.severity-high { border-left-color: #ff2a2a; background: rgba(255, 42, 42, 0.05); }
    .log-entry.severity-medium { border-left-color: #f0a000; background: rgba(240, 160, 0, 0.05); }
    .log-entry.severity-low { border-left-color: #00ff41; background: rgba(0, 255, 65, 0.05); }

    .log-time { color: #888; margin-right: 5px; }
    .log-ip { color: #fff; font-weight: bold; }
    .log-type { color: var(--cyan); margin-top: 4px; display: block; }
    .log-target { color: #aaa; font-size: 0.7rem; display: block; margin-top: 2px; }

    /* Overlay Stats */
    .stats-overlay {
        position: absolute;
        bottom: 20px;
        left: 20px;
        display: flex;
        gap: 20px;
        pointer-events: none;
    }
    .stat-box {
        background: rgba(0, 0, 0, 0.7);
        border: 1px solid rgba(0, 255, 255, 0.3);
        padding: 10px 15px;
        border-radius: 4px;
        backdrop-filter: blur(5px);
        font-family: var(--mono);
        text-align: center;
    }
    .stat-val { font-size: 2rem; color: var(--cyan); font-weight: bold; }
    .stat-label { font-size: 0.7rem; color: #aaa; text-transform: uppercase; letter-spacing: 1px; }

</style>

<div class="map-container">
    
    <!-- CANVAS ZONE -->
    <div style="position: relative; width: 100%; height: 100%;">
        <canvas id="threat-canvas"></canvas>
        <div class="stats-overlay">
            <div class="stat-box">
                <div class="stat-val" id="stat-attacks">0</div>
                <div class="stat-label"><?= $lang === 'es' ? 'Ataques Bloqueados' : 'Attacks Blocked' ?></div>
            </div>
            <div class="stat-box">
                <div class="stat-val" style="color: #ff2a2a;" id="stat-critical">0</div>
                <div class="stat-label"><?= $lang === 'es' ? 'Amenazas Críticas' : 'Critical Threats' ?></div>
            </div>
        </div>
    </div>

    <!-- LOGS ZONE -->
    <div class="live-logs-panel">
        <div class="logs-header">
            <span><?= $lang === 'es' ? 'FEED GLOBAL DE AMENAZAS' : 'GLOBAL THREAT FEED' ?></span>
            <span class="live-pulse"></span>
        </div>
        <div class="logs-body" id="logs-body">
            <!-- Logs se inyectan aquí -->
            <div style="text-align:center; color:#555; margin-top: 20px;">[ INITIALIZING HONEYPOT SENSORS... ]</div>
        </div>
    </div>

</div>



<!-- Escondemos el footer en esta vista para hacerla full screen real -->
<style>footer { display: none !important; }</style>

</body>
</html>
