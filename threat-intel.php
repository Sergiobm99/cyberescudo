<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $lang === 'es' ? 'Threat Intelligence — CyberEscudo' : 'Threat Intelligence — CyberEscudo';
require __DIR__ . '/templates/header.php';
?>

<style>
    .intel-container { max-width: 1300px; margin: 3rem auto; padding: 0 1.5rem; }
    
    .intel-header { text-align: center; margin-bottom: 3rem; }
    .intel-title { font-family: var(--mono); color: var(--cyan); font-size: 2.5rem; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 0.5rem; }
    .intel-subtitle { color: #888; font-size: 0.95rem; font-family: var(--mono); }
    
    .live-indicator { 
        display: inline-block; width: 10px; height: 10px; 
        background: #ff2a2a; border-radius: 50%; margin-right: 8px;
        box-shadow: 0 0 10px #ff2a2a; animation: pulse-live 1.5s infinite;
    }
    @keyframes pulse-live { 0% {opacity: 1;} 50% {opacity: 0.4;} 100% {opacity: 1;} }

    .intel-grid { display: grid; grid-template-columns: 1fr; gap: 2rem; }
    @media (min-width: 992px) { .intel-grid { grid-template-columns: 1fr 1fr; } }

    .intel-panel {
        background: rgba(0, 0, 0, 0.4); border: 1px solid var(--border);
        border-radius: 8px; padding: 1.5rem;
        box-shadow: 0 0 20px rgba(0, 255, 255, 0.02);
    }
    .panel-title {
        font-family: var(--mono); color: #fff; font-size: 1.2rem;
        border-bottom: 1px solid rgba(0, 255, 255, 0.2);
        padding-bottom: 1rem; margin-bottom: 1.5rem;
        display: flex; align-items: center; gap: 10px;
    }

    .feed-list { list-style: none; padding: 0; margin: 0; }
    .feed-item {
        background: rgba(255, 255, 255, 0.02);
        border-left: 3px solid var(--cyan);
        padding: 1rem; margin-bottom: 1rem; border-radius: 0 4px 4px 0;
        transition: all 0.3s ease;
    }
    .feed-item:hover {
        background: rgba(0, 255, 255, 0.05); border-left-color: #fff;
        transform: translateX(5px);
    }
    .feed-item.cve-item { border-left-color: #ff2a2a; }
    .feed-item.cve-item:hover { border-left-color: #ff5050; background: rgba(255, 42, 42, 0.05); }

    .feed-date { font-family: var(--mono); font-size: 0.75rem; color: #888; margin-bottom: 0.5rem; display: block; }
    .feed-title { color: #fff; font-size: 0.95rem; font-weight: 600; text-decoration: none; display: block; line-height: 1.4; margin-bottom: 0.5rem;}
    .feed-title:hover { color: var(--cyan); }
    .cve-item .feed-title:hover { color: #ff5050; }
    
    .feed-badge {
        display: inline-block; font-family: var(--mono); font-size: 0.65rem;
        padding: 2px 6px; border-radius: 3px; font-weight: bold;
    }
    .badge-cve { background: rgba(255, 42, 42, 0.1); color: #ff2a2a; border: 1px solid rgba(255, 42, 42, 0.3); }
    .badge-news { background: rgba(0, 255, 255, 0.1); color: var(--cyan); border: 1px solid rgba(0, 255, 255, 0.3); }
</style>

<main class="content-page" style="max-width: 100%;">
    <div class="intel-container">
        <div class="intel-header">
            <h1 class="intel-title"><span class="live-indicator"></span><?= $lang === 'es' ? 'Centro de Inteligencia' : 'Intelligence Center' ?></h1>
            <p class="intel-subtitle"><?= $lang === 'es' ? 'Monitorización global de vulnerabilidades (0-days) y brechas de datos en tiempo real.' : 'Global real-time monitoring of vulnerabilities (0-days) and data breaches.' ?></p>
        </div>

        <div class="intel-grid">
            <div class="intel-panel">
                <h2 class="panel-title">
                    <span style="color: #ff2a2a;">[+]</span> <?= $lang === 'es' ? 'Vulnerabilidades Críticas (CVE)' : 'Critical Vulnerabilities (CVE)' ?>
                </h2>
                <ul class="feed-list" id="full-cve-feed">
                    <div style="text-align: center; padding: 2rem; color: var(--cyan); font-family: var(--mono);">
                        <div class="cyber-spinner"></div><br><br>
                        <?= $lang === 'es' ? 'INTERCEPTANDO DATOS...' : 'INTERCEPTING DATA...' ?>
                    </div>
                </ul>
            </div>

            <div class="intel-panel">
                <h2 class="panel-title">
                    <span style="color: var(--cyan);">[+]</span> <?= $lang === 'es' ? 'Brechas y Alertas Globales' : 'Global Breaches & Alerts' ?>
                </h2>
                <ul class="feed-list" id="full-news-feed">
                    <div style="text-align: center; padding: 2rem; color: var(--cyan); font-family: var(--mono);">
                        <div class="cyber-spinner"></div><br><br>
                        <?= $lang === 'es' ? 'INTERCEPTANDO DATOS...' : 'INTERCEPTING DATA...' ?>
                    </div>
                </ul>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/templates/footer.php'; ?>