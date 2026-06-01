<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $lang === 'es' ? 'BloodHound AD Simulator — CyberEscudo' : 'BloodHound AD Simulator — CyberEscudo';
require __DIR__ . '/templates/header.php';
?>

<style>
    .bh-container {
        display: flex;
        flex-direction: column;
        min-height: calc(100vh - 70px);
        background: #000;
        overflow-y: auto;
        overflow-x: hidden;
    }

    @media (min-width: 992px) {
        .bh-container {
            flex-direction: row;
            height: calc(100vh - 70px);
            overflow: hidden;
        }
    }

    /* Sidebar Control Panel */
    .bh-sidebar {
        width: 100%;
        background: rgba(5, 10, 15, 0.95);
        border-right: 1px solid rgba(0, 255, 255, 0.2);
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        z-index: 10;
        overflow-y: auto;
    }
    @media (min-width: 992px) {
        .bh-sidebar { width: 350px; }
    }

    .bh-title {
        color: var(--cyan);
        font-family: var(--mono);
        font-size: 1.5rem;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 1px solid #333;
        padding-bottom: 10px;
    }

    .bh-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .stat-box {
        background: #000;
        border: 1px solid #222;
        padding: 10px;
        text-align: center;
        border-radius: 4px;
        font-family: var(--mono);
    }
    .stat-val { font-size: 1.5rem; font-weight: bold; }
    .stat-label { font-size: 0.7rem; color: #888; text-transform: uppercase; }

    .stat-users .stat-val { color: #00ff41; }
    .stat-computers .stat-val { color: #0088ff; }
    .stat-groups .stat-val { color: #f0a000; }
    .stat-domains .stat-val { color: #ff2a2a; }

    .scenario-selector {
        background: #000;
        color: #fff;
        border: 1px solid var(--cyan);
        padding: 10px;
        font-family: var(--mono);
        border-radius: 4px;
        font-size: 0.9rem;
        width: 100%;
        margin-bottom: 10px;
    }

    .attack-explanation {
        position: absolute;
        bottom: 20px;
        left: 20px;
        width: 380px;
        max-height: 500px;
        overflow-y: auto;
        background: rgba(10,10,15,0.95);
        border: 1px solid var(--cyan);
        border-left: 3px solid #ff2a2a;
        padding: 15px;
        font-family: var(--mono);
        font-size: 0.85rem;
        color: #ccc;
        display: none;
        z-index: 50;
        box-shadow: 0 0 20px rgba(0,0,0,0.8);
    }
    .attack-step {
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px dashed #333;
    }
    .attack-step:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
    .step-title { color: #ff2a2a; font-weight: bold; margin-bottom: 5px; display: flex; align-items: center; gap: 5px; }
    .step-edge { color: var(--cyan); }
    .cmd-box { background: rgba(0,255,255,0.1); padding: 5px; border-radius: 3px; margin-top: 5px; font-size: 0.75rem; color: #fff; word-break: break-all; }

    .bh-btn {
        background: transparent;
        color: var(--cyan);
        border: 1px solid var(--cyan);
        padding: 12px;
        font-family: var(--mono);
        font-weight: bold;
        text-transform: uppercase;
        cursor: pointer;
        border-radius: 4px;
        transition: 0.3s;
        text-align: center;
    }
    .bh-btn:hover {
        background: var(--cyan);
        color: #000;
        box-shadow: 0 0 15px rgba(0,255,255,0.4);
    }
    .bh-btn.btn-danger {
        color: #ff2a2a;
        border-color: #ff2a2a;
    }
    .bh-btn.btn-danger:hover {
        background: #ff2a2a;
        color: #fff;
        box-shadow: 0 0 15px rgba(255,42,42,0.4);
    }

    /* Graph Canvas Area */
    .bh-graph-area {
        flex: 1;
        min-height: 500px;
        position: relative;
        background: radial-gradient(circle at center, #0a1118 0%, #000000 100%);
        overflow: hidden;
    }
    @media (min-width: 992px) {
        .bh-graph-area { min-height: auto; }
    }

    #bh-canvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: block;
        cursor: grab;
    }
    #bh-canvas:active { cursor: grabbing; }

    .legend {
        position: absolute;
        bottom: 20px;
        right: 20px;
        background: rgba(0,0,0,0.8);
        border: 1px solid #333;
        padding: 10px;
        border-radius: 4px;
        font-family: var(--mono);
        font-size: 0.8rem;
        pointer-events: none;
    }
    .legend-item { display: flex; align-items: center; gap: 8px; margin-bottom: 5px; color: #ccc; }
    .legend-color { width: 12px; height: 12px; border-radius: 50%; }

    .tooltip {
        position: absolute;
        background: rgba(0,5,10,0.9);
        border: 1px solid var(--cyan);
        padding: 10px;
        border-radius: 4px;
        color: #fff;
        font-family: var(--mono);
        font-size: 0.85rem;
        pointer-events: none;
        display: none;
        z-index: 100;
        box-shadow: 0 0 15px rgba(0,255,255,0.2);
    }

    .node-info-panel {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 320px;
        background: rgba(10, 10, 15, 0.95);
        border: 1px solid var(--cyan);
        box-shadow: 0 0 15px rgba(0, 255, 255, 0.1);
        color: #fff;
        z-index: 100;
        font-family: monospace;
        display: none;
        flex-direction: column;
    }
    .node-info-header {
        background: rgba(0, 255, 255, 0.1);
        padding: 10px;
        border-bottom: 1px solid var(--cyan);
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: bold;
        font-size: 1.1em;
    }
    .node-info-body {
        padding: 10px;
        font-size: 0.85em;
        overflow-y: auto;
        max-height: 400px;
    }
    .node-info-row {
        margin-bottom: 5px;
        border-bottom: 1px dashed #333;
        padding-bottom: 5px;
    }
    .node-info-label {
        color: var(--cyan);
        font-weight: bold;
    }
    .btn-close {
        background: transparent;
        border: none;
        color: var(--danger);
        font-size: 1.5em;
        cursor: pointer;
        outline: none;
    }
    .btn-close:hover {
        color: #ff4d4d;
    }
</style>

<div class="bh-container">
    <!-- Sidebar -->
    <div class="bh-sidebar">
        <div class="bh-title">
            <i class="fas fa-project-diagram"></i> BLOODHOUND SIM
        </div>
        
        <p style="color:#aaa; font-size:0.85rem; line-height:1.4;">
            <?= $lang === 'es' ? 'Simulador de grafos de ataque en Active Directory. Explora las relaciones de confianza y encuentra caminos hacia Domain Admin.' : 'Active Directory attack graph simulator. Explore trust relationships and find paths to Domain Admin.' ?>
        </p>

        <div class="bh-stats">
            <div class="stat-box stat-users">
                <div class="stat-val" id="stat-u">0</div>
                <div class="stat-label">Users</div>
            </div>
            <div class="stat-box stat-computers">
                <div class="stat-val" id="stat-c">0</div>
                <div class="stat-label">Computers</div>
            </div>
            <div class="stat-box stat-groups">
                <div class="stat-val" id="stat-g">0</div>
                <div class="stat-label">Groups</div>
            </div>
            <div class="stat-box stat-domains">
                <div class="stat-val" id="stat-d">0</div>
                <div class="stat-label">Domains</div>
            </div>
        </div>

        <select class="scenario-selector" id="bh-scenario">
            <option value="gpo"><?= $lang === 'es' ? 'Escenario 1: GPO Abuse & Lateral Movement' : 'Scenario 1: GPO Abuse & Lateral Movement' ?></option>
            <option value="dcsync"><?= $lang === 'es' ? 'Escenario 2: Kerberos Delegation & DCSync' : 'Scenario 2: Kerberos Delegation & DCSync' ?></option>
            <option value="adcs"><?= $lang === 'es' ? 'Escenario 3: AD CS ESC8 (NTLM Relay)' : 'Scenario 3: AD CS ESC8 (NTLM Relay)' ?></option>
            <option value="asrep"><?= $lang === 'es' ? 'Escenario 4: AS-REP Roasting (No Pre-Auth)' : 'Scenario 4: AS-REP Roasting (No Pre-Auth)' ?></option>
            <option value="laps"><?= $lang === 'es' ? 'Escenario 5: LAPS Password Read & Pass-The-Hash' : 'Scenario 5: LAPS Password Read & Pass-The-Hash' ?></option>
            <option value="rbcd"><?= $lang === 'es' ? 'Escenario 6: Resource-Based Constrained Delegation (RBCD)' : 'Scenario 6: Resource-Based Constrained Delegation (RBCD)' ?></option>
            <option value="shadowcred"><?= $lang === 'es' ? 'Escenario 7: Shadow Credentials (Whisker/PKINIT)' : 'Scenario 7: Shadow Credentials (Whisker/PKINIT)' ?></option>
            <option value="foresttrust"><?= $lang === 'es' ? 'Escenario 8: Cross-Forest Trust Abuse (Golden Ticket)' : 'Scenario 8: Cross-Forest Trust Abuse (Golden Ticket)' ?></option>
        </select>

        <button class="bh-btn btn-danger" id="btn-attack-path">
            <i class="fas fa-route"></i> <?= $lang === 'es' ? 'Ejecutar Ruta de Ataque' : 'Execute Attack Path' ?>
        </button>

        <button class="bh-btn" id="btn-reset-graph">
            <i class="fas fa-undo"></i> <?= $lang === 'es' ? 'Reiniciar Grafo' : 'Reset Graph' ?>
        </button>



        <div style="margin-top:auto; font-size:0.8rem; color:#666; font-family:var(--mono);">
            [>] Engine: Native Canvas 2D<br>
            [>] Layout: Force-Directed Physics
        </div>
    </div>

    <!-- Graph Area -->
    <div class="bh-graph-area">
        <canvas id="bh-canvas"></canvas>
        
        <div class="attack-explanation" id="attack-explanation">
            <!-- Injected via JS -->
        </div>
        
        <!-- Node Info Panel -->
        <div id="node-info-panel" class="node-info-panel">
            <div class="node-info-header">
                <div><i id="node-info-icon" class="fas"></i> <span id="node-info-title">Node Name</span></div>
                <button id="btn-close-info" class="btn-close">&times;</button>
            </div>
            <div class="node-info-body" id="node-info-body">
                <!-- details go here -->
            </div>
        </div>
        
        <div class="legend">
            <div class="legend-item"><div class="legend-color" style="background:#00ff41;"></div> User</div>
            <div class="legend-item"><div class="legend-color" style="background:#0088ff;"></div> Computer</div>
            <div class="legend-item"><div class="legend-color" style="background:#f0a000;"></div> Group</div>
            <div class="legend-item"><div class="legend-color" style="background:#b400ff;"></div> OU / GPO / Cert</div>
            <div class="legend-item"><div class="legend-color" style="background:#ff2a2a;"></div> Domain</div>
            <hr style="border-color:#333; margin:5px 0;">
            <div class="legend-item" style="color:#ff2a2a; font-weight:bold;">--- Attack Path</div>
            <div class="legend-item" style="color:#00ffff; border-bottom: 1px dashed #00ffff; width:20px;"></div> <span style="font-size:0.7rem; color:#ccc;">Exploit/Abuse</span>
        </div>

        <div class="tooltip" id="bh-tooltip"></div>
    </div>
</div>

<?php require __DIR__ . '/templates/footer.php'; ?>
