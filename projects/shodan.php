<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Shodan: OSINT y Reconocimiento Pasivo — CyberEscudo' : 'Shodan: OSINT & Passive Reconnaissance — CyberEscudo';
$contentTitle = $lang==='es' ? 'Shodan: OSINT y Reconocimiento Pasivo' : 'Shodan: OSINT & Passive Reconnaissance';
$contentDate  = '2024-09-05';
$contentDiff  = 'basic';
$contentTags  = ['Shodan','OSINT','Reconocimiento','IoT','CVE','Pasivo'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p><strong>Shodan</strong> es el motor de búsqueda de dispositivos conectados a Internet. Indexa banners de servicios, versiones de software y metadatos de millones de hosts globalmente. Es la herramienta más potente de reconocimiento pasivo: no enviamos ningún paquete al objetivo.</p>

  <h2>1. Configuración de la CLI de Shodan</h2>
  <pre><code># Instalar la CLI:
pip3 install shodan

# Autenticar con tu API key (requiere cuenta gratuita o de pago):
shodan init TU_API_KEY

# Ver información de tu cuenta:
shodan info

# Ayuda de comandos:
shodan --help</code></pre>

  <h2>2. Búsquedas básicas por web</h2>
  <pre><code># Buscar por banner de servicio:
apache
nginx 1.18
IIS/10.0

# Buscar por país:
country:ES apache
country:US "webcam"

# Buscar por ciudad:
city:"Madrid" port:22
city:"Barcelona" product:MySQL

# Buscar por organización/ASN:
org:"Telefonica"
org:"Amazon" port:3389

# Buscar por rango de red/CIDR:
net:192.168.0.0/24
net:203.0.113.0/24

# Buscar por puerto específico:
port:8080 product:Tomcat
port:27017             # MongoDB expuesto
port:9200              # Elasticsearch expuesto
port:6379              # Redis sin autenticación</code></pre>

  <h2>3. Filtros avanzados de Shodan</h2>
  <pre><code># CVE específica en producción:
vuln:CVE-2021-44228    # Log4Shell — servidores afectados en todo el mundo
vuln:CVE-2019-19781    # Citrix ADC (Shitrix)
vuln:CVE-2017-0144     # EternalBlue (MS17-010)

# Producto + versión:
product:OpenSSH version:7.4
product:"Apache httpd" version:2.4.49   # Apache Path Traversal CVE-2021-41773

# Sistemas de control industrial (ICS/SCADA):
product:Siemens
"Modbus" port:502
"SCADA" port:102

# Cámaras y dispositivos IoT:
"Server: IP Webcam Server"
"webcamXP"
product:"Hikvision IP Camera"
"GoAhead-Webs" port:80

# Paneles de administración expuestos:
http.title:"phpMyAdmin"
http.title:"Grafana"
http.title:"Kibana"
http.title:"Jenkins"
http.title:"Admin Panel"

# Certificados SSL de un dominio:
ssl:"objetivo.com"
ssl.cert.subject.cn:"*.objetivo.com"</code></pre>

  <h2>4. Uso de la CLI de Shodan</h2>
  <pre><code># Búsqueda básica:
shodan search "apache 2.4.49"

# Contar resultados sin mostrarlos:
shodan count "port:27017 MongoDB"

# Buscar y mostrar solo IPs:
shodan search --fields ip_str "port:6379 -auth" | awk '{print $1}'

# Buscar hosts de una organización:
shodan search --fields ip_str,port,org "org:Telefonica" 

# Consultar información de una IP:
shodan host 1.2.3.4

# Monitorización de alertas (plan pago):
shodan alert create "Mi empresa" 203.0.113.0/24
shodan alert list</code></pre>

  <h2>5. Shodan + Python API</h2>
  <pre><code>import shodan
import json

API_KEY = "TU_API_KEY"
api = shodan.Shodan(API_KEY)

# Buscar servicios MongoDB expuestos en España:
try:
    results = api.search('port:27017 country:ES')
    print(f'Total resultados: {results["total"]}')
    
    for r in results['matches']:
        print(f"IP: {r['ip_str']}")
        print(f"Puerto: {r['port']}")
        print(f"Org: {r.get('org', 'N/A')}")
        print(f"Versión: {r.get('version', 'N/A')}")
        print("---")
        
except shodan.APIError as e:
    print(f'Error: {e}')

# Obtener información completa de un host:
host = api.host("1.2.3.4")
print(json.dumps(host, indent=2, default=str))</code></pre>

  <h2>6. Búsquedas combinadas para pentesting</h2>
  <pre><code># Paneles de login de VPN expuestos:
http.title:"Pulse Connect Secure"
http.title:"GlobalProtect"
http.title:"Cisco AnyConnect"

# Dispositivos de red con credenciales por defecto:
"default password" http.title:"Router"
product:"MikroTik" port:8291

# Bases de datos sin autenticación:
product:CouchDB port:5984
"Elasticsearch" port:9200 country:ES

# Servidores con versiones vulnerables antiguas:
product:"Apache httpd" version:"2.2"
product:OpenSSL version:1.0

# RDP expuesto:
port:3389 os:"Windows Server 2008"

# Panels de control industrial en España:
country:ES port:102 "Siemens"</code></pre>

  <h2>7. Dorking con Google + Shodan combinados</h2>
  <table>
    <thead><tr><th>Objetivo</th><th>Filtro Shodan</th></tr></thead>
    <tbody>
      <tr><td>MongoDB sin auth</td><td><code>port:27017 -"requires auth"</code></td></tr>
      <tr><td>Redis sin auth</td><td><code>port:6379 "redis_version" -"requirepass"</code></td></tr>
      <tr><td>Elasticsearch abierto</td><td><code>port:9200 json country:ES</code></td></tr>
      <tr><td>Log4Shell vulnerable</td><td><code>vuln:CVE-2021-44228</code></td></tr>
      <tr><td>Cámaras IP</td><td><code>product:"Hikvision" port:80</code></td></tr>
      <tr><td>Jenkins sin auth</td><td><code>http.title:"Dashboard [Jenkins]" -"Authentication"</code></td></tr>
      <tr><td>Grafana expuesto</td><td><code>http.title:"Grafana" country:ES</code></td></tr>
      <tr><td>phpMyAdmin</td><td><code>http.title:"phpMyAdmin" country:ES</code></td></tr>
    </tbody>
  </table>
</div>
<?php else: ?>
<div class="prose">
  <p><strong>Shodan</strong> is the search engine for internet-connected devices. It indexes service banners, software versions, and metadata from millions of hosts worldwide. It's the most powerful passive reconnaissance tool — we send no packets to the target.</p>

  <h2>1. Shodan CLI Setup</h2>
  <pre><code># Install CLI:
pip3 install shodan

# Authenticate with your API key (free account works):
shodan init YOUR_API_KEY

# Check account info:
shodan info</code></pre>

  <h2>2. Basic Web Searches</h2>
  <pre><code># By service banner:
apache
nginx 1.18

# By country:
country:US "webcam"
country:ES apache

# By city:
city:"Madrid" port:22
city:"London" product:MySQL

# By organisation:
org:"Amazon" port:3389
org:"Cloudflare"

# By CIDR range:
net:203.0.113.0/24

# By open port:
port:27017          # Exposed MongoDB
port:9200           # Exposed Elasticsearch
port:6379           # Unauthenticated Redis</code></pre>

  <h2>3. Advanced Shodan Filters</h2>
  <pre><code># Specific CVE in production:
vuln:CVE-2021-44228    # Log4Shell
vuln:CVE-2019-19781    # Citrix ADC
vuln:CVE-2017-0144     # EternalBlue (MS17-010)

# Product + version:
product:"Apache httpd" version:2.4.49   # CVE-2021-41773
product:OpenSSH version:7.4

# ICS/SCADA:
product:Siemens
"Modbus" port:502

# IoT cameras:
product:"Hikvision IP Camera"
"GoAhead-Webs" port:80

# Exposed admin panels:
http.title:"phpMyAdmin"
http.title:"Grafana"
http.title:"Jenkins"
http.title:"Kibana"

# SSL certificates:
ssl:"target.com"
ssl.cert.subject.cn:"*.target.com"</code></pre>

  <h2>4. Shodan CLI Usage</h2>
  <pre><code># Basic search:
shodan search "apache 2.4.49"

# Count results only:
shodan count "port:27017 MongoDB"

# Get IPs only:
shodan search --fields ip_str "port:6379 -auth"

# Get host info:
shodan host 1.2.3.4

# Alerts (paid plan):
shodan alert create "My company" 203.0.113.0/24
shodan alert list</code></pre>

  <h2>5. Shodan Python API</h2>
  <pre><code>import shodan
import json

API_KEY = "YOUR_API_KEY"
api = shodan.Shodan(API_KEY)

# Search for exposed MongoDB in the US:
results = api.search('port:27017 country:US')
print(f'Total: {results["total"]}')

for r in results['matches']:
    print(f"IP: {r['ip_str']} | Org: {r.get('org','N/A')}")

# Full host info:
host = api.host("1.2.3.4")
print(json.dumps(host, indent=2, default=str))</code></pre>

  <h2>6. Pentesting-Oriented Searches</h2>
  <pre><code># Exposed VPN login panels:
http.title:"Pulse Connect Secure"
http.title:"GlobalProtect"
http.title:"Cisco AnyConnect"

# Databases without authentication:
product:CouchDB port:5984
"Elasticsearch" port:9200

# Old vulnerable versions:
product:"Apache httpd" version:"2.2"
product:OpenSSL version:1.0

# Exposed RDP:
port:3389 os:"Windows Server 2008"</code></pre>

  <h2>7. Quick Reference Filters</h2>
  <table>
    <thead><tr><th>Target</th><th>Shodan Filter</th></tr></thead>
    <tbody>
      <tr><td>MongoDB no auth</td><td><code>port:27017 -"requires auth"</code></td></tr>
      <tr><td>Redis no auth</td><td><code>port:6379 "redis_version" -"requirepass"</code></td></tr>
      <tr><td>Open Elasticsearch</td><td><code>port:9200 json</code></td></tr>
      <tr><td>Log4Shell</td><td><code>vuln:CVE-2021-44228</code></td></tr>
      <tr><td>IP Cameras</td><td><code>product:"Hikvision" port:80</code></td></tr>
      <tr><td>Jenkins no auth</td><td><code>http.title:"Dashboard [Jenkins]" -"Authentication"</code></td></tr>
      <tr><td>phpMyAdmin</td><td><code>http.title:"phpMyAdmin"</code></td></tr>
    </tbody>
  </table>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';
