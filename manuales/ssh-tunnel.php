<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'SSH Tunnel — CyberEscudo' : 'SSH Tunnel — CyberEscudo';
$contentTitle = $lang==='es' ? 'SSH Tunnel: Acceso Seguro a través de Múltiples Saltos' : 'SSH Tunnel: Secure Access Through Multiple Hops';
$contentDate  = '2022-02-01';
$contentTags  = ['SSH','Tunnel','Port Forwarding','FoxyProxy','Red'];
ob_start();
if ($lang==='es'): ?>
<div class="prose">
  <p>Creación de un <strong>túnel SSH encadenado</strong> a través de múltiples máquinas para acceder de forma segura a un servidor web interno desde una red externa.</p>

  <h2>Topología</h2>
  <pre><code>Kali (172.26.0.x) → VPS1 (192.168.50.10) → VPS2 (192.168.100.10) → Servidor Apache (192.168.10.x)</code></pre>

  <h2>1. Instalación de SSH</h2>
  <pre><code># En Kali:
apt-get install openssh-server
service ssh status

# En VPS1 y VPS2 (Ubuntu):
sudo apt-get install openssh-server openssh-client

# En el Servidor (instalar Apache):
sudo apt install apache2
# Modificar página por defecto:
nano /var/www/html/index.html</code></pre>

  <h2>2. Pruebas de conectividad</h2>
  <pre><code># Desde Kali, hacer ping a todas las máquinas para verificar conectividad</code></pre>

  <h2>3. Crear el túnel SSH encadenado</h2>
  <p>El túnel se construye de dentro a fuera. Abrir tres terminales en secuencia:</p>

  <h3>Terminal 1 — Kali → VPS1 (Local Port Forwarding)</h3>
  <pre><code>ssh -L 5678:127.0.0.1:4321 usuario@192.168.50.10
# Puerto local 5678 → reenvía al 4321 de VPS1</code></pre>

  <h3>Terminal 2 — VPS1 → VPS2 (Local Port Forwarding)</h3>
  <pre><code>ssh -L 4321:127.0.0.1:1234 usuario@192.168.100.10
# Puerto local 4321 → reenvía al 1234 de VPS2</code></pre>

  <h3>Terminal 3 — VPS2 → Servidor (Dynamic Port Forwarding)</h3>
  <pre><code>ssh -D 1234 usuario@192.168.10.10
# -D crea un proxy SOCKS dinámico en el puerto 1234</code></pre>

  <h2>4. Configurar FoxyProxy en Kali</h2>
  <p>En el navegador de Kali, configurar FoxyProxy con el proxy local:</p>
  <ul>
    <li><strong>Tipo:</strong> SOCKS5</li>
    <li><strong>IP:</strong> 127.0.0.1</li>
    <li><strong>Puerto:</strong> 5678</li>
  </ul>

  <h2>5. Acceder al servidor a través del túnel</h2>
  <pre><code># En el navegador de Kali (con FoxyProxy activado):
http://127.0.0.1
# o
http://localhost

# Resultado: muestra la página Apache del servidor interno</code></pre>
</div>
<?php else: ?>
<div class="prose">
  <p>Creating a <strong>chained SSH tunnel</strong> through multiple hops to securely access an internal web server from an external network.</p>

  <h2>Topology</h2>
  <pre><code>Kali (172.26.0.x) → VPS1 (192.168.50.10) → VPS2 (192.168.100.10) → Apache Server</code></pre>

  <h2>1. Install SSH and Apache</h2>
  <pre><code># Kali:
apt-get install openssh-server
# VPS1 and VPS2:
sudo apt-get install openssh-server openssh-client
# Server:
sudo apt install apache2</code></pre>

  <h2>2. Build the Chained Tunnel (3 terminals)</h2>
  <h3>Terminal 1 — Kali → VPS1</h3>
  <pre><code>ssh -L 5678:127.0.0.1:4321 user@192.168.50.10</code></pre>

  <h3>Terminal 2 — VPS1 → VPS2</h3>
  <pre><code>ssh -L 4321:127.0.0.1:1234 user@192.168.100.10</code></pre>

  <h3>Terminal 3 — VPS2 → Server</h3>
  <pre><code>ssh -D 1234 user@192.168.10.10   # Dynamic SOCKS proxy</code></pre>

  <h2>3. Configure FoxyProxy on Kali</h2>
  <ul>
    <li><strong>Type:</strong> SOCKS5 | <strong>IP:</strong> 127.0.0.1 | <strong>Port:</strong> 5678</li>
  </ul>

  <h2>4. Access the Internal Server</h2>
  <pre><code># In the Kali browser with FoxyProxy ON:
http://127.0.0.1
# Displays the Apache page of the internal server</code></pre>
</div>
<?php endif;
$contentBody=ob_get_clean();
require __DIR__.'/../templates/content-page.php';
