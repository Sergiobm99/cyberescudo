<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'OP: GHOST TRAFFIC — Mission Center' : 'OP: GHOST TRAFFIC — Mission Center';
$current_page = 'projects/mission-ghost.php';
require __DIR__ . '/../templates/header.php';
?>

<style>
    .briefing-container {
        max-width: 800px;
        margin: 4rem auto;
        background: #0a0a0a;
        border: 1px solid #333;
        border-top: 4px solid var(--danger);
        padding: 3rem;
        position: relative;
        box-shadow: 0 10px 30px rgba(0,0,0,0.8);
    }

    .briefing-header {
        border-bottom: 1px dashed #444;
        padding-bottom: 1.5rem;
        margin-bottom: 2rem;
    }

    .classification {
        color: var(--danger);
        font-family: var(--mono);
        font-weight: bold;
        letter-spacing: 2px;
        display: inline-block;
        border: 1px solid var(--danger);
        padding: 4px 10px;
        margin-bottom: 1rem;
        font-size: 0.8rem;
    }

    .briefing-title {
        font-size: 2.2rem;
        color: #fff;
        margin: 0;
        text-transform: uppercase;
        font-family: var(--mono);
    }

    .intel-block {
        background: rgba(255, 255, 255, 0.02);
        border-left: 2px solid var(--cyan);
        padding: 1.5rem;
        margin-bottom: 2rem;
        font-family: var(--mono);
        color: #ccc;
        line-height: 1.6;
    }

    .objective-list {
        list-style: none;
        padding: 0;
        color: #aaa;
    }

    .objective-list li {
        margin-bottom: 10px;
        padding-left: 20px;
        position: relative;
    }

    .objective-list li::before {
        content: '>';
        color: var(--cyan);
        position: absolute;
        left: 0;
        font-weight: bold;
    }

    .download-section {
        margin-top: 3rem;
        text-align: center;
        padding: 2rem;
        background: #050505;
        border: 1px solid #222;
    }

    .btn-download {
        display: inline-block;
        background: transparent;
        color: var(--cyan);
        border: 2px solid var(--cyan);
        padding: 12px 30px;
        font-family: var(--mono);
        font-size: 1.1rem;
        font-weight: bold;
        text-decoration: none;
        transition: all 0.3s;
        text-transform: uppercase;
    }

    .btn-download:hover {
        background: var(--cyan);
        color: #000;
        box-shadow: 0 0 15px rgba(0, 255, 255, 0.4);
    }

    .validation-hint {
        margin-top: 2rem;
        font-family: var(--mono);
        font-size: 0.85rem;
        color: #666;
        text-align: center;
    }
</style>

<main class="content-page">
    <div class="briefing-container">
        
        <div class="briefing-header">
            <div class="classification"><?= $lang === 'es' ? 'NIVEL: TOP SECRET' : 'LEVEL: TOP SECRET' ?></div>
            <h1 class="briefing-title">OP: GHOST TRAFFIC</h1>
            <div style="color: #666; font-family: var(--mono); margin-top: 10px;">
                <?= $lang === 'es' ? 'ID EXPEDIENTE:' : 'CASE ID:' ?> #FX-2024-091A
            </div>
        </div>

        <div class="intel-block">
            <strong style="color: #fff;"><?= $lang === 'es' ? '[ INFORME DE INTELIGENCIA ]' : '[ INTELLIGENCE BRIEF ]' ?></strong><br><br>
            <?= $lang === 'es' ? 
                'Nuestros sistemas IDS han detectado anomalías en el tráfico saliente desde la estación de trabajo de un empleado sospechoso. El firewall no bloqueó la conexión porque el atacante utilizó protocolos estándar de red (ICMP/DNS) para evadir la detección.<br><br>Hemos interceptado una captura de tráfico (.pcap). Creemos que el atacante ha exfiltrado una clave de acceso crítica.' : 
                'Our IDS systems detected anomalies in outbound traffic from a suspicious employee\'s workstation. The firewall failed to block the connection because the attacker used standard network protocols (ICMP/DNS) to evade detection.<br><br>We have intercepted a traffic capture (.pcap). We believe the attacker has exfiltrated a critical access key.' 
            ?>
        </div>

        <h3 style="color: #eee; margin-bottom: 1rem;"><?= $lang === 'es' ? 'Objetivos de la Misión:' : 'Mission Objectives:' ?></h3>
        <ul class="objective-list">
            <li><?= $lang === 'es' ? 'Descargar y analizar el archivo de captura de red.' : 'Download and analyze the network capture file.' ?></li>
            <li><?= $lang === 'es' ? 'Identificar el protocolo utilizado para el túnel (ICMP o DNS).' : 'Identify the protocol used for the tunnel (ICMP or DNS).' ?></li>
            <li><?= $lang === 'es' ? 'Extraer el payload (la clave secreta) de los paquetes.' : 'Extract the payload (the secret key) from the packets.' ?></li>
        </ul>

        <div class="download-section">
            <div style="margin-bottom: 1.5rem; color: #888; font-family: var(--mono);">
                <?= $lang === 'es' ? 'Archivo adjunto: evidencia_red.zip (Cifrado)' : 'Attachment: network_evidence.zip (Encrypted)' ?><br>
                <?= $lang === 'es' ? 'Contraseña del ZIP: infected' : 'ZIP Password: infected' ?>
            </div>
            
            <a href="<?= e(BASE_URL) ?>/assets/challenges/ghost_traffic.zip" class="btn-download" download="ghost_traffic.zip">
    [ <?= $lang === 'es' ? 'DESCARGAR EVIDENCIA' : 'DOWNLOAD EVIDENCE' ?> ]
</a>
        </div>

        <div class="validation-hint">
            <?= $lang === 'es' ? 'Para completar la misión, abre la terminal principal e introduce:' : 'To complete the mission, open the main terminal and enter:' ?><br>
            <strong style="color: var(--cyan);">submit OP-GHOST-TRAFFIC FLAG{tu_flag_aqui}</strong>
        </div>

    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>