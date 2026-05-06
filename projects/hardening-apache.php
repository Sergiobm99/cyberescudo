<?php
/**
 * CyberEscudo — Proyecto: Hardening de Apache
 * Contenido: Práctica 5.1 — Sergio Belmonte Morales
 */
require_once __DIR__ . '/../bootstrap.php';

$pageTitle    = $lang === 'es' ? 'Hardening de Apache y PHP — CyberEscudo' : 'Apache & PHP Hardening — CyberEscudo';
$contentTitle = $lang === 'es' ? 'Hardening de Apache y PHP' : 'Apache & PHP Hardening';
$contentDate  = '2022-05-11';
$contentDiff  = 'intermediate';
$contentTags  = ['Apache', 'PHP.ini', 'Hardening', 'security.conf', 'Sysadmin', 'Blue Team'];

ob_start();
if ($lang === 'es'): ?>
<div class="prose">
  <p>La seguridad por defecto de un servidor web suele priorizar la compatibilidad sobre la protección. El <strong>Hardening</strong> (Endurecimiento) consiste en reducir la superficie de ataque deshabilitando módulos innecesarios, ocultando información del sistema y mitigando vectores web comunes. A continuación, exploraremos los tres pilares de configuración en un entorno LAMP (Linux, Apache, MySQL, PHP).</p>

  <h2>1. apache2.conf — El Núcleo de Apache (<code>/etc/apache2/apache2.conf</code>)</h2>
  <p>Define el comportamiento general del servidor, el control de conexiones y los permisos de los directorios raíz.</p>

  <h3>Control de Conexiones (Mitigación DoS)</h3>
  <ul>
    <li><strong>Timeout:</strong> Tiempo (en segundos) que el servidor espera al cliente para recibir un paquete. Valores altos (ej. 300) exponen a ataques *Slowloris*. <strong>Recomendado: 60</strong>.</li>
    <li><strong>KeepAlive:</strong> Permite enviar múltiples peticiones sobre una misma conexión TCP, mejorando el rendimiento, pero consumiendo memoria si hay muchos clientes inactivos.</li>
  </ul>
  <pre><code>Timeout 60
KeepAlive On
MaxKeepAliveRequests 100
KeepAliveTimeout 5</code></pre>

  <h3>Restricción de Directorios (Prevenir Path Traversal)</h3>
  <p>Por defecto, Apache debe denegar el acceso a la raíz del sistema de archivos (<code>/</code>) y solo permitir el acceso a <code>/var/www/html</code>.</p>
  <pre><code># Bloquear el acceso a todo el disco duro del servidor
&lt;Directory /&gt;
    Options FollowSymLinks
    AllowOverride None
    Require all denied
&lt;/Directory&gt;

# Configuración segura para la carpeta pública
&lt;Directory /var/www/html&gt;
    # El símbolo MENOS (-) desactiva el listado de archivos si no hay index.html
    Options -Indexes +FollowSymLinks
    AllowOverride None
    Require all granted
&lt;/Directory&gt;</code></pre>

  <h2>2. security.conf — Cabeceras y Firmas (<code>/etc/apache2/conf-available/security.conf</code>)</h2>
  <p>Aquí controlamos qué le cuenta Apache al mundo exterior. Ocultar información dificulta el trabajo a herramientas como Nmap o Nikto.</p>

  <h3>Ocultación de Versiones</h3>
  <pre><code># MAL: Muestra "Apache/2.4.41 (Ubuntu)"
ServerTokens OS
ServerSignature On

# BIEN: Solo muestra "Apache" y desactiva el pie de página
ServerTokens Prod
ServerSignature Off

