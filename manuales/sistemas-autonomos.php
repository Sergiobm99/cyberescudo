<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Análisis de Sistemas Autónomos con BGP — CyberEscudo' : 'Autonomous Systems Analysis with BGP — CyberEscudo';
$contentTitle = $lang==='es' ? 'Análisis de Sistemas Autónomos con BGP' : 'Autonomous Systems Analysis with BGP';
$contentDate  = '2022-02-15';
$contentTags  = ['BGP','Sistemas Autónomos','Mikrotik','Routing','Redes'];
ob_start();
if ($lang==='es'): ?>
<div class="prose">
  <p>Práctica de configuración de <strong>enrutamiento BGP entre sistemas autónomos</strong> con routers Mikrotik, simulando una red de múltiples ASes interconectados.</p>

  <h2>Topología</h2>
  <ul>
    <li>Router A (AS 65000) ↔ Router B (AS 100) ↔ Router C (AS 100)</li>
    <li>Router D (AS 100) ↔ Router E (AS 100) ↔ Router F (AS 65000)</li>
    <li>Router A se comunica con Router F a través de la red de ASes intermedios.</li>
  </ul>

  <h2>Configuración del Router A (AS 65000)</h2>
  <pre><code># Configurar IPs en las interfaces:
ip address add address=[ip/mascara] interface=[interfaz]

# Crear interfaz bridge:
interface bridge add name=bridge1

# Configurar BGP — AS 65000, redistribuir rutas conectadas:
routing bgp instance set default as=65000 redistribute-connected=yes

# Peers BGP (routers vecinos):
routing bgp peer add remote-address=10.1.1.2 remote-as=100 allow-as-in=1
routing bgp peer add remote-address=10.1.1.6 remote-as=100 allow-as-in=1</code></pre>

  <h2>Configuración de routers B, C, D, E, F</h2>
  <p>Misma estructura: configurar IPs por interfaz y añadir peers BGP hacia los routers vecinos correspondientes según la topología.</p>

  <h2>Comprobaciones</h2>
  <pre><code># Desde Router A — ping al Router F (extremo opuesto):
ping 10.3.3.2

# Ver tabla de enrutamiento completa del Router A:
ip route print detail

# Verificar desde Router F que puede alcanzar todos los routers:
ip route print</code></pre>

  <h2>Conceptos clave</h2>
  <table>
    <thead><tr><th>Concepto</th><th>Descripción</th></tr></thead>
    <tbody>
      <tr><td>AS (Sistema Autónomo)</td><td>Conjunto de redes bajo una misma política de enrutamiento, identificado por un número ASN.</td></tr>
      <tr><td>BGP</td><td>Border Gateway Protocol — protocolo de enrutamiento entre sistemas autónomos (inter-AS).</td></tr>
      <tr><td>allow-as-in</td><td>Permite que un router acepte rutas que ya contienen su propio AS en el path (evita loop detection).</td></tr>
      <tr><td>redistribute-connected</td><td>Anuncia las redes directamente conectadas al router vía BGP.</td></tr>
    </tbody>
  </table>
</div>
<?php else: ?>
<div class="prose">
  <p>BGP routing configuration between <strong>autonomous systems</strong> using Mikrotik routers, simulating a multi-AS interconnected network.</p>

  <h2>Topology</h2>
  <ul>
    <li>Router A (AS 65000) ↔ Router B (AS 100) ↔ Router C (AS 100)</li>
    <li>Router D (AS 100) ↔ Router E (AS 100) ↔ Router F (AS 65000)</li>
    <li>Goal: Router A communicates with Router F through the intermediate AS network.</li>
  </ul>

  <h2>Router A Configuration (AS 65000)</h2>
  <pre><code># Configure interface IPs:
ip address add address=[ip/mask] interface=[iface]

# Create bridge interface:
interface bridge add name=bridge1

# Configure BGP:
routing bgp instance set default as=65000 redistribute-connected=yes

# Add BGP peers:
routing bgp peer add remote-address=10.1.1.2 remote-as=100 allow-as-in=1
routing bgp peer add remote-address=10.1.1.6 remote-as=100 allow-as-in=1</code></pre>

  <h2>Routers B, C, D, E, F</h2>
  <p>Same structure: configure interface IPs and add BGP peers to each neighbouring router according to the topology.</p>

  <h2>Verification</h2>
  <pre><code># From Router A — ping Router F:
ping 10.3.3.2

# View full routing table:
ip route print detail

# From Router F — verify reachability:
ip route print</code></pre>

  <h2>Key Concepts</h2>
  <table>
    <thead><tr><th>Concept</th><th>Description</th></tr></thead>
    <tbody>
      <tr><td>AS</td><td>Autonomous System — set of networks under a single routing policy, identified by an ASN.</td></tr>
      <tr><td>BGP</td><td>Border Gateway Protocol — inter-AS routing protocol.</td></tr>
      <tr><td>allow-as-in</td><td>Allows accepting routes that already contain the router own AS in the path.</td></tr>
      <tr><td>redistribute-connected</td><td>Announces directly connected networks via BGP.</td></tr>
    </tbody>
  </table>
</div>
<?php endif;
$contentBody=ob_get_clean();
require __DIR__.'/../templates/content-page.php';
