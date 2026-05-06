<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Reto CTF #19: Hashcat Rig — CyberEscudo' : 'CTF Challenge #19: Hashcat Rig — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Simulador de construcción de comandos para Hashcat.' : 'Command building simulator for Hashcat.';
$current_page = 'ctf/ctf-19.php';
require __DIR__ . '/../templates/header.php';

$feedback = "";
$flag = "";
$esExito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cmd'])) {
    $cmd = trim($_POST['cmd']);
    
    // Validaciones del comando Hashcat
    $isHashcat = preg_match('/^hashcat\b/i', $cmd);
    
    // El hash empieza por $6$, por tanto el modo es 1800 (SHA512crypt)
    $hasMode = preg_match('/-m\s+1800\b/i', $cmd);
    
    // Ataque de diccionario es -a 0
    $hasAttackMode = preg_match('/-a\s+0\b/i', $cmd);
    
    // Archivo de hash (asumimos hash.txt o shadow.txt) - Solo comprobamos que haya algo antes del wordlist
    
    // Wordlist rockyou.txt
    $hasWordlist = preg_match('/rockyou\.txt/i', $cmd);
    
    // Regla best64.rule
    $hasRule = preg_match('/-r\s+.*best64\.rule/i', $cmd);

    if ($isHashcat && $hasMode && $hasAttackMode && $hasWordlist && $hasRule) {
        $esExito = true;
        $feedback = $lang === 'es' 
            ? "¡GPU al 100% de carga! El ataque ha sido configurado correctamente. Hashcat ha reventado la contraseña en 4 minutos." 
            : "GPU at 100% load! Attack configured successfully. Hashcat cracked the password in 4 minutes.";
        $flag = "FLAG{gpu_hashcat_operator}";
    } else {
        $errores = [];
        if (!$isHashcat) $errores[] = $lang === 'es' ? "Debe empezar por 'hashcat'" : "Must start with 'hashcat'";
        if (!$hasAttackMode) $errores[] = $lang === 'es' ? "Falta el modo de ataque de diccionario (-a 0)" : "Missing dictionary attack mode (-a 0)";
        if (!$hasMode) $errores[] = $lang === 'es' ? "Modo de hash incorrecto. Fíjate en el inicio del hash ($6$) y busca su equivalente en el manual (-m X)" : "Incorrect hash mode. Look at the hash start ($6$) and find its equivalent (-m X)";
        if (!$hasWordlist) $errores[] = $lang === 'es' ? "Falta especificar el diccionario (rockyou.txt)" : "Missing dictionary specification (rockyou.txt)";
        if (!$hasRule) $errores[] = $lang === 'es' ? "Falta inyectar la regla (-r best64.rule)" : "Missing rule injection (-r best64.rule)";
        
        $feedback = "[ERROR CONFIGURACIÓN] " . implode(" | ", $errores);
    }
}

// Log falso del shadow
$shadow_file = "root:\$6\$r1aB7Lz9\$kX2O...Lz7K1:19000:0:99999:7:::";
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 900px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// <?= $lang === 'es' ? 'RETO CTF 19' : 'CTF CHALLENGE 19' ?></span>
        <h1 style="text-align: center; margin-bottom: 2rem;"><?= $lang === 'es' ? 'GPU Cracking Rig' : 'GPU Cracking Rig' ?></h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <div style="background: #111; padding: 1.5rem; border-left: 4px solid #ffb86c; margin-bottom: 2rem;">
                <h3 style="color: #ffb86c; margin-top: 0;">🔥 <?= $lang === 'es' ? 'Objetivo Adquirido' : 'Target Acquired' ?></h3>
                <p style="color: var(--white); font-size: 0.95rem; line-height: 1.6; margin-bottom: 1rem;">
                    <?= $lang === 'es' ? 'Has guardado la siguiente línea en un archivo local llamado <code>hash.txt</code>:' : 'You have saved the following line into a local file named <code>hash.txt</code>:' ?>
                </p>
                <code style="display:block; background:#000; padding:10px; color:#ff2a2a; border-radius:4px; margin-bottom:1rem; word-break: break-all;">
                    <?= htmlspecialchars($shadow_file) ?>
                </code>
                <p style="color: var(--white); font-size: 0.95rem; line-height: 1.6; margin-bottom: 0;">
                    <?= $lang === 'es' ? 'Construye el comando <strong>hashcat</strong> exacto para crackearlo. Necesitas:<br>1. Definir el modo de ataque de diccionario.<br>2. Definir el modo de hash correcto deduciéndolo de su estructura.<br>3. Pasar tu archivo <code>hash.txt</code>.<br>4. Usar el diccionario <code>rockyou.txt</code>.<br>5. Aplicar la regla <code>best64.rule</code>.' : 'Construct the exact <strong>hashcat</strong> command to crack it. You need to:<br>1. Define the dictionary attack mode.<br>2. Define the correct hash mode by deducing it from its structure.<br>3. Pass your <code>hash.txt</code> file.<br>4. Use the <code>rockyou.txt</code> dictionary.<br>5. Apply the <code>best64.rule</code> rule.' ?>
                </p>
            </div>
            
            <form method="POST" action="">
                <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                    <?= $lang === 'es' ? 'Consola de tu Rig de Minería:' : 'Mining Rig Console:' ?>
                </label>
                <div style="display: flex; align-items: center; background: #050505; border: 1px solid #333; padding: 0.5rem; border-radius: 4px; margin-bottom: 1.5rem;">
                    <span style="color: #00ff00; margin-right: 10px; font-family: var(--mono);">hacker@gpu-rig:~#</span>
                    <input type="text" name="cmd" class="cyber-input" style="flex: 1; border: none; background: transparent; padding: 0; box-shadow: none;" placeholder="hashcat -a ..." autocomplete="off" value="<?= htmlspecialchars($_POST['cmd'] ?? '') ?>">
                </div>
                
                <button type="submit" style="width: 100%; background: var(--cyan); border: none; color: #000; padding: 15px; font-family: var(--mono); font-weight: bold; cursor: pointer; font-size: 1rem; transition: background 0.3s;">
                    <?= $lang === 'es' ? 'INICIAR EXTRACCIÓN (CRACK)' : 'START EXTRACTION (CRACK)' ?>
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
                        <?= $lang === 'es' ? 'Felicidades. Valida la flag en la terminal principal:' : 'Congratulations. Validate the flag in the main terminal:' ?> <strong>submit <?= $flag ?></strong>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>