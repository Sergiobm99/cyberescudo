<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Reto CTF #16: Docker Socket Escape — CyberEscudo' : 'CTF Challenge #16: Docker Socket Escape — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Simulador de escape de contenedores abusando del socket de Docker.' : 'Container escape simulator abusing the Docker socket.';
$current_page = 'ctf/ctf-16.php';
require __DIR__ . '/../templates/header.php';

$feedback = "";
$flag = "";
$esExito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cmd'])) {
    $cmd = trim($_POST['cmd']);
    
    // Validar el payload de escape del Docker Socket
    // docker -H unix:///var/run/docker.sock run -v /:/mnt alpine chroot /mnt sh
    
    $isDocker = preg_match('/^docker\b/i', $cmd);
    $hasSocket = preg_match('/-H\s+unix:\/\/\/var\/run\/docker\.sock/i', $cmd);
    $hasRun = preg_match('/\brun\b/i', $cmd);
    
    // Debe montar la raíz (/) hacia una carpeta de destino (ej: /mnt o /host)
    $hasMount = preg_match('/-v\s+\/:\/[a-zA-Z0-9_-]+/i', $cmd);
    
    // Debe usar una imagen base ligera como alpine, ubuntu o debian
    $hasImage = preg_match('/\b(alpine|ubuntu|debian|busybox)\b/i', $cmd);
    
    // Uso opcional pero recomendado de chroot para obtener una shell limpia en la raíz del host
    $hasChroot = preg_match('/\bchroot\b/i', $cmd);

    if ($isDocker && $hasSocket && $hasRun && $hasMount && $hasImage && $hasChroot) {
        $esExito = true;
        $feedback = $lang === 'es' 
            ? "¡EXPLOIT COMPLETADO! El demonio de Docker del Host ha ejecutado tu orden, montando el disco principal en tu nuevo contenedor. Tienes acceso ROOT." 
            : "EXPLOIT COMPLETED! The Host's Docker daemon executed your command, mounting the main drive into your new container. You have ROOT access.";
        $flag = "FLAG{docker_socket_pwned}";
    } else {
        $errores = [];
        if (!$isDocker) $errores[] = $lang === 'es' ? "Debe empezar por 'docker'" : "Must start with 'docker'";
        if (!$hasSocket) $errores[] = $lang === 'es' ? "Falta el flag del host (-H unix:///var/run/docker.sock)" : "Missing host flag (-H unix:///var/run/docker.sock)";
        if (!$hasRun) $errores[] = $lang === 'es' ? "Falta la orden de ejecución (run)" : "Missing execution command (run)";
        if (!$hasMount) $errores[] = $lang === 'es' ? "Falta montar la raíz en un volumen (-v /:/mnt)" : "Missing root volume mount (-v /:/mnt)";
        if (!$hasImage) $errores[] = $lang === 'es' ? "Falta especificar la imagen base (ej. alpine)" : "Missing base image (e.g. alpine)";
        if (!$hasChroot) $errores[] = $lang === 'es' ? "Falta el comando para cambiar de raíz (chroot /mnt sh)" : "Missing change root command (chroot /mnt sh)";
        
        $feedback = "[ERROR DE SINTAXIS] " . implode(" | ", $errores);
    }
}
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 900px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// <?= $lang === 'es' ? 'RETO CTF 16' : 'CTF CHALLENGE 16' ?></span>
        <h1 style="text-align: center; margin-bottom: 2rem;"><?= $lang === 'es' ? 'Container Breakout' : 'Container Breakout' ?></h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <div style="background: #111; padding: 1.5rem; border-left: 4px solid #ff2a2a; margin-bottom: 2rem;">
                <h3 style="color: #ff2a2a; margin-top: 0;">🏴‍☠️ <?= $lang === 'es' ? 'Escalada Crítica al Host' : 'Critical Host Escalation' ?></h3>
                <p style="color: var(--white); font-size: 0.95rem; line-height: 1.6; margin-bottom: 0;">
                    <?= $lang === 'es' ? 'Estás dentro de un contenedor comprometido. Tras hacer enumeración con <code>ls -la /var/run/</code> descubres que el socket de Docker está expuesto.<br><br>Escribe el comando exacto para conectarte a ese socket (<code>-H</code>), ejecutar (<code>run</code>) un nuevo contenedor basado en <strong>alpine</strong>, montar la raíz completa del servidor (<code>-v /:/mnt</code>) y abrir una shell interactiva como root absoluto usando <strong>chroot</strong> sobre el directorio /mnt.' : 'You are inside a compromised container. After enumerating with <code>ls -la /var/run/</code> you discover the Docker socket is exposed.<br><br>Write the exact command to connect to that socket (<code>-H</code>), execute (<code>run</code>) a new container based on <strong>alpine</strong>, mount the server\'s entire root (<code>-v /:/mnt</code>) and open an interactive shell as absolute root using <strong>chroot</strong> on the /mnt directory.' ?>
                </p>
            </div>
            
            <form method="POST" action="">
                <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                    <?= $lang === 'es' ? 'Consola del Contenedor (appuser):' : 'Container Shell (appuser):' ?>
                </label>
                <div style="display: flex; align-items: center; background: #050505; border: 1px solid #333; padding: 0.5rem; border-radius: 4px; margin-bottom: 1.5rem;">
                    <span style="color: #4a90e2; margin-right: 10px; font-family: var(--mono);">appuser@web-container-01:~$</span>
                    <input type="text" name="cmd" class="cyber-input" style="flex: 1; border: none; background: transparent; padding: 0; box-shadow: none;" placeholder="docker -H ..." autocomplete="off" value="<?= htmlspecialchars($_POST['cmd'] ?? '') ?>">
                </div>
                
                <button type="submit" style="width: 100%; background: var(--cyan); border: none; color: #000; padding: 15px; font-family: var(--mono); font-weight: bold; cursor: pointer; font-size: 1rem; transition: background 0.3s;">
                    <?= $lang === 'es' ? 'EJECUTAR ESCAPE' : 'EXECUTE ESCAPE' ?>
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
                        <?= $lang === 'es' ? 'Canjea tu logro en la terminal:' : 'Redeem your achievement in the terminal:' ?> <strong>submit <?= $flag ?></strong>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>