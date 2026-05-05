<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Reto CTF #06: Sudo Privesc — CyberEscudo' : 'CTF Challenge #06: Sudo Privesc — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Simulador de escalada de privilegios en Linux abusando de sudo.' : 'Linux privilege escalation simulator abusing sudo.';
$current_page = 'ctf/ctf-06.php';
require __DIR__ . '/../templates/header.php';

$output = "";
$flag = "";
$esExito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cmd'])) {
    $cmd = trim($_POST['cmd']);
    $cmd_lower = strtolower($cmd);
    
    // Simulación segura de respuestas de consola
    if ($cmd_lower === 'whoami') {
        $output = "www-data";
    } 
    elseif ($cmd_lower === 'id') {
        $output = "uid=33(www-data) gid=33(www-data) groups=33(www-data)";
    }
    elseif ($cmd_lower === 'sudo -l') {
        $output = "Matching Defaults entries for www-data on target-srv:\n    env_reset, mail_badpass, secure_path=/usr/local/sbin\:/usr/local/bin\:/usr/sbin\:/usr/bin\:/sbin\:/bin\n\nUser www-data may run the following commands on target-srv:\n    (root) NOPASSWD: /usr/bin/find";
    }
    // Lógica para detectar el bypass de sudo find (ej: sudo find . -exec /bin/bash \;)
    elseif (preg_match('/^sudo\s+find\s+.*-exec\s+(?:\/bin\/)?(?:bash|sh).*;/i', $cmd_lower)) {
        $esExito = true;
        $output = "root@target-srv:~# id\nuid=0(root) gid=0(root) groups=0(root)\nroot@target-srv:~# cat /root/flag.txt\n...";
        $flag = "FLAG{sudo_find_root_shell}";
    }
    elseif (strpos($cmd_lower, 'sudo') !== false && strpos($cmd_lower, 'find') === false && $cmd_lower !== 'sudo -l') {
        $output = "Sorry, user www-data is not allowed to execute '" . htmlspecialchars($cmd) . "' as root on target-srv.";
    }
    elseif (empty($cmd_lower)) {
        $output = "";
    }
    else {
        $output = "bash: " . htmlspecialchars(explode(' ', $cmd)[0]) . ": command not found";
    }
}
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 800px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// <?= $lang === 'es' ? 'RETO CTF 06' : 'CTF CHALLENGE 06' ?></span>
        <h1 style="text-align: center; margin-bottom: 2rem;"><?= $lang === 'es' ? 'Escalada de Privilegios' : 'Privilege Escalation' ?></h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <p style="color: var(--gray); margin-bottom: 1.5rem;">
                <?= $lang === 'es' ? 'Has conseguido una reverse shell inicial en el servidor objetivo. Ahora eres el usuario <code>www-data</code>. Utiliza la terminal interactiva de abajo para enumerar la máquina, detectar el fallo de configuración en <code>sudo</code> y obtener una shell de root para leer la flag.' : 'You got an initial reverse shell on the target server as <code>www-data</code>. Use the interactive terminal below to enumerate the machine, detect the <code>sudo</code> misconfiguration, and spawn a root shell to read the flag.' ?>
            </p>
            
            <div style="background: #050505; border: 1px solid #333; border-radius: 5px; padding: 1rem; margin-bottom: 1.5rem; font-family: var(--mono); color: #00ff00;">
                
                <?php if (isset($_POST['cmd']) && !empty($_POST['cmd'])): ?>
                    <div style="margin-bottom: 10px; color: #fff;">
                        <span style="color: #ff2a2a;">www-data@target-srv</span>:<span style="color: #4a90e2;">~</span>$ <?= htmlspecialchars($_POST['cmd']) ?>
                    </div>
                <?php endif; ?>

                <?php if ($output !== ""): ?>
                    <div style="white-space: pre-wrap; margin-bottom: 20px; color: <?= $esExito ? '#ff2a2a' : '#00ff00' ?>;">
<?= $output ?>
                    </div>
                <?php endif; ?>

                <?php if ($esExito): ?>
                    <div style="margin-top: 1rem; padding: 1rem; border: 1px dashed #ff2a2a; text-align: center; animation: pulse 2s infinite;">
                        <h3 style="color: #ff2a2a; margin: 0; letter-spacing: 2px;">SYSTEM PWNED</h3>
                        <h2 style="color: #fff; margin: 1rem 0;"><?= $flag ?></h2>
                        <p style="margin:0; font-size: 0.85rem; color: var(--cyan);">
                            <?= $lang === 'es' ? 'Terminal local. Ejecuta:' : 'Local terminal. Run:' ?> <strong>submit <?= $flag ?></strong>
                        </p>
                    </div>
                <?php else: ?>
                    <form method="POST" action="" style="display: flex; align-items: center;">
                        <span style="color: #ff2a2a; margin-right: 8px;">www-data@target-srv</span>:<span style="color: #4a90e2; margin-right: 8px;">~</span>$ 
                        <input type="text" name="cmd" autofocus autocomplete="off" style="flex: 1; background: transparent; border: none; color: #fff; font-family: var(--mono); font-size: 1rem; outline: none;">
                        <!-- Botón oculto para permitir submit con Enter -->
                        <button type="submit" style="display: none;"></button>
                    </form>
                <?php endif; ?>
            </div>
            
            <p style="text-align: center; font-size: 0.8rem; color: var(--gray);">
                <?= $lang === 'es' ? 'Pista: Comandos soportados: whoami, id, sudo -l' : 'Hint: Supported commands: whoami, id, sudo -l' ?>
            </p>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>