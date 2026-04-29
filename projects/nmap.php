<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Nmap: Reconocimiento y Escaneo de Redes — CyberEscudo' : 'Nmap: Network Reconnaissance & Scanning — CyberEscudo';
$contentTitle = $lang==='es' ? 'Nmap: Reconocimiento y Escaneo de Redes' : 'Nmap: Network Reconnaissance & Scanning';
$contentDate  = '2022-03-01';
$contentTags  = ['Nmap','Reconocimiento','Pentesting','Puertos','NSE'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p><strong>Nmap</strong> (Network Mapper) es la herramienta de reconocimiento activo más utilizada en pentesting. Permite descubrir hosts, puertos abiertos, versiones de servicios y ejecutar scripts NSE de seguridad.</p>

  <h2>1. Descubrimiento de hosts en red</h2>
  <pre><code># Ping scan — hosts activos sin escanear puertos:
nmap -sn 192.168.1.0/24

# Lista de hosts sin enviar paquetes (DNS inverso):
nmap -sL 192.168.1.0/24</code></pre>

  <h2>2. Tipos de escaneo de puertos</h2>
  <pre><code># SYN Scan (stealth) — más rápido y discreto (requiere root):
nmap -sS 192.168.1.10

# TCP Connect — sin privilegios root:
nmap -sT 192.168.1.10

# UDP Scan — servicios UDP (DNS, SNMP, etc.):
nmap -sU 192.168.1.10

# Escaneo de todos los puertos (1-65535):
nmap -p- 192.168.1.10

# Puertos específicos:
nmap -p 22,80,443,3306 192.168.1.10

# Rango de puertos:
nmap -p 1-1000 192.168.1.10</code></pre>

  <h2>3. Detección de versiones y SO</h2>
  <pre><code># Detección de versión de servicios:
nmap -sV 192.168.1.10

# Detección de sistema operativo:
nmap -O 192.168.1.10

# Combinación agresiva (versión + SO + scripts + traceroute):
nmap -A 192.168.1.10</code></pre>

  <h2>4. Escaneos avanzados con NSE (Nmap Scripting Engine)</h2>
  <pre><code># Ejecutar scripts por defecto:
nmap -sC 192.168.1.10

# Buscar vulnerabilidades conocidas:
nmap --script vuln 192.168.1.10

# Script específico — comprobar SMB:
nmap --script smb-vuln-ms17-010 192.168.1.10

# Scripts HTTP:
nmap --script http-title,http-headers 192.168.1.10

# Scripts SSL/TLS:
nmap --script ssl-enum-ciphers -p 443 192.168.1.10

# Fuerza bruta SSH:
nmap --script ssh-brute -p 22 192.168.1.10</code></pre>

  <h2>5. Evasión de firewalls e IDS</h2>
  <pre><code># Fragmentar paquetes para evadir IDS:
nmap -f 192.168.1.10

# Usar señuelos (decoy):
nmap -D RND:10 192.168.1.10

# Escaneo lento para no alertar IDS:
nmap -T1 192.168.1.10

# Velocidades: T0 (paranoid) → T5 (insane)
# Para pentesting silencioso: T2 o T3
# Para CTFs/laboratorios: T4 o T5</code></pre>

  <h2>6. Exportar resultados</h2>
  <pre><code># Formato normal:
nmap -oN resultado.txt 192.168.1.10

# Formato XML (para importar en Metasploit):
nmap -oX resultado.xml 192.168.1.10

# Todos los formatos a la vez:
nmap -oA escaneo_completo 192.168.1.10</code></pre>

  <h2>Tabla de opciones más utilizadas</h2>
  <table>
    <thead><tr><th>Flag</th><th>Descripción</th></tr></thead>
    <tbody>
      <tr><td><code>-sS</code></td><td>SYN scan (stealth, requiere root)</td></tr>
      <tr><td><code>-sV</code></td><td>Detección de versión de servicios</td></tr>
      <tr><td><code>-O</code></td><td>Detección de sistema operativo</td></tr>
      <tr><td><code>-A</code></td><td>Escaneo agresivo completo</td></tr>
      <tr><td><code>-p-</code></td><td>Todos los puertos (1-65535)</td></tr>
      <tr><td><code>-sC</code></td><td>Scripts NSE por defecto</td></tr>
      <tr><td><code>--script vuln</code></td><td>Scripts de detección de vulnerabilidades</td></tr>
      <tr><td><code>-T4</code></td><td>Velocidad agresiva</td></tr>
      <tr><td><code>-oA</code></td><td>Exportar en todos los formatos</td></tr>
    </tbody>
  </table>
</div>
<?php else: ?>
<div class="prose">
  <p><strong>Nmap</strong> (Network Mapper) is the most widely used active reconnaissance tool in penetration testing. It discovers live hosts, open ports, service versions, OS fingerprints, and can run NSE security scripts.</p>

  <h2>1. Host Discovery</h2>
  <pre><code># Ping scan — find live hosts without scanning ports:
nmap -sn 192.168.1.0/24

# List hosts via reverse DNS (no packets sent):
nmap -sL 192.168.1.0/24</code></pre>

  <h2>2. Port Scan Types</h2>
  <pre><code># SYN Scan (stealth) — fastest and quietest (requires root):
nmap -sS 192.168.1.10

# TCP Connect — no root required:
nmap -sT 192.168.1.10

# UDP Scan — DNS, SNMP, TFTP services:
nmap -sU 192.168.1.10

# Scan all 65535 ports:
nmap -p- 192.168.1.10

# Specific ports:
nmap -p 22,80,443,3306 192.168.1.10

# Port range:
nmap -p 1-1000 192.168.1.10</code></pre>

  <h2>3. Version & OS Detection</h2>
  <pre><code># Service version detection:
nmap -sV 192.168.1.10

# OS fingerprinting:
nmap -O 192.168.1.10

# Aggressive scan (version + OS + scripts + traceroute):
nmap -A 192.168.1.10</code></pre>

  <h2>4. NSE Scripts</h2>
  <pre><code># Default NSE scripts:
nmap -sC 192.168.1.10

# Vulnerability detection scripts:
nmap --script vuln 192.168.1.10

# Specific script — check EternalBlue (MS17-010):
nmap --script smb-vuln-ms17-010 192.168.1.10

# HTTP scripts:
nmap --script http-title,http-headers 192.168.1.10

# SSL/TLS cipher enumeration:
nmap --script ssl-enum-ciphers -p 443 192.168.1.10

# SSH brute force:
nmap --script ssh-brute -p 22 192.168.1.10</code></pre>

  <h2>5. Firewall & IDS Evasion</h2>
  <pre><code># Fragment packets to evade IDS:
nmap -f 192.168.1.10

# Decoy scan (mix real scan with fake sources):
nmap -D RND:10 192.168.1.10

# Slow scan to avoid triggering IDS:
nmap -T1 192.168.1.10

# Timing templates: T0 (paranoid) → T5 (insane)
# Silent pentesting: T2 or T3
# Labs/CTFs: T4 or T5</code></pre>

  <h2>6. Exporting Results</h2>
  <pre><code># Normal text format:
nmap -oN result.txt 192.168.1.10

# XML format (importable into Metasploit):
nmap -oX result.xml 192.168.1.10

# All formats at once:
nmap -oA full_scan 192.168.1.10</code></pre>

  <h2>Most Used Flags</h2>
  <table>
    <thead><tr><th>Flag</th><th>Description</th></tr></thead>
    <tbody>
      <tr><td><code>-sS</code></td><td>SYN (stealth) scan — requires root</td></tr>
      <tr><td><code>-sV</code></td><td>Service version detection</td></tr>
      <tr><td><code>-O</code></td><td>OS detection</td></tr>
      <tr><td><code>-A</code></td><td>Aggressive scan (all-in-one)</td></tr>
      <tr><td><code>-p-</code></td><td>All 65535 ports</td></tr>
      <tr><td><code>-sC</code></td><td>Default NSE scripts</td></tr>
      <tr><td><code>--script vuln</code></td><td>Vulnerability detection scripts</td></tr>
      <tr><td><code>-T4</code></td><td>Aggressive timing</td></tr>
      <tr><td><code>-oA</code></td><td>Export in all formats</td></tr>
    </tbody>
  </table>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';
