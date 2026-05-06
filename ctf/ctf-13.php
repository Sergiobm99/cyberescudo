<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Reto CTF #13: Iptables Defender — CyberEscudo' : 'CTF Challenge #13: Iptables Defender — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Simulador de construcción de reglas de Firewall con iptables.' : 'Firewall rule building simulator with iptables.';
$current_page = 'ctf/ctf-13.php';
require __DIR__ . '/../templates/header.php';

$feedback = "";
$flag = "";
$esExito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cmd'])) {
    $cmd = trim($_POST['cmd']);
    
    // Validaciones estrictas con Expresiones Regulares
    // 1. Comando base
    $isIptables = preg_match('/^iptables\s+/i', $cmd);
    
    // 2. Añadir a la cadena de entrada: -A INPUT (o -I INPUT)
    $hasChain = preg_match('/-(A|I)\s+INPUT\b/i', $cmd);
    
    // 3. Protocolo TCP: -p tcp
    $hasProtocol = preg_match('/-p\s+tcp\b/i', $cmd);
    
    // 4. IP Atacante: -s 198.51.100.22
    $hasSource = preg_match('/-s\s+198\.51\.100\.22\b/i', $cmd);
    
    // 5. Puerto SSH: --dport 22
    $hasPort = preg_match('/--dport\s+22\b/i', $cmd);
    
    // 6. Acción DROP: -j DROP
    $hasTarget = preg_match('/-j\s+DROP\b/i', $cmd);

    if ($isIptables && $hasChain && $hasProtocol && $hasSource && $hasPort && $hasTarget) {
        $esExito = true;
        $feedback = $lang === 'es' 
            ? "¡Regla insertada en el Kernel! El ataque ha sido mitigado. El tráfico de la IP maliciosa hacia el puerto SSH ahora es ignorado silenciosamente." 
            : "Rule inserted into the Kernel! The attack has been mitigated. Traffic from the malicious IP to the SSH port is now silently dropped.";
        $flag = "FLAG{iptables_defender_wall}";
    } else {
        $errores = [];
        if (!$isIptables) $errores[] = $lang === 'es' ? "Debe empezar por 'iptables'" : "Must start with 'iptables'";
        if (!$hasChain) $errores[] = $lang === 'es' ? "Debe aplicar a la cadena de entrada (-A INPUT)" : "Must apply to input chain (-A INPUT)";
        if (!$hasProtocol) $errores[] = $lang === 'es' ? "Falta especificar el protocolo (-p tcp)" : "Missing protocol (-p tcp)";
        if (!$hasSource) $errores[] = $lang === 'es' ? "Falta la IP atacante (-s 198.51.100.22)" : "Missing attacker IP (-s 198.51.100.22)";
        if (!$hasPort) $errores[] = $lang === 'es' ? "Falta el puerto destino (--dport 22)" : "Missing destination port (--dport 22)";
        if (!$hasTarget) $errores[] = $lang === 'es' ? "La acción debe ser descartar silenciosamente (-j DROP)" : "Action must be silently drop (-j DROP)";
        
        $feedback = "[ERROR] " . implode(" | ", $errores);
    }
}
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 900px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// <?= $lang === 'es' ? 'RETO CTF 13' : 'CTF CHALLENGE 13' ?></span>
        <h1 style="text-align: center; margin-bottom: 2rem;"><?= $lang === 'es' ? 'Defensa Activa: Firewall' : 'Active Defense: Firewall' ?></h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <div style="background: #111; padding: 1.5rem; border-left: 4px solid #ff2a2a; margin-bottom: 2rem;">
                <h3 style="color: #ff2a2a; margin-top: 0;">🚨 <?= $lang === 'es' ? 'INCIDENTE EN CURSO' : 'ONGOING INCIDENT' ?></h3>
                <p style="color: var(--white); font-size: 0.95rem; line-height: 1.6; margin-bottom: 0;">
                    <?= $lang === 'es' ? 'Tu sistema de monitorización detecta miles de intentos de inicio de sesión SSH fallidos por segundo desde una misma dirección. <br><br><strong>IP Atacante:</strong> 198.51.100.22<br><strong>Servicio Atacado:</strong> SSH (Protocolo TCP, Puerto 22)<br><br>Escribe el comando <strong>iptables</strong> completo para <strong>añadir</strong> una regla a la cadena de entrada que <strong>descarte silenciosamente</strong> ese tráfico específico.' : 'Your monitoring system detects thousands of failed SSH login attempts per second from a single address. <br><br><strong>Attacker IP:</strong> 198.51.100.22<br><strong>Target Service:</strong> SSH (TCP Protocol, Port 22)<br><br>Write the complete <strong>iptables</strong> command to <strong>append</strong> a rule to the input chain that <strong>silently drops</strong> that specific traffic.' ?>
                </p>
            </div>
            
            <form method="POST" action="">
                <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                    <?= $lang === 'es' ? 'Línea de Comandos (Root):' : 'Command Line (Root):' ?>
                </label>
                <div style="display: flex; align-items: center; background: #050505; border: 1px solid #333; padding: 0.5rem; border-radius: 4px; margin-bottom: 1.5rem;">
                    <span style="color: #ff2a2a; margin-right: 10px; font-family: var(--mono);">root@server-prod:~#</span>
                    <input type="text" name="cmd" class="cyber-input" style="flex: 1; border: none; background: transparent; padding: 0; box-shadow: none;" placeholder="iptables -A ..." autocomplete="off" value="<?= htmlspecialchars($_POST['cmd'] ?? '') ?>">
                </div>
                
                <button type="submit" style="width: 100%; background: var(--cyan); border: none; color: #000; padding: 15px; font-family: var(--mono); font-weight: bold; cursor: pointer; font-size: 1rem; transition: background 0.3s;">
                    <?= $lang === 'es' ? 'APLICAR REGLA (KERNEL NETFILTER)' : 'APPLY RULE (NETFILTER KERNEL)' ?>
                </button>
            </form>

            <?php if($feedback !== ""): ?>
                <div style="margin-top: 2rem; padding: 1rem; background: <?= $esExito ? 'rgba(0,255,0,0.1)' : 'rgba(255,42,42,0.1)' ?>; border: 1px solid <?= $esExito ? '#00ff00' : '#ff2a2a' ?>; font-family: var(--mono); font-size: 0.9rem; color: <?= $esExito ? '#00ff00' : '#ff2a2a' ?>; line-height: 1.5;">
                    <?= $feedback ?>
                </div>
            <?php endif; ?>

            <?php if($esExito): ?>
                <div style="margin-top: 1.5rem; text-align: center; animation: pulse 2s infinite;">
                    <h3 style="color: #00ff00; margin: 1rem 0; font-family: var(--mono); letter-spacing: 2px;"><?= $flag ?></h3>
                    <p style="margin:0; font-size: 0.85rem; color: var(--cyan);">
                        <?= $lang === 'es' ? 'Terminal oculta. Ejecuta:' : 'Hidden terminal. Run:' ?> <strong>submit <?= $flag ?></strong>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>