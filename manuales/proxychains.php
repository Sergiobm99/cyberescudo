<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Uso de Proxychains — CyberEscudo' : 'Using Proxychains — CyberEscudo';
$contentTitle = $lang==='es' ? 'Uso de Proxychains' : 'Using Proxychains';
$contentDate  = '2022-01-15';
$contentTags  = ['Proxychains','Tor','Proxy','Anonimato','Kali Linux'];
ob_start();
if ($lang==='es'): ?>
<div class="prose">
  <p><strong>Proxychains</strong> permite encadenar proxies y forzar a cualquier aplicación a pasar su tráfico a través de ellos, sin necesidad de configurar el proxy en cada programa.</p>
  <h2>1. Instalación</h2>
  <pre><code>apt install proxychains</code></pre>

  <h2>2. Copia de seguridad del archivo de configuración</h2>
  <pre><code>cp /etc/proxychains.conf /etc/proxychains.conf.backup</code></pre>

  <h2>3. Navegar con Tor a través de Proxychains</h2>
  <p>El proxy por defecto en el archivo de configuración es Tor (<code>socks4 127.0.0.1 9050</code>).</p>
  <pre><code># Iniciar Tor
service tor start
service tor status

# Lanzar el navegador a través de Proxychains (sin ser root):
proxychains firefox https://www.cual-es-mi-ip.net/</code></pre>

  <h2>4. Usar un proxy personalizado</h2>
  <p>Obtener una lista de proxies en: <a href="https://hidemy.name/es/proxy-list/">hidemy.name/es/proxy-list/</a></p>
  <p>Editar <code>/etc/proxychains.conf</code> — sección <code>[ProxyList]</code>:</p>
  <pre><code># Comentar el proxy de Tor:
# socks4  127.0.0.1 9050

# Añadir el proxy seleccionado:
socks5  IP_DEL_PROXY  PUERTO</code></pre>

  <pre><code># Detener Tor (no es necesario con proxy externo):
service tor stop

# Lanzar el navegador con el nuevo proxy:
proxychains firefox https://www.cual-es-mi-ip.net/</code></pre>
</div>
<?php else: ?>
<div class="prose">
  <p><strong>Proxychains</strong> forces any application to route its traffic through configured proxies, without modifying each program individually.</p>

  <h2>1. Installation</h2>
  <pre><code>apt install proxychains</code></pre>

  <h2>2. Back Up Configuration</h2>
  <pre><code>cp /etc/proxychains.conf /etc/proxychains.conf.backup</code></pre>

  <h2>3. Browse via Tor</h2>
  <p>The default proxy in the config file is Tor (<code>socks4 127.0.0.1 9050</code>).</p>
  <pre><code>service tor start
service tor status
proxychains firefox https://www.cual-es-mi-ip.net/</code></pre>

  <h2>4. Use a Custom External Proxy</h2>
  <p>Get proxies at: <a href="https://hidemy.name/es/proxy-list/">hidemy.name/es/proxy-list/</a></p>
  <p>Edit <code>/etc/proxychains.conf</code> — <code>[ProxyList]</code> section:</p>
  <pre><code># Comment out Tor:
# socks4  127.0.0.1 9050

# Add your proxy:
socks5  PROXY_IP  PORT</code></pre>
  <pre><code>service tor stop
proxychains firefox https://www.cual-es-mi-ip.net/</code></pre>
</div>
<?php endif;
$contentBody=ob_get_clean();
require __DIR__.'/../templates/content-page.php';
