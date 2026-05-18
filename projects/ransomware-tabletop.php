<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle = $lang === 'es' ? 'CyberEscudo — Tabletop Simulator: Ransomware' : 'CyberEscudo — Tabletop Simulator: Ransomware';
$pageDescription = $lang === 'es' 
    ? 'Simulador interactivo de Respuesta a Incidentes (IR). Pon a prueba tus habilidades de Blue Team conteniendo un ataque de ransomware.' 
    : 'Interactive Incident Response (IR) Simulator. Test your Blue Team skills containing a ransomware attack.';
require __DIR__ . '/../templates/header.php';
?>

<style>
    /* ─── ESTILOS DEL SIMULADOR IR (TABLETOP) ─── */
    .tabletop-container { max-width: 1200px; margin: 3rem auto; padding: 0 1.5rem; }
    
    /* PANTALLA INICIAL */
    #start-screen { display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 4rem 0; }
    .ir-title { font-family: var(--mono); color: var(--cyan); font-size: clamp(2rem, 6vw, 3.5rem); margin-bottom: 1.5rem; text-transform: uppercase; line-height: 1.1; text-shadow: 0 0 20px rgba(0, 255, 255, 0.3); }
    .ir-start-btn { background: var(--cyan); color: #000; font-family: var(--mono); font-weight: 900; font-size: 1.2rem; padding: 1rem 3rem; border: none; border-radius: 4px; cursor: pointer; text-transform: uppercase; box-shadow: 0 0 20px rgba(0, 255, 255, 0.4); transition: all 0.2s ease; margin-top: 1rem; }
    .ir-start-btn:hover { transform: scale(1.05); box-shadow: 0 0 35px rgba(0, 255, 255, 0.7); }
    
    /* HUD */
    .ir-hud { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; background: rgba(10, 15, 20, 0.9); border: 1px solid var(--cyan); border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 0 20px rgba(0, 255, 255, 0.1); }
    .hud-stat { text-align: center; flex: 1; min-width: 150px; margin-bottom: 0.5rem;}
    .hud-label { font-family: var(--mono); font-size: 0.75rem; color: var(--gray); text-transform: uppercase; letter-spacing: 2px; margin-bottom: 0.5rem; }
    .hud-value { font-family: var(--mono); font-size: 1.8rem; font-weight: bold; text-shadow: 0 0 10px currentColor; transition: color 0.3s; }
    
    /* LAYOUT JUEGO (Terminal + Logs) */
    .sim-layout { display: grid; grid-template-columns: 1fr; gap: 1.5rem; }
    @media (min-width: 992px) { .sim-layout { grid-template-columns: 2fr 1fr; } }
    
    /* Terminal Principal */
    .ir-terminal { background: #050505; border: 1px solid var(--border); border-left: 4px solid var(--cyan); border-radius: 8px; padding: 2rem; min-height: 250px; margin-bottom: 1.5rem; position: relative; overflow: hidden; }
    .ir-terminal::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%), linear-gradient(90deg, rgba(255, 0, 0, 0.06), rgba(0, 255, 0, 0.02), rgba(0, 0, 255, 0.06)); background-size: 100% 2px, 3px 100%; pointer-events: none; z-index: 10; opacity: 0.3; }
    #story-text { font-family: var(--mono); color: #d4d4d4; font-size: 1.05rem; line-height: 1.6; text-shadow: 0 0 5px rgba(255,255,255,0.2); }
    .text-typing::after { content: '█'; animation: blink 1s step-end infinite; }
    
    /* Botones Decisión */
    .ir-choices { display: flex; flex-direction: column; gap: 0.8rem; }
    .ir-choice-btn { background: rgba(0, 255, 255, 0.05); border: 1px solid rgba(0, 255, 255, 0.3); color: var(--white); padding: 1rem 1.5rem; text-align: left; font-family: var(--font); font-size: 0.95rem; cursor: pointer; border-radius: 6px; transition: all 0.2s; display: flex; justify-content: space-between; align-items: center; gap: 1rem; line-height: 1.4;}
    .ir-choice-btn:hover { background: rgba(0, 255, 255, 0.15); border-color: var(--cyan); box-shadow: 0 0 15px rgba(0, 255, 255, 0.2); transform: translateX(5px); }
    .ir-choice-meta { font-family: var(--mono); font-size: 0.75rem; color: var(--gray); text-align: right; min-width: 110px; flex-shrink: 0;}
    
    /* Panel SOC Live Logs */
    .live-logs-panel { background: rgba(0, 0, 0, 0.8); border: 1px solid #333; border-radius: 8px; padding: 1rem; display: flex; flex-direction: column; height: 100%; min-height: 400px; box-shadow: inset 0 0 20px rgba(0,0,0,0.8); }
    .log-header { font-family: var(--mono); font-size: 0.75rem; color: var(--cyan); border-bottom: 1px solid #333; padding-bottom: 0.5rem; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 1px; display: flex; justify-content: space-between; align-items: center;}
    .log-status-dot { width: 8px; height: 8px; background: #00ff00; border-radius: 50%; box-shadow: 0 0 8px #00ff00; animation: pulse-fast 1s infinite; }
    .log-container { flex: 1; overflow-y: hidden; position: relative; }
    .log-stream { position: absolute; bottom: 0; width: 100%; display: flex; flex-direction: column; gap: 4px; font-family: var(--mono); font-size: 0.7rem; }
    .log-entry { color: #888; animation: slideUp 0.3s ease-out forwards; opacity: 0; }
    .log-crit { color: #ff2a2a; } .log-warn { color: #f0c000; } .log-info { color: #00d45a; }

    /* AFTER ACTION REPORT (AAR) */
    #aar-screen { display: none; background: rgba(10, 15, 20, 0.95); border: 1px solid var(--cyan); border-radius: 12px; padding: 3rem; text-align: center; box-shadow: 0 0 40px rgba(0, 255, 255, 0.2); }
    .aar-rank { font-size: 6rem; font-family: var(--mono); font-weight: 900; margin: 1rem 0; line-height: 1; text-shadow: 0 0 30px currentColor; }
    .rank-S { color: #b400ff; } .rank-A { color: #00d45a; } .rank-B { color: #00ffff; } .rank-C { color: #f0c000; } .rank-F { color: #ff2a2a; }
    .aar-stats { display: flex; justify-content: center; gap: 3rem; margin: 2rem 0; flex-wrap: wrap; }
    .aar-stat-box { background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); min-width: 200px; }

    @keyframes pulse-fast { 0% { opacity: 1; } 50% { opacity: 0.4; } 100% { opacity: 1; } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes blink { 0%, 50% { opacity: 1; } 51%, 100% { opacity: 0; } }
</style>

<div class="tabletop-container" id="ir-tabletop" data-lang="<?= $lang ?>">
    
    <div id="start-screen">
        <h1 class="ir-title">IR Tabletop:<br>Ransomware</h1>
        <p style="color: var(--gray); font-size: 1.1rem; max-width: 650px; margin: 0 0 2rem 0; line-height: 1.6;">
            <?= $lang === 'es' 
                ? "Eres el Analista de Nivel 3 al mando. Es viernes, 17:00 PM. Tienes <strong>60 minutos</strong> y un presupuesto de <strong>1.000.000€</strong> para salvar la empresa.<br><br>Sigue el ciclo de vida de incidentes del NIST. Cada decisión altera tu presupuesto y el tiempo. Al finalizar, recibirás tu evaluación oficial (AAR)." 
                : "You are the Tier 3 Analyst in charge. It's Friday, 17:00 PM. You have <strong>60 minutes</strong> and a <strong>€1,000,000</strong> budget to save the company.<br><br>Follow the NIST incident lifecycle. Every decision impacts your budget and time. Upon completion, you will receive your official evaluation (AAR)." ?>
        </p>
        <button id="btn-start-sim" class="ir-start-btn">
            <?= $lang === 'es' ? '[ INICIAR INCIDENTE ]' : '[ START INCIDENT ]' ?>
        </button>
    </div>

    <div id="sim-interface" style="display: none;">
        
        <div class="ir-hud">
            <div class="hud-stat">
                <div class="hud-label"><?= $lang === 'es' ? 'Presupuesto' : 'Budget' ?></div>
                <div class="hud-value val-budget" id="hud-budget" style="color: #00d45a;">1,000,000 €</div>
            </div>
            <div class="hud-stat">
                <div class="hud-label"><?= $lang === 'es' ? 'Fase NIST' : 'NIST Phase' ?></div>
                <div class="hud-value val-phase" id="hud-phase" style="color: var(--cyan);">Identificación</div>
            </div>
            <div class="hud-stat">
                <div class="hud-label"><?= $lang === 'es' ? 'Tiempo Crítico' : 'Critical Time' ?></div>
                <div class="hud-value val-timer" id="hud-timer" style="color: #fff;">60:00</div>
            </div>
        </div>

        <div class="sim-layout">
            <div>
                <div class="ir-terminal">
                    <div id="story-text" class="text-typing"></div>
                </div>
                <div class="ir-choices" id="choices-container"></div>
            </div>

            <div class="live-logs-panel hide-mobile">
                <div class="log-header">
                    <span>> SOC_LIVE_FEED.sh</span>
                    <div class="log-status-dot"></div>
                </div>
                <div class="log-container">
                    <div class="log-stream" id="log-stream">
                        </div>
                </div>
            </div>
        </div>
    </div>

    <div id="aar-screen">
        <h2 style="font-family: var(--mono); color: var(--white); text-transform: uppercase; letter-spacing: 2px;">After Action Report</h2>
        <p style="color: var(--gray);" id="aar-desc"></p>
        
        <div class="aar-rank" id="aar-rank">S</div>
        
        <div class="aar-stats">
            <div class="aar-stat-box">
                <div class="hud-label"><?= $lang === 'es' ? 'Presupuesto Salvado' : 'Budget Saved' ?></div>
                <div class="hud-value" id="aar-budget" style="color: #00d45a;">0 €</div>
            </div>
            <div class="aar-stat-box">
                <div class="hud-label"><?= $lang === 'es' ? 'Tiempo Restante' : 'Time Remaining' ?></div>
                <div class="hud-value" id="aar-time" style="color: var(--cyan);">00:00</div>
            </div>
        </div>

        <div style="display: flex; justify-content: center; gap: 1rem; margin-top: 1rem;">
            <button id="btn-restart-sim" class="cyber-btn-free"><?= $lang === 'es' ? 'REINICIAR SIMULACIÓN' : 'RESTART SIMULATION' ?></button>
            <a href="https://www.linkedin.com/feed/" target="_blank" class="ir-start-btn" style="text-decoration: none; padding: 0.8rem 2rem; margin: 0; font-size: 1rem; background: #0a66c2; color: #fff; box-shadow: 0 0 15px rgba(10, 102, 194, 0.4);">
                <?= $lang === 'es' ? 'COMPARTIR EN LINKEDIN' : 'SHARE ON LINKEDIN' ?>
            </a>
        </div>
    </div>

</div>

<?php require __DIR__ . '/../templates/footer.php'; ?>