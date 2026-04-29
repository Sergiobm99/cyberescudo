<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Nikto y Dirb: Escaneo de Vulnerabilidades Web — CyberEscudo' : 'Nikto & Dirb: Web Vulnerability Scanning — CyberEscudo';
$contentTitle = $lang==='es' ? 'Nikto y Dirb: Escaneo de Vulnerabilidades Web' : 'Nikto & Dirb: Web Vulnerability Scanning';
$contentDate  = '2022-04-18';
$contentTags  = ['Nikto','Dirb','Gobuster','Escaneo Web','Directorios'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p>Herramientas de escaneo automático de vulnerabilidades web: <strong>Nikto</strong> para detectar misconfiguraciones y CVEs, y <strong>Dirb/Gobuster</strong> para descubrir directorios y archivos ocultos.</p>

  <h2>1. Nikto — Escáner de vulnerabilidades web</h2>
  <p>Nikto analiza servidores web en busca de configuraciones inseguras, versiones antiguas, archivos peligrosos y más de 6700 vulnerabilidades conocidas.</p>
  <pre><code># Escaneo básico:
nikto -h http://192.168.1.10

# Con puerto específico:
nikto -h http://192.168.1.10 -p 8080

# HTTPS:
nikto -h https://192.168.1.10 -ssl

# Guardar resultados en HTML:
nikto -h http://192.168.1.10 -o resultado.html -Format html

# Escaneo más exhaustivo (más plugins):
nikto -h http://192.168.1.10 -Plugins @@ALL

# Con autenticación HTTP Basic:
nikto -h http://192.168.1.10 -id admin:password

# Con cookie de sesión:
nikto -h http://192.168.1.10 -c "PHPSESSID=abc123; security=low"</code></pre>

  <h3>Qué detecta Nikto</h3>
  <ul>
    <li>Versiones desactualizadas de Apache, Nginx, PHP, etc.</li>
    <li>Archivos sensibles: <code>/.git</code>, <code>/backup.zip</code>, <code>/phpinfo.php</code>, <code>/.env</code></li>
    <li>Cabeceras de seguridad ausentes (X-Frame-Options, CSP, etc.)</li>
    <li>Métodos HTTP peligrosos habilitados (PUT, DELETE, TRACE)</li>
    <li>Vulnerabilidades CVE conocidas</li>
  </ul>

  <h2>2. Dirb — Descubrimiento de directorios</h2>
  <pre><code># Escaneo básico con wordlist por defecto:
dirb http://192.168.1.10

# Wordlist específica:
dirb http://192.168.1.10 /usr/share/wordlists/dirb/big.txt

# Con extensiones de archivo:
dirb http://192.168.1.10 -X .php,.txt,.html,.bak

# Ignorar código de respuesta:
dirb http://192.168.1.10 -N 404

# Con autenticación:
dirb http://192.168.1.10 -u admin:password</code></pre>

  <h2>3. Gobuster — Más rápido que Dirb</h2>
  <pre><code># Descubrimiento de directorios:
gobuster dir -u http://192.168.1.10 \
  -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt \
  -x php,html,txt,bak

# Con código de respuesta a mostrar:
gobuster dir -u http://192.168.1.10 \
  -w /usr/share/wordlists/dirb/big.txt \
  -s 200,301,302

# Descubrimiento de subdominios (DNS):
gobuster dns -d target.com \
  -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-5000.txt</code></pre>

  <h2>4. Flujo de reconocimiento web completo</h2>
  <pre><code># Paso 1 — Nmap: detectar servicios web y versiones:
nmap -sV -p 80,443,8080,8443 192.168.1.10

# Paso 2 — Nikto: detectar vulnerabilidades conocidas:
nikto -h http://192.168.1.10

# Paso 3 — Gobuster: descubrir rutas y archivos:
gobuster dir -u http://192.168.1.10 -w big.txt -x php,txt,bak

# Paso 4 — Burp Suite: analizar manualmente los endpoints descubiertos

# Paso 5 — SQLMap / XSS manual: probar inputs encontrados</code></pre>
</div>
<?php else: ?>
<div class="prose">
  <p>Automated web vulnerability scanning with <strong>Nikto</strong> and directory/file discovery with <strong>Dirb</strong> and <strong>Gobuster</strong>.</p>

  <h2>1. Nikto — Web Vulnerability Scanner</h2>
  <p>Nikto audits web servers for insecure configurations, outdated software, dangerous files and over 6700 known vulnerabilities.</p>
  <pre><code># Basic scan:
nikto -h http://192.168.1.10

# Custom port:
nikto -h http://192.168.1.10 -p 8080

# HTTPS:
nikto -h https://192.168.1.10 -ssl

# Save results as HTML report:
nikto -h http://192.168.1.10 -o result.html -Format html

# With session cookie:
nikto -h http://192.168.1.10 -c "PHPSESSID=abc123; security=low"</code></pre>

  <h3>What Nikto Detects</h3>
  <ul>
    <li>Outdated versions of Apache, Nginx, PHP, etc.</li>
    <li>Sensitive files: <code>/.git</code>, <code>/backup.zip</code>, <code>/phpinfo.php</code>, <code>/.env</code></li>
    <li>Missing security headers (X-Frame-Options, CSP, etc.)</li>
    <li>Dangerous HTTP methods enabled (PUT, DELETE, TRACE)</li>
    <li>Known CVE vulnerabilities</li>
  </ul>

  <h2>2. Dirb — Directory Discovery</h2>
  <pre><code"># Basic scan with default wordlist:
dirb http://192.168.1.10

# Custom wordlist:
dirb http://192.168.1.10 /usr/share/wordlists/dirb/big.txt

# With file extensions:
dirb http://192.168.1.10 -X .php,.txt,.html,.bak</code></pre>

  <h2>3. Gobuster — Faster Alternative</h2>
  <pre><code"># Directory brute force:
gobuster dir -u http://192.168.1.10 \
  -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt \
  -x php,html,txt,bak

# DNS subdomain discovery:
gobuster dns -d target.com \
  -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-5000.txt</code></pre>

  <h2>4. Complete Web Recon Workflow</h2>
  <pre><code"># Step 1 — Nmap: detect web services and versions:
nmap -sV -p 80,443,8080,8443 192.168.1.10

# Step 2 — Nikto: detect known vulnerabilities:
nikto -h http://192.168.1.10

# Step 3 — Gobuster: discover paths and files:
gobuster dir -u http://192.168.1.10 -w big.txt -x php,txt,bak

# Step 4 — Burp Suite: manually analyze discovered endpoints

# Step 5 — SQLMap / manual XSS: test inputs found in step 3</code></pre>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';
