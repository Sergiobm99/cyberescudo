<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $lang === 'es' ? 'Visual Code Auditor — CyberEscudo' : 'Visual Code Auditor — CyberEscudo';
require __DIR__ . '/templates/header.php';
?>

<style>
    .auditor-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 70px);
        background: #020508;
        padding: 15px;
        gap: 15px;
    }
    @media (min-width: 992px) {
        .auditor-container { flex-direction: row; }
    }

    .panel-box {
        background: #050a10;
        border: 1px solid #112233;
        border-radius: 4px;
        display: flex;
        flex-direction: column;
    }

    .editor-panel { flex: 2; position: relative; }
    .findings-panel { flex: 1; overflow-y: auto; }

    .panel-header {
        background: #0a1520;
        padding: 10px 15px;
        font-family: var(--mono);
        color: var(--cyan);
        border-bottom: 1px solid #112233;
        font-size: 0.9rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .code-textarea {
        flex: 1;
        width: 100%;
        background: transparent;
        color: #fff;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.9rem;
        border: none;
        padding: 15px;
        resize: none;
        outline: none;
        line-height: 1.5;
    }

    .btn-audit {
        background: rgba(0, 255, 255, 0.1);
        color: var(--cyan);
        border: 1px solid var(--cyan);
        padding: 6px 15px;
        font-family: var(--mono);
        font-weight: bold;
        cursor: pointer;
        border-radius: 3px;
        transition: 0.2s;
    }
    .btn-audit:hover { background: var(--cyan); color: #000; box-shadow: 0 0 10px rgba(0,255,255,0.4); }

    .findings-list {
        padding: 15px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .finding-card {
        background: #000;
        border: 1px solid #223;
        border-left: 3px solid #888;
        padding: 10px;
        border-radius: 4px;
        font-family: var(--mono);
    }
    .finding-card.critical { border-left-color: #ff2a2a; background: rgba(255,42,42,0.05); }
    .finding-card.high { border-left-color: #f0a000; background: rgba(240,160,0,0.05); }
    
    .f-title { font-weight: bold; font-size: 0.9rem; margin-bottom: 5px; color: #fff; }
    .finding-card.critical .f-title { color: #ff2a2a; }
    .finding-card.high .f-title { color: #f0a000; }
    
    .f-desc { font-size: 0.8rem; color: #aaa; margin-bottom: 5px; }
    .f-line { font-size: 0.75rem; color: var(--cyan); background: rgba(0,255,255,0.1); display: inline-block; padding: 2px 5px; border-radius: 3px; }

    /* Templates for quick load */
    .template-selector {
        background: #000;
        color: #fff;
        border: 1px solid #334;
        padding: 5px;
        font-family: var(--mono);
        outline: none;
    }
</style>

<div class="auditor-container">
    
    <div class="panel-box editor-panel">
        <div class="panel-header">
            <div>
                <i class="fas fa-code"></i> <?= $lang==='es'?'CÓDIGO FUENTE':'SOURCE CODE' ?>
                <select id="auditor-template" class="template-selector" style="margin-left: 10px;">
                    <option value=""><?= $lang==='es'?'-- Cargar Ejemplo --':'-- Load Sample --' ?></option>
                    <option value="solidity">Solidity (Reentrancy)</option>
                    <option value="php">PHP (SQLi & XSS)</option>
                    <option value="python">Python (Command Injection)</option>
                </select>
            </div>
            <button class="btn-audit" id="btn-audit"><i class="fas fa-search"></i> <?= $lang==='es'?'ESCANEAR CÓDIGO':'SCAN CODE' ?></button>
        </div>
        <textarea id="auditor-code" class="code-textarea" placeholder="<?= $lang==='es'?"// Pega tu código PHP, Python o Solidity aquí...\n// Haz clic en ESCANEAR CÓDIGO para analizar vulnerabilidades.":"// Paste your PHP, Python, or Solidity code here...\n// Click SCAN CODE to analyze for vulnerabilities." ?>"></textarea>
    </div>

    <div class="panel-box findings-panel">
        <div class="panel-header">
            <span><i class="fas fa-bug"></i> <?= $lang==='es'?'HALLAZGOS DE AUDITORÍA':'AUDIT FINDINGS' ?></span>
            <span id="findings-count" style="background:#223; padding:2px 8px; border-radius:10px;">0</span>
        </div>
        <div class="findings-list" id="findings-list">
            <div style="text-align:center; color:#555; padding: 20px;"><?= $lang==='es'?'Ningún código escaneado todavía.':'No code scanned yet.' ?></div>
        </div>
    </div>

</div>

<?php require __DIR__ . '/templates/footer.php'; ?>
