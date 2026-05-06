<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Nikto y Dirb: Escaneo de Vulnerabilidades Web — CyberEscudo' : 'Nikto & Dirb: Web Vulnerability Scanning — CyberEscudo';
$contentTitle = $lang==='es' ? 'Nikto y Dirb: Escaneo de Vulnerabilidades Web' : 'Nikto & Dirb: Web Vulnerability Scanning';
$contentDate  = '2022-04-18';
$contentDiff  = 'intermediate';
$contentTags  = ['Nikto','Dirb','Gobuster','Escaneo Web','DAST','Reconocimiento'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p>El escaneo automatizado es una fase crucial del reconocimiento web (DAST - <em>Dynamic Application Security Testing</em>). Herramientas como <strong>Nikto</strong> detectan fallos de configuración y CVEs conocidos, mientras que <strong>Dirb</strong> y <strong>Gobuster</strong> mapean la superficie de ataque descubriendo directorios y archivos ocultos.</p>

  <h2>1. Nikto — Escáner de vulnerabilidades Web</h2>
  <p>Nikto es un escáner de código abierto escrito en Perl que analiza servidores web en busca de más de 6700 vulnerabilidades, versiones desactualizadas de software y fallos de configuración. Aunque es "ruidoso" y fácilmente detectable por un WAF (Web Application Firewall), sigue siendo un estándar en la industria.</p>
  
  <h3>Escaneo Básico y Autenticación</h3>
  <pre><code># Escaneo básico al puerto 80:
nikto -h http://192.168.1.10

# Especificar puerto (ej. HTTPS en un puerto no estándar):
nikto -h https://192.168.1.10 -p 8443 -ssl

# Escanear a través de un proxy (Ej: Burp Suite para depuración):
nikto -h http://192.168.1.10 -useproxy http://127.0.0.1:8080

# Con autenticación HTTP Basic:
nikto -h http://192.168.1.10 -id admin:password

# Con Cookie de Sesión (para escanear zonas autenticadas):
nikto -h http://192.168.1.10 -c "PHPSESSID=abc12345; security=low"</code></pre>

  <h3>Tuning (Afinación) y Evasión en Nikto</h3>
  <p>Lanzar Nikto con todas sus pruebas puede tardar horas e inundar los logs del cliente. Puedes afinar (<code>-Tuning</code>) el escáner para que solo busque tipos específicos de vulnerabilidades:</p>
  <ul>
      <li><code>1</code> - Archivos interesantes / Registros.</li>
      <li><code>2</code> - Misconfiguraciones / Por defecto.</li>
      <li><code>3</code> - Fuga de información (Information Disclosure).</li>
      <li><code>4</code> - Ataques de inyección (XSS, SQLi).</li>
      <li><code>8</code> - Ejecución de comandos.</li>
  </ul>
  <pre><code># Buscar solo fugas de información y archivos interesantes:
nikto -h http://192.168.1.10 -Tuning 13

# Técnicas de Evasión de IDS/WAF (Parámetro -evasion):
# 1: Codificación URI aleatoria
# 2: Añadir directorios falsos /./
# 8: Invalidación de terminadores de Windows
nikto -h http://192.168.1.10 -evasion 128</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 17 (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Análisis Forense de Nikto
      </h3>
      <p style="margin-bottom: 1.5rem;">Un analista Junior ha ejecutado un escaneo completo de Nikto contra un servidor Legacy de la empresa, pero no sabe interpretar los resultados. Lee el reporte crudo del escáner e identifica las 3 vulnerabilidades críticas descubiertas.</p>
      <a href="/ctf/ctf-17.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 17
      </a>
  </div>

  <h2>2. Dirb y Dirbuster — Clásicos de Descubrimiento</h2>
  <p>Un servidor web no publica un índice de todas sus páginas. <strong>Dirb</strong> ataca a fuerza bruta las rutas utilizando diccionarios (wordlists) para descubrir paneles ocultos (ej. <code>/admin</code> o <code>/backup.zip</code>).</p>
  <pre><code># Escaneo básico con el diccionario por defecto (common.txt):
dirb http://192.168.1.10

# Especificar un diccionario más grande:
dirb http://192.168.1.10 /usr/share/wordlists/dirb/big.txt

# Buscar extensiones específicas (-X):
dirb http://192.168.1.10 -X .php,.txt,.html,.bak,.sql

# Ignorar un código de respuesta HTTP (ej. ignorar los 403 Forbidden):
dirb http://192.168.1.10 -N 403

# Añadir un User-Agent personalizado para evitar bloqueos básicos:
dirb http://192.168.1.10 -a "Mozilla/5.0 (Windows NT 10.0; Win64; x64)"</code></pre>

  <h2>3. Gobuster — Velocidad Moderna en Go</h2>
  <p><strong>Gobuster</strong> ha reemplazado a Dirb en la mayoría de los flujos de trabajo modernos. Al estar escrito en Go, maneja la concurrencia mucho mejor, lo que lo hace exponencialmente más rápido. No es recursivo por defecto, lo que te da un control exacto de dónde estás buscando.</p>
  <pre><code># Descubrimiento de directorios a máxima velocidad (50 hilos):
gobuster dir -u http://192.168.1.10 \
  -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt \
  -x php,html,txt,bak -t 50

# Ocultar los códigos de estado irrelevantes (solo mostrar 200 OK y 301 Redirect):
gobuster dir -u http://192.168.1.10 \
  -w /usr/share/wordlists/dirb/big.txt \
  -s 200,301,302 \
  -b 404,403

# Descubrimiento de Subdominios (Fuzzing de DNS):
gobuster dns -d target.com \
  -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-5000.txt

# Fuzzing de Virtual Hosts (VHosts):
gobuster vhost -u http://192.168.1.10 \
  -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-5000.txt \
  --append-domain</code></pre>

  <h2>4. El Flujo de Trabajo (Recon Workflow)</h2>
  <p>Para no dar palos de ciego, un pentester sigue un flujo de trabajo metodológico. Las herramientas se encadenan:</p>
  <ol>
      <li><strong>Nmap:</strong> Descubre qué puertos están abiertos (<code>nmap -sV -p 80,443 10.0.0.1</code>).</li>
      <li><strong>Nikto:</strong> Se lanza contra esos puertos HTTP para un chequeo rápido de vulnerabilidades "Low Hanging Fruit".</li>
      <li><strong>Gobuster:</strong> Ejecuta una fuerza bruta masiva de directorios y subdominios en segundo plano.</li>
      <li><strong>Burp Suite:</strong> El pentester toma los archivos interesantes descubiertos por Gobuster (ej. <code>/api/v1/users</code>) y los explota manualmente proxyando las peticiones.</li>
  </ol>

  <h2>5. Formatos de Salida e Integración Continua (CI/CD)</h2>
  <p>Tanto Nikto como Gobuster son muy utilizados en DevSecOps para auditar los entornos de Staging antes de pasar a Producción. Exportar los resultados a formatos legibles por máquinas (XML, JSON) es vital.</p>
  <pre><code># Nikto - Salida a XML (Para integrar con SonarQube o DefectDojo):
nikto -h http://192.168.1.10 -o reporte_nikto.xml -Format xml

# Nikto - Generar un reporte HTML bonito para clientes:
nikto -h http://192.168.1.10 -o reporte_nikto.html -Format html

# Gobuster - Guardar salida estándar:
gobuster dir -u http://192.168.1.10 -w dicc.txt -o gobuster_results.txt</code></pre>
</div>

<?php else: ?>
<div class="prose">
  <p>Automated scanning is a crucial phase of web reconnaissance (DAST - <em>Dynamic Application Security Testing</em>). Tools like <strong>Nikto</strong> detect misconfigurations and known CVEs, while <strong>Dirb</strong> and <strong>Gobuster</strong> map the attack surface by discovering hidden directories and files.</p>

  <h2>1. Nikto — Web Vulnerability Scanner</h2>
  <p>Nikto is an open-source scanner written in Perl that audits web servers for over 6700 vulnerabilities, outdated software versions, and configuration flaws. While "noisy" and easily caught by WAFs, it remains an industry standard.</p>
  
  <h3>Basic Scanning & Authentication</h3>
  <pre><code># Basic scan on port 80:
nikto -h http://192.168.1.10

# Scan through a proxy (e.g., Burp Suite for debugging):
nikto -h http://192.168.1.10 -useproxy http://127.0.0.1:8080

# With HTTP Basic Auth:
nikto -h http://192.168.1.10 -id admin:password

# With Session Cookie:
nikto -h http://192.168.1.10 -c "PHPSESSID=abc12345; security=low"</code></pre>

  <h3>Nikto Tuning & Evasion</h3>
  <p>Running all Nikto checks can take hours. You can tune (<code>-Tuning</code>) the scanner to target specific vulnerabilities:</p>
  <ul>
      <li><code>1</code> - Interesting files / Logs.</li>
      <li><code>2</code> - Misconfigurations / Default files.</li>
      <li><code>3</code> - Information Disclosure.</li>
      <li><code>4</code> - Injection attacks (XSS, SQLi).</li>
      <li><code>8</code> - Command Execution.</li>
  </ul>
  <pre><code># Search only for info disclosure and interesting files:
nikto -h http://192.168.1.10 -Tuning 13

# IDS/WAF Evasion Techniques (-evasion parameter):
nikto -h http://192.168.1.10 -evasion 128</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 17 (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Nikto Forensics Analysis
      </h3>
      <p style="margin-bottom: 1.5rem;">A Junior analyst has run a full Nikto scan against the company's Legacy server, but doesn't know how to interpret the results. Read the raw scanner report and identify the 3 critical vulnerabilities discovered.</p>
      <a href="/ctf/ctf-17.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 17 CHALLENGE
      </a>
  </div>

  <h2>2. Dirb — The Classic Discovery Tool</h2>
  <p>A web server doesn't publish an index of all its pages. <strong>Dirb</strong> brute-forces paths using wordlists to discover hidden panels.</p>
  <pre><code># Specify a larger dictionary:
dirb http://192.168.1.10 /usr/share/wordlists/dirb/big.txt

# Search for specific extensions (-X):
dirb http://192.168.1.10 -X .php,.txt,.html,.bak,.sql

# Custom User-Agent to avoid basic blocks:
dirb http://192.168.1.10 -a "Mozilla/5.0 (Windows NT 10.0)"</code></pre>

  <h2>3. Gobuster — Modern Speed in Go</h2>
  <p><strong>Gobuster</strong> has replaced Dirb in most modern workflows. Being written in Go, it handles concurrency much better, making it exponentially faster.</p>
  <pre><code># High-speed directory discovery (50 threads):
gobuster dir -u http://192.168.1.10 \
  -w /usr/share/wordlists/dirbuster/directory-list-2.3-medium.txt \
  -x php,html,txt,bak -t 50

# DNS Subdomain Fuzzing:
gobuster dns -d target.com \
  -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-5000.txt</code></pre>

  <h2>4. The Recon Workflow</h2>
  <ol>
      <li><strong>Nmap:</strong> Discover open ports.</li>
      <li><strong>Nikto:</strong> Quick check for "Low Hanging Fruit" vulnerabilities.</li>
      <li><strong>Gobuster:</strong> Massive directory and subdomain brute force in the background.</li>
      <li><strong>Burp Suite:</strong> Manually exploit the interesting files discovered by Gobuster.</li>
  </ol>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';