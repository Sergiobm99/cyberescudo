<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Nmap: Reconocimiento y Escaneo de Redes — CyberEscudo' : 'Nmap: Network Reconnaissance & Scanning — CyberEscudo';
$contentTitle = $lang==='es' ? 'Nmap: Reconocimiento y Escaneo de Redes' : 'Nmap: Network Reconnaissance & Scanning';
$contentDate  = '2022-03-01';
$contentDiff  = 'advanced';
$contentTags  = ['Nmap','Reconocimiento','Pentesting','TCP/IP','NSE'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p><strong>Nmap</strong> (Network Mapper) es el estándar de la industria para el descubrimiento de redes y auditoría de seguridad. No es solo un escáner de puertos; es una herramienta compleja capaz de evadir firewalls, detectar sistemas operativos interactuando con el stack TCP/IP y ejecutar scripts de explotación.</p>

  <h2>1. Entendiendo la base: TCP 3-Way Handshake</h2>
  <p>Para entender cómo Nmap descubre puertos, primero hay que entender cómo se comunican los ordenadores mediante TCP. Una conexión normal sigue tres pasos:</p>
  <ol>
      <li><strong>SYN:</strong> El cliente pide conectarse.</li>
      <li><strong>SYN-ACK:</strong> El servidor acepta y responde.</li>
      <li><strong>ACK:</strong> El cliente confirma y se establece la conexión.</li>
  </ol>

  <h3>TCP Connect Scan (<code>-sT</code>) vs SYN Stealth Scan (<code>-sS</code>)</h3>
  <p>El escaneo <strong>Connect (-sT)</strong> completa los 3 pasos. Es ruidoso porque el servidor registra la conexión completa en sus logs de acceso. Sin embargo, el <strong>SYN Scan (-sS)</strong> (escaneo por defecto si eres <code>root</code>) envía el SYN, recibe el SYN-ACK (sabe que el puerto está abierto), pero en lugar de enviar el ACK final, envía un <strong>RST (Reset)</strong> rompiendo la conexión al instante. Como la conexión nunca se completa, muchos firewalls y servidores antiguos no la registran.</p>

  <h2>2. Descubrimiento de Hosts (Ping Sweeps) avanzados</h2>
  <p>Los firewalls modernos bloquean el típico "Ping" (ICMP Echo Request). Nmap ofrece formas alternativas de saber si un host está vivo antes de escanear sus puertos:</p>
  <pre><code># Ping básico (Desactivar escaneo de puertos):
nmap -sn 192.168.1.0/24

# Forzar descubrimiento usando TCP ACK en el puerto 80 (Saltar bloqueos ICMP):
nmap -sn -PA80 192.168.1.0/24

# Descubrimiento mediante UDP (Útil contra firewalls estrictos con TCP):
nmap -sn -PU53 192.168.1.0/24

# NO hacer Ping (Asumir que el host está vivo, clave si el firewall bloquea todo el ping):
nmap -Pn 192.168.1.10</code></pre>

  <h2>3. Evasión Avanzada de Firewalls (IDS/IPS)</h2>
  <p>Los sistemas de detección de intrusos (IDS) detectan patrones de Nmap rápidamente. Aquí tienes el arsenal para ser invisible:</p>
  <pre><code># 1. Fragmentación de paquetes: Divide los paquetes TCP en fragmentos muy pequeños (8 bytes) 
# para que el IDS no pueda reconstruir la firma del escaneo.
nmap -f 192.168.1.10

# 2. Señuelos (Decoys): Oculta tu IP entre un montón de IPs falsas. 
# El objetivo verá escaneos viniendo de Google, de su propia red y de ti.
nmap -D 8.8.8.8,10.0.0.1,RND:5,ME 192.168.1.10

# 3. Spoofing de MAC: Cambia tu dirección MAC para evadir controles de Capa 2 (NACs).
nmap --spoof-mac 00:11:22:33:44:55 192.168.1.10
# (O usar --spoof-mac Cisco, --spoof-mac Apple)

# 4. Modificar el puerto de origen: Muchos firewalls confían ciegamente en tráfico 
# que viene del puerto 53 (DNS) o 80 (HTTP).
nmap --source-port 53 192.168.1.10</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 10 (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Análisis Forense de Escaneo
      </h3>
      <p style="margin-bottom: 1.5rem;">Escanear es solo el 10% del trabajo; el 90% restante es saber interpretar los datos. He capturado el resultado de un escaneo Nmap muy ruidoso contra un servidor corporativo. Analiza el log y encuentra las tres brechas críticas de seguridad para conseguir la bandera.</p>
      <a href="/ctf/ctf-10.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 10
      </a>
  </div>

  <h2>4. Optimización de Rendimiento y Tiempos</h2>
  <p>Escanear 65.535 puertos a través de internet puede tardar horas. Dominar los <em>Timings</em> es vital:</p>
  <pre><code># Plantillas de tiempo (T0 a T5):
nmap -T4 192.168.1.10  # Rápido, recomendado para redes locales fiables.
nmap -T2 192.168.1.10  # Lento (Polite), usa menos ancho de banda, evade IDS.

# Control granular profundo:
# Forzar a Nmap a enviar al menos 1000 paquetes por segundo:
nmap --min-rate 1000 192.168.1.10

# Limitar los reintentos si se pierden paquetes (acelera el escaneo):
nmap --max-retries 1 192.168.1.10</code></pre>

  <h2>5. Nmap Scripting Engine (NSE)</h2>
  <p>El verdadero poder de Nmap radica en sus más de 600 scripts en lenguaje Lua. Se dividen en categorías como <code>vuln</code>, <code>exploit</code>, <code>brute</code>, <code>safe</code>.</p>
  <pre><code># Ejecutar los scripts seguros por defecto (Equivalente a -sC)
nmap --script default 192.168.1.10

# Evaluar vulnerabilidades conocidas en un servicio específico:
nmap --script vuln -p 443 192.168.1.10

# Atacar por fuerza bruta un FTP o SSH:
nmap --script ftp-brute --script-args userdb=users.txt,passdb=pass.txt -p 21 192.168.1.10

# Fuzzing de directorios web incrustado en Nmap:
nmap --script http-enum -p 80 192.168.1.10</code></pre>

  <h2>6. Formatos de Exportación y Grepeo</h2>
  <p>Nunca hagas un escaneo sin guardarlo. Nmap provee tres formatos principales (<code>-oN</code> Normal, <code>-oX</code> XML, <code>-oG</code> Grepable). Para guardar los tres a la vez, usa <code>-oA</code>.</p>
  <pre><code># Guardar todos los formatos con el nombre "escaneo_inicial"
nmap -sS -p- -sV -oA escaneo_inicial 192.168.1.10

# Analizar un archivo Grepable rápido desde la terminal (extraer IPs con puerto 80 abierto):
cat escaneo_inicial.gnmap | grep "80/open" | awk '{print $2}'</code></pre>

</div>

<?php else: ?>
<div class="prose">
  <p><strong>Nmap</strong> (Network Mapper) is the industry standard for network discovery and security auditing. It's not just a port scanner; it's a complex tool capable of evading firewalls, interacting directly with the TCP/IP stack for OS fingerprinting, and executing exploit scripts.</p>

  <h2>1. The Foundation: TCP 3-Way Handshake</h2>
  <p>To understand Nmap, you must understand TCP. A normal connection follows three steps: <strong>SYN</strong> (Request), <strong>SYN-ACK</strong> (Acknowledge), and <strong>ACK</strong> (Confirm).</p>

  <h3>TCP Connect Scan (<code>-sT</code>) vs SYN Stealth Scan (<code>-sS</code>)</h3>
  <p>The <strong>Connect (-sT)</strong> scan completes all 3 steps, logging your IP on the target server. The <strong>SYN Scan (-sS)</strong> (default for <code>root</code>) sends the SYN, receives the SYN-ACK, but immediately sends an <strong>RST (Reset)</strong> instead of an ACK. The connection is aborted before it's established, effectively bypassing older firewalls and application logs.</p>

  <h2>2. Advanced Host Discovery</h2>
  <p>Modern firewalls block standard ICMP Pings. Nmap offers alternative ways to check if a host is alive:</p>
  <pre><code># Basic Ping sweep (Disable port scan):
nmap -sn 192.168.1.0/24

# TCP ACK ping on port 80 (Bypass ICMP blocks):
nmap -sn -PA80 192.168.1.0/24

# Assume host is up (Crucial if firewall blocks all ping sweeps):
nmap -Pn 192.168.1.10</code></pre>

  <h2>3. Advanced Firewall & IDS Evasion</h2>
  <p>Intrusion Detection Systems (IDS) catch Nmap patterns easily. Here is your stealth arsenal:</p>
  <pre><code># 1. Packet Fragmentation (Splits TCP headers into 8-byte chunks):
nmap -f 192.168.1.10

# 2. Decoys (Hide your IP among fake IPs like Google and random ones):
nmap -D 8.8.8.8,10.0.0.1,RND:5,ME 192.168.1.10

# 3. MAC Spoofing (Bypass Layer 2 NACs):
nmap --spoof-mac Apple 192.168.1.10

# 4. Source Port Manipulation (Firewalls often trust DNS/HTTP ports blindly):
nmap --source-port 53 192.168.1.10</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 10 (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Scan Forensics Analysis
      </h3>
      <p style="margin-bottom: 1.5rem;">Scanning is only 10% of the job; interpreting the data is the other 90%. I've captured the output of a noisy Nmap scan against a corporate server. Analyze the log and find the three critical security breaches to get the flag.</p>
      <a href="/ctf/ctf-10.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 10 CHALLENGE
      </a>
  </div>

  <h2>4. Performance & Timing Tuning</h2>
  <pre><code># Timing templates (T0 to T5):
nmap -T4 192.168.1.10  # Fast, recommended for reliable LANs.

# Deep granular control:
# Force Nmap to send at least 1000 packets per second:
nmap --min-rate 1000 192.168.1.10

# Limit retries for dropped packets to speed up scans:
nmap --max-retries 1 192.168.1.10</code></pre>

  <h2>5. Nmap Scripting Engine (NSE)</h2>
  <p>Nmap includes over 600 Lua scripts divided into categories like <code>vuln</code>, <code>exploit</code>, <code>brute</code>.</p>
  <pre><code># Check known vulnerabilities on specific services:
nmap --script vuln -p 443 192.168.1.10

# Web directory fuzzing built into Nmap:
nmap --script http-enum -p 80 192.168.1.10</code></pre>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';