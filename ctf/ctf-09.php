<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Reto CTF #09: Code Review — CyberEscudo' : 'CTF Challenge #09: Code Review — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Simulador SAST: Auditoría y parcheo de código vulnerable.' : 'SAST Simulator: Vulnerable code auditing and patching.';
$current_page = 'ctf/ctf-09.php';
require __DIR__ . '/../templates/header.php';

$feedback = "";
$flag = "";
$esExito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger las respuestas (en minúsculas y limpias de espacios)
    $ans1 = strtolower(trim($_POST['patch1'] ?? ''));
    $ans2 = strtolower(trim($_POST['patch2'] ?? ''));
    $ans3 = strtolower(trim($_POST['patch3'] ?? ''));
    
    // Validar las soluciones correctas de Desarrollo Seguro en PHP
    // Patch 1: XSS -> htmlspecialchars o htmlentities
    $isP1Correct = ($ans1 === 'htmlspecialchars' || $ans1 === 'htmlentities');
    
    // Patch 2: SQLi -> prepare (de PDO)
    $isP2Correct = ($ans2 === 'prepare');
    
    // Patch 3: Hashing -> password_hash
    $isP3Correct = ($ans3 === 'password_hash');

    if ($isP1Correct && $isP2Correct && $isP3Correct) {
        $esExito = true;
        $feedback = $lang === 'es' 
            ? "¡PIPELINE APROBADO! Todos los parches de seguridad han sido aplicados correctamente. Código listo para producción." 
            : "PIPELINE APPROVED! All security patches applied correctly. Code ready for production.";
        $flag = "FLAG{secure_code_reviewer}";
    } else {
        $errores = [];
        if (!$isP1Correct) $errores[] = "Snippet 1";
        if (!$isP2Correct) $errores[] = "Snippet 2";
        if (!$isP3Correct) $errores[] = "Snippet 3";
        
        $feedback = $lang === 'es' 
            ? "[SAST ALERTA] Fallo de compilación. Vulnerabilidades críticas no resueltas en: " . implode(", ", $errores)
            : "[SAST ALERT] Build failed. Critical unresolved vulnerabilities in: " . implode(", ", $errores);
    }
}
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 900px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// <?= $lang === 'es' ? 'RETO CTF 09' : 'CTF CHALLENGE 09' ?></span>
        <h1 style="text-align: center; margin-bottom: 2rem;"><?= $lang === 'es' ? 'Auditoría de Código (SAST)' : 'Code Audit (SAST)' ?></h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <div style="background: #111; padding: 1.5rem; border-left: 4px solid var(--cyan); margin-bottom: 2rem;">
                <h3 style="color: var(--white); margin-top: 0;"><?= $lang === 'es' ? 'PR #404: Revisión de Seguridad' : 'PR #404: Security Review' ?></h3>
                <p style="color: var(--gray); font-size: 0.95rem; line-height: 1.6; margin-bottom: 0;">
                    <?= $lang === 'es' ? 'El equipo de desarrollo ha subido código heredado a la rama <code>main</code>. Como analista DevSecOps, tu pipeline ha bloqueado la subida porque detectó funciones inseguras. Introduce en los campos el nombre exacto de la <strong>función de PHP segura</strong> que debería usarse para solucionar cada vulnerabilidad.' : 'The dev team pushed legacy code to <code>main</code>. As a DevSecOps analyst, your pipeline blocked the commit due to insecure functions. Enter the exact name of the <strong>secure PHP function</strong> that should be used to fix each vulnerability.' ?>
                </p>
            </div>
            
            <form method="POST" action="">
                
                <!-- SNIPPET 1: XSS -->
                <div style="margin-bottom: 2rem; padding: 1rem; background: #050505; border: 1px solid #333; border-radius: 5px;">
                    <h4 style="color: #ff2a2a; margin-top: 0; font-family: var(--mono);">[ ! ] Vulnerability: Cross-Site Scripting (XSS)</h4>
                    <pre style="color: #ccc; background: #000; padding: 10px; font-size: 0.85rem; margin-bottom: 15px;"><code>&lt;div class="comment"&gt;
    &lt;?php echo $_POST['user_comment']; ?&gt;
&lt;/div&gt;</code></pre>
                    <label style="color: var(--cyan); font-family: var(--mono); font-size: 0.9rem;">
                        <?= $lang === 'es' ? '¿Qué función usarías para escapar el output?' : 'What function would you use to escape the output?' ?>
                    </label>
                    <input type="text" name="patch1" class="cyber-input" style="width: 100%; margin-top: 5px;" placeholder="Ej: strip_tags" autocomplete="off" value="<?= htmlspecialchars($_POST['patch1'] ?? '') ?>">
                </div>

                <!-- SNIPPET 2: SQLi -->
                <div style="margin-bottom: 2rem; padding: 1rem; background: #050505; border: 1px solid #333; border-radius: 5px;">
                    <h4 style="color: #ff2a2a; margin-top: 0; font-family: var(--mono);">[ ! ] Vulnerability: SQL Injection (SQLi)</h4>
                    <pre style="color: #ccc; background: #000; padding: 10px; font-size: 0.85rem; margin-bottom: 15px;"><code>$email = $_GET['email'];
$db->query("SELECT * FROM users WHERE email = '$email'");</code></pre>
                    <label style="color: var(--cyan); font-family: var(--mono); font-size: 0.9rem;">
                        <?= $lang === 'es' ? '¿Qué método de PDO usarías en lugar de query()?' : 'What PDO method would you use instead of query()?' ?>
                    </label>
                    <input type="text" name="patch2" class="cyber-input" style="width: 100%; margin-top: 5px;" placeholder="Ej: execute" autocomplete="off" value="<?= htmlspecialchars($_POST['patch2'] ?? '') ?>">
                </div>

                <!-- SNIPPET 3: Crypto -->
                <div style="margin-bottom: 2rem; padding: 1rem; background: #050505; border: 1px solid #333; border-radius: 5px;">
                    <h4 style="color: #ff2a2a; margin-top: 0; font-family: var(--mono);">[ ! ] Vulnerability: Insecure Cryptography</h4>
                    <pre style="color: #ccc; background: #000; padding: 10px; font-size: 0.85rem; margin-bottom: 15px;"><code>$pass = $_POST['password'];
$secure_pass = md5($pass);
saveToDatabase($secure_pass);</code></pre>
                    <label style="color: var(--cyan); font-family: var(--mono); font-size: 0.9rem;">
                        <?= $lang === 'es' ? '¿Qué función moderna de PHP usarías para hashear la contraseña?' : 'What modern PHP function would you use to hash the password?' ?>
                    </label>
                    <input type="text" name="patch3" class="cyber-input" style="width: 100%; margin-top: 5px;" placeholder="Ej: crypt" autocomplete="off" value="<?= htmlspecialchars($_POST['patch3'] ?? '') ?>">
                </div>
                
                <button type="submit" style="width: 100%; background: var(--cyan); border: none; color: #000; padding: 15px; font-family: var(--mono); font-weight: bold; cursor: pointer; font-size: 1rem; transition: background 0.3s;">
                    <?= $lang === 'es' ? 'EJECUTAR PIPELINE (BUILD)' : 'RUN PIPELINE (BUILD)' ?>
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
                        <?= $lang === 'es' ? 'Canjea tu logro en la terminal:' : 'Redeem your achievement in the terminal:' ?> <strong>submit <?= $flag ?></strong>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>