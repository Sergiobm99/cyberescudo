<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Reto CTF #07: Fuzzing Mastery — CyberEscudo' : 'CTF Challenge #07: Fuzzing Mastery — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Simulador de construcción de comandos para ffuf.' : 'Command building simulator for ffuf.';
$current_page = 'ctf/ctf-07.php';
require __DIR__ . '/../templates/header.php';

$feedback = "";
$flag = "";
$esExito = false;
$cmd = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cmd'])) {
    $cmd = trim($_POST['cmd']);
    
    // Validamos el comando mediante expresiones regulares seguras
    // 1. Debe usar ffuf
    $isFfuf = preg_match('/^ffuf/i', $cmd);
    
    // 2. Debe apuntar a la URL correcta con FUZZ
    $hasUrl = preg_match('/-u\s+["\']?http:\/\/megacorp\.local\/FUZZ["\']?/i', $cmd);
    
    // 3. Debe usar el diccionario raft-small.txt
    $hasWordlist = preg_match('/-w\s+["\']?raft-small\.txt["\']?/i', $cmd);
    
    // 4. Debe filtrar el tamaño de 512 bytes (-fs 512)
    $hasFilter = preg_match('/-fs\s+512/i', $cmd);

    if ($isFfuf && $hasUrl && $hasWordlist && $hasFilter) {
        $esExito = true;
        $feedback = $lang === 'es' 
            ? "¡Comando perfecto! El escáner ignoró la basura y encontró el directorio oculto: /admin_dashboard" 
            : "Perfect command! The scanner ignored the garbage and found the hidden directory: /admin_dashboard";
        $flag = "FLAG{ffuf_filter_ninja}";
    } else {
        $errores = [];
        if (!$isFfuf) $errores[] = $lang === 'es' ? "Debe empezar por 'ffuf'" : "Must start with 'ffuf'";
        if (!$hasUrl) $errores[] = $lang === 'es' ? "Falta la URL objetivo (-u) con la palabra FUZZ al final" : "Missing target URL (-u) ending with FUZZ";
        if (!$hasWordlist) $errores[] = $lang === 'es' ? "Falta especificar el diccionario correcto (-w)" : "Missing the correct wordlist (-w)";
        if (!$hasFilter) $errores[] = $lang === 'es' ? "Falta el filtro de tamaño para ignorar los 512 bytes (-fs)" : "Missing the size filter to ignore 512 bytes (-fs)";
        
        $feedback = "[ERROR] " . implode(" | ", $errores);
    }
}
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 800px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// <?= $lang === 'es' ? 'RETO CTF 07' : 'CTF CHALLENGE 07' ?></span>
        <h1 style="text-align: center; margin-bottom: 2rem;"><?= $lang === 'es' ? 'Evasión de Catch-All' : 'Catch-All Evasion' ?></h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <div style="background: #111; padding: 1.5rem; border-left: 4px solid var(--cyan); margin-bottom: 2rem;">
                <h3 style="color: var(--white); margin-top: 0;"><?= $lang === 'es' ? 'Misión de Reconocimiento:' : 'Reconnaissance Mission:' ?></h3>
                <p style="color: var(--gray); font-size: 0.95rem; line-height: 1.6;">
                    <?= $lang === 'es' ? 'El servidor <strong>http://megacorp.local</strong> está mal configurado. Cualquier directorio que busques devuelve un código HTTP 200 OK y una página de error personalizada que pesa exactamente <strong>512 bytes</strong>.<br><br>Escribe el comando <strong>ffuf</strong> completo para hacer fuzzing en los directorios de ese servidor usando el diccionario <strong>raft-small.txt</strong> y aplicando un filtro para evitar que la pantalla se llene de falsos positivos de 512 bytes.' : 'The server <strong>http://megacorp.local</strong> is misconfigured. Any directory you search returns an HTTP 200 OK with a custom error page weighing exactly <strong>512 bytes</strong>.<br><br>Write the complete <strong>ffuf</strong> command to fuzz the directories of that server using the wordlist <strong>raft-small.txt</strong> and applying a filter to avoid filling the screen with 512-byte false positives.' ?>
                </p>
            </div>
            
            <form method="POST" action="">
                <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                    <?= $lang === 'es' ? 'Comando a ejecutar:' : 'Command to execute:' ?>
                </label>
                <div style="display: flex; align-items: center; background: #050505; border: 1px solid #333; padding: 0.5rem; border-radius: 4px; margin-bottom: 1.5rem;">
                    <span style="color: #ff2a2a; margin-right: 10px; font-family: var(--mono);">root@kali:~#</span>
                    <input type="text" name="cmd" class="cyber-input" style="flex: 1; border: none; background: transparent; padding: 0; box-shadow: none;" placeholder="ffuf -u ..." autocomplete="off" value="<?= htmlspecialchars($_POST['cmd'] ?? '') ?>">
                </div>
                
                <button type="submit" style="width: 100%; background: var(--cyan); border: none; color: #000; padding: 15px; font-family: var(--mono); font-weight: bold; cursor: pointer; font-size: 1rem; transition: background 0.3s;">
                    <?= $lang === 'es' ? 'LANZAR ATAQUE' : 'LAUNCH ATTACK' ?>
                </button>
            </form>

            <?php if($feedback !== ""): ?>
                <div style="margin-top: 2rem; padding: 1rem; background: <?= $esExito ? 'rgba(0,255,0,0.1)' : 'rgba(255,42,42,0.1)' ?>; border: 1px solid <?= $esExito ? '#00ff00' : '#ff2a2a' ?>; font-family: var(--mono); font-size: 0.9rem; color: <?= $esExito ? '#00ff00' : '#ff2a2a' ?>;">
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