<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $lang === 'es' ? 'SOC Simulator — CyberEscudo' : 'SOC Simulator — CyberEscudo';
require __DIR__ . '/templates/header.php';
?>

<style>
    .soc-container { 
        display: grid; 
        grid-template-columns: 1fr; 
        gap: 1.5rem; 
        max-width: 1300px; 
        margin: 2rem auto 5rem; 
        padding: 0 1.5rem;
    }
    @media (min-width: 992px) {
        .soc-container { grid-template-columns: 2.5fr 1fr; }
    }

    /* === PANEL DE LOGS === */
    .log-panel { 
        background: #050505; 
        border: 1px solid var(--cyan); 
        border-radius: 6px; 
        height: 600px; 
        display: flex; 
        flex-direction: column; 
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0, 255, 255, 0.05);
    }
    .log-header { 
        background: rgba(0, 255, 255, 0.1); 
        padding: 12px 15px; 
        font-family: var(--mono); 
        font-size: 0.85rem;
        color: var(--cyan); 
        border-bottom: 1px solid var(--cyan); 
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    .status-live {
        color: #ff2a2a;
        animation: pulse-live 1.5s infinite;
    }
    @keyframes pulse-live { 0% {opacity:1;} 50% {opacity:0.3;} 100% {opacity:1;} }

    .log-window { 
        flex: 1; 
        padding: 15px; 
        overflow-y: auto; 
        font-family: var(--mono); 
        font-size: 0.85rem; 
        background: radial-gradient(circle at center, #0a0a0a 0%, #000 100%);
    }
    .log-line { 
        padding: 6px 10px; 
        margin-bottom: 4px; 
        border-radius: 4px; 
        border-left: 2px solid transparent;
        cursor: crosshair; 
        transition: background 0.2s;
        display: flex;
        gap: 10px;
        word-break: break-all;
    }
    .log-line:hover { background: rgba(255, 255, 255, 0.05); }
    .log-line.selected { 
        background: rgba(0, 255, 255, 0.15); 
        border-left: 2px solid var(--cyan); 
    }
    
    .l-time { color: #666; min-width: 70px;}
    .l-ip { color: #00ff41; min-width: 120px; font-weight: bold;}
    .l-req { color: #fff; }
    .l-status { color: #00ff41; min-width: 40px;}

    /* === PANEL DE CONTROL === */
    .control-panel { 
        background: rgba(255, 255, 255, 0.02); 
        border: 1px solid #333; 
        border-radius: 6px; 
        padding: 20px; 
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .stat-box { 
        background: #000; 
        border: 1px solid #222; 
        padding: 20px; 
        text-align: center; 
        border-radius: 6px;
    }
    .score-title { color: #888; font-family: var(--mono); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }
    .score-val { font-size: 3rem; color: var(--cyan); font-weight: 900; font-family: var(--mono); line-height: 1;}
    
    .rules-box {
        color: #aaa;
        font-size: 0.85rem;
        line-height: 1.6;
        padding: 10px;
        border-left: 2px solid #444;
    }

    .selected-info {
        background: #000;
        border: 1px dashed var(--cyan);
        padding: 15px;
        border-radius: 4px;
        font-family: var(--mono);
        font-size: 0.8rem;
        color: var(--cyan);
        min-height: 80px;
        word-break: break-all;
    }

    .btn-block { 
        background: transparent; 
        color: #ff2a2a; 
        border: 1px solid #ff2a2a; 
        padding: 15px; 
        font-size: 1.1rem; 
        font-weight: bold; 
        cursor: pointer; 
        border-radius: 4px; 
        font-family: var(--mono); 
        text-transform: uppercase; 
        transition: all 0.3s; 
    }
    .btn-block:hover:not(:disabled) { 
        background: #ff2a2a; 
        color: #fff;
        box-shadow: 0 0 15px rgba(255, 42, 42, 0.4); 
    }
    .btn-block:disabled { 
        border-color: #333; 
        color: #555; 
        cursor: not-allowed; 
    }

    .btn-analyze {
        background: transparent; 
        color: var(--cyan); 
        border: 1px solid var(--cyan); 
        padding: 15px; 
        font-size: 1.1rem; 
        font-weight: bold; 
        cursor: pointer; 
        border-radius: 4px; 
        font-family: var(--mono); 
        text-transform: uppercase; 
        transition: all 0.3s; 
    }
    .btn-analyze:hover:not(:disabled) {
        background: var(--cyan); 
        color: #000;
        box-shadow: 0 0 15px rgba(0, 255, 255, 0.4); 
    }
    .btn-analyze:disabled {
        border-color: #333; 
        color: #555; 
        cursor: not-allowed; 
    }

    .analysis-box {
        display: none; 
        margin-top: 5px; 
        padding: 15px; 
        background: rgba(0, 255, 255, 0.05); 
        border: 1px solid var(--cyan); 
        border-radius: 6px; 
        font-family: var(--mono);
        font-size: 0.85rem;
        color: #fff;
        line-height: 1.5;
    }

    .alert-msg { 
        padding: 12px; 
        border-radius: 4px; 
        font-family: var(--mono); 
        font-size: 0.85rem; 
        text-align: center; 
        display: none; 
        font-weight: bold;
    }

    /* === CAJA DE VICTORIA === */
    .victory-box {
        display: none; 
        margin-top: 10px; 
        padding: 20px; 
        background: rgba(170, 0, 255, 0.1); 
        border: 1px solid #aa00ff; 
        border-radius: 6px; 
        text-align: center;
        box-shadow: 0 0 20px rgba(170, 0, 255, 0.2);
    }
</style>

<main class="content-page" style="max-width: 100%;">
    <div style="text-align: center; margin-bottom: 1rem; padding-top: 2rem;">
        <h1 class="glitch-text" data-text="SIEM // SOC SIMULATOR" style="font-family: var(--mono); font-size: 2.5rem; text-transform: uppercase;">
            SIEM // SOC SIMULATOR
        </h1>
        <p style="color: #888; font-family: var(--mono); font-size: 0.9rem;">
            <?= $lang === 'es' ? '[ ENTRENAMIENTO BLUE TEAM ] — Monitoriza la red y neutraliza amenazas.' : '[ BLUE TEAM TRAINING ] — Monitor the network and neutralize threats.' ?>
        </p>
    </div>

    <div class="soc-container">
        <div class="log-panel">
            <div class="log-header">
                <span>>_ TRAFFIC_MONITOR_v2.4</span>
                <span class="status-live">● LIVE FEED</span>
            </div>
            <div class="log-window" id="log-window">
                <div style="color: #666; text-align: center; margin-top: 20px;">[ INICIANDO CAPTURA DE PAQUETES ]</div>
            </div>
        </div>

        <div class="control-panel">
            <div class="stat-box">
                <div class="score-title"><?= $lang === 'es' ? 'AMENAZAS MITIGADAS' : 'THREATS MITIGATED' ?></div>
                <div class="score-val"><span id="score-display">0</span> <span style="color:#333; font-size: 1.5rem;">/ 5</span></div>
            </div>

            <div class="rules-box">
                <strong><?= $lang === 'es' ? 'OBJETIVO:' : 'MISSION:' ?></strong> 
                <?= $lang === 'es' ? 'Identifica 5 ataques ocultos en el tráfico web (Inyecciones SQL, XSS, LFI).' : 'Identify 5 attacks hidden in the web traffic (SQLi, XSS, LFI).' ?><br><br>
                <strong><?= $lang === 'es' ? 'PENALIZACIÓN:' : 'PENALTY:' ?></strong> 
                <?= $lang === 'es' ? 'Bloquear tráfico legítimo restará 1 punto (Falso Positivo).' : 'Blocking legitimate traffic deducts 1 point (False Positive).' ?>
            </div>

            <div class="selected-info" id="selected-info">
                [ <?= $lang === 'es' ? 'HAZ CLIC EN UN LOG PARA ANALIZARLO' : 'CLICK ON A LOG TO ANALYZE' ?> ]
            </div>

            <div style="display: flex; gap: 10px;">
                <button class="btn-analyze" id="btn-analyze" disabled style="flex: 1;">
                    <?= $lang === 'es' ? '🔍 ANALIZAR PAYLOAD' : '🔍 ANALYZE PAYLOAD' ?>
                </button>
                <button class="btn-block" id="btn-block" disabled style="flex: 1;">
                    <?= $lang === 'es' ? '🛑 BLOQUEAR IP' : '🛑 BLOCK IP' ?>
                </button>
            </div>

            <div id="analysis-box" class="analysis-box"></div>

            <div id="alert-box" class="alert-msg"></div>

            <div id="victory-box" class="victory-box">
                <h3 style="color:#aa00ff; font-family:var(--mono); margin-bottom:10px; font-size: 1.2rem;">TARGET SECURED</h3>
                <p style="font-size:0.8rem; color:#ccc; margin-bottom:15px;">
                    <?= $lang === 'es' ? 'Has demostrado tus habilidades defensivas. Tu flag de recompensa:' : 'You proved your defensive skills. Your reward flag:' ?>
                </p>
                <code style="background:#000; color:#aa00ff; padding:8px 15px; font-size:1.1rem; border: 1px dashed #aa00ff; border-radius: 4px; display: inline-block; margin-bottom: 15px;">FLAG{SOC_ANALYST_PRIME}</code>
                
                <p style="font-size:0.75rem; color:#888; font-style: italic; margin-top: 5px;">
                    <?= $lang === 'es' ? '* No es necesario introducir esta flag en la consola.' : '* You do not need to enter this flag in the console.' ?>
                </p>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/templates/footer.php'; ?>