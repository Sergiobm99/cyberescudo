<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Reto CTF #27: Smali Patching — CyberEscudo' : 'CTF Challenge #27: Smali Patching — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Simulador de ingeniería inversa y parcheo en Smali.' : 'Reverse engineering and Smali patching simulator.';
$current_page = 'ctf/ctf-27.php';
require __DIR__ . '/../templates/header.php';

$feedback = "";
$flag = "";
$esExito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ans1 = strtolower(trim($_POST['q1'] ?? ''));
    $ans2 = strtolower(trim($_POST['q2'] ?? ''));
    $ans3 = strtolower(trim($_POST['q3'] ?? ''));
    
    // Validar respuestas
    
    // Q1: Herramienta de de/recompilación a smali (apktool)
    $isQ1Correct = preg_match('/apktool/i', $ans1);
    
    // Q2: Instrucción invertida (if-nez)
    $isQ2Correct = preg_match('/if-nez/i', $ans2);
    
    // Q3: Herramienta de lectura Java (jadx, jadx-gui, o dex2jar)
    $isQ3Correct = preg_match('/(jadx|dex2jar|jd-gui)/i', $ans3);

    if ($isQ1Correct && $isQ2Correct && $isQ3Correct) {
        $esExito = true;
        $feedback = $lang === 'es' 
            ? "¡Binario parcheado y firmado con éxito! El malware ha ignorado el entorno protegido y ha ejecutado su carga útil. Tienes el control total." 
            : "Binary patched and signed successfully! The malware ignored the protected environment and executed its payload. You have full control.";
        $flag = "FLAG{smali_patching_ninja}";
    } else {
        $errores = [];
        if (!$isQ1Correct) $errores[] = "Reversing Tool (Q1)";
        if (!$isQ2Correct) $errores[] = "Smali Logic (Q2)";
        if (!$isQ3Correct) $errores[] = "Decompiler (Q3)";
        
        $feedback = $lang === 'es' 
            ? "[CRASH] La aplicación se ha cerrado inesperadamente. Errores en: " . implode(", ", $errores)
            : "[CRASH] The application closed unexpectedly. Errors in: " . implode(", ", $errores);
    }
}

$smali_code = <<<SMALI
.method private checkRoot()Z
    .locals 2
    invoke-static {}, Lcom/malware/Utils;->isRooted()Z
    move-result v0
    
    # [!] LÓGICA DE CONTROL DE FLUJO
    if-eqz v0, :cond_0
    
    # Ejecuta el Malware si pasa la comprobación
    invoke-virtual {p0}, Lcom/malware/Main;->startPayload()V
    
    :cond_0
    # Cierra la aplicación (Anti-Análisis)
    invoke-static {v1}, Ljava/lang/System;->exit(I)V
    return-void
.end method
SMALI;
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 900px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// <?= $lang === 'es' ? 'RETO CTF 27' : 'CTF CHALLENGE 27' ?></span>
        <h1 style="text-align: center; margin-bottom: 2rem;"><?= $lang === 'es' ? 'Reverse Engineering: Patching' : 'Reverse Engineering: Patching' ?></h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <p style="color: var(--gray); margin-bottom: 1.5rem; text-align: center;">
                <?= $lang === 'es' ? 'Analiza el siguiente bloque de código desensamblado (Smali) que pertenece a la función de comprobación de Root de un Malware.' : 'Analyze the following disassembled code block (Smali) belonging to a Malware\'s Root check function.' ?>
            </p>

            <div style="background: #000; padding: 1.5rem; border: 1px solid #333; border-radius: 5px; font-family: var(--mono); font-size: 0.9rem; color: #a1e6ff; white-space: pre-wrap; margin-bottom: 2rem; line-height: 1.4;">
<?= htmlspecialchars($smali_code) ?>
            </div>
            
            <form method="POST" action="">
                
                <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                    <div>
                        <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                            1. <?= $lang === 'es' ? '¿Qué herramienta por línea de comandos utilizaste para extraer este código Smali y recompilarlo luego?' : 'What command-line tool did you use to extract this Smali code and recompile it later?' ?>
                        </label>
                        <input type="text" name="q1" class="cyber-input" style="width: 100%;" placeholder="Ej: nombre_herramienta" autocomplete="off" value="<?= htmlspecialchars($_POST['q1'] ?? '') ?>">
                    </div>

                    <div>
                        <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                            2. <?= $lang === 'es' ? 'Observa la línea <code>if-eqz v0, :cond_0</code>. ¿Por qué instrucción exacta la sustituirías para invertir la lógica y engañar a la app?' : 'Look at the line <code>if-eqz v0, :cond_0</code>. What exact instruction would you replace it with to invert the logic and trick the app?' ?>
                        </label>
                        <input type="text" name="q2" class="cyber-input" style="width: 100%;" placeholder="Ej: if-..." autocomplete="off" value="<?= htmlspecialchars($_POST['q2'] ?? '') ?>">
                    </div>

                    <div>
                        <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                            3. <?= $lang === 'es' ? 'Antes de tocar el Smali, seguramente leíste el código Java de forma cómoda. Nombra una herramienta capaz de decompilar una APK a código Java legible.' : 'Before touching Smali, you probably read the Java code comfortably. Name a tool capable of decompiling an APK to readable Java code.' ?>
                        </label>
                        <input type="text" name="q3" class="cyber-input" style="width: 100%;" placeholder="Ej: jadx" autocomplete="off" value="<?= htmlspecialchars($_POST['q3'] ?? '') ?>">
                    </div>
                </div>
                
                <button type="submit" style="width: 100%; background: var(--cyan); border: none; color: #000; padding: 15px; font-family: var(--mono); font-weight: bold; cursor: pointer; font-size: 1rem; transition: background 0.3s;">
                    <?= $lang === 'es' ? 'INYECCIÓN SMALI Y RECOMPILACIÓN' : 'SMALI INJECTION & RECOMPILE' ?>
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
                        <?= $lang === 'es' ? 'Ejecuta esto en la terminal principal del sistema:' : 'Run this in the main system terminal:' ?> <strong>submit <?= $flag ?></strong>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>