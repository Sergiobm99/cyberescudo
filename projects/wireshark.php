<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Wireshark: Análisis Forense de Red — CyberEscudo' : 'Wireshark: Network Forensics Analysis — CyberEscudo';
$contentTitle = $lang==='es' ? 'Wireshark y TShark: Análisis de Tráfico' : 'Wireshark & TShark: Traffic Analysis';
$contentDate  = '2022-03-20';
$contentDiff  = 'advanced';
$contentTags  = ['Wireshark','Forense','PCAP','TShark','Sniffing', 'Blue Team'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p><strong>Wireshark</strong> es el analizador de protocolos de red (sniffer) más utilizado del mundo. Captura el tráfico de la red en tiempo real y lo desglosa capa por capa (según el modelo OSI), permitiéndote ver exactamente qué está ocurriendo a nivel microscópico. En ciberseguridad, se utiliza para análisis forense de incidentes (DFIR), análisis de malware, y detección de fugas de datos. </p>

  <h2>1. Arquitectura y Captura de Tráfico</h2>
  <p>Para capturar tráfico, tu tarjeta de red debe ponerse en <strong>Modo Promiscuo</strong> (escuchar todos los paquetes que viajan por el cable/aire, no solo los dirigidos a ti). Las capturas se guardan en archivos <code>.pcap</code> o el más moderno <code>.pcapng</code>.</p>
  <pre><code># Captura desde la terminal usando tshark (La versión CLI de Wireshark):
tshark -i eth0

# Capturar y guardar en un archivo para análisis posterior:
tshark -i eth0 -w captura_incidente.pcapng

# Leer un archivo .pcapng:
tshark -r captura_incidente.pcapng</code></pre>

  <h3>Filtros de Captura (BPF - Berkeley Packet Filter)</h3>
  <p>Se aplican <strong>ANTES</strong> de capturar. Sirven para no llenar el disco duro si estás en una red de 10Gbps. Si el paquete no coincide, se descarta y nunca se guarda.</p>
  <pre><code># Capturar solo tráfico hacia o desde el servidor web:
tshark -i eth0 -f "host 192.168.1.50"

# Capturar solo tráfico TCP (ignorando UDP y ARP):
tshark -i eth0 -f "tcp"

# No capturar mi propia conexión SSH para evitar ruido infinito:
tshark -i eth0 -f "not port 22"</code></pre>

  <h2>2. Filtros de Visualización (Display Filters)</h2>
  <p>Se aplican <strong>DESPUÉS</strong> de capturar, directamente en la barra superior de Wireshark. Son el bisturí del analista forense. No borran paquetes, solo ocultan los que no coinciden.</p>
  <pre><code># Filtros por Dirección IP:
ip.addr == 192.168.1.100       # Origen o Destino
ip.src == 10.0.0.5             # Solo si es Origen (Source)
ip.dst == 8.8.8.8              # Solo si es Destino (Destination)
ip.addr >= 192.168.1.1 and ip.addr <= 192.168.1.50 # Rangos

# Filtros por Protocolo y Puerto:
tcp.port == 443                # HTTPS
udp.dstport == 53              # Consultas DNS
http                           # Todo el tráfico HTTP (en claro)

# Filtrar por contenido del Payload (Búsqueda de cadenas de texto):
frame contains "password"
http.request.uri contains "admin"
tcp.payload contains "MZ"      # Detectar firmas de ejecutables Windows (.exe)</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 20 (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Analista Forense (DFIR)
      </h3>
      <p style="margin-bottom: 1.5rem;">Tienes un archivo PCAP de 2GB de un incidente. Sabes que la IP infectada <code>192.168.1.100</code> estableció una conexión segura y exfiltró datos al puerto <code>443</code>. Escribe el <strong>Display Filter</strong> exacto para aislar únicamente el paquete que inició esta conexión (el paquete TCP SYN).</p>
      <a href="/ctf/ctf-20.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 20
      </a>
  </div>

  <h2>3. Desencriptar Tráfico HTTPS (SSL/TLS)</h2>
  <p>Hoy en día, casi todo viaja cifrado. Wireshark solo verá "Application Data" incomprensible. Sin embargo, si tienes control sobre el cliente o el servidor, puedes desencriptarlo.</p>
  <pre><code># 1. Configurar la variable de entorno en el equipo víctima antes de que abra el navegador:
export SSLKEYLOGFILE=/tmp/sslkeylog.log

# 2. El navegador (Chrome/Firefox) guardará las claves criptográficas simétricas ahí.
# 3. En Wireshark ve a: Edit -> Preferences -> Protocols -> TLS
# 4. En el campo "(Pre)-Master-Secret log filename", carga el archivo sslkeylog.log.
# ¡Magia! El tráfico HTTPS ahora aparecerá como HTTP normal en texto claro.</code></pre>

  <h2>4. Extracción de Archivos (Malware/Documentos)</h2>
  <p>Si un usuario descargó un malware por HTTP o FTP sin cifrar, el archivo viaja fragmentado en los paquetes. Wireshark puede reconstruirlo.</p>
  <ul>
      <li>Ve a <strong>File -> Export Objects -> HTTP</strong>.</li>
      <li>Verás una lista de todas las imágenes, scripts y binarios transferidos.</li>
      <li>Selecciona el archivo sospechoso (ej. <code>update.exe</code>) y dale a "Save". Ahora puedes enviarlo a VirusTotal.</li>
  </ul>

  <h2>5. Análisis del TCP 3-Way Handshake y Anomalías</h2>
  <p>Para detectar escaneos de Nmap o ataques DoS, debes entender los "Flags" de TCP. Toda conexión normal empieza con SYN -> SYN,ACK -> ACK. </p>
  <pre><code># Detectar un escaneo sigiloso (Nmap SYN Scan):
tcp.flags.syn == 1 and tcp.flags.ack == 0

# Detectar un ataque de denegación de servicio o caída de red (Paquetes Reset):
tcp.flags.reset == 1

# Ver retransmisiones (indica congestión o pérdida de paquetes en la red):
tcp.analysis.retransmission</code></pre>

  <h2>6. TShark: Análisis Estadístico en CLI</h2>
  <p>A veces un PCAP es tan grande (varios Gigabytes) que abrirlo en la interfaz gráfica congela el ordenador. <code>tshark</code> te permite parsear datos masivos extrayendo solo lo necesario.</p>
  <pre><code># Mostrar solo IPs de origen y URLs visitadas (Ideal para sacar un listado de dominios):
tshark -r captura.pcap -Y "http.request" -T fields -e ip.src -e http.host

# Jerarquía de protocolos (¿De qué está compuesto este PCAP?):
tshark -r captura.pcap -q -z io,phs

# Top Talkers (¿Qué IP generó más tráfico?):
tshark -r captura.pcap -q -z endpoints,ip</code></pre>
</div>

<?php else: ?>
<div class="prose">
  <p><strong>Wireshark</strong> is the world's most popular network protocol analyzer. It captures network traffic in real-time and breaks it down layer by layer (OSI model). In cybersecurity, it is critical for Network Forensics (DFIR), malware analysis, and detecting data leaks. </p>

  <h2>1. Architecture & Packet Capturing</h2>
  <p>To capture traffic, your network card enters <strong>Promiscuous Mode</strong>. Captures are saved as <code>.pcap</code> or <code>.pcapng</code> files.</p>
  <pre><code># Capture from CLI using tshark:
tshark -i eth0 -w incident_capture.pcapng

# Read an existing .pcapng:
tshark -r incident_capture.pcapng</code></pre>

  <h3>Capture Filters (BPF)</h3>
  <p>Applied <strong>BEFORE</strong> capturing to drop unwanted traffic and save disk space.</p>
  <pre><code># Only capture web server traffic:
tshark -i eth0 -f "host 192.168.1.50"

# Ignore SSH to avoid infinite noise loops:
tshark -i eth0 -f "not port 22"</code></pre>

  <h2>2. Display Filters</h2>
  <p>Applied <strong>AFTER</strong> capturing. These are the analyst's scalpel. They don't delete packets, just hide the noise.</p>
  <pre><code># By IP Address:
ip.addr == 192.168.1.100       # Source or Destination
ip.src == 10.0.0.5             # Source only
ip.dst == 8.8.8.8              # Destination only

# By Protocol & Port:
tcp.port == 443                # HTTPS
udp.dstport == 53              # DNS Queries

# Payload Content (String search):
frame contains "password"
tcp.payload contains "MZ"      # Detect Windows executable signatures</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 20 (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Network Forensics Investigator
      </h3>
      <p style="margin-bottom: 1.5rem;">You have a 2GB PCAP file from an incident. You know the infected IP <code>192.168.1.100</code> established a secure connection and exfiltrated data to port <code>443</code>. Write the exact <strong>Display Filter</strong> to isolate ONLY the packet that initiated this connection (the TCP SYN packet).</p>
      <a href="/ctf/ctf-20.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 20 CHALLENGE
      </a>
  </div>

  <h2>3. Decrypting HTTPS Traffic (SSL/TLS)</h2>
  <p>To view encrypted traffic in clear text, you need the client's symmetric keys.</p>
  <pre><code># 1. Set environment variable before the victim opens the browser:
export SSLKEYLOGFILE=/tmp/sslkeylog.log

# 2. In Wireshark: Edit -> Preferences -> Protocols -> TLS
# 3. Load sslkeylog.log into "(Pre)-Master-Secret log filename".
# Encrypted traffic is now readable!</code></pre>

  <h2>4. Extracting Files (Malware/Docs)</h2>
  <p>If malware was downloaded via cleartext HTTP, Wireshark can rebuild the executable.</p>
  <ul>
      <li>Go to <strong>File -> Export Objects -> HTTP</strong>.</li>
      <li>Select the suspicious binary and click "Save".</li>
  </ul>

  <h2>5. TCP Handshake & Anomalies</h2>
  <p>Understand TCP Flags to detect DoS or port scans. </p>
  <pre><code># Detect Nmap Stealth SYN Scan:
tcp.flags.syn == 1 and tcp.flags.ack == 0

# Detect Connection Drops (RST):
tcp.flags.reset == 1</code></pre>

  <h2>6. TShark: CLI Statistical Analysis</h2>
  <p>For massive PCAPs, use <code>tshark</code> to extract specific fields without crashing your GUI.</p>
  <pre><code># Extract all Source IPs and HTTP Hosts visited:
tshark -r capture.pcap -Y "http.request" -T fields -e ip.src -e http.host

# Top Talkers (Which IP generated the most traffic?):
tshark -r capture.pcap -q -z endpoints,ip</code></pre>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';