# Evitar ataques de Cross-Site Tracing (XST)
TraceEnable Off</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 23 (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Simulador de Hardening (Sysadmin)
      </h3>
      <p style="margin-bottom: 1.5rem;">Un escáner de vulnerabilidades acaba de destrozar nuestro entorno de Staging. El reporte indica listado de directorios activo, fuga de versión del SO y ejecución de comandos PHP habilitada. Como Sysadmin, audita y parchea los archivos de configuración para asegurar el despliegue a Producción.</p>
      <a href="/ctf/ctf-23.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 23
      </a>
  </div>

  <h3>Inyección de Cabeceras de Seguridad HTTP</h3>
  <p>Se requiere activar el módulo headers (<code>a2enmod headers</code>) para proteger a los usuarios de ataques en el lado del cliente (Client-Side).</p>
  <pre><code># Prevenir Clickjacking (Cargar la web en un iframe invisible)
Header set X-Frame-Options: "SAMEORIGIN"

# Prevenir MIME Sniffing
Header set X-Content-Type-Options: "nosniff"

# HSTS: Obligar a los navegadores a usar siempre HTTPS
Header always set Strict-Transport-Security "max-age=63072000; includeSubDomains"</code></pre>

  <h2>3. php.ini — Fortificando el Intérprete (<code>/etc/php/8.1/apache2/php.ini</code>)</h2>
  <p>PHP es un lenguaje muy permisivo. Debemos bloquear funciones que permitan a un atacante ejecutar comandos del sistema (RCE) si consiguen subir una webshell.</p>

  <h3>Fugas de Información y Errores</h3>
  <pre><code># Oculta la cabecera HTTP "X-Powered-By: PHP/8.1.2"
expose_php = Off

# Los errores detallados (stack traces) JAMÁS deben verse en Producción
display_errors = Off
display_startup_errors = Off
log_errors = On
error_log = /var/log/php_errors.log</code></pre>

  <h3>Deshabilitar Funciones Peligrosas (RCE Prevention)</h3>
  <p>Si la aplicación no necesita interactuar con el sistema operativo, estas funciones deben morir.</p>
  <pre><code>disable_functions = exec, passthru, shell_exec, system, proc_open, popen, curl_exec, curl_multi_exec, parse_ini_file, show_source</code></pre>

  <h3>Seguridad de Sesiones y Cookies</h3>
  <p>Evitar el secuestro de sesiones (Session Hijacking) y la fijación de sesiones.</p>
  <pre><code>; Usar modo estricto y evitar paso de ID por URL
session.use_strict_mode = 1
session.use_only_cookies = 1

; Proteger la cookie del acceso mediante JavaScript (Mitiga XSS)
session.cookie_httponly = 1

; Enviar la cookie SOLO si la conexión es HTTPS
session.cookie_secure = 1</code></pre>

</div>

<?php else: ?>
<div class="prose">
  <p>The default security of a web server usually prioritizes compatibility over protection. <strong>Hardening</strong> involves reducing the attack surface by disabling unnecessary modules, hiding system information, and mitigating common web vectors. Below, we explore the three configuration pillars in a LAMP stack.</p>

  <h2>1. apache2.conf — Apache Core (<code>/etc/apache2/apache2.conf</code>)</h2>
  
  <h3>Connection Control (DoS Mitigation)</h3>
  <pre><code>Timeout 60
KeepAlive On
MaxKeepAliveRequests 100
KeepAliveTimeout 5</code></pre>

  <h3>Directory Restrictions (Path Traversal Prevention)</h3>
  <p>Apache should deny access to the filesystem root (<code>/</code>) by default and only allow access to <code>/var/www/html</code>.</p>
  <pre><code>&lt;Directory /&gt;
    Options FollowSymLinks
    AllowOverride None
    Require all denied
&lt;/Directory&gt;

&lt;Directory /var/www/html&gt;
    # The MINUS sign (-) disables directory listing
    Options -Indexes +FollowSymLinks
    AllowOverride None
    Require all granted
&lt;/Directory&gt;</code></pre>

  <h2>2. security.conf — Headers and Signatures</h2>
  
  <h3>Version Hiding</h3>
  <pre><code># BAD: Shows "Apache/2.4.41 (Ubuntu)"
ServerTokens OS
ServerSignature On

# GOOD: Only shows "Apache"
ServerTokens Prod
ServerSignature Off
TraceEnable Off</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 23 (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Hardening Simulator (Sysadmin)
      </h3>
      <p style="margin-bottom: 1.5rem;">A vulnerability scanner just tore apart our Staging environment. The report shows active directory listing, OS version leakage, and PHP command execution enabled. As a Sysadmin, audit and patch the config files to secure the Production deployment.</p>
      <a href="/ctf/ctf-23.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 23 CHALLENGE
      </a>
  </div>

  <h3>HTTP Security Headers</h3>
  <pre><code># Prevent Clickjacking
Header set X-Frame-Options: "SAMEORIGIN"

# Prevent MIME Sniffing
Header set X-Content-Type-Options: "nosniff"

# HSTS
Header always set Strict-Transport-Security "max-age=63072000; includeSubDomains"</code></pre>

  <h2>3. php.ini — Fortifying the Interpreter</h2>
  
  <h3>Information Leaks & Errors</h3>
  <pre><code># Hide the "X-Powered-By" header
expose_php = Off

# NEVER display errors in Production
display_errors = Off
log_errors = On</code></pre>

  <h3>Disable Dangerous Functions (RCE Prevention)</h3>
  <pre><code>disable_functions = exec, passthru, shell_exec, system, proc_open, popen, curl_exec, show_source</code></pre>

  <h3>Session & Cookie Security</h3>
  <pre><code>session.use_strict_mode = 1
session.use_only_cookies = 1
session.cookie_httponly = 1
session.cookie_secure = 1</code></pre>
</div>
<?php endif;
$contentBody = ob_get_clean();
require __DIR__ . '/../templates/content-page.php';