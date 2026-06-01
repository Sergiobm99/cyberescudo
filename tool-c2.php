<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $lang === 'es' ? 'C2 Web Simulator — CyberEscudo' : 'C2 Web Simulator — CyberEscudo';
require __DIR__ . '/templates/header.php';
?>

<style>
    .c2-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 70px);
        background: #020508;
        padding: 10px;
        gap: 10px;
        overflow: hidden;
    }
    @media (min-width: 992px) {
        .c2-container { flex-direction: row; }
    }

    /* Left Panel: Beacons */
    .c2-panel-left {
        flex: 1;
        background: #050a10;
        border: 1px solid #112233;
        border-radius: 4px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .c2-header {
        background: #0a1520;
        padding: 10px;
        font-family: var(--mono);
        color: var(--cyan);
        border-bottom: 1px solid #112233;
        font-size: 0.9rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .beacon-list {
        flex: 1;
        overflow-y: auto;
        padding: 0;
        margin: 0;
        list-style: none;
        font-family: var(--mono);
        font-size: 0.8rem;
    }

    .beacon-item {
        padding: 10px;
        border-bottom: 1px solid #112;
        cursor: pointer;
        display: grid;
        grid-template-columns: 30px 1fr auto;
        align-items: center;
        gap: 10px;
        transition: 0.2s;
    }
    .beacon-item:hover { background: rgba(0, 255, 255, 0.05); }
    .beacon-item.active { background: rgba(0, 255, 255, 0.1); border-left: 3px solid var(--cyan); }
    
    .b-icon { color: #555; }
    .beacon-item.active .b-icon { color: var(--cyan); }
    .b-details strong { color: #fff; }
    .b-details span { color: #888; display: block; font-size: 0.7rem; }
    .b-status { width: 8px; height: 8px; border-radius: 50%; background: #00ff41; box-shadow: 0 0 5px #00ff41; }

    /* Right Panel: Terminal */
    .c2-panel-right {
        flex: 2;
        background: #000;
        border: 1px solid #112233;
        border-radius: 4px;
        display: flex;
        flex-direction: column;
    }

    .c2-terminal {
        flex: 1;
        padding: 15px;
        font-family: var(--mono);
        font-size: 0.85rem;
        color: #ccc;
        overflow-y: auto;
        line-height: 1.4;
    }

    .t-out { margin-bottom: 5px; }
    .t-in { color: #f0a000; font-weight: bold; }
    .t-sys { color: var(--cyan); }
    .t-err { color: #ff2a2a; }
    .t-suc { color: #00ff41; }

    .c2-input-area {
        background: #050a10;
        border-top: 1px solid #112233;
        display: flex;
        padding: 10px;
        align-items: center;
        gap: 10px;
    }

    .c2-prompt { color: var(--cyan); font-family: var(--mono); font-weight: bold; }
    
    #c2-cmd {
        flex: 1;
        background: transparent;
        border: none;
        color: #fff;
        font-family: var(--mono);
        font-size: 0.9rem;
        outline: none;
    }
    
    .c2-stats {
        position: absolute;
        bottom: 20px;
        right: 30px;
        font-family: var(--mono);
        font-size: 0.7rem;
        color: #444;
        text-align: right;
    }

    /* Ocultar el footer para inmersión full screen */
    footer { display: none !important; }
</style>

<div class="c2-container">
    
    <!-- Left: Beacon List -->
    <div class="c2-panel-left">
        <div class="c2-header">
            <span><i class="fas fa-network-wired"></i> <?= $lang==='es'?'BEACONS ACTIVOS':'ACTIVE BEACONS' ?></span>
            <span id="beacon-count" style="color: #fff; background: #ff2a2a; padding: 2px 6px; border-radius: 10px; font-size: 0.7rem;">0</span>
        </div>
        <ul class="beacon-list" id="beacon-list">
            <!-- Beacons injected via JS -->
        </ul>
    </div>

    <!-- Right: Terminal -->
    <div class="c2-panel-right">
        <div class="c2-header" id="terminal-header">
            <span><i class="fas fa-terminal"></i> <?= $lang==='es'?'TERMINAL [NINGUNO SELECCIONADO]':'TERMINAL [NO SELECTION]' ?></span>
            <span style="color:#555;">v4.2.1</span>
        </div>
        
        <div class="c2-terminal" id="c2-terminal">
            <div class="t-sys"><?= $lang==='es'?'Bienvenido al Simulador Command & Control (C2).':'Welcome to the Command & Control (C2) Simulator.' ?></div>
            <div class="t-sys"><?= $lang==='es'?'Esperando conexiones de beacons...':'Waiting for beacon check-ins...' ?></div>
        </div>

        <div class="c2-input-area">
            <span class="c2-prompt">beacon></span>
            <input type="text" id="c2-cmd" placeholder="<?= $lang==='es'?"Escribe 'help' para ver comandos...":"Type 'help' for commands..." ?>" disabled autocomplete="off" spellcheck="false">
        </div>
    </div>

</div>

<?php require __DIR__ . '/templates/footer.php'; ?>
