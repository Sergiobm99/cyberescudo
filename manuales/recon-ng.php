<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Recon-NG: Recopilación Automática de Información — CyberEscudo' : 'Recon-NG: Automated Information Gathering — CyberEscudo';
$contentTitle = $lang==='es' ? 'Recon-NG: Recopilación Automática de Información' : 'Recon-NG: Automated Information Gathering';
$contentDate  = '2022-02-20';
$contentTags  = ['Recon-NG','OSINT','Reconocimiento','Shodan','Pentesting'];
ob_start();
if ($lang==='es'): ?>
<div class="prose">
  <p><strong>Recon-NG</strong> es un framework de reconocimiento pasivo en Python que automatiza la recopilación de información sobre dominios, IPs y redes usando múltiples módulos y fuentes de datos.</p>

  <h2>1. Iniciar Recon-NG</h2>
  <pre><code>recon-ng</code></pre>

  <h2>2. Instalar y usar el módulo hackertarget</h2>
  <p>Módulo para descubrir hosts asociados a un dominio usando la API de HackerTarget:</p>
  <pre><code># Instalar el módulo:
marketplace install recon/domains-hosts/hackertarget

# Cargar el módulo:
modules load recon/domains-hosts/hackertarget

# Configurar el dominio objetivo:
options set SOURCE grammy.com

# Ejecutar:
run</code></pre>
  <p>Resultado: lista de subdominios e IPs asociados al dominio objetivo.</p>

  <h2>3. Instalar y usar el módulo Shodan</h2>
  <p>Módulo para descubrir hosts dentro de bloques de red usando Shodan (requiere API key):</p>
  <pre><code># Instalar:
marketplace install recon/netblocks-hosts/shodan_net

# Cargar y configurar:
modules load recon/netblocks-hosts/shodan_net
options set SOURCE [bloque_de_red]
run</code></pre>

  <h2>4. Ejemplos de objetivos analizados</h2>
  <table>
    <thead><tr><th>Dominio</th><th>Módulo usado</th><th>Información obtenida</th></tr></thead>
    <tbody>
      <tr><td>grammy.com</td><td>hackertarget</td><td>Subdominios, IPs Akamai, CDN</td></tr>
      <tr><td>incibe.es</td><td>hackertarget</td><td>Hosts, IPs Telefónica/RIMA</td></tr>
      <tr><td>uco.es</td><td>hackertarget + shodan</td><td>Subdominios universitarios, servicios expuestos</td></tr>
    </tbody>
  </table>

  <h2>Comandos útiles de Recon-NG</h2>
  <pre><code># Ver todos los módulos disponibles:
marketplace search

# Ver módulos instalados:
modules search

# Ver opciones del módulo actual:
options list

# Ver los resultados almacenados en la base de datos:
show hosts
show contacts
show ports</code></pre>
</div>
<?php else: ?>
<div class="prose">
  <p><strong>Recon-NG</strong> is a Python-based reconnaissance framework that automates information gathering about domains, IPs and networks using multiple modules and data sources.</p>

  <h2>1. Start Recon-NG</h2>
  <pre><code>recon-ng</code></pre>

  <h2>2. hackertarget Module — Subdomain Discovery</h2>
  <pre><code>marketplace install recon/domains-hosts/hackertarget
modules load recon/domains-hosts/hackertarget
options set SOURCE grammy.com
run</code></pre>
  <p>Returns subdomains and IPs associated with the target domain.</p>

  <h2>3. Shodan Module — Netblock Scanning</h2>
  <pre><code>marketplace install recon/netblocks-hosts/shodan_net
modules load recon/netblocks-hosts/shodan_net
options set SOURCE [netblock]
run</code></pre>

  <h2>4. Domains Analysed</h2>
  <table>
    <thead><tr><th>Domain</th><th>Module</th><th>Information Found</th></tr></thead>
    <tbody>
      <tr><td>grammy.com</td><td>hackertarget</td><td>Subdomains, Akamai IPs, CDN</td></tr>
      <tr><td>incibe.es</td><td>hackertarget</td><td>Hosts, Telefónica/RIMA IPs</td></tr>
      <tr><td>uco.es</td><td>hackertarget + shodan</td><td>University subdomains, exposed services</td></tr>
    </tbody>
  </table>

  <h2>Useful Commands</h2>
  <pre><code>marketplace search   # List all available modules
modules search       # List installed modules
options list         # View current module options
show hosts           # View collected hosts
show contacts        # View collected contacts</code></pre>
</div>
<?php endif;
$contentBody=ob_get_clean();
require __DIR__.'/../templates/content-page.php';
