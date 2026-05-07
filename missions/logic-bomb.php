<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'OP: SECURE DEV — Mission Center' : 'OP: SECURE DEV — Mission Center';
$current_page = 'missions/logic-bomb.php';
require __DIR__ . '/../templates/header.php';
?>

<style>
    .briefing-container {
        max-width: 850px;
        margin: 4rem auto;
        background: #0a0a0a;
        border: 1px solid #333;
        border-top: 4px solid var(--cyan);
        padding: 3rem;
        position: relative;
        box-shadow: 0 10px 30px rgba(0,0,0,0.8);
    }

    .briefing-header {
        border-bottom: 1px dashed #444;
        padding-bottom: 1.5rem;
        margin-bottom: 2rem;
    }

    .classification {
        color: var(--cyan);
        font-family: var(--mono);
        font-weight: bold;
        letter-spacing: 2px;
        display: inline-block;
        border: 1px solid var(--cyan);
        padding: 4px 10px;
        margin-bottom: 1rem;
        font-size: 0.8rem;
    }

    .briefing-title {
        font-size: 2.2rem;
        color: #fff;
        margin: 0;
        text-transform: uppercase;
        font-family: var(--mono);
    }

    .intel-block {
        background: rgba(255, 255, 255, 0.02);
        border-left: 2px solid #aaa;
        padding: 1.5rem;
        margin-bottom: 2rem;
        font-family: var(--mono);
        color: #ccc;
        line-height: 1.6;
    }

    /* Estilos del visor de código */
    .code-viewer {
        background: #050505;
        border: 1px solid #222;
        border-radius: 6px;
        margin: 2rem 0;
        overflow: hidden;
    }

    .code-header {
        background: #111;
        padding: 10px 15px;
        border-bottom: 1px solid #222;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-family: var(--mono);
        font-size: 0.85rem;
        color: #888;
    }

    .code-body {
        padding: 1.5rem;
        margin: 0;
        font-family: 'Courier New', Courier, monospace;
        font-size: 0.9rem;
        color: #e0e0e0;
        overflow-x: auto;
        line-height: 1.5;
    }

    /* Resaltado de sintaxis básico para Python */
    .kw { color: #cc7832; font-weight: bold; } /* Keywords */
    .str { color: #6a8759; } /* Strings */
    .fn { color: #ffc66d; } /* Functions */
    .com { color: #808080; font-style: italic; } /* Comments */

    .validation-hint {
        margin-top: 3rem;
        font-family: var(--mono);
        font-size: 0.85rem;
        color: #666;
        text-align: center;
        border-top: 1px dashed #333;
        padding-top: 1.5rem;
    }
</style>

<main class="content-page">
    <div class="briefing-container">
        
        <div class="briefing-header">
            <div class="classification"><?= $lang === 'es' ? 'NIVEL: INTERMEDIO' : 'LEVEL: INTERMEDIATE' ?></div>
            <h1 class="briefing-title">OP: SECURE DEV</h1>
            <div style="color: #666; font-family: var(--mono); margin-top: 10px;">
                <?= $lang === 'es' ? 'ID EXPEDIENTE:' : 'CASE ID:' ?> #AUDIT-PY-99X
            </div>
        </div>

        <div class="intel-block">
            <strong style="color: #fff;"><?= $lang === 'es' ? '[ INFORME DE INTELIGENCIA ]' : '[ INTELLIGENCE BRIEF ]' ?></strong><br><br>
            <?= $lang === 'es' ? 
                'Un desarrollador descontento (Insider Threat) fue despedido recientemente. Sospechamos que introdujo una "Bomba Lógica" (Backdoor) en el script de mantenimiento automatizado del servidor antes de irse.<br><br>Tu misión es auditar el código fuente, localizar la vulnerabilidad que permite la ejecución remota de comandos (RCE) y extraer la clave de acceso que el atacante dejó ofuscada.' : 
                'A disgruntled developer (Insider Threat) was recently terminated. We suspect they introduced a "Logic Bomb" (Backdoor) into the server\'s automated maintenance script before leaving.<br><br>Your mission is to audit the source code, locate the vulnerability allowing Remote Command Execution (RCE), and extract the access key the attacker left obfuscated.' 
            ?>
        </div>

        <h3 style="color: #eee; margin-bottom: 1rem;"><?= $lang === 'es' ? 'Evidencia: task_runner.py' : 'Evidence: task_runner.py' ?></h3>
        
        <div class="code-viewer">
            <div class="code-header">
                <span><span style="color: var(--cyan);">~/repo/scripts/</span>task_runner.py</span>
                <span>Python 3.9</span>
            </div>
            <pre class="code-body"><code><span class="kw">import</span> os
<span class="kw">import</span> json
<span class="kw">import</span> base64

<span class="kw">def</span> <span class="fn">process_maintenance_task</span>(task_payload):
    <span class="com">"""
    <?= $lang === 'es' ? 'Procesa las tareas de mantenimiento enviadas por el dashboard interno.' : 'Processes maintenance tasks sent by the internal dashboard.' ?>
    """</span>
    <span class="kw">try</span>:
        task = json.loads(task_payload)
        task_name = task.get(<span class="str">"name"</span>, <span class="str">"unknown"</span>)
        
        <span class="com"># <?= $lang === 'es' ? 'Validación de seguridad estándar' : 'Standard security validation' ?></span>
        <span class="kw">if</span> task_name == <span class="str">"backup"</span>:
            os.system(<span class="str">"tar -czvf /backup/sys.tar.gz /var/www/"</span>)
            <span class="kw">return</span> <span class="str">"Backup complete"</span>
            
        <span class="com"># <?= $lang === 'es' ? 'Mantenimiento de logs' : 'Log maintenance' ?></span>
        <span class="kw">elif</span> task_name == <span class="str">"clear_logs"</span>:
            os.system(<span class="str">"rm -rf /var/log/temp/*"</span>)
            <span class="kw">return</span> <span class="str">"Logs cleared"</span>

        <span class="com"># --- DEPRECATED OVERRIDE MODULE ---</span>
        <span class="kw">elif</span> task_name == <span class="str">"system_override_99"</span>:
            <span class="com"># <?= $lang === 'es' ? 'TODO: Eliminar en la próxima versión. Dejado para debug.' : 'TODO: Remove in next build. Left for debugging.' ?></span>
            secret_key = <span class="str">"RkxBR3tweXRob25fZXZhbF9pc19ldmlsXzk5eH0="</span>
            
            <span class="kw">if</span> task.get(<span class="str">"auth"</span>) == base64.b64decode(secret_key).decode(<span class="str">'utf-8'</span>):
                <span class="com"># Peligro: Ejecución dinámica de código introducido por el usuario</span>
                eval(task.get(<span class="str">"debug_cmd"</span>))
                
    <span class="kw">except</span> Exception <span class="kw">as</span> e:
        <span class="kw">return</span> <span class="str">f"Error: {e}"</span></code></pre>
        </div>

       <div class="validation-hint">
            <?= $lang === 'es' ? 'Identifica la ofuscación y decodifica la clave. Luego abre la terminal e introduce:' : 'Identify the obfuscation and decode the key. Then open the terminal and enter:' ?><br>
            <strong style="color: var(--cyan);">submit OP-SECURE-DEV FLAG{...}</strong>
            
            <div style="margin-top: 25px;">
                <a href="/tool-base64.php" target="_blank" 
                   style="display: inline-block; background: rgba(0, 255, 255, 0.05); border: 1px solid rgba(0, 255, 255, 0.2); color: var(--cyan); font-family: var(--mono); padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 0.8rem; transition: all 0.3s;" 
                   onmouseover="this.style.background='rgba(0, 255, 255, 0.1)'; this.style.borderColor='var(--cyan)'; this.style.boxShadow='0 0 10px rgba(0, 255, 255, 0.2)';" 
                   onmouseout="this.style.background='rgba(0, 255, 255, 0.05)'; this.style.borderColor='rgba(0, 255, 255, 0.2)'; this.style.boxShadow='none';">
                    <span style="opacity: 0.5; margin-right: 5px;">[SYS_TOOL]</span> <?= $lang === 'es' ? 'Lanzar Decodificador' : 'Launch Decoder' ?> ↗
                </a>
            </div>
        </div>

    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>