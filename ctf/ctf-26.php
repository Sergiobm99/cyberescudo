<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Reto CTF #26: IPC Exploiter — CyberEscudo' : 'CTF Challenge #26: IPC Exploiter — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Simulador de extracción de datos mediante Content Providers en Android.' : 'Data extraction simulator via Android Content Providers.';
$current_page = 'ctf/ctf-26.php';
require __DIR__ . '/../templates/header.php';

$feedback = "";
$flag = "";
$esExito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cmd'])) {
    $cmd = trim($_POST['cmd']);
    
    // Validar sintaxis del comando ADB Content Query
    // Objetivo: content query --uri content://com.bank.secure.provider/keys
    
    // 1. Comando base
    $isContentQuery = preg_match('/^content\s+query\b/i', $cmd);
    
    // 2. Parámetro URI
    $hasUriFlag = preg_match('/--uri\b/i', $cmd);
    
    // 3. La URI exacta a inyectar
    $hasExactUri = preg_match('/content:\/\/com\.bank\.secure\.provider\/keys/i', $cmd);

    if ($isContentQuery && $hasUriFlag && $hasExactUri) {
        $esExito = true;
        $feedback = $lang === 'es' 
            ? "¡Exfiltración exitosa! Has utilizado el Content Provider mal configurado para volcar todas las claves criptográficas de la memoria del teléfono." 
            : "Successful exfiltration! You used the misconfigured Content Provider to dump all cryptographic keys from the phone's memory.";
        $flag = "FLAG{android_ipc_pwned}";
    } else {
        $errores = [];
        if (!$isContentQuery) $errores[] = $lang === 'es' ? "Debe iniciar con el comando de consulta de contenido ('content query')" : "Must start with the content query command ('content query')";
        if (!$hasUriFlag) $errores[] = $lang === 'es' ? "Falta el flag para especificar la ruta (--uri)" : "Missing flag to specify the path (--uri)";
        if (!$hasExactUri) $errores[] = $lang === 'es' ? "La URI exacta especificada en el informe no coincide" : "The exact URI specified in the report does not match";
        
        $feedback = "[SECURITY EXCEPTION] " . implode(" | ", $errores);
    }
}
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 900px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// <?= $lang === 'es' ? 'RETO CTF 26' : 'CTF CHALLENGE 26' ?></span>
        <h1 style="text-align: center; margin-bottom: 2rem;"><?= $lang === 'es' ? 'IPC: Provider Hijacking' : 'IPC: Provider Hijacking' ?></h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <div style="background: #111; padding: 1.5rem; border-left: 4px solid #ff2a2a; margin-bottom: 2rem;">
                <h3 style="color: #ff2a2a; margin-top: 0;">📱 <?= $lang === 'es' ? 'Vulnerabilidad Detectada en AndroidManifest' : 'Vulnerability Detected in AndroidManifest' ?></h3>
                <p style="color: var(--white); font-size: 0.95rem; line-height: 1.6; margin-bottom: 1rem;">
                    <?= $lang === 'es' ? 'Haciendo ingeniería inversa a la aplicación, has encontrado la siguiente declaración de un proveedor de contenido interno que fue exportado por error a todo el sistema operativo:' : 'While reverse engineering the application, you found the following declaration of an internal content provider that was mistakenly exported to the entire operating system:' ?>
                </p>
                <code style="display:block; background:#000; padding:10px; color:#4a90e2; border-radius:4px; margin-bottom:1rem; word-break: break-all;">
                    &lt;provider android:name=".KeysProvider" android:authorities="com.bank.secure.provider" android:exported="true" /&gt;
                </code>
                <p style="color: var(--white); font-size: 0.95rem; line-height: 1.6; margin-bottom: 0;">
                    <?= $lang === 'es' ? 'Estás dentro de la terminal <code>adb shell</code>. Escribe el comando completo para realizar una consulta de contenido (<strong>content query</strong>) pasándole la ruta exacta (<strong>--uri</strong>) que apunte al recurso <code>content://com.bank.secure.provider/keys</code>.' : 'You are inside the <code>adb shell</code> terminal. Write the full command to perform a <strong>content query</strong> passing the exact path (<strong>--uri</strong>) pointing to the resource <code>content://com.bank.secure.provider/keys</code>.' ?>
                </p>
            </div>
            
            <form method="POST" action="">
                <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                    ADB Shell:
                </label>
                <div style="display: flex; align-items: center; background: #050505; border: 1px solid #333; padding: 0.5rem; border-radius: 4px; margin-bottom: 1.5rem;">
                    <span style="color: #00ff00; margin-right: 10px; font-family: var(--mono);">root@android:/ #</span>
                    <input type="text" name="cmd" class="cyber-input" style="flex: 1; border: none; background: transparent; padding: 0; box-shadow: none;" placeholder="content query ..." autocomplete="off" value="<?= htmlspecialchars($_POST['cmd'] ?? '') ?>">
                </div>
                
                <button type="submit" style="width: 100%; background: var(--cyan); border: none; color: #000; padding: 15px; font-family: var(--mono); font-weight: bold; cursor: pointer; font-size: 1rem; transition: background 0.3s;">
                    <?= $lang === 'es' ? 'VOLCAR BASE DE DATOS LOCAL' : 'DUMP LOCAL DATABASE' ?>
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
                        <?= $lang === 'es' ? 'Introduce la flag en la terminal principal de CyberEscudo:' : 'Enter the flag in the main CyberEscudo terminal:' ?> <strong>submit <?= $flag ?></strong>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>