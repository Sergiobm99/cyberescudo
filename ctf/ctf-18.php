<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Reto CTF #18: Burp Suite Mastery — CyberEscudo' : 'CTF Challenge #18: Burp Suite Mastery — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Simulador de configuración y uso avanzado de Burp Suite.' : 'Burp Suite advanced configuration and usage simulator.';
$current_page = 'ctf/ctf-18.php';
require __DIR__ . '/../templates/header.php';

$feedback = "";
$flag = "";
$esExito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ans1 = strtolower(trim($_POST['q1'] ?? ''));
    $ans2 = strtolower(trim($_POST['q2'] ?? ''));
    $ans3 = strtolower(trim($_POST['q3'] ?? ''));
    
    // Validar respuestas
    // 1. Intruder attack type para probar todas las combinaciones (Cluster bomb)
    $isQ1Correct = ($ans1 === 'cluster bomb' || $ans1 === 'clusterbomb');
    
    // 2. Módulo matemático para analizar aleatoriedad de tokens
    $isQ2Correct = ($ans2 === 'sequencer');
    
    // 3. Extensión famosa para encontrar vulnerabilidades de autorización (Autorize)
    $isQ3Correct = ($ans3 === 'autorize' || $ans3 === 'authorize');

    if ($isQ1Correct && $isQ2Correct && $isQ3Correct) {
        $esExito = true;
        $feedback = $lang === 'es' 
            ? "¡Configuración perfecta! Los módulos han hecho su trabajo, has interceptado el token y comprometido el servidor." 
            : "Perfect configuration! The modules did their job, you intercepted the token and compromised the server.";
        $flag = "FLAG{burp_suite_jedi}";
    } else {
        $errores = [];
        if (!$isQ1Correct) $errores[] = "Pregunta 1";
        if (!$isQ2Correct) $errores[] = "Pregunta 2";
        if (!$isQ3Correct) $errores[] = "Pregunta 3";
        
        $feedback = $lang === 'es' 
            ? "[ERROR] Falla en la configuración de los módulos: " . implode(", ", $errores)
            : "[ERROR] Module configuration failure: " . implode(", ", $errores);
    }
}
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 900px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// <?= $lang === 'es' ? 'RETO CTF 18' : 'CTF CHALLENGE 18' ?></span>
        <h1 style="text-align: center; margin-bottom: 2rem;"><?= $lang === 'es' ? 'Auditoría Web Avanzada' : 'Advanced Web Audit' ?></h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <p style="color: var(--gray); margin-bottom: 2rem; text-align: center; font-size: 1.05rem;">
                <?= $lang === 'es' ? 'Has interceptado tráfico hacia una API bancaria. Necesitas configurar Burp Suite correctamente para ejecutar tus ataques. Responde a las 3 preguntas de configuración del proxy.' : 'You have intercepted traffic to a banking API. You need to configure Burp Suite correctly to execute your attacks. Answer the 3 proxy configuration questions.' ?>
            </p>
            
            <form method="POST" action="">
                
                <div style="margin-bottom: 1.5rem; background: #050505; border: 1px solid #333; padding: 1.5rem; border-radius: 5px;">
                    <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                        [1] <?= $lang === 'es' ? 'Configuración del Intruder' : 'Intruder Configuration' ?>
                    </label>
                    <p style="color: #aaa; font-size: 0.9rem; margin-top: 0; margin-bottom: 10px;">
                        <?= $lang === 'es' ? 'Tienes un formulario con <code>user=§U§</code> y <code>pass=§P§</code>. Quieres probar <strong>todas las combinaciones posibles</strong> cruzando tu lista de usuarios con tu lista de contraseñas. ¿Qué tipo de ataque ("Attack Type") del Intruder debes seleccionar?' : 'You have a form with <code>user=§U§</code> and <code>pass=§P§</code>. You want to test <strong>all possible combinations</strong> crossing your user list with your password list. What Intruder "Attack Type" should you select?' ?>
                    </p>
                    <input type="text" name="q1" class="cyber-input" style="width: 100%;" placeholder="Ej: Sniper" autocomplete="off" value="<?= htmlspecialchars($_POST['q1'] ?? '') ?>">
                </div>

                <div style="margin-bottom: 1.5rem; background: #050505; border: 1px solid #333; padding: 1.5rem; border-radius: 5px;">
                    <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                        [2] <?= $lang === 'es' ? 'Análisis de Tokens' : 'Token Analysis' ?>
                    </label>
                    <p style="color: #aaa; font-size: 0.9rem; margin-top: 0; margin-bottom: 10px;">
                        <?= $lang === 'es' ? 'Has notado que la API emite una cookie llamada <code>AuthToken</code>. Tienes dudas de si está generada criptográficamente segura. ¿A qué pestaña principal (módulo) de Burp Suite enviarías la petición para analizar matemáticamente la entropía y aleatoriedad del token?' : 'You noticed the API issues a cookie called <code>AuthToken</code>. You doubt if it is cryptographically securely generated. To which main Burp Suite tab (module) would you send the request to mathematically analyze the token\'s entropy and randomness?' ?>
                    </p>
                    <input type="text" name="q2" class="cyber-input" style="width: 100%;" placeholder="Ej: Repeater" autocomplete="off" value="<?= htmlspecialchars($_POST['q2'] ?? '') ?>">
                </div>

                <div style="margin-bottom: 2rem; background: #050505; border: 1px solid #333; padding: 1.5rem; border-radius: 5px;">
                    <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                        [3] <?= $lang === 'es' ? 'Extensiones (BApp Store)' : 'Extensions (BApp Store)' ?>
                    </label>
                    <p style="color: #aaa; font-size: 0.9rem; margin-top: 0; margin-bottom: 10px;">
                        <?= $lang === 'es' ? 'Necesitas testear vulnerabilidades IDOR y escalada de privilegios repitiendo automáticamente tus peticiones de Admin como si fueras un usuario raso. ¿Cuál es la extensión más famosa del BApp Store que hace esto?' : 'You need to test for IDOR and privilege escalation vulnerabilities by automatically repeating your Admin requests as if you were a low-level user. What is the most famous BApp Store extension that does this?' ?>
                    </p>
                    <input type="text" name="q3" class="cyber-input" style="width: 100%;" placeholder="Ej: Param Miner" autocomplete="off" value="<?= htmlspecialchars($_POST['q3'] ?? '') ?>">
                </div>
                
                <button type="submit" style="width: 100%; background: var(--cyan); border: none; color: #000; padding: 15px; font-family: var(--mono); font-weight: bold; cursor: pointer; font-size: 1rem; transition: background 0.3s;">
                    <?= $lang === 'es' ? 'EJECUTAR CADENA DE ATAQUE' : 'EXECUTE ATTACK CHAIN' ?>
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
                        <?= $lang === 'es' ? 'Valida tu flag en la terminal principal:' : 'Validate your flag in the main terminal:' ?> <strong>submit <?= $flag ?></strong>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>