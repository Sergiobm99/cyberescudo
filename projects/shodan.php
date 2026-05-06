<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Shodan: OSINT y Reconocimiento Pasivo — CyberEscudo' : 'Shodan: OSINT & Passive Reconnaissance — CyberEscudo';
$contentTitle = $lang==='es' ? 'Shodan: OSINT y Reconocimiento Pasivo' : 'Shodan: OSINT & Passive Reconnaissance';
$contentDate  = '2024-09-05';
$contentDiff  = 'intermediate';
$contentTags  = ['Shodan','OSINT','Reconocimiento','IoT','CVE','Pasivo'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p><strong>Shodan</strong> es el motor de búsqueda más peligroso y fascinante de Internet. Mientras que Google rastrea páginas web buscando contenido, Shodan escanea puertos (Banner Grabbing) buscando dispositivos: servidores, webcams, sistemas de control industrial (SCADA), routers y bases de datos. Es la joya de la corona del <strong>Reconocimiento Pasivo</strong>: toda la información ya ha sido indexada por Shodan, por lo que tú nunca tocas la IP del objetivo (haciéndote invisible).</p>

  <h2>1. Arquitectura y Configuración (CLI)</h2>
  <p>Aunque Shodan tiene interfaz web, los profesionales utilizan su CLI (Interfaz de Línea de Comandos) y su API para automatizar tareas y evadir las limitaciones visuales de la web.</p>
  <pre><code># Instalación mediante Python:
pip3 install shodan

# Autenticación (Necesitas tu API Key de account.shodan.io):
shodan init TU_API_KEY_AQUI

# Verificar el estado de tu cuenta (Créditos de búsqueda y escaneo):
shodan info</code></pre>

  <h2>2. Filtros de Red y Geometría</h2>
  <p>Los operadores básicos permiten segmentar Internet por zonas geográficas o propiedades de red. Recuerda no dejar espacios después de los dos puntos (<code>:</code>).</p>
  <pre><code># Buscar por ASN (Autonomous System Number - Identificador del proveedor):
asn:AS3352

# Rango de IPs (CIDR) - Ideal para auditar los activos de una empresa:
net:203.0.113.0/24

# Filtros geográficos combinados:
country:ES city:"Madrid" port:22
country:US org:"Amazon.com"</code></pre>

  <h2>3. Filtros Web Avanzados y MurmurHash (Favicons)</h2>
  <p>Una de las técnicas OSINT más potentes es el rastreo de <strong>Favicons</strong>. Shodan calcula un hash matemático (MurmurHash3) del icono de la pestaña de la web. Si un atacante de Phishing copia la web de un banco, a menudo copia el favicon. Buscando ese hash, puedes descubrir todas las webs de Phishing o paneles ocultos de un framework.</p>
  <pre><code># Encontrar servidores Spring Boot expuestos por su favicon:
http.favicon.hash:116323821

# Buscar en el título o cuerpo del HTML:
http.title:"Dashboard [Jenkins]"
http.html:"defaced by"

# Buscar por el asunto o emisor del Certificado SSL:
ssl.cert.subject.cn:"*.empresa.com"
ssl.cert.issuer.cn:"Let's Encrypt"</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 15 (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Simulador Shodan Dorking
      </h3>
      <p style="margin-bottom: 1.5rem;">Inteligencia de Amenazas te ha asignado una tarea crítica: Necesitamos cuantificar cuántos servidores <strong>Tomcat</strong> están vulnerables a <strong>Log4Shell (CVE-2021-44228)</strong> en los <strong>Estados Unidos (US)</strong>. Construye el Shodan Dork exacto para obtener esta información.</p>
      <a href="/ctf/ctf-15.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 15
      </a>
  </div>

  <h2>4. Búsqueda de Vulnerabilidades y Componentes</h2>
  <p>Shodan cruza las versiones extraídas de los banners (ej. Apache 2.4.49) con la base de datos de CVEs (National Vulnerability Database). Esto permite encontrar objetivos explotables de forma instantánea.</p>
  <pre><code># Buscar una vulnerabilidad específica (Log4Shell):
vuln:CVE-2021-44228

# Filtrar servidores que tienen CUALQUIER vulnerabilidad verificada:
has_vuln:True port:443 country:ES

# Buscar por producto y versión específica:
product:"OpenSSH" version:"7.4"
product:"Microsoft IIS" version:"10.0"</code></pre>

  <h2>5. CLI Mastery: Descarga y Parseo Offline</h2>
  <p>Trabajar mirando la pantalla del terminal no es escalable. Los profesionales de OSINT descargan los datos y los filtran en local para no consumir créditos de la API constantemente.</p>
  <pre><code># 1. Descargar resultados a un archivo JSON comprimido (consume créditos de exportación):
shodan download servidores_ftp "port:21 Anonymous access allowed" --limit 1000

# 2. Parsear el archivo descargado para extraer solo IPs y puertos:
shodan parse --fields ip_str,port servidores_ftp.json.gz

# 3. Generar estadísticas (Facets) sin descargar los datos:
# ¿Cuáles son los 5 países con más bases de datos MongoDB abiertas?
shodan stats --facets country:5 "port:27017 -auth"</code></pre>

  <h2>6. Detección de Honeypots (Señuelos)</h2>
  <p>Los investigadores y el Blue Team despliegan "Honeypots" (sistemas falsos) para atrapar a los hackers. Shodan tiene un algoritmo de Machine Learning que calcula la probabilidad de que una IP sea una trampa.</p>
  <pre><code># Evaluar la puntuación de Honeypot de una IP (1.0 = 100% Trampa, 0.0 = Real):
shodan honeyscore 1.2.3.4</code></pre>

  <h2>7. Scripting con Python API (Automatización)</h2>
  <p>Integrar Shodan en tus propios scripts de Python te permite automatizar la vigilancia del perímetro corporativo de forma diaria.</p>
  <pre><code>import shodan
import json

api = shodan.Shodan('TU_API_KEY')

try:
    # Buscar cámaras Hikvision en Madrid
    query = 'product:"Hikvision" city:"Madrid"'
    resultados = api.search(query)
    
    print(f"Dispositivos encontrados: {resultados['total']}")
    for host in resultados['matches']:
        print(f"IP: {host['ip_str']} | ISP: {host.get('isp', 'N/A')}")
        
except shodan.APIError as e:
    print(f"Error en la API: {e}")</code></pre>

  <h2>8. Tabla de Dorks OSINT Clásicos</h2>
  <table>
    <thead><tr><th>Objetivo (Target)</th><th>Shodan Dork</th></tr></thead>
    <tbody>
      <tr><td>RDP (Escritorios Remotos) Expuestos</td><td><code>port:3389 has_screenshot:true</code></td></tr>
      <tr><td>Bases de Datos MongoDB Abiertas</td><td><code>port:27017 "MongoDB Server Information" -"auth"</code></td></tr>
      <tr><td>Cámaras Web IP Sin Contraseña</td><td><code>"Server: SQ-WEBCAM"</code> o <code>"GoAhead-Webs"</code></td></tr>
      <tr><td>Paneles Solares / Sistemas SCADA</td><td><code>port:502 "Modbus"</code></td></tr>
      <tr><td>Paneles de Jenkins vulnerables</td><td><code>http.title:"Dashboard [Jenkins]" -"Authentication"</code></td></tr>
    </tbody>
  </table>

</div>

<?php else: ?>
<div class="prose">
  <p><strong>Shodan</strong> is the most dangerous and fascinating search engine on the Internet. While Google crawls web pages looking for content, Shodan scans ports (Banner Grabbing) looking for devices: servers, webcams, industrial control systems (SCADA), routers, and databases. It is the crown jewel of <strong>Passive Reconnaissance</strong>.</p>

  <h2>1. Architecture & Setup (CLI)</h2>
  <p>Professionals use the CLI (Command Line Interface) and API to automate tasks and evade visual web limitations.</p>
  <pre><code># Python installation:
pip3 install shodan

# Authentication (API Key from account.shodan.io):
shodan init YOUR_API_KEY_HERE

# Check account status (query/scan credits):
shodan info</code></pre>

  <h2>2. Network & Geometry Filters</h2>
  <p>Basic operators allow you to segment the Internet by geography or network properties.</p>
  <pre><code># By ASN (Autonomous System Number):
asn:AS3352

# By IP range (CIDR) - Great for corporate perimeter auditing:
net:203.0.113.0/24

# Combined geographic filters:
country:US org:"Amazon.com" port:443</code></pre>

  <h2>3. Advanced Web Filters & MurmurHash (Favicons)</h2>
  <p>One of the most powerful OSINT techniques is <strong>Favicon hashing</strong>. Shodan calculates a mathematical hash (MurmurHash3) of the website's tab icon. You can use it to find phishing sites or hidden admin panels across the entire internet.</p>
  <pre><code># Find exposed Spring Boot servers by their favicon hash:
http.favicon.hash:116323821

# Search inside HTML title or body:
http.title:"Dashboard [Jenkins]"
http.html:"defaced by"

# Search by SSL Certificate issuer/subject:
ssl.cert.subject.cn:"*.company.com"</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 15 (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Shodan Dorking Simulator
      </h3>
      <p style="margin-bottom: 1.5rem;">Threat Intelligence has assigned you a critical task: We need to quantify how many <strong>Tomcat</strong> servers are vulnerable to <strong>Log4Shell (CVE-2021-44228)</strong> in the <strong>United States (US)</strong>. Construct the exact Shodan Dork to obtain this information.</p>
      <a href="/ctf/ctf-15.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 15 CHALLENGE
      </a>
  </div>

  <h2>4. Vulnerability & Component Hunting</h2>
  <p>Shodan cross-references extracted versions (e.g., Apache 2.4.49) with the CVE database. This allows instant discovery of exploitable targets.</p>
  <pre><code># Search for a specific vulnerability (Log4Shell):
vuln:CVE-2021-44228

# Filter servers that have ANY verified vulnerability:
has_vuln:True port:443 country:US

# Search by specific product and version:
product:"OpenSSH" version:"7.4"</code></pre>

  <h2>5. CLI Mastery: Download & Offline Parsing</h2>
  <p>Working on a terminal screen isn't scalable. OSINT pros download the data and filter it locally to save API credits.</p>
  <pre><code># 1. Download results to a compressed JSON file:
shodan download ftp_servers "port:21 Anonymous access allowed" --limit 1000

# 2. Parse the downloaded file to extract only IPs and ports:
shodan parse --fields ip_str,port ftp_servers.json.gz

# 3. Generate statistics (Facets) without downloading data:
# Top 5 countries with open MongoDB databases?
shodan stats --facets country:5 "port:27017 -auth"</code></pre>

  <h2>6. Honeypot Detection</h2>
  <p>Blue Teams deploy "Honeypots" (fake systems) to trap hackers. Shodan has an ML algorithm that calculates the probability of an IP being a trap.</p>
  <pre><code># Evaluate Honeypot score (1.0 = 100% Trap, 0.0 = Real):
shodan honeyscore 1.2.3.4</code></pre>

  <h2>7. Python API Scripting (Automation)</h2>
  <pre><code>import shodan
api = shodan.Shodan('YOUR_API_KEY')

try:
    results = api.search('product:"Hikvision" city:"London"')
    print(f"Found: {results['total']}")
except shodan.APIError as e:
    print(f"Error: {e}")</code></pre>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';