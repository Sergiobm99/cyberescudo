<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Reto CTF #02: Command Injection — CyberEscudo' : 'CTF Challenge #02: Command Injection — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Simulador de inyección de comandos en una utilidad de red.' : 'Command injection simulator in a network utility.';
$current_page = 'ctf/ctf-02.php';
require __DIR__ . '/../templates/header.php';

$salida = "";
$flag = "";
$esExito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target = $_POST['target'] ?? '';
    
    // 100% SEGURO: Simulamos la ejecución analizando el texto, sin tocar el SO real.
    $target_lower = strtolower($target);
    
    // Verificamos si hay un intento de concatenar comandos críticos (cat /etc/passwd o id)
    if (preg_match('/(;|\&|\|).*?(cat\s+\/etc\/passwd|id|whoami)/i', $target_lower)) {
        $esExito = true;
        $salida = "uid=33(www-data) gid=33(www-data) groups=33(www-data)\nroot:x:0:0:root:/root:/bin/bash\ndaemon:x:1:1:daemon:/usr/sbin:/usr/sbin/nologin";
        $flag = "FLAG{rce_root_master}";
    } 
    // Verificamos si hay un intento de inyección simple (ls, pwd)
    elseif (preg_match('/(;|\&|\|).*?(ls|pwd)/i', $target_lower)) {
        $esExito = true;
        $salida = "index.php\nconfig.php\nsecret_keys.txt\n/var/www/html";
        $flag = "FLAG{cmd_inj_explorer}";
    }
    // Verificamos si pone una IP normal
    elseif (filter_var(trim($target), FILTER_VALIDATE_IP)) {
        $salida = "PING {$target} (56 data bytes)\n64 bytes from {$target}: icmp_seq=1 ttl=64 time=0.042 ms\n64 bytes from {$target}: icmp_seq=2 ttl=64 time=0.038 ms\n\n--- {$target} ping statistics ---\n2 packets transmitted, 2 received, 0% packet loss";
    } 
    else {
        $salida = "ping: unknown host " . htmlspecialchars($target);
    }
}
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 700px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// <?= $lang === 'es' ? 'RETO CTF 02' : 'CTF CHALLENGE 02' ?></span>
        <h1 style="text-align: center; margin-bottom: 2rem;"><?= $lang === 'es' ? 'Utilidad de Red (Ping)' : 'Network Utility (Ping)' ?></h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <p style="text-align: center; color: var(--gray); margin-bottom: 2rem;">
                <?= $lang === 'es' ? 'Introduce una dirección IP para comprobar si el servidor tiene conectividad.' : 'Enter an IP address to check if the server has connectivity.' ?>
            </p>
            
            <form method="POST" action="" style="display: flex; gap: 10px; margin-bottom: 2rem;">
                <input type="text" name="target" class="cyber-input" style="flex: 1;" placeholder="Ej: 8.8.8.8" autocomplete="off" value="<?= htmlspecialchars($_POST['target'] ?? '') ?>">
                <button type="submit" style="background: var(--cyan); border: none; color: #000; padding: 0 20px; font-family: var(--mono); font-weight: bold; cursor: pointer;">
                    <?= $lang === 'es' ? 'EJECUTAR PING' : 'RUN PING' ?>
                </button>
            </form>

            <?php if($salida !== ""): ?>
                <div style="background: #000; padding: 1rem; border-left: 3px solid <?= $esExito ? '#00ff00' : 'var(--cyan)' ?>; font-family: var(--mono); font-size: 0.85rem; color: #00ff00; white-space: pre-wrap; word-break: break-all;">
$ ping -c 2 <?= htmlspecialchars($_POST['target']) ?>

<?= $salida ?>
                </div>
            <?php endif; ?>

            <?php if($flag !== ""): ?>
                <div style="margin-top: 2rem; padding: 1rem; background: rgba(0,255,0,0.1); border: 1px dashed #00ff00; text-align: center; animation: pulse 2s infinite;">
                    <h3 style="color: #00ff00; margin: 0.5rem 0; font-family: var(--mono); letter-spacing: 2px;"><?= $flag ?></h3>
                    <p style="margin:0; font-size: 0.8rem; color: var(--cyan);">
                        <?= $lang === 'es' ? '¡Código malicioso inyectado! Canjea esta bandera en la terminal secreta:' : 'Malicious code injected! Redeem this flag in the secret terminal:' ?> <strong>submit <?= $flag ?></strong>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>