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
</style>

<main class="content-page" style="background: radial-gradient(circle at center, #0a0a0a 0%, #000 100%);">
    <div class="md-container" style="padding-top: 5rem; padding-bottom: 6rem;">
        <div style="border-left: 4px solid var(--danger); padding-left: 20px; margin-bottom: 40px;">
            <h1 style="font-family: var(--mono); font-size: 2.5rem; text-transform: uppercase;">
                <?= $lang === 'es' ? 'Centro de Operaciones: Black Ops' : 'Black Ops: Mission Center' ?>
            </h1>
            <p style="color: #666; font-family: var(--mono);">
                <?= $lang === 'es' ? '[ ACCESO AUTORIZADO ] — Selecciona un objetivo para iniciar el despliegue.' : '[ ACCESS GRANTED ] — Select a target to initiate deployment.' ?>
            </p>
        </div>

        <div class="mission-grid">
            <div class="mission-card">
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

            <div class="mission-card">
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
                   <a href="javascript:void(0);" onclick="alert('<?= $lang === 'es' ? 'Misión en desarrollo. Desbloqueo inminente.' : 'Mission in development. Unlocking soon.' ?>')" class="btn-deploy" style="border-color: #555; color: #aaa;"><?= $lang === 'es' ? 'VER CÓDIGO' : 'VIEW SOURCE' ?></a>
                </div>
            </div>

            <div class="mission-card" style="opacity: 0.5;">
                <div>
                    <span class="status-badge" style="background: #222; color: #555;">Locked</span>
                    <div style="color: #444; font-family: var(--mono); font-size: 0.8rem;">OP: DEEP_STATE</div>
                    <h3 class="mission-title"><?= $lang === 'es' ? 'Mensajes en la Sombra' : 'Shadow Messages' ?></h3>
                    <p style="color: #444; font-size: 0.9rem;">
                        <?= $lang === 'es' ? 'Misión bloqueada. Requiere validar las misiones anteriores para acceder a los archivos cifrados.' : 'Mission locked. Requires clearing previous missions to access encrypted files.' ?>
                    </p>
                </div>
                <div class="mission-footer" style="border-top-color: #111;">
                    <span>TIPO: Steganography</span>
                    <span style="color: #444; font-family: var(--mono);">[ OFFLINE ]</span>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>