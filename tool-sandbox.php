<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $lang === 'es' ? 'Sandbox-X Malware Analyzer — CyberEscudo' : 'Sandbox-X Malware Analyzer — CyberEscudo';
require __DIR__ . '/templates/header.php';
?>

<style>
    .sandbox-container { max-width: 1400px; margin: 3rem auto; padding: 0 1.5rem; display: flex; flex-direction: column; gap: 2rem; }
    
    .sandbox-header { text-align: center; }
    .sandbox-title { font-family: var(--mono); color: var(--cyan); font-size: 2.5rem; text-transform: uppercase; letter-spacing: 2px; }
    .sandbox-subtitle { color: #888; font-size: 0.95rem; font-family: var(--mono); margin-top: 10px; }

    /* Control Panel */
    .control-panel { background: rgba(0,0,0,0.4); border: 1px solid #333; padding: 20px; border-radius: 8px; display: flex; flex-wrap: wrap; gap: 15px; align-items: center; justify-content: space-between; }
    
    .sample-selector { flex: 1; min-width: 250px; background: #000; color: #fff; border: 1px solid var(--cyan); padding: 12px; font-family: var(--mono); border-radius: 4px; font-size: 1rem; }
    
    .btn-detonate { background: transparent; color: #ff2a2a; border: 1px solid #ff2a2a; padding: 12px 30px; font-family: var(--mono); font-weight: bold; text-transform: uppercase; cursor: pointer; border-radius: 4px; font-size: 1.1rem; transition: 0.3s; }
    .btn-detonate:hover:not(:disabled) { background: #ff2a2a; color: #fff; box-shadow: 0 0 15px rgba(255,42,42,0.4); }
    .btn-detonate:disabled { border-color: #444; color: #666; cursor: not-allowed; }

    /* Analysis Dashboard */
    .analysis-dashboard { display: grid; grid-template-columns: 1fr; gap: 20px; display: none; }
    @media (min-width: 992px) { .analysis-dashboard { grid-template-columns: 1.5fr 1fr; } }

    .panel { background: #050505; border: 1px solid #222; border-radius: 8px; padding: 15px; display: flex; flex-direction: column; }
    .panel-title { color: var(--cyan); font-family: var(--mono); font-size: 1.1rem; margin-bottom: 15px; border-bottom: 1px solid #222; padding-bottom: 10px; }

    /* Process Tree */
    .process-tree { font-family: var(--mono); font-size: 0.85rem; line-height: 1.8; color: #ccc; }
    .process-node { opacity: 0; transform: translateY(-10px); transition: all 0.5s ease; padding-left: 20px; position: relative; border-left: 1px dashed #444; margin-left: 10px; }
    .process-node::before { content: '├─ '; position: absolute; left: 0; color: #444; }
    .process-node.visible { opacity: 1; transform: translateY(0); }
    
    .proc-name { color: #fff; font-weight: bold; }
    .proc-pid { color: #888; font-size: 0.75rem; }
    .proc-cmd { color: #f0a000; display: block; margin-left: 20px; margin-top: 5px; background: rgba(240, 160, 0, 0.1); padding: 5px; border-radius: 3px; word-break: break-all; }
    .proc-bad { color: #ff2a2a; text-shadow: 0 0 5px rgba(255,42,42,0.5); }

    /* Net Logs */
    .net-logs { background: #000; border: 1px solid #111; border-radius: 4px; padding: 10px; height: 250px; overflow-y: auto; font-family: var(--mono); font-size: 0.8rem; }
    .net-line { margin-bottom: 5px; opacity: 0; animation: fadeIn 0.3s forwards; border-left: 2px solid transparent; padding-left: 5px; }
    @keyframes fadeIn { to { opacity: 1; } }
    .net-dns { border-left-color: var(--cyan); }
    .net-http { border-left-color: #f0a000; }
    .net-tcp { border-left-color: #ff2a2a; }

    /* Status Indicator */
    .status-box { font-family: var(--mono); font-size: 1.5rem; text-align: center; color: #888; margin: 2rem 0; font-weight: bold; }
    .status-pulse { animation: pulse 1s infinite; }

    /* Final Report */
    .report-section { background: rgba(0,255,255,0.05); border: 1px solid var(--cyan); border-radius: 8px; padding: 20px; display: none; margin-top: 2rem; }
    .report-grid { display: grid; grid-template-columns: 1fr; gap: 20px; margin-top: 15px; }
    @media (min-width: 992px) { .report-grid { grid-template-columns: 1fr 1fr; } }
    
    .mitre-tag { display: inline-block; background: rgba(255, 42, 42, 0.1); border: 1px solid #ff2a2a; color: #ff2a2a; padding: 4px 8px; border-radius: 4px; font-family: var(--mono); font-size: 0.75rem; margin-right: 5px; margin-bottom: 5px; }
    .yara-code { background: #000; color: #00ff41; padding: 15px; border-radius: 4px; font-family: var(--mono); font-size: 0.8rem; white-space: pre-wrap; overflow-x: auto; border: 1px solid #333; }

</style>

<main class="content-page">
    <div class="sandbox-container">
        
        <div class="sandbox-header">
            <h1 class="sandbox-title"><i class="fas fa-biohazard" style="color: #ff2a2a;"></i> SANDBOX-X</h1>
            <p class="sandbox-subtitle"><?= $lang === 'es' ? 'Análisis Dinámico de Malware (Behavioral Analysis Engine)' : 'Dynamic Malware Analysis (Behavioral Analysis Engine)' ?></p>
        </div>

        <div class="control-panel">
            <div style="flex: 1;">
                <label style="color: #888; font-family: var(--mono); font-size: 0.8rem; display: block; margin-bottom: 5px;"><?= $lang === 'es' ? 'SELECCIONAR MUESTRA (PAYLOAD)' : 'SELECT SAMPLE (PAYLOAD)' ?></label>
                <select class="sample-selector" id="sample-selector">
                    <option value="macro">URGENTE_FACTURA_03.docx (Office Macro / Emotet style)</option>
                    <option value="ransom">Windows_Update_BETA.exe (Ransomware / CryptoLocker style)</option>
                    <option value="powershell">SystemDiagnostics.vbs (Fileless / C2 Beacon)</option>
                    <option value="wannacry">WanaDecryptor.exe (WannaCry Worm / SMB Exploit)</option>
                    <option value="stealer">Discord_Nitro_Free.exe (InfoStealer / RedLine style)</option>
                    <option value="miner">Adobe_Flash_Setup.exe (Cryptominer / Process Hollowing)</option>
                </select>
            </div>
            <button class="btn-detonate" id="btn-detonate"><?= $lang === 'es' ? 'Detonar Payload' : 'Detonate Payload' ?></button>
        </div>

        <div class="status-box" id="status-box">
            <?= $lang === 'es' ? '[ LISTO PARA EJECUTAR ]' : '[ READY TO EXECUTE ]' ?>
        </div>

        <div class="analysis-dashboard" id="analysis-dashboard">
            <div class="panel">
                <div class="panel-title"><?= $lang === 'es' ? 'ÁRBOL DE PROCESOS (Process Execution Flow)' : 'PROCESS TREE (Execution Flow)' ?></div>
                <div class="process-tree" id="process-tree">
                    <!-- Tree injected via JS -->
                </div>
            </div>
            
            <div class="panel">
                <div class="panel-title"><?= $lang === 'es' ? 'MONITOR DE RED (Network Activity)' : 'NETWORK MONITOR (Activity)' ?></div>
                <div class="net-logs" id="net-logs">
                    <!-- Net logs injected via JS -->
                </div>
            </div>
        </div>

        <div class="report-section" id="report-section">
            <h2 style="color: var(--cyan); font-family: var(--mono); margin-bottom: 10px;"><i class="fas fa-file-alt"></i> <?= $lang === 'es' ? 'REPORTE DE INTELIGENCIA DE AMENAZAS' : 'THREAT INTELLIGENCE REPORT' ?></h2>
            <div style="color: #ff2a2a; font-family: var(--mono); font-weight: bold; font-size: 1.2rem; margin-bottom: 15px;"><?= $lang === 'es' ? 'VEREDICTO:' : 'VERDICT:' ?> <span style="background: #ff2a2a; color: #fff; padding: 2px 8px; border-radius: 3px;"><?= $lang === 'es' ? 'MALICIOSO' : 'MALICIOUS' ?></span></div>
            
            <div class="report-grid">
                <div>
                    <h3 style="color: #aaa; font-family: var(--mono); font-size: 0.9rem; margin-bottom: 10px;">MITRE ATT&CK TACTICS & TECHNIQUES</h3>
                    <div id="mitre-container">
                        <!-- MITRE tags injected via JS -->
                    </div>
                </div>
                <div>
                    <h3 style="color: #aaa; font-family: var(--mono); font-size: 0.9rem; margin-bottom: 10px;">YARA RULE (Auto-Generated)</h3>
                    <div class="yara-code" id="yara-container">
                        <!-- YARA injected via JS -->
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>



<?php require __DIR__ . '/templates/footer.php'; ?>
