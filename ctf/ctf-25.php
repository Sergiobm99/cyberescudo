<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Reto CTF #25: DIVA Audit — CyberEscudo' : 'CTF Challenge #25: DIVA Audit — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Simulador de comandos ADB y análisis de Android.' : 'ADB commands and Android analysis simulator.';
$current_page = 'ctf/ctf-25.php';
require __DIR__ . '/../templates/header.php';

$feedback = "";
$flag = "";
$esExito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ans1 = trim($_POST['q1'] ?? '');
    $ans2 = trim($_POST['q2'] ?? '');
    $ans3 = trim($_POST['q3'] ?? '');
    
    // Validar respuestas
    
    // Q1: logcat | grep diva
    $isQ1Correct = preg_match('/logcat\s*\|\s*grep/i', $ans1);
    
    // Q2: /data/data/jakhar.aseem.diva/databases
    $isQ2Correct = preg_match('/\/data\/data\/jakhar\.aseem\.diva\/databases\/?/i', $ans2);
    
    // Q3: jadx o jadx-gui
    $isQ3Correct = preg_match('/jadx/i', $ans3);

    if ($isQ1Correct && $isQ2Correct && $isQ3Correct) {
        $esExito = true;
        $feedback = $lang === 'es' 
            ? "¡Auditoría superada! Has extraído exitosamente las credenciales de la base de datos, interceptado las tarjetas de crédito del log, y revertido el código Java." 
            : "Audit passed! You successfully extracted database credentials, intercepted credit cards from the log, and reversed the Java code.";
        $flag = "FLAG{diva_android_auditor}";
    } else {
        $errores = [];
        if (!$isQ1Correct) $errores[] = "Insecure Logging (Q1)";
        if (!$isQ2Correct) $errores[] = "Sandbox Path (Q2)";
        if (!$isQ3Correct) $errores[] = "Reverse Engineering Tool (Q3)";
        
        $feedback = $lang === 'es' 
            ? "[ERROR] Comandos o rutas incorrectas en: " . implode(", ", $errores)
            : "[ERROR] Incorrect commands or paths in: " . implode(", ", $errores);
    }
}
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 900px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// <?= $lang === 'es' ? 'RETO CTF 25' : 'CTF CHALLENGE 25' ?></span>
        <h1 style="text-align: center; margin-bottom: 2rem;"><?= $lang === 'es' ? 'Mobile Forensics & ADB' : 'Mobile Forensics & ADB' ?></h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <p style="color: var(--gray); margin-bottom: 2rem; text-align: center; font-size: 1.05rem;">
                <?= $lang === 'es' ? 'Demuestra tus conocimientos sobre la arquitectura interna de Android y ADB respondiendo a las preguntas de la auditoría de DIVA.' : 'Demonstrate your knowledge of Android\'s internal architecture and ADB by answering the DIVA audit questions.' ?>
            </p>
            
            <form method="POST" action="">
                
                <!-- Q1 -->
                <div style="margin-bottom: 1.5rem; background: #050505; border: 1px solid #333; padding: 1.5rem; border-radius: 5px;">
                    <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                        [1] Monitorización de Registros (Logs)
                    </label>
                    <p style="color: #aaa; font-size: 0.9rem; margin-top: 0; margin-bottom: 10px;">
                        <?= $lang === 'es' ? 'Estás dentro de <code>adb shell</code>. ¿Qué comando utilizarías para ver los registros del sistema operativo y a su vez <strong>filtrar</strong> la salida utilizando tuberías (pipes) para mostrar solo la etiqueta "diva-log"?' : 'You are inside <code>adb shell</code>. What command would you use to view the OS logs and <strong>filter</strong> the output using pipes to only show the "diva-log" tag?' ?>
                    </p>
                    <input type="text" name="q1" class="cyber-input" style="width: 100%;" placeholder="Ej: comando | grep ..." autocomplete="off" value="<?= htmlspecialchars($_POST['q1'] ?? '') ?>">
                </div>

                <!-- Q2 -->
                <div style="margin-bottom: 1.5rem; background: #050505; border: 1px solid #333; padding: 1.5rem; border-radius: 5px;">
                    <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                        [2] Android Sandboxing (Bases de datos)
                    </label>
                    <p style="color: #aaa; font-size: 0.9rem; margin-top: 0; margin-bottom: 10px;">
                        <?= $lang === 'es' ? 'Has conseguido ser <code>root</code> en el dispositivo móvil. ¿Cuál es la ruta absoluta (empezando por la raíz <code>/</code>) hacia el directorio donde se almacenan las <strong>databases</strong> privadas de la aplicación DIVA (cuyo nombre de paquete es <code>jakhar.aseem.diva</code>)?' : 'You got <code>root</code> on the mobile device. What is the absolute path (starting from the root <code>/</code>) to the directory where the private <strong>databases</strong> of the DIVA app (package name <code>jakhar.aseem.diva</code>) are stored?' ?>
                    </p>
                    <input type="text" name="q2" class="cyber-input" style="width: 100%;" placeholder="Ej: /ruta/hacia/las/bases/de/datos/" autocomplete="off" value="<?= htmlspecialchars($_POST['q2'] ?? '') ?>">
                </div>

                <!-- Q3 -->
                <div style="margin-bottom: 2rem; background: #050505; border: 1px solid #333; padding: 1.5rem; border-radius: 5px;">
                    <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                        [3] Ingeniería Inversa (Reverse Engineering)
                    </label>
                    <p style="color: #aaa; font-size: 0.9rem; margin-top: 0; margin-bottom: 10px;">
                        <?= $lang === 'es' ? 'Leer bytecode de Dalvik (Smali) es complicado. ¿Qué potente herramienta open-source usarías para descompilar una APK y leer su código directamente en <strong>Java</strong> legible?' : 'Reading Dalvik bytecode (Smali) is hard. What powerful open-source tool would you use to decompile an APK and read its code directly in readable <strong>Java</strong>?' ?>
                    </p>
                    <input type="text" name="q3" class="cyber-input" style="width: 100%;" placeholder="Nombre de la herramienta..." autocomplete="off" value="<?= htmlspecialchars($_POST['q3'] ?? '') ?>">
                </div>
                
                <button type="submit" style="width: 100%; background: var(--cyan); border: none; color: #000; padding: 15px; font-family: var(--mono); font-weight: bold; cursor: pointer; font-size: 1rem; transition: background 0.3s;">
                    <?= $lang === 'es' ? 'COMPILAR REPORTE DE AUDITORÍA' : 'COMPILE AUDIT REPORT' ?>
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
                        <?= $lang === 'es' ? 'Ingresa tu medalla en la terminal CyberEscudo:' : 'Enter your medal in the CyberEscudo terminal:' ?> <strong>submit <?= $flag ?></strong>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>