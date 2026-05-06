<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Reto CTF #23: Apache Hardening — CyberEscudo' : 'CTF Challenge #23: Apache Hardening — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Simulador de auditoría y configuración de Apache/PHP.' : 'Apache/PHP audit and configuration simulator.';
$current_page = 'ctf/ctf-23.php';
require __DIR__ . '/../templates/header.php';

$feedback = "";
$flag = "";
$esExito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ans1 = trim($_POST['q1'] ?? '');
    $ans2 = trim($_POST['q2'] ?? '');
    $ans3 = strtolower(trim($_POST['q3'] ?? ''));
    
    // Validar respuestas
    // 1. Directiva para ocultar versión SO: ServerTokens Prod (aceptamos Prod o ServerTokens Prod)
    $isQ1Correct = (preg_match('/prod/i', $ans1) && !preg_match('/os/i', $ans1));
    
    // 2. Desactivar listado de directorios: -Indexes
    $isQ2Correct = (preg_match('/-indexes/i', $ans2));
    
    // 3. Directiva PHP para funciones peligrosas: disable_functions
    $isQ3Correct = ($ans3 === 'disable_functions');

    if ($isQ1Correct && $isQ2Correct && $isQ3Correct) {
        $esExito = true;
        $feedback = $lang === 'es' 
            ? "¡Configuraciones parcheadas! Has cerrado las fugas de información, evitado el Directory Traversal y mitigado la ejecución remota de comandos." 
            : "Configurations patched! You have closed information leaks, prevented Directory Traversal, and mitigated remote command execution.";
        $flag = "FLAG{apache_hardening_master}";
    } else {
        $errores = [];
        if (!$isQ1Correct) $errores[] = "Server Info Leak (Q1)";
        if (!$isQ2Correct) $errores[] = "Directory Listing (Q2)";
        if (!$isQ3Correct) $errores[] = "PHP RCE Risk (Q3)";
        
        $feedback = $lang === 'es' 
            ? "[ALERTA] Servidor aún vulnerable en: " . implode(", ", $errores)
            : "[ALERT] Server still vulnerable in: " . implode(", ", $errores);
    }
}
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 900px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// <?= $lang === 'es' ? 'RETO CTF 23' : 'CTF CHALLENGE 23' ?></span>
        <h1 style="text-align: center; margin-bottom: 2rem;"><?= $lang === 'es' ? 'Server Defender: Hardening' : 'Server Defender: Hardening' ?></h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <p style="color: var(--gray); margin-bottom: 2rem; text-align: center; font-size: 1.05rem;">
                <?= $lang === 'es' ? 'Debes auditar los archivos de configuración antes del pase a Producción. Escribe el <strong>valor o directiva exacta</strong> solicitada para asegurar el servidor.' : 'You must audit the configuration files before moving to Production. Enter the exact <strong>value or directive</strong> requested to secure the server.' ?>
            </p>
            
            <form method="POST" action="">
                
                <!-- Q1 -->
                <div style="margin-bottom: 1.5rem; background: #050505; border: 1px solid #333; padding: 1.5rem; border-radius: 5px;">
                    <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                        [1] security.conf -> Ocultación de Firmas
                    </label>
                    <p style="color: #aaa; font-size: 0.9rem; margin-top: 0; margin-bottom: 10px;">
                        <?= $lang === 'es' ? 'Actualmente Apache devuelve <code>Apache/2.4.41 (Ubuntu)</code> en las cabeceras HTTP. ¿Qué valor debes asignarle a la directiva <strong>ServerTokens</strong> para que solo devuelva <code>Apache</code>?' : 'Currently Apache returns <code>Apache/2.4.41 (Ubuntu)</code> in the HTTP headers. What value should you assign to the <strong>ServerTokens</strong> directive so it only returns <code>Apache</code>?' ?>
                    </p>
                    <input type="text" name="q1" class="cyber-input" style="width: 100%;" placeholder="Ej: Full, OS, Minimal..." autocomplete="off" value="<?= htmlspecialchars($_POST['q1'] ?? '') ?>">
                </div>

                <!-- Q2 -->
                <div style="margin-bottom: 1.5rem; background: #050505; border: 1px solid #333; padding: 1.5rem; border-radius: 5px;">
                    <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                        [2] apache2.conf -> Listado de Directorios
                    </label>
                    <p style="color: #aaa; font-size: 0.9rem; margin-top: 0; margin-bottom: 10px;">
                        <?= $lang === 'es' ? 'Dentro del bloque <code>&lt;Directory /var/www/html&gt;</code>, ¿qué opción exacta (incluyendo el símbolo) debes poner para <strong>evitar</strong> que los usuarios vean un índice de archivos si falta el index.html?' : 'Inside the <code>&lt;Directory /var/www/html&gt;</code> block, what exact option (including the symbol) must you set to <strong>prevent</strong> users from seeing an index of files if index.html is missing?' ?>
                    </p>
                    <input type="text" name="q2" class="cyber-input" style="width: 100%;" placeholder="Ej: +FollowSymLinks" autocomplete="off" value="<?= htmlspecialchars($_POST['q2'] ?? '') ?>">
                </div>

                <!-- Q3 -->
                <div style="margin-bottom: 2rem; background: #050505; border: 1px solid #333; padding: 1.5rem; border-radius: 5px;">
                    <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                        [3] php.ini -> RCE Prevention
                    </label>
                    <p style="color: #aaa; font-size: 0.9rem; margin-top: 0; margin-bottom: 10px;">
                        <?= $lang === 'es' ? '¿Cuál es el nombre de la <strong>directiva</strong> en PHP que permite crear una lista negra separada por comas de comandos del sistema como <code>system</code> o <code>shell_exec</code> para que no puedan ejecutarse?' : 'What is the name of the <strong>directive</strong> in PHP that allows creating a comma-separated blacklist of system commands like <code>system</code> or <code>shell_exec</code> so they cannot be executed?' ?>
                    </p>
                    <input type="text" name="q3" class="cyber-input" style="width: 100%;" placeholder="Ej: block_commands" autocomplete="off" value="<?= htmlspecialchars($_POST['q3'] ?? '') ?>">
                </div>
                
                <button type="submit" style="width: 100%; background: var(--cyan); border: none; color: #000; padding: 15px; font-family: var(--mono); font-weight: bold; cursor: pointer; font-size: 1rem; transition: background 0.3s;">
                    <?= $lang === 'es' ? 'APLICAR POLÍTICAS DE SEGURIDAD' : 'APPLY SECURITY POLICIES' ?>
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
                        <?= $lang === 'es' ? 'Desbloquea tu rol defensivo en la terminal:' : 'Unlock your defensive role in the terminal:' ?> <strong>submit <?= $flag ?></strong>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>