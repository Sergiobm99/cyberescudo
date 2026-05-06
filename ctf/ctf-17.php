<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Reto CTF #17: Nikto Forensics — CyberEscudo' : 'CTF Challenge #17: Nikto Forensics — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Análisis forense de un reporte de escaneo web con Nikto.' : 'Forensic analysis of a Nikto web scan report.';
$current_page = 'ctf/ctf-17.php';
require __DIR__ . '/../templates/header.php';

$feedback = "";
$flag = "";
$esExito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ans1 = strtoupper(trim($_POST['q1'] ?? ''));
    $ans2 = trim($_POST['q2'] ?? '');
    $ans3 = strtoupper(trim($_POST['q3'] ?? ''));
    
    // Validar respuestas
    // 1. CVE del Shellshock
    $isQ1Correct = ($ans1 === 'CVE-2014-6271');
    
    // 2. Ruta exacta del backup
    $isQ2Correct = ($ans2 === '/config.bak');
    
    // 3. Método HTTP peligroso
    $isQ3Correct = ($ans3 === 'PUT');

    if ($isQ1Correct && $isQ2Correct && $isQ3Correct) {
        $esExito = true;
        $feedback = $lang === 'es' 
            ? "¡Excelente análisis! Has extraído los vectores de ataque clave del escáner automático." 
            : "Excellent analysis! You have extracted the key attack vectors from the automated scanner.";
        $flag = "FLAG{nikto_recon_expert}";
    } else {
        $feedback = $lang === 'es' 
            ? "[ERROR] El informe de vulnerabilidades es incorrecto. Revisa el log detenidamente."
            : "[ERROR] The vulnerability report is incorrect. Review the log carefully.";
    }
}

// Log simulado de Nikto
$nikto_log = <<<LOG
- Nikto v2.1.6
---------------------------------------------------------------------------
+ Target IP:          10.10.10.20
+ Target Hostname:    legacy-corp.local
+ Target Port:        80
+ Start Time:         2026-05-06 10:00:00 (GMT)
---------------------------------------------------------------------------
+ Server: Apache/2.2.14 (Ubuntu)
+ The anti-clickjacking X-Frame-Options header is not present.
+ The X-XSS-Protection header is not defined.
+ Allowed HTTP Methods: GET, HEAD, POST, PUT, DELETE, OPTIONS
+ OSVDB-397: HTTP method ('Allow' Header): 'PUT' method could allow clients to save files on the web server.
+ OSVDB-3268: /config.bak: Backup file found. This may contain sensitive information.
+ OSVDB-3092: /cgi-bin/test.cgi: This might be interesting...
+ OSVDB-112004: /cgi-bin/status: Site appears vulnerable to the 'Shellshock' vulnerability (CVE-2014-6271).
+ 8919 requests: 0 error(s) and 7 item(s) reported on remote host
+ End Time:           2026-05-06 10:05:23 (GMT) (323 seconds)
---------------------------------------------------------------------------
+ 1 host(s) tested
LOG;
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 900px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// <?= $lang === 'es' ? 'RETO CTF 17' : 'CTF CHALLENGE 17' ?></span>
        <h1 style="text-align: center; margin-bottom: 2rem;"><?= $lang === 'es' ? 'Análisis Forense de Nikto' : 'Nikto Forensics Analysis' ?></h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <p style="color: var(--gray); margin-bottom: 1.5rem; text-align: center;">
                <?= $lang === 'es' ? 'Analiza el siguiente output extraído de la herramienta <strong>Nikto</strong> y responde a las preguntas clave para preparar tu reporte de auditoría.' : 'Analyze the following output extracted from the <strong>Nikto</strong> tool and answer the key questions to prepare your audit report.' ?>
            </p>

            <div style="background: #000; padding: 1.5rem; border: 1px solid #333; border-radius: 5px; font-family: var(--mono); font-size: 0.85rem; color: #a1e6ff; white-space: pre-wrap; margin-bottom: 2rem; line-height: 1.4;">
<?= htmlspecialchars($nikto_log) ?>
            </div>
            
            <form method="POST" action="">
                
                <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                    <div>
                        <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                            1. <?= $lang === 'es' ? '¿Cuál es el código CVE exacto de la vulnerabilidad crítica descubierta?' : 'What is the exact CVE code of the critical vulnerability discovered?' ?>
                        </label>
                        <input type="text" name="q1" class="cyber-input" style="width: 100%;" placeholder="Ej: CVE-2020-1234" autocomplete="off" value="<?= htmlspecialchars($_POST['q1'] ?? '') ?>">
                    </div>

                    <div>
                        <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                            2. <?= $lang === 'es' ? '¿Cuál es la ruta exacta del archivo de copia de seguridad (backup) revelado?' : 'What is the exact path of the revealed backup file?' ?>
                        </label>
                        <input type="text" name="q2" class="cyber-input" style="width: 100%;" placeholder="Ej: /backup.zip" autocomplete="off" value="<?= htmlspecialchars($_POST['q2'] ?? '') ?>">
                    </div>

                    <div>
                        <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                            3. <?= $lang === 'es' ? '¿Qué método HTTP peligroso está permitido y dejaría a un atacante subir archivos?' : 'Which dangerous HTTP method is allowed and would let an attacker upload files?' ?>
                        </label>
                        <input type="text" name="q3" class="cyber-input" style="width: 100%;" placeholder="Ej: TRACE" autocomplete="off" value="<?= htmlspecialchars($_POST['q3'] ?? '') ?>">
                    </div>
                </div>
                
                <button type="submit" style="width: 100%; background: var(--cyan); border: none; color: #000; padding: 15px; font-family: var(--mono); font-weight: bold; cursor: pointer; font-size: 1rem; transition: background 0.3s;">
                    <?= $lang === 'es' ? 'VALIDAR REPORTE' : 'VALIDATE REPORT' ?>
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
                        <?= $lang === 'es' ? 'Inyecta la flag en la terminal de CyberEscudo:' : 'Inject the flag into the CyberEscudo terminal:' ?> <strong>submit <?= $flag ?></strong>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>