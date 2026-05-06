<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Reto CTF #14: Snort Rule Engineer — CyberEscudo' : 'CTF Challenge #14: Snort Rule Engineer — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Simulador de creación de reglas para Snort IDS.' : 'Snort IDS rule creation simulator.';
$current_page = 'ctf/ctf-14.php';
require __DIR__ . '/../templates/header.php';

$feedback = "";
$flag = "";
$esExito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rule'])) {
    $rule = trim($_POST['rule']);
    
    // Validaciones estrictas con Expresiones Regulares para Snort Syntax
    
    // 1. Debe empezar por 'alert tcp'
    $isAlertTcp = preg_match('/^alert\s+tcp\s+/i', $rule);
    
    // 2. Debe apuntar al puerto 21 de nuestra red (-> $HOME_NET 21)
    $hasPort21 = preg_match('/->\s+(?:\$HOME_NET|any)\s+21\b/i', $rule);
    
    // 3. Debe contener el string "USER root" dentro del bloque de opciones
    $hasContent = preg_match('/content\s*:\s*["\']USER\s+root["\']/i', $rule);
    
    // 4. Debe tener un SID válido (sid:numero)
    $hasSid = preg_match('/sid\s*:\s*\d+/i', $rule);
    
    // 5. Debe tener un msg (msg:"algo")
    $hasMsg = preg_match('/msg\s*:\s*["\'][^"\']+["\']/i', $rule);

    if ($isAlertTcp && $hasPort21 && $hasContent && $hasSid && $hasMsg) {
        $esExito = true;
        $feedback = $lang === 'es' 
            ? "¡Sintaxis de firma validada! La regla ha sido cargada en el motor IDS. El ataque FTP con el usuario root ha sido detectado y bloqueado." 
            : "Signature syntax validated! The rule has been loaded into the IDS engine. The FTP attack using the root user has been detected and blocked.";
        $flag = "FLAG{snort_rule_engineer}";
    } else {
        $errores = [];
        if (!$isAlertTcp) $errores[] = $lang === 'es' ? "Debe iniciar con 'alert tcp'" : "Must start with 'alert tcp'";
        if (!$hasPort21) $errores[] = $lang === 'es' ? "Debe apuntar al destino puerto 21 (-> \$HOME_NET 21)" : "Must point to destination port 21 (-> \$HOME_NET 21)";
        if (!$hasContent) $errores[] = $lang === 'es' ? "Falta buscar el contenido malicioso (content:\"USER root\")" : "Missing malicious content search (content:\"USER root\")";
        if (!$hasSid) $errores[] = $lang === 'es' ? "Toda regla debe tener un identificador (sid:100001)" : "Every rule needs an identifier (sid:100001)";
        if (!$hasMsg) $errores[] = $lang === 'es' ? "Toda regla debe tener un mensaje de alerta (msg:\"...\")" : "Every rule needs an alert message (msg:\"...\")";
        
        $feedback = "[ERROR DE SINTAXIS SNORT] " . implode(" | ", $errores);
    }
}
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 900px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// <?= $lang === 'es' ? 'RETO CTF 14' : 'CTF CHALLENGE 14' ?></span>
        <h1 style="text-align: center; margin-bottom: 2rem;"><?= $lang === 'es' ? 'Snort Rule Engineer' : 'Snort Rule Engineer' ?></h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <div style="background: #111; padding: 1.5rem; border-left: 4px solid var(--cyan); margin-bottom: 2rem;">
                <h3 style="color: var(--cyan); margin-top: 0;">📡 <?= $lang === 'es' ? 'Amenaza: Brute Force Detectado' : 'Threat: Brute Force Detected' ?></h3>
                <p style="color: var(--white); font-size: 0.95rem; line-height: 1.6; margin-bottom: 0;">
                    <?= $lang === 'es' ? 'Una red de bots (botnet) está intentando acceder a los servidores de la empresa a través del protocolo FTP (Puerto 21), utilizando conexiones no cifradas e intentando loguearse con el usuario <strong>root</strong>.<br><br>Escribe una regla de <strong>Snort</strong> completa que genere una alerta cuando detecte el payload <code>USER root</code> dirigiéndose a nuestra red local (<code>$HOME_NET</code>).' : 'A botnet is trying to access the company servers through the FTP protocol (Port 21), using unencrypted connections and trying to log in with the <strong>root</strong> user.<br><br>Write a complete <strong>Snort</strong> rule that generates an alert when it detects the payload <code>USER root</code> heading to our local network (<code>$HOME_NET</code>).' ?>
                </p>
            </div>
            
            <form method="POST" action="">
                <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                    [ /etc/snort/rules/local.rules ]
                </label>
                <div style="display: flex; align-items: center; background: #050505; border: 1px solid #333; padding: 0.5rem; border-radius: 4px; margin-bottom: 1.5rem;">
                    <span style="color: #4a90e2; margin-right: 10px; font-family: var(--mono);">1</span>
                    <input type="text" name="rule" class="cyber-input" style="flex: 1; border: none; background: transparent; padding: 0; box-shadow: none;" placeholder="alert tcp $EXTERNAL_NET any -> ..." autocomplete="off" value="<?= htmlspecialchars($_POST['rule'] ?? '') ?>">
                </div>
                
                <button type="submit" style="width: 100%; background: var(--cyan); border: none; color: #000; padding: 15px; font-family: var(--mono); font-weight: bold; cursor: pointer; font-size: 1rem; transition: background 0.3s;">
                    <?= $lang === 'es' ? 'CARGAR FIRMA AL MOTOR IDS' : 'LOAD SIGNATURE INTO IDS ENGINE' ?>
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
                        <?= $lang === 'es' ? 'Abre la terminal de CyberEscudo y canjea tu medalla:' : 'Open the CyberEscudo terminal and redeem your medal:' ?> <strong>submit <?= $flag ?></strong>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>