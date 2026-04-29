<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Tor en Kali Linux — CyberEscudo' : 'Tor on Kali Linux — CyberEscudo';
$contentTitle = $lang==='es' ? 'Tor en Kali Linux' : 'Tor on Kali Linux';
$contentDate  = '2022-01-10';
$contentTags  = ['Tor','Anonimato','Kali Linux','Proxy','SOCKS'];
ob_start();
if ($lang==='es'): ?>
<div class="prose">
  <p>Manual para instalar y configurar <strong>Tor</strong> en Kali Linux como proxy SOCKS para navegar de forma anónima.</p>
  <h2>1. Instalación</h2>
  <pre><code>apt-get update
apt-get install tor</code></pre>

  <h2>2. Verificar el servicio</h2>
  <pre><code># Comprobar estado
service tor status

# Activar si no está corriendo
service tor start

# Verificar que escucha en el puerto 9050
netstat -ano | head</code></pre>

  <h2>3. Comprobar IP pública antes de configurar</h2>
  <p>Acceder a <a href="https://www.cual-es-mi-ip.net/">cual-es-mi-ip.net</a> y apuntar la IP actual.</p>

  <h2>4. Configurar el navegador para usar Tor</h2>
  <p>En Firefox: <code>Preferencias → General → Network Settings</code></p>
  <ol>
    <li>Seleccionar <strong>Manual Proxy Configuration</strong>.</li>
    <li>En <strong>SOCKS Host</strong>: <code>127.0.0.1</code></li>
    <li>Puerto: <code>9050</code></li>
    <li>Seleccionar <strong>SOCKS v5</strong>.</li>
  </ol>
  <pre><code># El proxy Tor escucha en:
127.0.0.1:9050 (SOCKS5)</code></pre>

  <h2>5. Verificar el cambio de IP</h2>
  <p>Volver a <a href="https://www.cual-es-mi-ip.net/">cual-es-mi-ip.net</a> y confirmar que la IP ha cambiado.</p>
  <p><strong>Importante:</strong> no realizar este proceso conectado a la red Andared u otras redes institucionales que puedan bloquear Tor.</p>
</div>
<?php else: ?>
<div class="prose">
  <p>Step-by-step guide to install and configure <strong>Tor</strong> on Kali Linux as a SOCKS proxy for anonymous browsing.</p>

  <h2>1. Installation</h2>
  <pre><code>apt-get update
apt-get install tor</code></pre>

  <h2>2. Service Verification</h2>
  <pre><code>service tor status
service tor start          # Start if not running
netstat -ano | head        # Verify listening on port 9050</code></pre>

  <h2>3. Check Your Public IP</h2>
  <p>Visit <a href="https://www.cual-es-mi-ip.net/">cual-es-mi-ip.net</a> and note your current address.</p>

  <h2>4. Configure Firefox to Use Tor</h2>
  <p>Preferences → General → Network Settings → Manual Proxy Configuration:</p>
  <ul>
    <li><strong>SOCKS Host:</strong> 127.0.0.1</li>
    <li><strong>Port:</strong> 9050</li>
    <li><strong>Type:</strong> SOCKS v5</li>
  </ul>

  <h2>5. Verify the IP Change</h2>
  <p>Return to cual-es-mi-ip.net — the IP should now be a Tor exit node.</p>
  <p><strong>Note:</strong> do not use Tor on institutional networks that block Tor traffic.</p>
</div>
<?php endif;
$contentBody=ob_get_clean();
require __DIR__.'/../templates/content-page.php';
