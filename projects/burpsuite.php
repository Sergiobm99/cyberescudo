<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Burp Suite: Interceptación y Testing Web — CyberEscudo' : 'Burp Suite: Web Interception & Testing — CyberEscudo';
$contentTitle = $lang==='es' ? 'Burp Suite: Interceptación y Testing Web' : 'Burp Suite: Web Interception & Testing';
$contentDate  = '2022-04-05';
$contentDiff  = 'advanced';
$contentTags  = ['BurpSuite','Web','Proxy','Interceptación','OWASP', 'DAST'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p><strong>Burp Suite</strong> (desarrollado por PortSwigger) es la suite de testing de seguridad web más utilizada del mundo. Actúa como un proxy HTTP/HTTPS (Man-in-the-Middle) entre tu navegador y el servidor, permitiendo interceptar, pausar, modificar y analizar absolutamente todas las peticiones y respuestas antes de que llegen a su destino.</p>

  <h2>1. Configuración Inicial y Proxy</h2>
  <p>Para que Burp intercepte el tráfico, necesitas configurar tu navegador para que envíe todo a través del puerto de escucha local de Burp (por defecto, el <code>8080</code>).</p>
  <pre><code># 1. Listener por defecto en Burp:
127.0.0.1:8080

# 2. Configurar el certificado CA (Imprescindible para interceptar HTTPS):
# Con el proxy activo en tu navegador, visita:
http://burpsuite
# Descarga el "CA Certificate", impórtalo en tu navegador (ej. Firefox)
# y marca "Confiar en esta CA para identificar sitios web".</code></pre>

  <h3>Opciones Avanzadas del Proxy: Match and Replace</h3>
  <p>En <code>Proxy -> Options</code> puedes configurar reglas automáticas de "Match and Replace". Por ejemplo, puedes decirle a Burp que busque automáticamente cualquier cabecera <code>User-Agent</code> y la cambie por una de un móvil (iOS/Android), o que cambie automáticamente un campo oculto <code>isAdmin=false</code> a <code>isAdmin=true</code> en todas las peticiones al vuelo.</p>

  <h2>2. Target Scope y Site Map</h2>
  <p>Al navegar a través del proxy, Burp construye pasivamente un mapa del sitio (<strong>Site Map</strong>) en la pestaña <em>Target</em>. Es crucial configurar el <strong>Scope</strong> (Alcance).</p>
  <ul>
      <li>Añade la URL objetivo al <em>Scope</em>.</li>
      <li>Activa el botón "Show only in-scope items" en el historial. Esto evitará que tu Burp se llene de peticiones basura de analíticas de Google, rastreadores de Mozilla o telemetría.</li>
  </ul>

  <h2>3. Repeater — El Laboratorio Manual</h2>
  <p>El <strong>Repeater</strong> es donde los pentesters pasan el 80% de su tiempo. Permite tomar una petición interceptada, enviarla a una pestaña aislada (<code>Ctrl + R</code>) y modificarla infinitas veces para ver cómo responde el servidor.</p>
  <pre><code># Técnicas típicas en Repeater:
# - Probar inyecciones SQL (añadiendo ' o " en los parámetros).
# - Cambiar métodos HTTP (Convertir un GET en POST o PUT) para ver si hay un WAF bypass.
# - Probar IDORs (Insecure Direct Object Reference) cambiando /api/user/5 por /api/user/6.
# - Testear vulnerabilidades de CORS manipulando la cabecera Origin.</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 18 (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Simulador Burp Suite Mastery
      </h3>
      <p style="margin-bottom: 1.5rem;">Un pentester Senior te ha dejado unas notas a medias sobre una auditoría que estaba realizando con Burp Suite. Te toca a ti configurar los módulos correctos de la herramienta para terminar de reventar el servidor.</p>
      <a href="/ctf/ctf-18.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 18
      </a>
  </div>

  <h2>4. Intruder — Automatización Ofensiva</h2>
  <p>El <strong>Intruder</strong> automatiza el envío de peticiones parametrizadas. Funciona marcando posiciones en la petición con el símbolo <code>§</code> y pasándole listas de payloads (diccionarios).</p>
  
  <h3>Tipos de Ataque en el Intruder:</h3>
  <table style="width:100%; border-collapse: collapse; margin-bottom: 1.5rem;">
      <tr style="border-bottom: 1px solid var(--cyan);"><th>Tipo</th><th>Comportamiento</th><th>Ejemplo de Uso</th></tr>
      <tr><td><strong>Sniper</strong></td><td>Un solo payload iterando sobre las posiciones una a una.</td><td>Fuzzing de un directorio o parámetro concreto.</td></tr>
      <tr><td><strong>Battering ram</strong></td><td>Mismo payload inyectado en TODAS las posiciones a la vez.</td><td>Probar si usuario y contraseña son idénticos (admin:admin).</td></tr>
      <tr><td><strong>Pitchfork</strong></td><td>Múltiples listas, itera de forma sincronizada (L1[1] con L2[1]).</td><td>Tienes una lista de usuarios conocidos y su contraseña filtrada correspondiente.</td></tr>
      <tr><td><strong>Cluster bomb</strong></td><td>Múltiples listas, prueba TODAS las combinaciones posibles.</td><td>Fuerza bruta clásica de login ciego (Wordlist Users x Wordlist Passwords).</td></tr>
  </table>

  <h3>Grep-Match y Grep-Extract</h3>
  <p>No basta con atacar, hay que analizar la respuesta. En las opciones del Intruder, <strong>Grep-Match</strong> busca una cadena específica en cada respuesta (ej: "Welcome back") e ilumina la fila si fue un éxito. <strong>Grep-Extract</strong> permite robar tokens CSRF de la respuesta y reutilizarlos automáticamente en la siguiente petición.</p>

  <h2>5. Sequencer — Análisis de Entropía</h2>
  <p>El <strong>Sequencer</strong> es un módulo matemático que analiza la "aleatoriedad" (entropía) de los tokens de sesión (ej: <code>PHPSESSID</code> o tokens CSRF). Captura 10,000 tokens y te dice si son predecibles. Si lo son, podrías adivinar el token del administrador y secuestrar su sesión.</p>

  <h2>6. Decoder y Comparer</h2>
  <p><strong>Decoder:</strong> Una navaja suiza para transformar datos. Puedes hacer cadenas de decodificación complejas: Tomar un token, decodificar de URL, luego de Base64, y luego descomprimir GZIP en un solo clic.</p>
  <p><strong>Comparer:</strong> Un <em>diff</em> visual. Se usa para analizar vulnerabilidades Blind (Ciegas). Le pasas una respuesta con <code>id=1</code> y otra con <code>id=1' AND 1=1</code> para ver si un solo byte de la respuesta cambió.</p>

  <h2>7. BApp Store (Extensiones)</h2>
  <p>La pestaña Extensions permite instalar plugins escritos en Java, Python o Ruby que dotan a Burp de "superpoderes". Los imprescindibles son:</p>
  <ul>
      <li><strong>Autorize:</strong> La herramienta definitiva para encontrar vulnerabilidades de escalada de privilegios (IDOR y BOLA). Navegas como Admin, y Autorize repite todo automáticamente como un usuario de bajos privilegios para ver si el servidor lo permite.</li>
      <li><strong>Turbo Intruder:</strong> Escrito en Python, permite lanzar ataques HTTP hiper-rápidos, ignorando validaciones, capaz de encontrar Race Conditions (condiciones de carrera).</li>
      <li><strong>Param Miner:</strong> Descubre parámetros ocultos no documentados en la API que podrían ser vulnerables.</li>
  </ul>
</div>

<?php else: ?>
<div class="prose">
  <p><strong>Burp Suite</strong> (developed by PortSwigger) is the world's leading toolkit for web security testing. It acts as an HTTP/HTTPS proxy (Man-in-the-Middle) between your browser and the server, allowing you to intercept, pause, modify, and analyze absolutely all requests and responses before they reach their destination.</p>

  <h2>1. Initial Setup and Proxy</h2>
  <pre><code># 1. Default listener in Burp:
127.0.0.1:8080

# 2. Configure the CA certificate (Essential for intercepting HTTPS):
# With the proxy active in your browser, visit:
http://burpsuite
# Download the "CA Certificate", import it into your browser 
# and check "Trust this CA to identify websites".</code></pre>

  <h3>Advanced Proxy Options: Match and Replace</h3>
  <p>In <code>Proxy -> Options</code> you can set automatic "Match and Replace" rules. For example, automatically change any <code>User-Agent</code> header to a mobile one, or automatically change a hidden field <code>isAdmin=false</code> to <code>isAdmin=true</code> in all requests on the fly.</p>

  <h2>2. Target Scope and Site Map</h2>
  <p>As you browse through the proxy, Burp passively builds a <strong>Site Map</strong> in the <em>Target</em> tab. It is crucial to configure the <strong>Scope</strong>.</p>
  <ul>
      <li>Add the target URL to the <em>Scope</em>.</li>
      <li>Toggle "Show only in-scope items" in the HTTP history to filter out noise from third-party trackers.</li>
  </ul>

  <h2>3. Repeater — The Manual Laboratory</h2>
  <p>The <strong>Repeater</strong> is where pentesters spend 80% of their time. It allows you to take an intercepted request, send it to an isolated tab (<code>Ctrl + R</code>), and modify it endlessly to see how the server responds.</p>
  <pre><code># Typical techniques in Repeater:
# - Test SQL injections (adding ' or " to parameters).
# - Change HTTP methods (Convert GET to POST/PUT) to test WAF bypasses.
# - Test IDORs (Insecure Direct Object Reference) by changing IDs.
# - Test CORS vulnerabilities by manipulating the Origin header.</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 18 (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Burp Suite Mastery Simulator
      </h3>
      <p style="margin-bottom: 1.5rem;">A Senior pentester left you some half-finished notes regarding a web audit using Burp Suite. It's up to you to configure the correct modules of the tool to fully compromise the server.</p>
      <a href="/ctf/ctf-18.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 18 CHALLENGE
      </a>
  </div>

  <h2>4. Intruder — Offensive Automation</h2>
  <p>The <strong>Intruder</strong> automates customized attacks. It works by marking positions in the request with the <code>§</code> symbol and feeding it payload lists (wordlists).</p>
  
  <h3>Intruder Attack Types:</h3>
  <ul>
      <li><strong>Sniper:</strong> One payload iterating over positions one by one.</li>
      <li><strong>Battering ram:</strong> Same payload injected into ALL positions at once.</li>
      <li><strong>Pitchfork:</strong> Multiple lists, iterating synchronously (L1[1] with L2[1]).</li>
      <li><strong>Cluster bomb:</strong> Multiple lists, testing ALL possible combinations (classic login brute force).</li>
  </ul>

  <h2>5. Sequencer — Entropy Analysis</h2>
  <p>The <strong>Sequencer</strong> is a mathematical module that analyzes the "randomness" (entropy) of session tokens (e.g., <code>PHPSESSID</code> or CSRF tokens). It captures thousands of tokens and determines if they are predictable.</p>

  <h2>6. Decoder and Comparer</h2>
  <p><strong>Decoder:</strong> A Swiss army knife for transforming data (URL encode, Base64, Hex, Gzip).</p>
  <p><strong>Comparer:</strong> A visual <em>diff</em> tool. Used to analyze Blind vulnerabilities to see if a single byte in the response changed.</p>

  <h2>7. BApp Store (Extensions)</h2>
  <p>The Extensions tab allows installing plugins that give Burp "superpowers". The essentials are:</p>
  <ul>
      <li><strong>Autorize:</strong> The ultimate tool for finding Broken Access Control (IDOR/BOLA).</li>
      <li><strong>Turbo Intruder:</strong> Extremely fast HTTP attacks, capable of finding Race Conditions.</li>
      <li><strong>Param Miner:</strong> Discovers hidden, undocumented API parameters.</li>
  </ul>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';