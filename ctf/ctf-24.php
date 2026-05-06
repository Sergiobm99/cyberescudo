<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Reto CTF #24: Android Activity Bypass — CyberEscudo' : 'CTF Challenge #24: Android Activity Bypass — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Simulador de ADB para pentesting móvil.' : 'ADB simulator for mobile pentesting.';
$current_page = 'ctf/ctf-24.php';
require __DIR__ . '/../templates/header.php';

$feedback = "";
$flag = "";
$esExito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cmd'])) {
    $cmd = trim($_POST['cmd']);
    
    // Validar sintaxis del comando ADB
    // Objetivo: adb shell am start -n com.android.insecurebankv2/.DoTransfer
    
    // 1. Invocar adb shell
    $isAdbShell = preg_match('/^adb\s+shell\b/i', $cmd);
    
    // 2. Usar el Activity Manager para iniciar (am start)
    $isAmStart = preg_match('/\bam\s+start\b/i', $cmd);
    
    // 3. Pasar el flag de componente (-n) y el nombre exacto de la actividad
    // Permite formato corto /.DoTransfer o completo /com.android.insecurebankv2.DoTransfer
    $hasComponent = preg_match('/-n\s+com\.android\.insecurebankv2\/?\.DoTransfer/i', $cmd);

    if ($isAdbShell && $isAmStart && $hasComponent) {
        $esExito = true;
        $feedback = $lang === 'es' 
            ? "¡Bypass exitoso! La aplicación ha cargado directamente la pantalla de transferencias sin pedirte credenciales." 
            : "Successful bypass! The application directly loaded the transfer screen without asking for credentials.";
        $flag = "FLAG{android_activity_bypassed}";
    } else {
        $errores = [];
        if (!$isAdbShell) $errores[] = $lang === 'es' ? "Debe iniciar abriendo la terminal del dispositivo ('adb shell')" : "Must start by opening device terminal ('adb shell')";
        if (!$isAmStart) $errores[] = $lang === 'es' ? "Falta el comando del Activity Manager ('am start')" : "Missing Activity Manager command ('am start')";
        if (!$hasComponent) $errores[] = $lang === 'es' ? "Falta especificar el componente exacto (-n com.android.insecurebankv2/.DoTransfer)" : "Missing exact component specification (-n com.android.insecurebankv2/.DoTransfer)";
        
        $feedback = "[ERROR INTENT] " . implode(" | ", $errores);
    }
}
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 900px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// <?= $lang === 'es' ? 'RETO CTF 24' : 'CTF CHALLENGE 24' ?></span>
        <h1 style="text-align: center; margin-bottom: 2rem;"><?= $lang === 'es' ? 'Mobile Pentesting: Activity Bypass' : 'Mobile Pentesting: Activity Bypass' ?></h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <div style="background: #111; padding: 1.5rem; border-left: 4px solid #ff2a2a; margin-bottom: 2rem;">
                <h3 style="color: #ff2a2a; margin-top: 0;">📱 <?= $lang === 'es' ? 'Depuración de Dispositivo Activa' : 'Active Device Debugging' ?></h3>
                <p style="color: var(--white); font-size: 0.95rem; line-height: 1.6; margin-bottom: 0;">
                    <?= $lang === 'es' ? 'Tienes el teléfono conectado por USB con el modo desarrollador activo. No sabes la contraseña del banco, pero el desarrollador olvidó proteger la vista de transferencias.<br><br>Escribe el comando <strong>adb</strong> completo que abra la terminal remota (<strong>shell</strong>), invoque al <strong>Activity Manager</strong> y ejecute directamente el componente (<strong>-n</strong>) <code>com.android.insecurebankv2/.DoTransfer</code>.' : 'You have the phone connected via USB with developer mode active. You do not know the bank password, but the developer forgot to protect the transfers view.<br><br>Write the full <strong>adb</strong> command that opens the remote terminal (<strong>shell</strong>), invokes the <strong>Activity Manager</strong>, and directly starts the component (<strong>-n</strong>) <code>com.android.insecurebankv2/.DoTransfer</code>.' ?>
                </p>
            </div>
            
            <form method="POST" action="">
                <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                    Terminal (Localhost):
                </label>
                <div style="display: flex; align-items: center; background: #050505; border: 1px solid #333; padding: 0.5rem; border-radius: 4px; margin-bottom: 1.5rem;">
                    <span style="color: #00ff00; margin-right: 10px; font-family: var(--mono);">kali@attackbox:~$</span>
                    <input type="text" name="cmd" class="cyber-input" style="flex: 1; border: none; background: transparent; padding: 0; box-shadow: none;" placeholder="adb shell ..." autocomplete="off" value="<?= htmlspecialchars($_POST['cmd'] ?? '') ?>">
                </div>
                
                <button type="submit" style="width: 100%; background: var(--cyan); border: none; color: #000; padding: 15px; font-family: var(--mono); font-weight: bold; cursor: pointer; font-size: 1rem; transition: background 0.3s;">
                    <?= $lang === 'es' ? 'EJECUTAR INTENT (BYPASS LOGIN)' : 'EXECUTE INTENT (BYPASS LOGIN)' ?>
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
                        <?= $lang === 'es' ? 'Inserta tu bandera en la terminal de CyberEscudo:' : 'Insert your flag in the CyberEscudo terminal:' ?> <strong>submit <?= $flag ?></strong>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>