<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Uso de FoxyProxy — CyberEscudo' : 'Using FoxyProxy — CyberEscudo';
$contentTitle = $lang==='es' ? 'Uso de FoxyProxy' : 'Using FoxyProxy';
$contentDate  = '2022-01-25';
$contentTags  = ['FoxyProxy','Proxy','Firefox','Tor','Anonimato'];
ob_start();
if ($lang==='es'): ?>
<div class="prose">
  <p><strong>FoxyProxy</strong> es una extensión de Firefox que permite gestionar y cambiar entre múltiples proxies de forma rápida desde la barra del navegador.</p>

  <h2>1. Instalación</h2>
  <p>Antes de instalar, anotar la IP pública actual en <a href="https://www.cual-es-mi-ip.net/">cual-es-mi-ip.net</a>.</p>
  <p>Instalar desde: <a href="https://addons.mozilla.org/es/firefox/addon/foxyproxy-standard/">addons.mozilla.org → FoxyProxy Standard</a></p>

  <h2>2. Configurar el proxy Tor</h2>
  <pre><code># Iniciar Tor:
service tor start</code></pre>
  <p>En FoxyProxy → <em>Add</em>:</p>
  <ul>
    <li><strong>Título:</strong> Tor</li>
    <li><strong>Tipo:</strong> SOCKS5</li>
    <li><strong>IP:</strong> 127.0.0.1</li>
    <li><strong>Puerto:</strong> 9050</li>
  </ul>
  <p>Activar el proxy pulsando el botón hasta que indique <em>ON</em>.</p>

  <h2>3. Verificar el cambio de IP</h2>
  <p>Acceder de nuevo a <a href="https://www.cual-es-mi-ip.net/">cual-es-mi-ip.net</a> — la IP debe ser diferente a la original.</p>

  <h2>4. Añadir proxies externos</h2>
  <p>Obtener proxies en: <a href="https://hidemy.name/es/proxy-list/">hidemy.name/es/proxy-list/</a></p>
  <p>En FoxyProxy → <em>Add</em> para cada proxy:</p>
  <ul>
    <li><strong>Tipo:</strong> HTTP o SOCKS según el proxy</li>
    <li><strong>IP y Puerto:</strong> los del proxy seleccionado</li>
  </ul>
  <p>Activar uno a la vez y verificar la IP asignada en cual-es-mi-ip.net.</p>
</div>
<?php else: ?>
<div class="prose">
  <p><strong>FoxyProxy</strong> is a Firefox extension for managing and switching between multiple proxy configurations directly from the browser toolbar.</p>

  <h2>1. Installation</h2>
  <p>Note your current public IP at <a href="https://www.cual-es-mi-ip.net/">cual-es-mi-ip.net</a> before installing.</p>
  <p>Install from: <a href="https://addons.mozilla.org/es/firefox/addon/foxyproxy-standard/">addons.mozilla.org — FoxyProxy Standard</a></p>

  <h2>2. Configure Tor Proxy</h2>
  <pre><code>service tor start</code></pre>
  <p>In FoxyProxy → Add:</p>
  <ul>
    <li><strong>Title:</strong> Tor</li>
    <li><strong>Type:</strong> SOCKS5</li>
    <li><strong>IP:</strong> 127.0.0.1</li>
    <li><strong>Port:</strong> 9050</li>
  </ul>
  <p>Enable by toggling to <em>ON</em>. Verify IP change at cual-es-mi-ip.net.</p>

  <h2>3. Adding External Proxies</h2>
  <p>Get proxy lists at: <a href="https://hidemy.name/es/proxy-list/">hidemy.name/es/proxy-list/</a></p>
  <p>In FoxyProxy → Add: set Type (HTTP or SOCKS), IP and Port from the selected proxy. Enable one at a time and verify the assigned IP.</p>
</div>
<?php endif;
$contentBody=ob_get_clean();
require __DIR__.'/../templates/content-page.php';
