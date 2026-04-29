<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Gobuster y ffuf: Fuzzing Web y Subdominios — CyberEscudo' : 'Gobuster & ffuf: Web & Subdomain Fuzzing — CyberEscudo';
$contentTitle = $lang==='es' ? 'Gobuster y ffuf: Fuzzing Web y Subdominios' : 'Gobuster & ffuf: Web & Subdomain Fuzzing';
$contentDate  = '2024-06-10';
$contentDiff  = 'basic';
$contentTags  = ['Gobuster','ffuf','Fuzzing','Directorios','Subdominios','Reconocimiento'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p><strong>Gobuster</strong> y <strong>ffuf</strong> son dos de las herramientas de fuzzing más usadas en pentesting web. Permiten descubrir directorios ocultos, archivos, parámetros y subdominios mediante ataques de diccionario. Son rápidas, modulares y esenciales en la fase de reconocimiento.</p>

  <h2>1. Instalación</h2>
  <pre><code># Gobuster (Go):
apt install gobuster

# ffuf (Go — más flexible):
apt install ffuf
# o compilar desde código fuente:
go install github.com/ffuf/ffuf/v2@latest

# Wordlists recomendadas (incluidas en Kali/SecLists):
apt install seclists
# Ruta: /usr/share/seclists/</code></pre>

  <h2>2. Gobuster — Enumeración de directorios</h2>
  <pre><code># Escaneo básico de directorios:
gobuster dir -u http://objetivo.com -w /usr/share/seclists/Discovery/Web-Content/common.txt

# Con extensiones de archivo:
gobuster dir -u http://objetivo.com \
  -w /usr/share/seclists/Discovery/Web-Content/raft-medium-files.txt \
  -x php,html,txt,bak,zip

# Ignorar códigos de respuesta concretos:
gobuster dir -u http://objetivo.com \
  -w /usr/share/wordlists/dirb/big.txt \
  -b 404,403

# Con cabeceras HTTP personalizadas (útil con cookies de sesión):
gobuster dir -u http://objetivo.com \
  -w /usr/share/seclists/Discovery/Web-Content/directory-list-2.3-medium.txt \
  -H "Cookie: session=abc123" \
  -H "Authorization: Bearer TOKEN"

# Con proxy (Burp Suite):
gobuster dir -u http://objetivo.com \
  -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt \
  --proxy http://127.0.0.1:8080</code></pre>

  <h2>3. Gobuster — Enumeración de subdominios</h2>
  <pre><code># Bruteforce de subdominios DNS:
gobuster dns -d objetivo.com \
  -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-5000.txt

# Con resolución de IPs:
gobuster dns -d objetivo.com \
  -w /usr/share/seclists/Discovery/DNS/bitquark-subdomains-top100000.txt \
  --show-ips

# Con servidor DNS personalizado (evitar detección):
gobuster dns -d objetivo.com \
  -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-20000.txt \
  -r 8.8.8.8</code></pre>

  <h2>4. Gobuster — VHost fuzzing</h2>
  <pre><code># Descubrir Virtual Hosts en un mismo servidor:
gobuster vhost -u http://objetivo.com \
  -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-5000.txt \
  --append-domain

# Filtrar por tamaño de respuesta (excluir falsos positivos):
gobuster vhost -u http://objetivo.com \
  -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-5000.txt \
  --append-domain \
  --exclude-length 250</code></pre>

  <h2>5. ffuf — Fuzzing avanzado</h2>
  <pre><code># Fuzzing de directorios (FUZZ = marcador de posición):
ffuf -u http://objetivo.com/FUZZ \
  -w /usr/share/seclists/Discovery/Web-Content/raft-medium-directories.txt

# Con extensiones múltiples:
ffuf -u http://objetivo.com/FUZZ \
  -w /usr/share/seclists/Discovery/Web-Content/raft-medium-files.txt \
  -e .php,.html,.txt,.bak

# Fuzzing de parámetros GET:
ffuf -u "http://objetivo.com/page.php?FUZZ=test" \
  -w /usr/share/seclists/Discovery/Web-Content/burp-parameter-names.txt

# Fuzzing de valores de parámetros:
ffuf -u "http://objetivo.com/page.php?id=FUZZ" \
  -w /usr/share/seclists/Fuzzing/LFI/LFI-Jhaddix.txt

# Fuzzing POST (formularios, APIs):
ffuf -u http://objetivo.com/login \
  -w /usr/share/wordlists/rockyou.txt \
  -X POST \
  -d "username=admin&password=FUZZ" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -fc 302</code></pre>

  <h2>6. ffuf — Filtros para eliminar falsos positivos</h2>
  <pre><code># Filtrar por código HTTP:
ffuf -u http://objetivo.com/FUZZ -w wordlist.txt -fc 404,403

# Filtrar por tamaño de respuesta (bytes):
ffuf -u http://objetivo.com/FUZZ -w wordlist.txt -fs 1234

# Filtrar por número de palabras en la respuesta:
ffuf -u http://objetivo.com/FUZZ -w wordlist.txt -fw 10

# Filtrar por número de líneas:
ffuf -u http://objetivo.com/FUZZ -w wordlist.txt -fl 25

# Modo silencioso + guardar en fichero JSON:
ffuf -u http://objetivo.com/FUZZ \
  -w /usr/share/seclists/Discovery/Web-Content/raft-large-directories.txt \
  -o resultados.json -of json -s</code></pre>

  <h2>7. ffuf — Fuzzing de subdominios y VHosts</h2>
  <pre><code># Subdominios con ffuf:
ffuf -u http://FUZZ.objetivo.com \
  -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-5000.txt \
  -fs 0

# VHost fuzzing (cabecera Host):
ffuf -u http://IP_DEL_SERVIDOR \
  -H "Host: FUZZ.objetivo.com" \
  -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-5000.txt \
  -fs 4242    # excluir respuesta default por tamaño</code></pre>

  <h2>8. Wordlists recomendadas por escenario</h2>
  <table>
    <thead><tr><th>Escenario</th><th>Wordlist</th></tr></thead>
    <tbody>
      <tr><td>Directorios comunes</td><td><code>Discovery/Web-Content/common.txt</code></td></tr>
      <tr><td>Directorios completo</td><td><code>Discovery/Web-Content/directory-list-2.3-medium.txt</code></td></tr>
      <tr><td>Archivos (con ext.)</td><td><code>Discovery/Web-Content/raft-medium-files.txt</code></td></tr>
      <tr><td>Parámetros GET/POST</td><td><code>Discovery/Web-Content/burp-parameter-names.txt</code></td></tr>
      <tr><td>Subdominios (rápido)</td><td><code>Discovery/DNS/subdomains-top1million-5000.txt</code></td></tr>
      <tr><td>Subdominios (completo)</td><td><code>Discovery/DNS/bitquark-subdomains-top100000.txt</code></td></tr>
      <tr><td>LFI payloads</td><td><code>Fuzzing/LFI/LFI-Jhaddix.txt</code></td></tr>
    </tbody>
  </table>
</div>
<?php else: ?>
<div class="prose">
  <p><strong>Gobuster</strong> and <strong>ffuf</strong> are two of the most popular fuzzing tools in web penetration testing. They discover hidden directories, files, parameters, and subdomains through dictionary-based attacks. Both are fast, flexible, and essential during the reconnaissance phase.</p>

  <h2>1. Installation</h2>
  <pre><code># Gobuster (Go):
apt install gobuster

# ffuf (Go — more flexible):
apt install ffuf
# or build from source:
go install github.com/ffuf/ffuf/v2@latest

# Recommended wordlists (SecLists):
apt install seclists
# Path: /usr/share/seclists/</code></pre>

  <h2>2. Gobuster — Directory Enumeration</h2>
  <pre><code># Basic directory scan:
gobuster dir -u http://target.com -w /usr/share/seclists/Discovery/Web-Content/common.txt

# With file extensions:
gobuster dir -u http://target.com \
  -w /usr/share/seclists/Discovery/Web-Content/raft-medium-files.txt \
  -x php,html,txt,bak,zip

# Ignore specific status codes:
gobuster dir -u http://target.com \
  -w /usr/share/wordlists/dirb/big.txt \
  -b 404,403

# With custom HTTP headers (session cookies):
gobuster dir -u http://target.com \
  -w /usr/share/seclists/Discovery/Web-Content/directory-list-2.3-medium.txt \
  -H "Cookie: session=abc123" \
  -H "Authorization: Bearer TOKEN"

# Through Burp Suite proxy:
gobuster dir -u http://target.com \
  -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt \
  --proxy http://127.0.0.1:8080</code></pre>

  <h2>3. Gobuster — Subdomain Enumeration</h2>
  <pre><code># DNS subdomain brute-force:
gobuster dns -d target.com \
  -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-5000.txt

# Show resolved IPs:
gobuster dns -d target.com \
  -w /usr/share/seclists/Discovery/DNS/bitquark-subdomains-top100000.txt \
  --show-ips

# Custom DNS resolver:
gobuster dns -d target.com \
  -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-20000.txt \
  -r 8.8.8.8</code></pre>

  <h2>4. Gobuster — VHost Fuzzing</h2>
  <pre><code># Discover Virtual Hosts on the same IP:
gobuster vhost -u http://target.com \
  -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-5000.txt \
  --append-domain

# Filter false positives by response size:
gobuster vhost -u http://target.com \
  -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-5000.txt \
  --append-domain \
  --exclude-length 250</code></pre>

  <h2>5. ffuf — Advanced Fuzzing</h2>
  <pre><code># Directory fuzzing (FUZZ = placeholder):
ffuf -u http://target.com/FUZZ \
  -w /usr/share/seclists/Discovery/Web-Content/raft-medium-directories.txt

# With multiple extensions:
ffuf -u http://target.com/FUZZ \
  -w /usr/share/seclists/Discovery/Web-Content/raft-medium-files.txt \
  -e .php,.html,.txt,.bak

# GET parameter name fuzzing:
ffuf -u "http://target.com/page.php?FUZZ=test" \
  -w /usr/share/seclists/Discovery/Web-Content/burp-parameter-names.txt

# GET parameter value fuzzing (LFI):
ffuf -u "http://target.com/page.php?id=FUZZ" \
  -w /usr/share/seclists/Fuzzing/LFI/LFI-Jhaddix.txt

# POST fuzzing (login forms, APIs):
ffuf -u http://target.com/login \
  -w /usr/share/wordlists/rockyou.txt \
  -X POST \
  -d "username=admin&password=FUZZ" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -fc 302</code></pre>

  <h2>6. ffuf — Filtering False Positives</h2>
  <pre><code># Filter by HTTP status code:
ffuf -u http://target.com/FUZZ -w wordlist.txt -fc 404,403

# Filter by response size (bytes):
ffuf -u http://target.com/FUZZ -w wordlist.txt -fs 1234

# Filter by word count:
ffuf -u http://target.com/FUZZ -w wordlist.txt -fw 10

# Filter by line count:
ffuf -u http://target.com/FUZZ -w wordlist.txt -fl 25

# Silent mode + save JSON output:
ffuf -u http://target.com/FUZZ \
  -w /usr/share/seclists/Discovery/Web-Content/raft-large-directories.txt \
  -o results.json -of json -s</code></pre>

  <h2>7. ffuf — Subdomain & VHost Fuzzing</h2>
  <pre><code># Subdomain fuzzing:
ffuf -u http://FUZZ.target.com \
  -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-5000.txt \
  -fs 0

# VHost fuzzing via Host header:
ffuf -u http://SERVER_IP \
  -H "Host: FUZZ.target.com" \
  -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-5000.txt \
  -fs 4242    # filter out default response by size</code></pre>

  <h2>8. Recommended Wordlists by Scenario</h2>
  <table>
    <thead><tr><th>Scenario</th><th>Wordlist</th></tr></thead>
    <tbody>
      <tr><td>Common directories</td><td><code>Discovery/Web-Content/common.txt</code></td></tr>
      <tr><td>Full directory list</td><td><code>Discovery/Web-Content/directory-list-2.3-medium.txt</code></td></tr>
      <tr><td>Files (with extensions)</td><td><code>Discovery/Web-Content/raft-medium-files.txt</code></td></tr>
      <tr><td>GET/POST parameters</td><td><code>Discovery/Web-Content/burp-parameter-names.txt</code></td></tr>
      <tr><td>Subdomains (fast)</td><td><code>Discovery/DNS/subdomains-top1million-5000.txt</code></td></tr>
      <tr><td>Subdomains (full)</td><td><code>Discovery/DNS/bitquark-subdomains-top100000.txt</code></td></tr>
      <tr><td>LFI payloads</td><td><code>Fuzzing/LFI/LFI-Jhaddix.txt</code></td></tr>
    </tbody>
  </table>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';
