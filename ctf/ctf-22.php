<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Reto CTF #22: SQLMap WAF Evasion — CyberEscudo' : 'CTF Challenge #22: SQLMap WAF Evasion — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Simulador de inyección SQL evadiendo WAF con SQLMap.' : 'SQL injection simulator evading WAF with SQLMap.';
$current_page = 'ctf/ctf-22.php';
require __DIR__ . '/../templates/header.php';

$feedback = "";
$flag = "";
$esExito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cmd'])) {
    $cmd = trim($_POST['cmd']);
    
    // Validar sintaxis del comando SQLMap avanzado
    // Objetivo: sqlmap -r request.txt -D corp_db -T admin_creds --dump --tamper=space2comment
    
    $isSqlmap = preg_match('/^sqlmap\b/i', $cmd);
    
    // Archivo de petición interceptada (-r request.txt)
    $hasRequest = preg_match('/-r\s+request\.txt\b/i', $cmd);
    
    // Base de datos objetivo (-D corp_db)
    $hasDB = preg_match('/-D\s+corp_db\b/i', $cmd);
    
    // Tabla objetivo (-T admin_creds)
    $hasTable = preg_match('/-T\s+admin_creds\b/i', $cmd);
    
    // Acción de volcado (--dump)
    $hasDump = preg_match('/--dump\b/i', $cmd);
    
    // Evasión WAF mediante Tamper Script (--tamper=space2comment)
    $hasTamper = preg_match('/--tamper[\s=]+["\']?space2comment(\.py)?["\']?/i', $cmd);

    if ($isSqlmap && $hasRequest && $hasDB && $hasTable && $hasDump && $hasTamper) {
        $esExito = true;
        $feedback = $lang === 'es' 
            ? "¡Bypass exitoso! El WAF ha ignorado tus payloads ofuscados con comentarios y SQLMap ha extraído las credenciales del administrador exitosamente." 
            : "Successful bypass! The WAF ignored your obfuscated payloads and SQLMap successfully extracted the administrator credentials.";
        $flag = "FLAG{sqlmap_tamper_wizard}";
    } else {
        $errores = [];
        if (!$isSqlmap) $errores[] = $lang === 'es' ? "Debe iniciar con 'sqlmap'" : "Must start with 'sqlmap'";
        if (!$hasRequest) $errores[] = $lang === 'es' ? "Falta pasar el archivo interceptado (-r request.txt)" : "Missing intercepted file (-r request.txt)";
        if (!$hasDB) $errores[] = $lang === 'es' ? "Base de datos objetivo no definida (-D corp_db)" : "Target database not defined (-D corp_db)";
        if (!$hasTable) $errores[] = $lang === 'es' ? "Tabla objetivo no definida (-T admin_creds)" : "Target table not defined (-T admin_creds)";
        if (!$hasDump) $errores[] = $lang === 'es' ? "Falta la acción de extracción (--dump)" : "Missing extraction action (--dump)";
        if (!$hasTamper) $errores[] = $lang === 'es' ? "Falta inyectar el script de ofuscación (--tamper=space2comment)" : "Missing obfuscation script injection (--tamper=space2comment)";
        
        $feedback = "[WAF BLOCK - ACCESO DENEGADO] " . implode(" | ", $errores);
    }
}
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 900px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// <?= $lang === 'es' ? 'RETO CTF 22' : 'CTF CHALLENGE 22' ?></span>
        <h1 style="text-align: center; margin-bottom: 2rem;"><?= $lang === 'es' ? 'WAF Bypass & Data Exfiltration' : 'WAF Bypass & Data Exfiltration' ?></h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <div style="background: #111; padding: 1.5rem; border-left: 4px solid #ff2a2a; margin-bottom: 2rem;">
                <h3 style="color: #ff2a2a; margin-top: 0;">🛡️ <?= $lang === 'es' ? 'Alerta: Cloudflare Activo' : 'Alert: Cloudflare Active' ?></h3>
                <p style="color: var(--white); font-size: 0.95rem; line-height: 1.6; margin-bottom: 0;">
                    <?= $lang === 'es' ? 'Has intentado inyectar el parámetro vulnerable pero el firewall (WAF) te expulsa al detectar espacios (<code>%20</code>) en las sentencias SQL.<br><br>Escribe el comando <strong>sqlmap</strong> que cargue tu captura local <code>request.txt</code>, apunte a la base de datos <code>corp_db</code>, y extraiga (dump) el contenido de la tabla <code>admin_creds</code>. Para engañar al WAF, debes usar el modificador de scripts <strong>tamper</strong> llamado <code>space2comment</code>.' : 'You tried to inject the vulnerable parameter but the firewall (WAF) kicks you out upon detecting spaces (<code>%20</code>) in SQL statements.<br><br>Write the <strong>sqlmap</strong> command that loads your local capture <code>request.txt</code>, points to the <code>corp_db</code> database, and extracts (dumps) the content of the <code>admin_creds</code> table. To fool the WAF, you must use the <strong>tamper</strong> script modifier named <code>space2comment</code>.' ?>
                </p>
            </div>
            
            <form method="POST" action="">
                <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                    Terminal:
                </label>
                <div style="display: flex; align-items: center; background: #050505; border: 1px solid #333; padding: 0.5rem; border-radius: 4px; margin-bottom: 1.5rem;">
                    <span style="color: #00ff00; margin-right: 10px; font-family: var(--mono);">pentester@kali:~#</span>
                    <input type="text" name="cmd" class="cyber-input" style="flex: 1; border: none; background: transparent; padding: 0; box-shadow: none;" placeholder="sqlmap -r ..." autocomplete="off" value="<?= htmlspecialchars($_POST['cmd'] ?? '') ?>">
                </div>
                
                <button type="submit" style="width: 100%; background: var(--cyan); border: none; color: #000; padding: 15px; font-family: var(--mono); font-weight: bold; cursor: pointer; font-size: 1rem; transition: background 0.3s;">
                    <?= $lang === 'es' ? 'LANZAR ATAQUE OFUSCADO' : 'LAUNCH OBFUSCATED ATTACK' ?>
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
                        <?= $lang === 'es' ? 'Reporta la flag en la terminal principal para validar la práctica:' : 'Report the flag in the main terminal to validate the practice:' ?> <strong>submit <?= $flag ?></strong>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>