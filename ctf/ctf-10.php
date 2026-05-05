<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Reto CTF #10: Nmap Forensics — CyberEscudo' : 'CTF Challenge #10: Nmap Forensics — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Análisis forense de un log de Nmap.' : 'Forensic analysis of an Nmap log.';
$current_page = 'ctf/ctf-10.php';
require __DIR__ . '/../templates/header.php';

$feedback = "";
$flag = "";
$esExito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ans1 = trim($_POST['q1'] ?? '');
    $ans2 = trim($_POST['q2'] ?? '');
    $ans3 = strtolower(trim($_POST['q3'] ?? ''));
    
    // Validar respuestas (flexibilidad en mayúsculas/minúsculas y barras)
    $isQ1Correct = ($ans1 === '2121'); // Puerto FTP
    $isQ2Correct = ($ans2 === '/secret_backdoor/' || $ans2 === '/secret_backdoor' || $ans2 === 'secret_backdoor'); // Directorio
    $isQ3Correct = ($ans3 === 'linux' || $ans3 === 'ubuntu' || $ans3 === 'ubuntu linux'); // Sistema Operativo

    if ($isQ1Correct && $isQ2Correct && $isQ3Correct) {
        $esExito = true;
        $feedback = $lang === 'es' 
            ? "¡EXCELENTE TRABAJO DE RECONOCIMIENTO! Has identificado los vectores de ataque clave." 
            : "EXCELLENT RECONNAISSANCE WORK! You have identified the key attack vectors.";
        $flag = "FLAG{nmap_recon_analyst}";
    } else {
        $feedback = $lang === 'es' 
            ? "[ERROR] El informe de inteligencia es incorrecto. Vuelve a revisar el log."
            : "[ERROR] The intelligence report is incorrect. Review the log again.";
    }
}

// Log simulado de Nmap
$nmap_log = <<<LOG
Starting Nmap 7.93 ( https://nmap.org ) at 2026-05-05 10:00 CEST
Nmap scan report for megacorp.local (10.10.10.50)
Host is up (0.045s latency).
Not shown: 996 closed tcp ports (reset)
PORT     STATE SERVICE VERSION
22/tcp   open  ssh     OpenSSH 8.2p1 Ubuntu 4ubuntu0.1 (Ubuntu Linux; protocol 2.0)
| ssh-hostkey: 
|   3072 9c:81:4e:37:e2:a2:6b:c2:d9:95:48:42:ab:12:44:aa (RSA)
80/tcp   open  http    Apache httpd 2.4.41 ((Ubuntu))
|_http-title: Megacorp Intranet
|_http-server-header: Apache/2.4.41 (Ubuntu)
| http-enum:
|   /login.php: Login page
|   /images/: Potentially interesting directory
|_  /secret_backdoor/: Interesting, a hidden directory
443/tcp  open  ssl/http Apache httpd 2.4.41 ((Ubuntu))
| ssl-enum-ciphers: 
|   TLSv1.2: 
|     ciphers: 
|       TLS_RSA_WITH_AES_128_CBC_SHA (rsa 2048) - A
2121/tcp open  ftp     ProFTPD 1.3.5
| ftp-anon: Anonymous FTP login allowed (FTP code 230)
| drwxrwxrwx   2 111      113          4096 May  5 09:34 pub
MAC Address: 00:0C:29:AB:CD:EF (VMware)
Device type: general purpose
Running: Linux 4.X|5.X
OS CPE: cpe:/o:linux:linux_kernel:5.4
OS details: Linux 4.15 - 5.6
Network Distance: 1 hop
Service Info: OS: Linux; CPE: cpe:/o:linux:linux_kernel

OS and Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
Nmap done: 1 IP address (1 host up) scanned in 12.45 seconds
LOG;
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 900px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// <?= $lang === 'es' ? 'RETO CTF 10' : 'CTF CHALLENGE 10' ?></span>
        <h1 style="text-align: center; margin-bottom: 2rem;"><?= $lang === 'es' ? 'Análisis Forense de Nmap' : 'Nmap Forensics Analysis' ?></h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <p style="color: var(--gray); margin-bottom: 1.5rem; text-align: center;">
                <?= $lang === 'es' ? 'Tu compañero de Red Team ha ejecutado un escaneo <code>nmap -sV -sC -O</code> contra el servidor objetivo y te ha enviado el log en crudo. Léelo cuidadosamente y extrae la información requerida.' : 'Your Red Team partner ran an <code>nmap -sV -sC -O</code> scan against the target server and sent you the raw log. Read it carefully and extract the required information.' ?>
            </p>

            <div style="background: #000; padding: 1.5rem; border: 1px solid #333; border-radius: 5px; font-family: var(--mono); font-size: 0.85rem; color: #a1e6ff; white-space: pre-wrap; overflow-x: auto; margin-bottom: 2rem; line-height: 1.4;">
<?= htmlspecialchars($nmap_log) ?>
            </div>
            
            <form method="POST" action="">
                
                <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                    <div>
                        <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                            1. <?= $lang === 'es' ? '¿En qué PUERTO corre el servicio FTP?' : 'On which PORT is the FTP service running?' ?>
                        </label>
                        <input type="text" name="q1" class="cyber-input" style="width: 100%;" placeholder="Ej: 80" autocomplete="off" value="<?= htmlspecialchars($_POST['q1'] ?? '') ?>">
                    </div>

                    <div>
                        <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                            2. <?= $lang === 'es' ? '¿Cuál es la ruta EXACTA del directorio oculto encontrado por HTTP Enum?' : 'What is the EXACT path of the hidden directory found by HTTP Enum?' ?>
                        </label>
                        <input type="text" name="q2" class="cyber-input" style="width: 100%;" placeholder="Ej: /admin_panel/" autocomplete="off" value="<?= htmlspecialchars($_POST['q2'] ?? '') ?>">
                    </div>

                    <div>
                        <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                            3. <?= $lang === 'es' ? '¿Qué Sistema Operativo (familia principal) corre el servidor?' : 'What Operating System (main family) is the server running?' ?>
                        </label>
                        <input type="text" name="q3" class="cyber-input" style="width: 100%;" placeholder="Ej: Windows" autocomplete="off" value="<?= htmlspecialchars($_POST['q3'] ?? '') ?>">
                    </div>
                </div>
                
                <button type="submit" style="width: 100%; background: var(--cyan); border: none; color: #000; padding: 15px; font-family: var(--mono); font-weight: bold; cursor: pointer; font-size: 1rem; transition: background 0.3s;">
                    <?= $lang === 'es' ? 'ENVIAR INFORME DE INTELIGENCIA' : 'SUBMIT INTELLIGENCE REPORT' ?>
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
                        <?= $lang === 'es' ? 'Desbloquea tu rango en la terminal:' : 'Unlock your rank in the terminal:' ?> <strong>submit <?= $flag ?></strong>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>