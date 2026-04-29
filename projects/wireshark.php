<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Wireshark: Análisis de Tráfico de Red — CyberEscudo' : 'Wireshark: Network Traffic Analysis — CyberEscudo';
$contentTitle = $lang==='es' ? 'Wireshark: Análisis de Tráfico de Red' : 'Wireshark: Network Traffic Analysis';
$contentDate  = '2022-03-20';
$contentTags  = ['Wireshark','Tráfico','Protocolos','Sniffing','PCAP'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p><strong>Wireshark</strong> es el analizador de protocolos de red más utilizado. Permite capturar y analizar tráfico en tiempo real, inspeccionar paquetes y detectar anomalías o credenciales en claro.</p>

  <h2>1. Captura de tráfico</h2>
  <pre><code># Captura desde terminal con tshark (CLI de Wireshark):
tshark -i eth0

# Capturar y guardar en archivo .pcap:
tshark -i eth0 -w captura.pcap

# Leer un archivo .pcap:
tshark -r captura.pcap

# Capturar solo N paquetes:
tshark -i eth0 -c 100</code></pre>

  <h2>2. Filtros de captura (BPF)</h2>
  <p>Se aplican antes de capturar — reducen el volumen de datos:</p>
  <pre><code># Solo tráfico HTTP:
tshark -i eth0 -f "port 80"

# Solo tráfico hacia/desde una IP:
tshark -i eth0 -f "host 192.168.1.10"

# Solo TCP:
tshark -i eth0 -f "tcp"

# Rango de puertos:
tshark -i eth0 -f "portrange 20-25"</code></pre>

  <h2>3. Filtros de visualización (Display Filters)</h2>
  <p>Se aplican en Wireshark sobre capturas ya realizadas:</p>
  <pre><code># Tráfico HTTP:
http

# Tráfico DNS:
dns

# Paquetes de una IP concreta:
ip.addr == 192.168.1.10

# Solo paquetes de origen:
ip.src == 192.168.1.10

# Puerto destino:
tcp.dstport == 443

# Protocolo FTP con credenciales:
ftp.request.command == "PASS"

# HTTP con credenciales en POST:
http.request.method == "POST"

# Buscar texto en el payload:
frame contains "password"

# Paquetes TCP SYN (inicio de conexión):
tcp.flags.syn == 1 and tcp.flags.ack == 0</code></pre>

  <h2>4. Extracción de credenciales</h2>
  <pre><code># Credenciales FTP (en claro):
ftp.request.command == "USER" or ftp.request.command == "PASS"

# Credenciales Telnet:
telnet

# Credenciales HTTP Basic Auth:
http.authorization

# Cookies de sesión HTTP:
http.cookie</code></pre>

  <h2>5. Seguir flujos de conversación</h2>
  <p>En Wireshark GUI: clic derecho en un paquete → <em>Follow → TCP Stream</em></p>
  <p>Reconstruye la conversación completa para ver el contenido de la sesión.</p>
  <pre><code># Con tshark: seguir stream TCP 0:
tshark -r captura.pcap -q -z follow,tcp,ascii,0</code></pre>

  <h2>6. Estadísticas y análisis</h2>
  <pre><code># Estadísticas de protocolos:
tshark -r captura.pcap -q -z io,phs

# Conversaciones TCP:
tshark -r captura.pcap -q -z conv,tcp

# IPs que más tráfico generan:
tshark -r captura.pcap -q -z endpoints,ip

# Exports HTTP (archivos transmitidos):
# Wireshark GUI: File → Export Objects → HTTP</code></pre>

  <h2>7. Detectar anomalías de seguridad</h2>
  <table>
    <thead><tr><th>Anomalía</th><th>Filtro Wireshark</th></tr></thead>
    <tbody>
      <tr><td>Escaneo de puertos (SYN scan)</td><td><code>tcp.flags == 0x002</code></td></tr>
      <tr><td>ARP Spoofing</td><td><code>arp.duplicate-address-detected</code></td></tr>
      <tr><td>Muchos RST (posible DoS)</td><td><code>tcp.flags.reset == 1</code></td></tr>
      <tr><td>Credenciales en claro</td><td><code>ftp or telnet or http</code></td></tr>
      <tr><td>DNS tunneling</td><td><code>dns and frame.len > 512</code></td></tr>
    </tbody>
  </table>
</div>
<?php else: ?>
<div class="prose">
  <p><strong>Wireshark</strong> is the world's most popular network protocol analyzer. It captures and analyzes traffic in real time, inspects packets, and helps detect anomalies or cleartext credentials.</p>

  <h2>1. Capturing Traffic</h2>
  <pre><code># Capture on interface eth0 with tshark (CLI):
tshark -i eth0

# Capture and save to .pcap file:
tshark -i eth0 -w capture.pcap

# Read an existing .pcap:
tshark -r capture.pcap

# Capture only N packets:
tshark -i eth0 -c 100</code></pre>

  <h2>2. Capture Filters (BPF)</h2>
  <p>Applied before capture — reduce data volume:</p>
  <pre><code># Only HTTP traffic:
tshark -i eth0 -f "port 80"

# Only traffic to/from a specific IP:
tshark -i eth0 -f "host 192.168.1.10"

# Only TCP:
tshark -i eth0 -f "tcp"</code></pre>

  <h2>3. Display Filters</h2>
  <p>Applied in Wireshark on already-captured data:</p>
  <pre><code">http                              # All HTTP traffic
dns                               # DNS queries/responses
ip.addr == 192.168.1.10           # Specific IP (src or dst)
ip.src == 192.168.1.10            # Source IP only
tcp.dstport == 443                # Destination port
ftp.request.command == "PASS"     # FTP passwords
http.request.method == "POST"     # HTTP POST requests
frame contains "password"         # String search in payload
tcp.flags.syn==1 && tcp.flags.ack==0  # TCP SYN (new connections)</code></pre>

  <h2>4. Extracting Credentials</h2>
  <pre><code"># FTP credentials (cleartext):
ftp.request.command == "USER" or ftp.request.command == "PASS"

# Telnet credentials:
telnet

# HTTP Basic Auth:
http.authorization

# Session cookies:
http.cookie</code></pre>

  <h2>5. Following TCP Streams</h2>
  <p>In Wireshark GUI: right-click a packet → <em>Follow → TCP Stream</em></p>
  <p>This reconstructs the full conversation, making it easy to read session content.</p>
  <pre><code"># With tshark — follow stream 0:
tshark -r capture.pcap -q -z follow,tcp,ascii,0</code></pre>

  <h2>6. Statistics & Analysis</h2>
  <pre><code"># Protocol hierarchy:
tshark -r capture.pcap -q -z io,phs

# TCP conversations:
tshark -r capture.pcap -q -z conv,tcp

# Top talkers (by IP):
tshark -r capture.pcap -q -z endpoints,ip</code></pre>

  <h2>7. Detecting Security Anomalies</h2>
  <table>
    <thead><tr><th>Anomaly</th><th>Wireshark Filter</th></tr></thead>
    <tbody>
      <tr><td>Port scan (SYN scan)</td><td><code>tcp.flags == 0x002</code></td></tr>
      <tr><td>ARP Spoofing</td><td><code>arp.duplicate-address-detected</code></td></tr>
      <tr><td>TCP RST flood (DoS)</td><td><code>tcp.flags.reset == 1</code></td></tr>
      <tr><td>Cleartext credentials</td><td><code>ftp or telnet or http</code></td></tr>
      <tr><td>DNS tunneling</td><td><code>dns and frame.len > 512</code></td></tr>
    </tbody>
  </table>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';
