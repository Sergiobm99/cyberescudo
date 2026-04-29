<?php
/**
 * CyberEscudo — Manual: .htaccess, Mod_Security y Mod_Evasive
 * Contenido: Práctica 2 (PRCTIC_2.DOC) — Sergio Belmonte Morales
 */
require_once __DIR__ . '/../bootstrap.php';

$pageTitle    = $lang === 'es' ? '.htaccess, Mod_Security y Mod_Evasive — CyberEscudo' : '.htaccess, Mod_Security & Mod_Evasive — CyberEscudo';
$contentTitle = $lang === 'es' ? '.htaccess, Mod_Security y Mod_Evasive' : '.htaccess, Mod_Security & Mod_Evasive';
$contentDate  = '2022-03-01';
$contentTags  = ['.htaccess', 'Mod_Security', 'Mod_Evasive', 'WAF', 'Apache'];

ob_start();
if ($lang === 'es'): ?>
<div class="prose">
  <p>Manual sobre el uso de <strong>.htaccess</strong> como herramienta de seguridad en Apache, la configuración del WAF <strong>Mod_Security</strong> y el módulo anti-DoS <strong>Mod_Evasive</strong>.</p>

  <h2>1. .htaccess como Herramienta de Seguridad</h2>
  <p><code>.htaccess</code> es un contenedor de directivas y reglas que, almacenado a nivel de directorio, complementa la configuración principal de Apache. Para que sea funcional, el <em>VirtualHost</em> debe tener:</p>
  <pre><code>AllowOverride All
