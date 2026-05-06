<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Monitorización con Snort IDS — CyberEscudo' : 'Network Monitoring with Snort IDS — CyberEscudo';
$contentTitle = $lang==='es' ? 'Monitorización de Red (Snort IDS)' : 'Network Monitoring (Snort IDS)';
$contentDate  = '2024-12-01';
$contentDiff  = 'intermediate';
$contentTags  = ['Snort','IDS','IPS','Blue Team','Traffic Analysis','PCAP'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p>Un <strong>Sistema de Detección de Intrusos (IDS)</strong> es como una cámara de seguridad para tu red. Escucha silenciosamente todo el tráfico que pasa por el cable y lo compara en tiempo real contra una base de datos de firmas maliciosas. <strong>Snort</strong>, creado originalmente por Martin Roesch y ahora mantenido por Cisco, es el motor IDS/IPS más desplegado a nivel mundial.</p>

  <h2>1. Modos de Operación de Snort</h2>
  <p>Snort es muy versátil y puede funcionar en tres modos distintos:</p>
  <ul>
      <li><strong>Modo Sniffer:</strong> Simplemente lee los paquetes de la red y los muestra en la consola de forma continua (similar a tcpdump).</li>
      <li><strong>Modo Packet Logger:</strong> Registra los paquetes en el disco (archivos .pcap) para un análisis forense posterior (DFIR).</li>
      <li><strong>Modo NIDS (Network Intrusion Detection System):</strong> El modo principal. Analiza el tráfico de red en busca de firmas o comportamientos anómalos, generando alertas según un archivo de reglas (<em>snort.conf</em>).</li>
  </ul>

  <h2>2. Configuración Básica (snort.conf)</h2>
  <p>El corazón de Snort es su archivo de configuración. Aquí definimos cuáles son las IPs que consideramos "nuestras" y cuáles son "externas".</p>
  <pre><code># /etc/snort/snort.conf

# 1. Definir la red local (HOME_NET)
# Es vital configurarlo bien para evitar falsos positivos
ipvar HOME_NET 192.168.1.0/24

# 2. Definir la red externa (EXTERNAL_NET)
# Normalmente es cualquier cosa que no sea HOME_NET
ipvar EXTERNAL_NET !$HOME_NET

# 3. Definir rutas de reglas
var RULE_PATH /etc/snort/rules

# 4. Incluir archivos de reglas (firmas)
include $RULE_PATH/local.rules
include $RULE_PATH/sql-injection.rules</code></pre>

  <h2>3. Anatomía de una Regla de Snort</h2>
  <p>Escribir reglas es la habilidad técnica más valiosa de un analista de red. Una regla de Snort se divide en dos partes principales: la <strong>Cabecera (Header)</strong> y las <strong>Opciones (Options)</strong>.</p>
  
  <h3>Cabecera de la Regla</h3>
  <p>Define <em>quién</em>, <em>dónde</em> y <em>qué</em> protocolo está involucrado.</p>
  <pre><code>[Acción] [Protocolo] [IP Origen] [Puerto Origen] -> [IP Destino] [Puerto Destino]</code></pre>
  <ul>
      <li><strong>Acción:</strong> <code>alert</code> (generar alerta), <code>log</code> (solo registrar), <code>drop</code> (bloquear, si está en modo IPS).</li>
      <li><strong>Protocolo:</strong> <code>tcp</code>, <code>udp</code>, <code>icmp</code> o <code>ip</code>.</li>
      <li><strong>Dirección (->):</strong> Indica el flujo del tráfico. <code>-></code> es unidireccional, <code>&lt;&gt;</code> es bidireccional.</li>
  </ul>

  <h3>Opciones de la Regla</h3>
  <p>Define <em>qué estamos buscando</em> dentro del paquete y <em>qué mensaje</em> mostrar.</p>
  <ul>
      <li><strong>msg:</strong> El mensaje que aparecerá en el log de alertas.</li>
      <li><strong>content:</strong> El patrón de texto o bytes hexadecimales a buscar dentro del payload.</li>
      <li><strong>nocase:</strong> Hace que la búsqueda del 'content' ignore mayúsculas/minúsculas.</li>
      <li><strong>sid:</strong> Snort ID. Un identificador único (las reglas custom deben usar un SID > 1000000).</li>
      <li><strong>rev:</strong> Número de revisión de la regla.</li>
  </ul>

  <!-- ─── SECCIÓN DEL RETO CTF 14 (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Motor de Reglas IDS
      </h3>
      <p style="margin-bottom: 1.5rem;">Inteligencia nos alerta de un ataque inminente por fuerza bruta hacia el servidor FTP interno utilizando el usuario "root". Escribe la regla de Snort exacta para interceptar y alertar sobre este patrón malicioso en la red.</p>
      <a href="/ctf/ctf-14.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 14
      </a>
  </div>

  <h2>4. Ejemplos Prácticos de Reglas</h2>
  <p>Veamos cómo detectar diferentes vectores de ataque analizando el tráfico crudo:</p>

  <h3>Detectar Inyección SQL Clásica (Web)</h3>
  <p>Los atacantes a menudo inyectan la cadena <code>' OR 1=1 --</code>. Como la web viaja por HTTP (TCP puerto 80/443), podemos atraparlo:</p>
  <pre><code>alert tcp $EXTERNAL_NET any -> $HOME_NET 80 (msg:"SQL Injection Attempt Detected"; content:"' OR 1=1"; nocase; sid:1000002; rev:1;)</code></pre>

  <h3>Detectar Escaneo Nmap XMAS</h3>
  <p>Un escaneo XMAS enciende las flags FIN, PSH y URG del protocolo TCP. Es un comportamiento totalmente anómalo en una red sana.</p>
  <pre><code>alert tcp $EXTERNAL_NET any -> $HOME_NET any (msg:"Nmap XMAS Tree Scan Detected"; flags:F,P,U; sid:1000003; rev:1;)</code></pre>

  <h3>Detectar Ejecución de Comandos (Shell Shock / RCE)</h3>
  <p>Buscamos comandos nativos de Linux viajando dentro de peticiones HTTP, como <code>/etc/passwd</code> o ejecuciones de bash.</p>
  <pre><code>alert tcp $EXTERNAL_NET any -> $HOME_NET 80 (msg:"RCE Attempt - /etc/passwd Access"; content:"/etc/passwd"; http_uri; sid:1000004; rev:1;)</code></pre>

  <h3>Uso de Bytes Hexadecimales</h3>
  <p>A veces los atacantes intentan ofuscar los datos o esconder malware. Podemos buscar bytes hexadecimales crudos rodeándolos con pipes (<code>|</code>).</p>
  <pre><code># Buscar una firma hexadecimal específica de un troyano en cualquier puerto:
alert tcp $EXTERNAL_NET any -> $HOME_NET any (msg:"Malware XYZ C2 Beacon"; content:"|00 00 00 01 4A 5B|"; sid:1000005;)</code></pre>

  <h2>5. Ejecución y Monitorización (CLI)</h2>
  <p>Una vez escrita la regla en <code>/etc/snort/rules/local.rules</code>, debemos iniciar Snort apuntando a la interfaz de red correcta (por ejemplo, <code>eth0</code>).</p>
  <pre><code># Probar si la configuración y las reglas tienen errores (Modo Test):
snort -T -c /etc/snort/snort.conf -i eth0

# Iniciar Snort imprimiendo alertas directamente en la consola (útil para debuggear):
snort -A console -q -c /etc/snort/snort.conf -i eth0

# Iniciar Snort en modo demonio (segundo plano) para producción:
snort -D -c /etc/snort/snort.conf -i eth0

# Leer los logs generados:
cat /var/log/snort/alert</code></pre>

  <h2>6. Evolución: Suricata y Zeek</h2>
  <p>Aunque Snort es el padre de los IDS, las infraestructuras modernas de 10Gbps+ suelen utilizar motores paralelos más nuevos. <strong>Suricata</strong> es un IDS/IPS multihilo que es 100% compatible con las reglas de Snort (puedes copiar y pegar tu <code>local.rules</code> y funcionará). Por otro lado, <strong>Zeek (antes Bro)</strong> no usa firmas, sino que se centra en el análisis de comportamiento a nivel de aplicación (creando logs hiperdetallados de DNS, HTTP, SSL).</p>

</div>

<?php else: ?>
<div class="prose">
  <p>An <strong>Intrusion Detection System (IDS)</strong> is like a security camera for your network. It silently listens to all traffic passing through the wire and compares it in real-time against a database of malicious signatures. <strong>Snort</strong> is the world's most widely deployed IDS/IPS engine.</p>

  <h2>1. Snort Modes of Operation</h2>
  <ul>
      <li><strong>Sniffer Mode:</strong> Reads network packets and displays them continuously on the console.</li>
      <li><strong>Packet Logger Mode:</strong> Logs packets to disk (.pcap files) for later forensic analysis (DFIR).</li>
      <li><strong>NIDS Mode:</strong> Analyzes network traffic for anomalous behaviors, generating alerts based on a rules file.</li>
  </ul>

  <h2>2. Basic Configuration (snort.conf)</h2>
  <pre><code># 1. Define local network (HOME_NET)
ipvar HOME_NET 192.168.1.0/24

# 2. Define external network (EXTERNAL_NET)
ipvar EXTERNAL_NET !$HOME_NET

# 3. Include rule files
include $RULE_PATH/local.rules</code></pre>

  <h2>3. Anatomy of a Snort Rule</h2>
  <p>A Snort rule is divided into two main parts: the <strong>Header</strong> and the <strong>Options</strong>.</p>
  
  <h3>Rule Header</h3>
  <pre><code>[Action] [Protocol] [Src IP] [Src Port] -> [Dst IP] [Dst Port]</code></pre>
  <ul>
      <li><strong>Action:</strong> <code>alert</code>, <code>log</code>, <code>drop</code>.</li>
      <li><strong>Protocol:</strong> <code>tcp</code>, <code>udp</code>, <code>icmp</code>, <code>ip</code>.</li>
      <li><strong>Direction:</strong> <code>-></code> (unidirectional), <code>&lt;&gt;</code> (bidirectional).</li>
  </ul>

  <h3>Rule Options</h3>
  <ul>
      <li><strong>msg:</strong> The message that will appear in the alert log.</li>
      <li><strong>content:</strong> The text pattern or hexadecimal bytes to search for.</li>
      <li><strong>sid:</strong> Snort ID (custom rules must use a SID > 1000000).</li>
  </ul>

  <!-- ─── SECCIÓN DEL RETO CTF 14 (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> IDS Rule Engine
      </h3>
      <p style="margin-bottom: 1.5rem;">Intelligence warns us of an imminent brute force attack towards the internal FTP server using the "root" user. Write the exact Snort rule to intercept and alert on this malicious pattern in the network.</p>
      <a href="/ctf/ctf-14.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 14 CHALLENGE
      </a>
  </div>

  <h2>4. Practical Rule Examples</h2>

  <h3>Detect Classic SQL Injection (Web)</h3>
  <pre><code>alert tcp $EXTERNAL_NET any -> $HOME_NET 80 (msg:"SQL Injection Attempt"; content:"' OR 1=1"; nocase; sid:1000002; rev:1;)</code></pre>

  <h3>Detect Nmap XMAS Scan</h3>
  <pre><code>alert tcp $EXTERNAL_NET any -> $HOME_NET any (msg:"Nmap XMAS Scan"; flags:F,P,U; sid:1000003; rev:1;)</code></pre>

  <h3>Using Hexadecimal Bytes</h3>
  <pre><code>alert tcp $EXTERNAL_NET any -> $HOME_NET any (msg:"Malware C2 Beacon"; content:"|00 00 01 4A|"; sid:1000005;)</code></pre>

  <h2>5. Execution (CLI)</h2>
  <pre><code># Test configuration syntax:
snort -T -c /etc/snort/snort.conf -i eth0

# Run printing alerts to console:
snort -A console -q -c /etc/snort/snort.conf -i eth0</code></pre>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';