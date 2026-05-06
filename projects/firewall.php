<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Configuración de Firewalls: iptables y firewalld — CyberEscudo' : 'Firewall Configuration: iptables & firewalld — CyberEscudo';
$contentTitle = $lang==='es' ? 'Configuración de Firewalls (iptables / firewalld)' : 'Firewall Configuration (iptables / firewalld)';
$contentDate  = '2024-11-10';
$contentDiff  = 'basic';
$contentTags  = ['Firewall','iptables','firewalld','Blue Team','Linux','Redes'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p>El firewall es la primera línea de defensa de cualquier infraestructura de red. En sistemas Linux, el filtrado de paquetes a nivel del kernel se realiza mediante <strong>Netfilter</strong>. Para interactuar con Netfilter, históricamente hemos utilizado <strong>iptables</strong>, y en distribuciones modernas (como RHEL/CentOS/Fedora) se utiliza <strong>firewalld</strong> como un frontend dinámico.</p>

  <h2>1. Fundamentos de iptables</h2>
  <p><code>iptables</code> funciona evaluando el tráfico de red contra un conjunto de reglas matemáticas. Si un paquete coincide con una regla, se aplica una acción (Target) como ACEPTAR (ACCEPT) o DESCARTAR (DROP).</p>
  
  <h3>Cadenas (Chains)</h3>
  <ul>
      <li><strong>INPUT:</strong> Tráfico entrante destinado a la propia máquina local (ej. alguien conectándose a tu servidor web).</li>
      <li><strong>OUTPUT:</strong> Tráfico saliente originado por tu máquina (ej. tu servidor descargando una actualización).</li>
      <li><strong>FORWARD:</strong> Tráfico enrutado a través de tu máquina (cuando el servidor actúa como router entre dos redes).</li>
  </ul>

  <h3>Políticas por defecto (The Default Drop)</h3>
  <p>La regla de oro en ciberseguridad es el <em>"Default Deny"</em>: bloquear todo por defecto y permitir solo lo estrictamente necesario.</p>
  <pre><code># Ver reglas actuales:
iptables -L -n -v

# 1. PERMITIR tráfico en la interfaz loopback (localhost)
iptables -A INPUT -i lo -j ACCEPT
iptables -A OUTPUT -o lo -j ACCEPT

# 2. PERMITIR conexiones ya establecidas (Stateful inspection)
iptables -A INPUT -m conntrack --ctstate ESTABLISHED,RELATED -j ACCEPT

# 3. CAMBIAR LA POLÍTICA POR DEFECTO A DROP (¡Cuidado, no te quedes fuera por SSH!)
iptables -P INPUT DROP
iptables -P FORWARD DROP
iptables -P OUTPUT ACCEPT # Normalmente se permite salir, aunque en alta seguridad también se restringe.</code></pre>

  <h2>2. Construcción de Reglas en iptables</h2>
  <p>La anatomía de un comando iptables es: <code>iptables -[Acción] [Cadena] -p [Protocolo] -s [IP Origen] --dport [Puerto Destino] -j [Target]</code></p>
  
  <pre><code># Permitir tráfico HTTP (puerto 80) y HTTPS (puerto 443) desde cualquier lugar:
iptables -A INPUT -p tcp --dport 80 -j ACCEPT
iptables -A INPUT -p tcp --dport 443 -j ACCEPT

# Permitir SSH (puerto 22) SOLO desde una IP específica (ej. la IP de la VPN corporativa):
iptables -A INPUT -p tcp -s 203.0.113.50 --dport 22 -j ACCEPT

# Bloquear (DROP) una IP maliciosa específica:
iptables -A INPUT -s 198.51.100.22 -j DROP

# Bloquear un rango de IPs completo (Subred):
iptables -A INPUT -s 10.0.0.0/8 -j DROP

# Insertar una regla al principio (-I) en lugar de al final (-A):
# Las reglas se leen de arriba a abajo. La primera que coincida, se aplica.
iptables -I INPUT 1 -s 185.12.99.0/24 -j DROP</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 13 (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Simulador de Reglas de Firewall
      </h3>
      <p style="margin-bottom: 1.5rem;">Estás bajo ataque. Un botnet está intentando reventar tu puerto SSH por fuerza bruta. Como administrador de sistemas, debes escribir el comando exacto de <code>iptables</code> para bloquear silenciosamente la IP atacante.</p>
      <a href="/ctf/ctf-13.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 13
      </a>
  </div>

  <h2>3. Prevención de Ataques de Red (iptables)</h2>
  <p>Podemos usar módulos extendidos (<code>-m</code>) para mitigar ataques comunes, como el escaneo de puertos o inundaciones SYN.</p>
  <pre><code># Mitigar SYN Floods (Limitando las conexiones a 20/segundo):
iptables -A INPUT -p tcp --syn -m limit --limit 20/s --limit-burst 50 -j ACCEPT
iptables -A INPUT -p tcp --syn -j DROP

# Bloquear ping (ICMP) para no ser descubiertos por escaneos básicos:
iptables -A INPUT -p icmp --icmp-type echo-request -j DROP

# Registro (Logging) de paquetes denegados antes de descartarlos:
iptables -A INPUT -j LOG --log-prefix "Paquete_Bloqueado: " --log-level 4</code></pre>

  <h2>4. NAT y Port Forwarding con iptables</h2>
  <p>La tabla NAT (Network Address Translation) permite redirigir tráfico de un puerto a otro, o de una IP a otra. Es fundamental en contenedores Docker y Routers Linux.</p>
  <pre><code># Habilitar el reenvío IP en el kernel:
echo 1 > /proc/sys/net/ipv4/ip_forward

# Redirigir el tráfico que llega al puerto 80 hacia el puerto 8080 interno:
iptables -t nat -A PREROUTING -p tcp --dport 80 -j REDIRECT --to-port 8080

# Enmascarar tráfico saliente (SNAT/Masquerade) para dar internet a una subred:
iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE</code></pre>

  <h2>5. Introducción a firewalld</h2>
  <p><code>firewalld</code> es el gestor moderno de firewalls en Red Hat/CentOS/Fedora. A diferencia de iptables, utiliza el concepto de <strong>Zonas</strong> (ej. public, internal, trusted) y permite aplicar cambios en caliente sin reiniciar el servicio ni cortar conexiones activas.</p>
  
  <h3>Gestión de Zonas y Servicios</h3>
  <pre><code># Ver el estado y la zona activa:
firewall-cmd --state
firewall-cmd --get-active-zones

# Ver qué está permitido en la zona pública:
firewall-cmd --zone=public --list-all

# Añadir el servicio HTTP (puerto 80) de forma permanente:
firewall-cmd --zone=public --add-service=http --permanent
firewall-cmd --reload  # ¡Obligatorio para aplicar cambios permanentes!

# Añadir un puerto no estándar (ej. 8443 TCP):
firewall-cmd --zone=public --add-port=8443/tcp --permanent</code></pre>

  <h3>Rich Rules en firewalld</h3>
  <p>Cuando necesitas reglas complejas (como permitir un puerto solo desde una IP específica), usas <em>Rich Rules</em>.</p>
  <pre><code># Permitir SSH (puerto 22) solo desde la IP 192.168.1.100:
firewall-cmd --permanent --zone=public \
  --add-rich-rule='rule family="ipv4" source address="192.168.1.100" port protocol="tcp" port="22" accept'

# Bloquear (Reject) una subred entera maliciosa:
firewall-cmd --permanent --zone=public \
  --add-rich-rule='rule family="ipv4" source address="10.50.0.0/16" reject'

firewall-cmd --reload</code></pre>

  <h2>6. Persistencia</h2>
  <p>Las reglas de <code>iptables</code> se borran al reiniciar el servidor. Para hacerlas permanentes, necesitamos guardarlas.</p>
  <pre><code># En Debian/Ubuntu (Instalar iptables-persistent primero):
sudo netfilter-persistent save
# o
sudo iptables-save > /etc/iptables/rules.v4

# En CentOS/RHEL con firewalld, añadir el flag "--permanent" guarda el estado automáticamente tras hacer reload.</code></pre>

</div>

<?php else: ?>
<div class="prose">
  <p>The firewall is the first line of defense for any network infrastructure. On Linux systems, packet filtering at the kernel level is handled by <strong>Netfilter</strong>. Historically, we interacted with Netfilter using <strong>iptables</strong>, but modern distributions (like RHEL/CentOS/Fedora) use <strong>firewalld</strong> as a dynamic frontend.</p>

  <h2>1. iptables Fundamentals</h2>
  <p><code>iptables</code> works by matching network traffic against a set of rules. If a packet matches, a Target action is applied (e.g., ACCEPT or DROP).</p>
  
  <h3>Chains</h3>
  <ul>
      <li><strong>INPUT:</strong> Incoming traffic destined for the local machine itself.</li>
      <li><strong>OUTPUT:</strong> Outgoing traffic originating from your machine.</li>
      <li><strong>FORWARD:</strong> Traffic routed through your machine (acting as a router).</li>
  </ul>

  <h3>Default Policies (The Default Drop)</h3>
  <p>The golden rule in cybersecurity is "Default Deny": block everything by default and explicitly allow only what is necessary.</p>
  <pre><code># View current rules:
iptables -L -n -v

# 1. ALLOW loopback traffic (localhost)
iptables -A INPUT -i lo -j ACCEPT
iptables -A OUTPUT -o lo -j ACCEPT

# 2. ALLOW established connections (Stateful inspection)
iptables -A INPUT -m conntrack --ctstate ESTABLISHED,RELATED -j ACCEPT

# 3. SET DEFAULT DROP POLICY (Careful, don't lock yourself out of SSH!)
iptables -P INPUT DROP
iptables -P FORWARD DROP
iptables -P OUTPUT ACCEPT</code></pre>

  <h2>2. Building iptables Rules</h2>
  <p>The syntax anatomy is: <code>iptables -[Action] [Chain] -p [Protocol] -s [Source IP] --dport [Dest Port] -j [Target]</code></p>
  
  <pre><code># Allow HTTP/HTTPS globally:
iptables -A INPUT -p tcp --dport 80 -j ACCEPT
iptables -A INPUT -p tcp --dport 443 -j ACCEPT

# Allow SSH ONLY from a specific IP:
iptables -A INPUT -p tcp -s 203.0.113.50 --dport 22 -j ACCEPT

# Block (DROP) a malicious IP:
iptables -A INPUT -s 198.51.100.22 -j DROP

# Insert a rule at the top (-I) instead of appending (-A):
iptables -I INPUT 1 -s 185.12.99.0/24 -j DROP</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 13 (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Firewall Rule Simulator
      </h3>
      <p style="margin-bottom: 1.5rem;">You are under attack. A botnet is trying to brute-force your SSH port. As a sysadmin, you must write the exact <code>iptables</code> command to silently drop the attacker's IP.</p>
      <a href="/ctf/ctf-13.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 13 CHALLENGE
      </a>
  </div>

  <h2>3. Network Attack Prevention</h2>
  <pre><code># Mitigate SYN Floods:
iptables -A INPUT -p tcp --syn -m limit --limit 20/s --limit-burst 50 -j ACCEPT
iptables -A INPUT -p tcp --syn -j DROP

# Block ICMP (Ping) to avoid basic recon:
iptables -A INPUT -p icmp --icmp-type echo-request -j DROP

# Log dropped packets:
iptables -A INPUT -j LOG --log-prefix "Dropped_Packet: " --log-level 4</code></pre>

  <h2>4. NAT and Port Forwarding</h2>
  <pre><code># Enable IP forwarding:
echo 1 > /proc/sys/net/ipv4/ip_forward

# Redirect port 80 to internal port 8080:
iptables -t nat -A PREROUTING -p tcp --dport 80 -j REDIRECT --to-port 8080</code></pre>

  <h2>5. firewalld Basics</h2>
  <p><code>firewalld</code> uses <strong>Zones</strong> and allows applying changes dynamically without dropping active connections.</p>
  
  <pre><code># Check active zones:
firewall-cmd --get-active-zones

# Allow HTTP permanently:
firewall-cmd --zone=public --add-service=http --permanent
firewall-cmd --reload

# Add a custom port:
firewall-cmd --zone=public --add-port=8443/tcp --permanent</code></pre>

  <h3>firewalld Rich Rules</h3>
  <pre><code># Allow SSH only from 192.168.1.100:
firewall-cmd --permanent --zone=public \
  --add-rich-rule='rule family="ipv4" source address="192.168.1.100" port protocol="tcp" port="22" accept'
firewall-cmd --reload</code></pre>

</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';