Options FollowSymLinks</code></pre>
  <p>La mayoría de reglas de seguridad usan <code>mod_rewrite</code>, que debe estar activo en el servidor.</p>

  <h3>Bloqueo de Inyecciones SQL</h3>
  <pre><code>RewriteCond %{QUERY_STRING} (;|<|>|'|"|%0A|%0D|%22|%27|%3C|%3E|%00).*(/\*|union|select|insert|cast|set|declare|drop|update|md5|benchmark) [NC,OR]
RewriteCond %{QUERY_STRING} \.\.\/\.\. [OR]
RewriteCond %{QUERY_STRING} (localhost|loopback|127\.0\.0\.1) [NC,OR]
RewriteCond %{QUERY_STRING} \.[a-z0-9] [NC,OR]
RewriteCond %{QUERY_STRING} (<|>|'|%0A|%0D|%27|%3C|%3E|%00) [NC]
RewriteRule .* - [F]</code></pre>

  <h3>Bloqueo de Agentes Maliciosos (Bots y Scrapers)</h3>
  <pre><code>RewriteCond %{HTTP_USER_AGENT} ^$ [OR]
RewriteCond %{HTTP_USER_AGENT} ^(java|curl|wget) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} (winhttp|HTTrack|clshttp|archiver|loader|email|harvest|extract|grab|miner) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} (libwww-perl|curl|wget|python|nikto|scan) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} (<|>|'|%0A|%0D|%27|%3C|%3E|%00) [NC]
RewriteRule .* - [F]</code></pre>

  <h3>Bloqueo de RFI y Navegación de Directorios</h3>
  <pre><code>RewriteCond %{REQUEST_METHOD} GET
RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=http:// [OR]
RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=(\.\.//?)+ [OR]
RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=/([a-z0-9_.]//?)+ [NC]
RewriteRule .* - [F]</code></pre>

  <h3>Bloqueo de Acceso a Directorios Sensibles</h3>
  <pre><code>RewriteRule ^(cache|includes|logs|tmp)/ - [F]</code></pre>

  <h3>Bloquear Acceso a Archivos por Extensión</h3>
  <pre><code>RewriteCond %{REQUEST_FILENAME} -f
RewriteCond %{REQUEST_URI} \.php|\.ini|\.xml [NC]
RewriteCond %{REQUEST_URI} \/library\/ [OR]
RewriteCond %{REQUEST_URI} \/images\/ [OR]
RewriteCond %{REQUEST_URI} \/cache\/
RewriteRule ^(.*)$ index.php [R=404]</code></pre>

  <h3>Protección por Contraseña de un Directorio</h3>
  <pre><code>AuthName "Introduce tus datos:"
AuthType Basic
AuthUserFile /www/miweb/.htpasswd
AuthGroupFile /dev/null
Require valid-user</code></pre>
  <p>El archivo <code>.htpasswd</code> debe situarse <strong>fuera</strong> del directorio público y, si es posible, fuera del <code>open_basedir</code> de PHP.</p>

  <h3>Protección por IP</h3>
  <pre><code>Deny from all
Allow from 83.165.114.0
Satisfy any</code></pre>
  <p>Con <code>Satisfy any</code> basta cumplir una de las dos condiciones (IP o contraseña). Con <code>Satisfy All</code> se deben cumplir ambas.</p>

  <h3>Manejo de Errores (Ofuscación)</h3>
  <pre><code>ErrorDocument 404 /errores/errorgenerico.php
ErrorDocument 403 /errores/errorgenerico.php
ErrorDocument 500 /errores/errorgenerico.php</code></pre>
  <p>Redirigir a una página genérica evita dar información sobre la estructura del servidor.</p>

  <h3>Deshabilitar Listado de Directorios</h3>
  <pre><code>Options -Indexes</code></pre>

  <h3>Impedir Ejecución de PHP en Directorios de Imágenes</h3>
  <pre><code>&lt;Files *.php&gt;
  Deny from all
&lt;/Files&gt;

&lt;!-- También bloquear nombres del tipo xxx.php.gif --&gt;
&lt;FilesMatch "\.(php|php\.)(.+)(\w|\d)$"&gt;
  Order Allow,Deny
  Deny from all
&lt;/FilesMatch&gt;</code></pre>

  <h3>Restringir Acceso a Archivos por Extensión</h3>
  <pre><code>&lt;Files ~ "\.old$"&gt;
  Order allow,deny
  Deny from all
  Satisfy all
&lt;/Files&gt;

&lt;FilesMatch "(^|/)_"&gt;
  Order allow,deny
  Deny from all
  Satisfy all
&lt;/FilesMatch&gt;</code></pre>

  <h2>2. Mod_Security (WAF)</h2>
  <p><strong>Mod_Security</strong> actúa como <em>Web Application Firewall</em> (WAF) para Apache. Filtra y bloquea peticiones HTTP sospechosas: fuerza bruta, XSS, SQLi, etc.</p>

  <h3>Instalación y Activación</h3>
  <pre><code>apt-get install libapache2-mod-security2
sudo a2enmod security2
apachectl -M | grep security</code></pre>

  <p>El archivo de configuración está en <code>/etc/modsecurity/modsecurity.conf</code>.</p>

  <h3>Directivas para Peticiones</h3>
  <table>
    <thead>
      <tr><th>Directiva</th><th>Descripción</th></tr>
    </thead>
    <tbody>
      <tr><td><code>SecRequestBodyAccess</code></td><td>Activa el análisis del cuerpo de las peticiones (On/Off). Necesario para inspeccionar parámetros POST.</td></tr>
      <tr><td><code>SecRequestBodyInMemoryLimit</code></td><td>Bytes reservados en RAM para los cuerpos de petición. Si se supera, se vuelcan a disco.</td></tr>
      <tr><td><code>SecRequestBodyLimit</code></td><td>Tamaño máximo del cuerpo. Si se supera, devuelve HTTP 413.</td></tr>
      <tr><td><code>SecRequestBodyNoFilesLimit</code></td><td>Límite para peticiones que no sean uploads. Por defecto 1 MB.</td></tr>
      <tr><td><code>SecRequestBodyLimitAction</code></td><td>Acción al superar el límite: <code>Reject</code> (bloquea) o <code>ProcessPartial</code>.</td></tr>
    </tbody>
  </table>

  <h3>Directivas para Respuestas</h3>
  <table>
    <thead>
      <tr><th>Directiva</th><th>Descripción</th></tr>
    </thead>
    <tbody>
      <tr><td><code>SecResponseBodyAccess</code></td><td>Activa el análisis del cuerpo de las respuestas (Off por defecto).</td></tr>
      <tr><td><code>SecResponseBodyLimit</code></td><td>Límite en bytes de las respuestas. Si se supera, devuelve HTTP 500.</td></tr>
      <tr><td><code>SecResponseBodyLimitAction</code></td><td><code>Reject</code> o <code>ProcessPartial</code> al superar el límite.</td></tr>
      <tr><td><code>SecResponseMimeType</code></td><td>Tipos MIME que se almacenan en buffers para análisis.</td></tr>
    </tbody>
  </table>

  <h2>3. Mod_Evasive (Anti-DoS)</h2>
  <p><strong>Mod_Evasive</strong> mantiene una tabla dinámica con las URLs solicitadas por cada IP. Cuando una IP supera el número de peticiones por segundo configurado, la bloquea devolviendo un HTTP 403.</p>

  <h3>Instalación</h3>
  <pre><code>apt-get install libapache2-mod-evasive</code></pre>
  <p>Archivo de configuración: <code>/etc/apache2/mods-available/evasive.conf</code></p>

  <h3>Directivas</h3>
  <table>
    <thead>
      <tr><th>Directiva</th><th>Descripción</th><th>Recomendado</th></tr>
    </thead>
    <tbody>
      <tr><td><code>DOSHashTableSize</code></td><td>Tamaño de la tabla de hash. Valor alto = mejor rendimiento, más RAM. En servidores con alta carga: 2048+.</td><td>2048</td></tr>
      <tr><td><code>DOSPageCount</code></td><td>Máximo de peticiones por página concreta antes de bloquear la IP.</td><td>2</td></tr>
      <tr><td><code>DOSSiteCount</code></td><td>Máximo de peticiones a cualquier recurso del servidor.</td><td>50</td></tr>
      <tr><td><code>DOSPageInterval</code></td><td>Intervalo de tiempo (segundos) para el conteo de <code>DOSPageCount</code>.</td><td>1</td></tr>
      <tr><td><code>DOSSiteInterval</code></td><td>Intervalo de tiempo para el conteo de <code>DOSSiteCount</code>.</td><td>1</td></tr>
      <tr><td><code>DOSBlockingPeriod</code></td><td>Segundos que permanece bloqueada una IP. Cada nueva petición durante el bloqueo reinicia el contador y suma 10 segundos.</td><td>10</td></tr>
      <tr><td><code>DOSSystemCommand</code></td><td>Comando de sistema a ejecutar cuando se bloquea una IP (ej. regla de iptables).</td><td>-</td></tr>
      <tr><td><code>DOSWhitelist</code></td><td>IPs excluidas del control (ej. <code>192.168.17.*</code>).</td><td>IPs propias</td></tr>
      <tr><td><code>DOSLogDir</code></td><td>Ruta del directorio de logs del módulo.</td><td>/var/log/apache2/</td></tr>
    </tbody>
  </table>

  <p>Ejemplo de configuración completa:</p>
  <pre><code>&lt;IfModule mod_evasive20.c&gt;
    DOSHashTableSize    2048
    DOSPageCount        2
    DOSSiteCount        50
    DOSPageInterval     1
    DOSSiteInterval     1
    DOSBlockingPeriod   10
    DOSLogDir           /var/log/apache2/evasive
    DOSWhitelist        127.0.0.1
&lt;/IfModule&gt;</code></pre>
</div>

<?php else: ?>
<div class="prose">
  <p>Manual on using <strong>.htaccess</strong> as a security tool, configuring the <strong>Mod_Security</strong> WAF and the <strong>Mod_Evasive</strong> anti-DoS module.</p>

  <h2>1. .htaccess Security Rules</h2>
  <pre><code># Block SQL injections
RewriteCond %{QUERY_STRING} (union|select|insert|drop|update) [NC]
RewriteRule .* - [F]

# Block malicious bots
RewriteCond %{HTTP_USER_AGENT} (HTTrack|nikto|sqlmap|nmap) [NC]
RewriteRule .* - [F]

# Disable directory listing
Options -Indexes</code></pre>

  <h2>2. Mod_Security</h2>
  <pre><code>apt-get install libapache2-mod-security2
sudo a2enmod security2</code></pre>
  <p>Configure at <code>/etc/modsecurity/modsecurity.conf</code>. Key directives: <code>SecRequestBodyAccess</code>, <code>SecRequestBodyLimit</code>, <code>SecResponseBodyAccess</code>.</p>

  <h2>3. Mod_Evasive</h2>
  <pre><code>apt-get install libapache2-mod-evasive</code></pre>
  <pre><code>&lt;IfModule mod_evasive20.c&gt;
    DOSHashTableSize    2048
    DOSPageCount        2
    DOSSiteCount        50
    DOSPageInterval     1
    DOSSiteInterval     1
    DOSBlockingPeriod   10
&lt;/IfModule&gt;</code></pre>
</div>
<?php endif;
$contentBody = ob_get_clean();
require __DIR__ . '/../templates/content-page.php';
