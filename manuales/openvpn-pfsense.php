<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'OpenVPN en pfSense — CyberEscudo' : 'OpenVPN on pfSense — CyberEscudo';
$contentTitle = $lang==='es' ? 'OpenVPN en pfSense' : 'OpenVPN on pfSense';
$contentDate  = '2022-01-20';
$contentTags  = ['OpenVPN','pfSense','VPN','Certificados','Mikrotik'];
ob_start();
if ($lang==='es'): ?>
<div class="prose">
  <p>Configuración de una <strong>VPN con OpenVPN</strong> sobre pfSense con router Mikrotik, incluyendo creación de certificados, usuarios y conexión desde Windows y Kali Linux.</p>

  <h2>Topología de red</h2>
  <ul>
    <li><strong>Kali Linux</strong> → ether3 (172.26.0.x)</li>
    <li><strong>Router Mikrotik</strong> → ether1 (WAN/DHCP), ether2 (10.10.10.2/30), ether3 (172.26.0.1/24)</li>
    <li><strong>pfSense</strong> → WAN 10.10.10.1/30, LAN 192.168.1.1/24</li>
    <li><strong>Windows</strong> → 192.168.1.150</li>
  </ul>

  <h2>1. Configuración del router Mikrotik</h2>
  <pre><code># IP interfaz 1 (WAN) con DHCP:
ip dhcp-client add interface=ether1 disable=no

# IP interfaz 2:
ip address add address=10.10.10.2/30 interface=ether2

# IP interfaz 3:
ip address add address=172.26.0.1/24 interface=ether3

# NAT para salir a internet por ether1:
ip firewall nat add chain=srcnat out-interface=ether1 action=masquerade

# Verificar rutas:
ip route print</code></pre>

  <h2>2. Instalación de OpenVPN en pfSense</h2>
  <pre><code># En el navegador: https://192.168.1.1
# System → Package Manager → Available Packages
# Instalar: openvpn-client-export</code></pre>

  <h2>3. Crear Autoridad de Certificación (CA)</h2>
  <pre><code># System → Certificate Manager → CAs → Add
# Método: Create an internal Certificate Authority
# Rellenar: nombre, datos de organización</code></pre>

  <h2>4. Crear certificado de servidor</h2>
  <pre><code># System → Certificate Manager → Certificates → Add
# Método: Create an internal Certificate
# Tipo: Server Certificate
# CA: la creada en el paso anterior</code></pre>

  <h2>5. Configurar servidor OpenVPN</h2>
  <pre><code># VPN → OpenVPN → Wizards
# Tipo: Local User Access
# CA y certificado: los creados anteriormente
# Tunnel Network: IP para comunicación VPN (ej: 10.8.0.0/24)
# ✓ Redirect Gateway
# Conexiones concurrentes: 5
# ✓ Firewall Rule
# ✓ OpenVPN Rule</code></pre>

  <h2>6. Crear usuarios</h2>
  <pre><code># System → User Manager → Add
# Nombre de usuario y contraseña
# ✓ Crear certificado para el usuario</code></pre>

  <h2>7. Conectar desde Windows</h2>
  <pre><code># Descargar: instalador OpenVPN + config del usuario (desde pfSense)
# Instalar OpenVPN
# Importar certificado: clic derecho en icono → Import
# Introducir credenciales → conectar
# Verificar: ipconfig (nuevo adaptador VPN)</code></pre>

  <h2>8. Conectar desde Kali Linux</h2>
  <pre><code># Instalar cliente OpenVPN:
apt install network-manager-openvpn openvpn network-openvpn-gnome

# Importar certificado descargado de pfSense:
nmcli connection import type openvpn file /ruta/certificado.ovpn

# Verificar conexión:
ifconfig</code></pre>
</div>
<?php else: ?>
<div class="prose">
  <p>Setting up a <strong>VPN with OpenVPN</strong> on pfSense with a Mikrotik router, including certificate creation, user management and client connection from Windows and Kali Linux.</p>

  <h2>Network Topology</h2>
  <ul>
    <li>Kali Linux → ether3 (172.26.0.x)</li>
    <li>Mikrotik Router → ether1 (WAN), ether2 (10.10.10.2/30), ether3 (172.26.0.1/24)</li>
    <li>pfSense → WAN 10.10.10.1/30, LAN 192.168.1.1/24</li>
    <li>Windows → 192.168.1.150</li>
  </ul>

  <h2>1. Mikrotik Router</h2>
  <pre><code>ip dhcp-client add interface=ether1 disable=no
ip address add address=10.10.10.2/30 interface=ether2
ip address add address=172.26.0.1/24 interface=ether3
ip firewall nat add chain=srcnat out-interface=ether1 action=masquerade</code></pre>

  <h2>2. Install OpenVPN Package on pfSense</h2>
  <pre><code># Browser: https://192.168.1.1
# System → Package Manager → Install: openvpn-client-export</code></pre>

  <h2>3. Create CA and Server Certificate</h2>
  <pre><code># System → Certificate Manager → CAs → Add (internal CA)
# System → Certificate Manager → Certificates → Add (Server Certificate)</code></pre>

  <h2>4. Configure OpenVPN Server</h2>
  <pre><code># VPN → OpenVPN → Wizards
# Type: Local User Access | Tunnel: 10.8.0.0/24
# Enable Redirect Gateway, Firewall Rule and OpenVPN Rule</code></pre>

  <h2>5. Create Users and Connect</h2>
  <pre><code># System → User Manager → Add (username + password + certificate)
# Windows: download OpenVPN installer + user .ovpn → import → connect
# Kali: nmcli connection import type openvpn file cert.ovpn</code></pre>
</div>
<?php endif;
$contentBody=ob_get_clean();
require __DIR__.'/../templates/content-page.php';
