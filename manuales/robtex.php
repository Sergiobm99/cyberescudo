<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'ROBTEX: Análisis de Dominios e IPs — CyberEscudo' : 'ROBTEX: Domain & IP Analysis — CyberEscudo';
$contentTitle = $lang==='es' ? 'ROBTEX: Análisis de Dominios e IPs' : 'ROBTEX: Domain & IP Analysis';
$contentDate  = '2022-02-10';
$contentTags  = ['ROBTEX','OSINT','DNS','Recon','BGP'];
ob_start();
if ($lang==='es'): ?>
<div class="prose">
  <p><strong>ROBTEX</strong> (<a href="https://www.robtex.com/">robtex.com</a>) es una herramienta OSINT para el análisis pasivo de dominios, IPs, registros DNS, BGP y sistemas autónomos.</p>

  <h2>Secciones de análisis en ROBTEX</h2>
  <table>
    <thead><tr><th>Sección</th><th>Información proporcionada</th></tr></thead>
    <tbody>
      <tr><td>Análisis</td><td>CNAME, IP asociada, PTR (hostname inverso), ubicación geográfica.</td></tr>
      <tr><td>DNSBL</td><td>Lista de bloqueo en tiempo real: dominios e IPs bloqueadas por spam/malware.</td></tr>
      <tr><td>Records</td><td>Registros DNS completos: A, CNAME, BGP (AS), descripción, ruta, PTR.</td></tr>
      <tr><td>SEO</td><td>Ranking Alexa, Cisco Umbrella y Majestic.</td></tr>
      <tr><td>Shared</td><td>Hostnames e IPs que comparten la misma infraestructura.</td></tr>
      <tr><td>Whois/History/Graph</td><td>Requieren registro en la plataforma.</td></tr>
    </tbody>
  </table>

  <h2>Análisis de www.grammys.com</h2>
  <pre><code>CNAME:    e11361.g.akamaiedge.net
IP:       23.6.189.58
PTR:      a23-6-189-58.deploy.akamaitechnologies.com
Ubicación: Cambridge, Estados Unidos
BGP AS:   AS16625 (Akamai Technologies)
Ruta:     23.6.176.0/20</code></pre>
  <p>DNSBL activa para este dominio en <code>dsn.rfc-clueless.org</code>, <code>fulldom.rfc-clueless.org</code> y otros.</p>
  <p>SEO: Alexa #64514 | Cisco Umbrella #388506 | Majestic #2924</p>

  <h2>Análisis de www.incibe.es</h2>
  <pre><code>IP:       195.53.165.153
Ubicación: España
Whois:    S.M.E. Instituto Nacional de Ciberseguridad de España
Ruta:     195.53.0.0/16
BGP AS:   AS3352 (Telefonica de España — RIMA)
PTR:      153.red-195-53-165.customer.static.ccgg.telefonica.net</code></pre>

  <h2>Uso en reconocimiento</h2>
  <ul>
    <li>Identificar la infraestructura real detrás de un CDN (ej: Akamai, Cloudflare).</li>
    <li>Encontrar otros dominios alojados en la misma IP (hosting compartido).</li>
    <li>Determinar el proveedor de red (ASN/BGP) y la geolocalización.</li>
    <li>Comprobar si la IP/dominio está en listas negras (DNSBL).</li>
  </ul>
</div>
<?php else: ?>
<div class="prose">
  <p><strong>ROBTEX</strong> (<a href="https://www.robtex.com/">robtex.com</a>) is an OSINT tool for passive analysis of domains, IPs, DNS records and BGP autonomous systems.</p>

  <h2>Analysis Sections</h2>
  <table>
    <thead><tr><th>Section</th><th>Information</th></tr></thead>
    <tbody>
      <tr><td>Analysis</td><td>CNAME, IP, PTR (reverse hostname), geolocation</td></tr>
      <tr><td>DNSBL</td><td>Real-time blocklist status for spam/malware</td></tr>
      <tr><td>Records</td><td>Full DNS records: A, CNAME, BGP (AS), route, PTR</td></tr>
      <tr><td>SEO</td><td>Alexa, Cisco Umbrella and Majestic ranking</td></tr>
      <tr><td>Shared</td><td>Hostnames and IPs on the same infrastructure</td></tr>
    </tbody>
  </table>

  <h2>Analysis: www.grammys.com</h2>
  <pre><code>CNAME:    e11361.g.akamaiedge.net
IP:       23.6.189.58
PTR:      a23-6-189-58.deploy.akamaitechnologies.com
Location: Cambridge, United States
BGP AS:   AS16625 (Akamai Technologies)
SEO:      Alexa #64514 | Majestic #2924</code></pre>

  <h2>Analysis: www.incibe.es</h2>
  <pre><code>IP:       195.53.165.153
Location: Spain
Whois:    Instituto Nacional de Ciberseguridad de Espana
Route:    195.53.0.0/16
BGP AS:   AS3352 (Telefonica de Espana — RIMA)</code></pre>

  <h2>Reconnaissance Uses</h2>
  <ul>
    <li>Identify real infrastructure behind CDNs (Akamai, Cloudflare).</li>
    <li>Discover other domains hosted on the same IP (shared hosting).</li>
    <li>Determine network provider (ASN/BGP) and geolocation.</li>
    <li>Check if IP/domain appears on DNSBL block lists.</li>
  </ul>
</div>
<?php endif;
$contentBody=ob_get_clean();
require __DIR__.'/../templates/content-page.php';
