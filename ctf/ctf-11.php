<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Reto CTF #11: Hydra Web Auth — CyberEscudo' : 'CTF Challenge #11: Hydra Web Auth — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Simulador de construcción de payloads HTTP para Hydra.' : 'HTTP payload construction simulator for Hydra.';
$current_page = 'ctf/ctf-11.php';
require __DIR__ . '/../templates/header.php';

$feedback = "";
$flag = "";
$esExito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cmd'])) {
    $cmd = trim($_POST['cmd']);
    
    // Validamos el comando mediante expresiones regulares
    $isHydra = preg_match('/^hydra/i', $cmd);
    $hasUser = preg_match('/-l\s+["\']?admin["\']?/i', $cmd);
    $hasWordlist = preg_match('/-P\s+["\']?rockyou\.txt["\']?/i', $cmd);
    $hasIP = preg_match('/10\.10\.20\.50/i', $cmd);
    $hasModule = preg_match('/http-post-form/i', $cmd);
    
    // Validar el string del form: "/panel/login.php:usr=^USER^&pwd=^PASS^:Incorrect"
    // Permitimos cierta flexibilidad en el espaciado
    $hasFormString = preg_match('/["\']?\/panel\/login\.php:usr=\^USER\^&pwd=\^PASS\^:Incorrect["\']?/i', $cmd);

    if ($isHydra && $hasUser && $hasWordlist && $hasIP && $hasModule && $hasFormString) {
        $esExito = true;
        $feedback = $lang === 'es' 
            ? "¡Sintaxis perfecta! El ataque ha comenzado y Hydra ha encontrado la contraseña en 3.4 segundos." 
            : "Perfect syntax! The attack started and Hydra found the password in 3.4 seconds.";
        $flag = "FLAG{hydra_syntax_master}";
    } else {
        $errores = [];
        if (!$isHydra) $errores[] = $lang === 'es' ? "Debe empezar por 'hydra'" : "Must start with 'hydra'";
        if (!$hasUser) $errores[] = $lang === 'es' ? "Falta el usuario objetivo (-l admin)" : "Missing target user (-l admin)";
        if (!$hasWordlist) $errores[] = $lang === 'es' ? "Falta el diccionario (-P rockyou.txt)" : "Missing wordlist (-P rockyou.txt)";
        if (!$hasIP) $errores[] = $lang === 'es' ? "Falta la IP objetivo (10.10.20.50)" : "Missing target IP (10.10.20.50)";
        if (!$hasModule) $errores[] = $lang === 'es' ? "Falta el módulo correcto (http-post-form)" : "Missing correct module (http-post-form)";
        if (!$hasFormString) $errores[] = $lang === 'es' ? "El string del formulario es incorrecto. Recuerda: 'ruta:cuerpo:mensaje_de_error'" : "The form string is incorrect. Remember: 'path:body:error_message'";
        
        $feedback = "[ERROR] " . implode(" | ", $errores);
    }
}

// Petición HTTP falsa interceptada
$http_request = <<<REQUEST
POST /panel/login.php HTTP/1.1
Host: 10.10.20.50
User-Agent: Mozilla/5.0
Content-Type: application/x-www-form-urlencoded
Content-Length: 27

usr=admin&pwd=MySecretPassword

HTTP/1.1 200 OK
Content-Type: text/html

<div class="alert">Incorrect password, please try again.</div>
REQUEST;
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 900px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// <?= $lang === 'es' ? 'RETO CTF 11' : 'CTF CHALLENGE 11' ?></span>
        <h1 style="text-align: center; margin-bottom: 2rem;"><?= $lang === 'es' ? 'Hydra Web Cracker' : 'Hydra Web Cracker' ?></h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <p style="color: var(--gray); margin-bottom: 1.5rem;">
                <?= $lang === 'es' ? 'Has interceptado la siguiente petición HTTP de inicio de sesión. Sabes que el usuario es <strong>admin</strong>. Utiliza el diccionario <strong>rockyou.txt</strong> y la palabra clave "<strong>Incorrect</strong>" como condición de fallo para escribir el comando de Hydra.' : 'You intercepted the following HTTP login request. You know the user is <strong>admin</strong>. Use the wordlist <strong>rockyou.txt</strong> and the keyword "<strong>Incorrect</strong>" as the failure condition to write the Hydra command.' ?>
            </p>

            <h4 style="color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">[ Intercepted Request - BurpSuite ]</h4>
            <div style="background: #000; padding: 1.5rem; border: 1px solid #333; border-radius: 5px; font-family: var(--mono); font-size: 0.85rem; color: #ffb86c; white-space: pre-wrap; margin-bottom: 2rem; line-height: 1.4;">
<?= htmlspecialchars($http_request) ?>
            </div>
            
            <form method="POST" action="">
                <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                    <?= $lang === 'es' ? 'Comando Hydra:' : 'Hydra Command:' ?>
                </label>
                <div style="display: flex; align-items: center; background: #050505; border: 1px solid #333; padding: 0.5rem; border-radius: 4px; margin-bottom: 1.5rem;">
                    <span style="color: #ff2a2a; margin-right: 10px; font-family: var(--mono);">root@kali:~#</span>
                    <input type="text" name="cmd" class="cyber-input" style="flex: 1; border: none; background: transparent; padding: 0; box-shadow: none;" placeholder="hydra -l ..." autocomplete="off" value="<?= htmlspecialchars($_POST['cmd'] ?? '') ?>">
                </div>
                
                <button type="submit" style="width: 100%; background: var(--cyan); border: none; color: #000; padding: 15px; font-family: var(--mono); font-weight: bold; cursor: pointer; font-size: 1rem; transition: background 0.3s;">
                    <?= $lang === 'es' ? 'BRUTE FORCE (LAUNCH)' : 'BRUTE FORCE (LAUNCH)' ?>
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
                        <?= $lang === 'es' ? 'Terminal oculta. Valida tu bandera:' : 'Hidden terminal. Validate your flag:' ?> <strong>submit <?= $flag ?></strong>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>