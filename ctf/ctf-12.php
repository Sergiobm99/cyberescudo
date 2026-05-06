<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Reto CTF #12: Python Scanner Dev — CyberEscudo' : 'CTF Challenge #12: Python Scanner Dev — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Simulador de desarrollo de escáner en Python.' : 'Python scanner development simulator.';
$current_page = 'ctf/ctf-12.php';
require __DIR__ . '/../templates/header.php';

$feedback = "";
$flag = "";
$esExito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ans1 = str_replace(' ', '', strtolower(trim($_POST['q1'] ?? '')));
    $ans2 = str_replace(' ', '', strtolower(trim($_POST['q2'] ?? '')));
    $ans3 = str_replace(' ', '', strtolower(trim($_POST['q3'] ?? '')));
    
    // Validaciones técnicas de Python y HTTP
    // 1. Sockets UDP: socket.SOCK_DGRAM
    $isQ1Correct = ($ans1 === 'socket.sock_dgram' || $ans1 === 'sock_dgram'); 
    
    // 2. Deshabilitar SSL en requests: verify=False
    $isQ2Correct = ($ans2 === 'verify=false' || $ans2 === 'verify=0'); 
    
    // 3. Cabecera Clickjacking: X-Frame-Options
    $isQ3Correct = ($ans3 === 'x-frame-options'); 

    if ($isQ1Correct && $isQ2Correct && $isQ3Correct) {
        $esExito = true;
        $feedback = $lang === 'es' 
            ? "¡Bugs solucionados! El código ha compilado correctamente y el escáner funciona al 100%." 
            : "Bugs fixed! The code compiled successfully and the scanner is fully operational.";
        $flag = "FLAG{python_scanner_architect}";
    } else {
        $errores = [];
        if (!$isQ1Correct) $errores[] = $lang === 'es' ? "Error en Sockets (Q1)" : "Socket Error (Q1)";
        if (!$isQ2Correct) $errores[] = $lang === 'es' ? "Error en HTTPS (Q2)" : "HTTPS Error (Q2)";
        if (!$isQ3Correct) $errores[] = $lang === 'es' ? "Error en Cabeceras (Q3)" : "Headers Error (Q3)";
        
        $feedback = $lang === 'es' 
            ? "[CRÍTICO] Fallo en la compilación del módulo de escaneo: " . implode(", ", $errores)
            : "[CRITICAL] Scan module build failed: " . implode(", ", $errores);
    }
}
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 900px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// <?= $lang === 'es' ? 'RETO CTF 12' : 'CTF CHALLENGE 12' ?></span>
        <h1 style="text-align: center; margin-bottom: 2rem;"><?= $lang === 'es' ? 'Scanner Debugging' : 'Scanner Debugging' ?></h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <p style="color: var(--gray); margin-bottom: 1.5rem; text-align: center; font-size: 1.05rem;">
                <?= $lang === 'es' ? 'El código base de tu escáner <code>cyber_scan.py</code> necesita ser reparado para soportar funciones avanzadas. Responde correctamente a los parámetros solicitados para desplegar la versión final.' : 'The codebase of your <code>cyber_scan.py</code> scanner needs to be repaired to support advanced functions. Answer the requested parameters correctly to deploy the final release.' ?>
            </p>
            
            <form method="POST" action="">
                
                <div style="margin-bottom: 1.5rem; background: #050505; border: 1px solid #333; padding: 1.5rem; border-radius: 5px;">
                    <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                        [1] <?= $lang === 'es' ? 'Escaneo UDP' : 'UDP Scanning' ?>
                    </label>
                    <p style="color: #aaa; font-size: 0.9rem; margin-top: 0; margin-bottom: 10px;">
                        <?= $lang === 'es' ? 'Para escanear puertos TCP usamos <code>socket.SOCK_STREAM</code>. ¿Qué constante de la librería socket debemos pasar para escanear puertos UDP (como DNS o SNMP)?' : 'To scan TCP ports we use <code>socket.SOCK_STREAM</code>. What constant from the socket library must we pass to scan UDP ports (like DNS or SNMP)?' ?>
                    </p>
                    <input type="text" name="q1" class="cyber-input" style="width: 100%;" placeholder="Ej: socket.XXX_XXXXX" autocomplete="off" value="<?= htmlspecialchars($_POST['q1'] ?? '') ?>">
                </div>

                <div style="margin-bottom: 1.5rem; background: #050505; border: 1px solid #333; padding: 1.5rem; border-radius: 5px;">
                    <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                        [2] <?= $lang === 'es' ? 'Evasión de Certificados HTTPS' : 'HTTPS Certificate Evasion' ?>
                    </label>
                    <p style="color: #aaa; font-size: 0.9rem; margin-top: 0; margin-bottom: 10px;">
                        <?= $lang === 'es' ? 'El módulo <code>requests.get(url, ...)</code> falla al escanear intranets locales por certificados SSL autofirmados. ¿Qué argumento exacto debes pasarle a la función para desactivar la verificación SSL?' : 'The <code>requests.get(url, ...)</code> module crashes when scanning local intranets due to self-signed SSL certs. What exact argument must you pass to the function to disable SSL verification?' ?>
                    </p>
                    <input type="text" name="q2" class="cyber-input" style="width: 100%;" placeholder="Ej: timeout=5" autocomplete="off" value="<?= htmlspecialchars($_POST['q2'] ?? '') ?>">
                </div>

                <div style="margin-bottom: 2rem; background: #050505; border: 1px solid #333; padding: 1.5rem; border-radius: 5px;">
                    <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                        [3] <?= $lang === 'es' ? 'Auditoría de Cabeceras' : 'Headers Audit' ?>
                    </label>
                    <p style="color: #aaa; font-size: 0.9rem; margin-top: 0; margin-bottom: 10px;">
                        <?= $lang === 'es' ? 'Has programado el escáner para que alerte sobre vulnerabilidades de UI Redressing (Clickjacking). ¿Qué cabecera HTTP específica (añadida en Python como string) debe buscar el escáner para ver si la web está protegida?' : 'You programmed the scanner to alert on UI Redressing (Clickjacking) vulnerabilities. What specific HTTP header (added in Python as a string) should the scanner look for to see if the site is protected?' ?>
                    </p>
                    <input type="text" name="q3" class="cyber-input" style="width: 100%;" placeholder="Ej: Strict-Transport-Security" autocomplete="off" value="<?= htmlspecialchars($_POST['q3'] ?? '') ?>">
                </div>
                
                <button type="submit" style="width: 100%; background: var(--cyan); border: none; color: #000; padding: 15px; font-family: var(--mono); font-weight: bold; cursor: pointer; font-size: 1rem; transition: background 0.3s;">
                    <?= $lang === 'es' ? 'COMPILAR Y DESPLEGAR ESCÁNER' : 'BUILD AND DEPLOY SCANNER' ?>
                </button>
            </form>

            <?php if($feedback !== ""): ?>
                <div style="margin-top: 2rem; padding: 1rem; background: <?= $esExito ? 'rgba(0,255,0,0.1)' : 'rgba(255,42,42,0.1)' ?>; border: 1px solid <?= $esExito ? '#00ff00' : '#ff2a2a' ?>; font-family: var(--mono); font-size: 0.9rem; color: <?= $esExito ? '#00ff00' : '#ff2a2a' ?>; text-align: center;">
                    <?= $feedback ?>
                </div>
            <?php endif; ?>

            <?php if($esExito): ?>
                <div style="margin-top: 1.5rem; text-align: center; animation: pulse 2s infinite;">
                    <h3 style="color: #00ff00; margin: 1rem 0; font-family: var(--mono); letter-spacing: 2px;"><?= $flag ?></h3>
                    <p style="margin:0; font-size: 0.85rem; color: var(--cyan);">
                        <?= $lang === 'es' ? 'Desbloquea tu rango de Desarrollador en la terminal:' : 'Unlock your Developer rank in the terminal:' ?> <strong>submit <?= $flag ?></strong>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>