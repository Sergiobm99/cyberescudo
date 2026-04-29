<?php
/**
 * CyberEscudo — Proyecto: Hardening de Apache
 * Contenido: Práctica 5.1 — Sergio Belmonte Morales
 */
require_once __DIR__ . '/../bootstrap.php';

$pageTitle    = $lang === 'es' ? 'Hardening de Apache — CyberEscudo' : 'Apache Hardening — CyberEscudo';
$contentTitle = $lang === 'es' ? 'Hardening de Apache' : 'Apache Hardening';
$contentDate  = '2022-05-11';
$contentTags  = ['Apache', 'PHP.ini', 'Hardening', 'security.conf'];

ob_start();
if ($lang === 'es'): ?>
<div class="prose">
  <p>Guía sobre la localización y configuración de los principales parámetros para securizar Apache y PHP: <code>apache2.conf</code>, <code>security.conf</code> y <code>php.ini</code>.</p>

  <h2>Instalación</h2>
  <pre><code>apt install apache2
apt install libapache2-mod-php php-mysql</code></pre>

  <h2>1. apache2.conf — <code>/etc/apache2/apache2.conf</code></h2>
  <p>Fichero principal de Apache. Define el comportamiento general del servidor y gestiona los módulos activos.</p>

  <h3>Timeout</h3>
  <p>Tiempo (segundos) que el servidor espera al cliente. Valores altos exponen a ataques DoS.</p>
  <ul>
    <li><strong>Por defecto:</strong> 300 &nbsp;|&nbsp; <strong>Recomendado:</strong> 10–60</li>
  </ul>
  <pre><code>Timeout 60</code></pre>

  <h3>KeepAlive</h3>
  <p>Permite múltiples peticiones sobre la misma conexión TCP. Aumenta el tiempo de carga y el riesgo de DoS.</p>
  <ul>
    <li><strong>Por defecto:</strong> On &nbsp;|&nbsp; <strong>Recomendado:</strong> Off</li>
  </ul>
  <pre><code>KeepAlive Off</code></pre>

  <h3>KeepAliveRequests</h3>
  <p>Límite de peticiones por conexión persistente. 0 = ilimitado (no recomendado).</p>
  <ul>
    <li><strong>Por defecto:</strong> 100 &nbsp;|&nbsp; <strong>Recomendado:</strong> 50–75</li>
  </ul>
  <pre><code>KeepAliveRequests 75</code></pre>

  <h3>KeepAliveTimeout</h3>
  <p>Segundos que el servidor espera nuevas peticiones en una conexión persistente.</p>
  <ul>
    <li><strong>Por defecto:</strong> 5 &nbsp;|&nbsp; <strong>Recomendado:</strong> el mínimo posible</li>
  </ul>
  <pre><code>KeepAliveTimeout 3</code></pre>

  <h2>2. security.conf — <code>/etc/apache2/conf-available/security.conf</code></h2>
  <p>Contiene directivas clave de seguridad para el servidor.</p>

  <h3>ServerTokens</h3>
  <p>Controla qué información del servidor se envía en las cabeceras HTTP de respuesta.</p>
  <ul>
    <li><strong>Por defecto:</strong> OS (muestra versión + SO) &nbsp;|&nbsp; <strong>Recomendado:</strong> Prod</li>
  </ul>
  <pre><code>ServerTokens Prod</code></pre>

  <h3>ServerSignature</h3>
  <p>Añade un pie de página con info del servidor en páginas de error y listados de directorio.</p>
  <ul>
    <li><strong>Por defecto:</strong> On &nbsp;|&nbsp; <strong>Recomendado:</strong> Off</li>
  </ul>
  <pre><code>ServerSignature Off</code></pre>

  <h3>X-Content-Type-Options</h3>
  <p>Protege frente a vulnerabilidades de MIME sniffing. Impide que el navegador adivine el tipo de contenido.</p>
  <pre><code>Header set X-Content-Type-Options "nosniff"</code></pre>

  <h3>X-Frame-Options</h3>
  <p>Previene ataques de <em>clickjacking</em> controlando si la página puede cargarse en un iframe.</p>
  <pre><code>Header set X-Frame-Options "SAMEORIGIN"</code></pre>

  <h2>3. php.ini — <code>/etc/php/8.1/cli/php.ini</code></h2>
  <p>Permite configurar el comportamiento de PHP, especialmente el manejo seguro de sesiones.</p>

  <h3>Configuración recomendada de sesiones</h3>
  <pre><code>; La cookie expira al cerrar el navegador
session.cookie_lifetime  = 0

; Usa cookies para almacenar el ID de sesión
session.use_cookies      = 1

; Solo cookies, nunca ID de sesión en la URL (previene session fixation)
session.use_only_cookies = 1

; Rechaza IDs de sesión no generados por el servidor
session.use_strict_mode  = 1

; La cookie no es accesible desde JavaScript (mitiga XSS)
session.cookie_httponly  = On

; Solo envía la cookie por HTTPS
session.cookie_secure    = On

; Tiempo en segundos antes de considerar la sesión basura
session.gc_maxlifetime   = 1440

; Deshabilita la inserción del ID de sesión en etiquetas HTML
session.use_trans_sid    = 0

; Evita cachear contenido de sesiones autenticadas
session.cache_limiter    = nocache

; SHA-1 es más seguro que MD5
session.hash_function    = 1</code></pre>

  <table>
    <thead>
      <tr><th>Parámetro</th><th>Por defecto</th><th>Recomendado</th><th>Motivo</th></tr>
    </thead>
    <tbody>
      <tr><td>cookie_lifetime</td><td>0</td><td>0</td><td>Expira al cerrar navegador</td></tr>
      <tr><td>use_only_cookies</td><td>1</td><td>1</td><td>Evita session fixation por URL</td></tr>
      <tr><td>use_strict_mode</td><td>0</td><td>1</td><td>Obligatorio por seguridad</td></tr>
      <tr><td>cookie_httponly</td><td>On</td><td>On</td><td>Bloquea acceso JS a la cookie</td></tr>
      <tr><td>cookie_secure</td><td>Off</td><td>On</td><td>Solo HTTPS</td></tr>
      <tr><td>use_trans_sid</td><td>On</td><td>Off</td><td>Elimina fugas de ID por URL</td></tr>
      <tr><td>hash_function</td><td>-</td><td>1 (SHA-1)</td><td>Más seguro que MD5</td></tr>
    </tbody>
  </table>
</div>
<?php else: ?>
<div class="prose">
  <p>This guide covers the main parameters to harden Apache and PHP: <code>apache2.conf</code>, <code>security.conf</code>, and <code>php.ini</code>.</p>

  <h2>Installation</h2>
  <pre><code>apt install apache2
apt install libapache2-mod-php php-mysql</code></pre>

  <h2>1. apache2.conf</h2>
  <pre><code>Timeout 60
KeepAlive Off
KeepAliveRequests 75
KeepAliveTimeout 3</code></pre>

  <h2>2. security.conf</h2>
  <pre><code>ServerTokens Prod
ServerSignature Off
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"</code></pre>

  <h2>3. php.ini — Session Security</h2>
  <pre><code>session.cookie_lifetime  = 0
session.use_cookies      = 1
session.use_only_cookies = 1
session.use_strict_mode  = 1
session.cookie_httponly  = On
session.cookie_secure    = On
session.gc_maxlifetime   = 1440
session.use_trans_sid    = 0
session.cache_limiter    = nocache
session.hash_function    = 1</code></pre>
</div>
<?php endif;
$contentBody = ob_get_clean();
require __DIR__ . '/../templates/content-page.php';
