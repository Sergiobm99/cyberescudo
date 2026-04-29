<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Firewall con iptables y firewalld — CyberEscudo' : 'Firewall with iptables & firewalld — CyberEscudo';
$contentTitle = $lang==='es' ? 'Firewall con iptables y firewalld' : 'Firewall with iptables & firewalld';
$contentDate  = '2022-02-25';
$contentTags  = ['iptables','firewalld','Firewall','UFW','Linux'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p>Configuración práctica de firewall en Linux con <strong>iptables</strong>, <strong>firewalld</strong> y <strong>UFW</strong>, cubriendo políticas por defecto, reglas de filtrado y NAT.</p>

  <h2>1. Conceptos básicos de iptables</h2>
  <pre><code># Ver reglas actuales:
iptables -L -v -n
iptables -L -v -n --line-numbers   # Con números de línea

# Tablas principales:
# filter: reglas de filtrado (INPUT, OUTPUT, FORWARD)
# nat:    traducción de direcciones (PREROUTING, POSTROUTING)
# mangle: modificación de paquetes</code></pre>

  <h2>2. Política por defecto — Deny All</h2>
  <pre><code># Bloquear todo el tráfico entrante y de reenvío (política restrictiva):
iptables -P INPUT DROP
iptables -P FORWARD DROP
iptables -P OUTPUT ACCEPT   # Permitir todo el tráfico saliente

# Mantener conexiones ya establecidas:
iptables -A INPUT -m state --state ESTABLISHED,RELATED -j ACCEPT

# Permitir loopback:
iptables -A INPUT -i lo -j ACCEPT</code></pre>

  <h2>3. Reglas de filtrado comunes</h2>
  <pre><code># Permitir SSH (evitar bloquearse):
iptables -A INPUT -p tcp --dport 22 -j ACCEPT

# Permitir HTTP y HTTPS:
iptables -A INPUT -p tcp --dport 80 -j ACCEPT
iptables -A INPUT -p tcp --dport 443 -j ACCEPT

# Permitir DNS (UDP):
iptables -A INPUT -p udp --dport 53 -j ACCEPT

# Permitir ping (ICMP):
iptables -A INPUT -p icmp --icmp-type echo-request -j ACCEPT

# Bloquear IP concreta:
iptables -A INPUT -s 192.168.1.100 -j DROP

# Limitar intentos de conexión (anti-brute force):
iptables -A INPUT -p tcp --dport 22 -m state --state NEW \
  -m recent --set --name SSH
iptables -A INPUT -p tcp --dport 22 -m state --state NEW \
  -m recent --update --seconds 60 --hitcount 4 --name SSH -j DROP</code></pre>

  <h2>4. NAT con iptables</h2>
  <pre><code># Masquerade (router/gateway):
iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE

# Activar IP forwarding:
echo 1 > /proc/sys/net/ipv4/ip_forward
# Permanente en /etc/sysctl.conf:
net.ipv4.ip_forward = 1

# Port forwarding (redirigir puerto externo a interno):
iptables -t nat -A PREROUTING -p tcp --dport 8080 \
  -j DNAT --to-destination 192.168.1.10:80</code></pre>

  <h2>5. Guardar y restaurar reglas</h2>
  <pre><code># Guardar reglas actuales:
iptables-save > /etc/iptables/rules.v4

# Restaurar reglas:
iptables-restore < /etc/iptables/rules.v4

# En Debian/Ubuntu, persistencia automática:
apt install iptables-persistent
netfilter-persistent save</code></pre>

  <h2>6. UFW — Interfaz simplificada</h2>
  <pre><code># Activar UFW:
ufw enable

# Política por defecto:
ufw default deny incoming
ufw default allow outgoing

# Permitir servicios:
ufw allow ssh
ufw allow http
ufw allow https
ufw allow 8080/tcp

# Denegar puertos:
ufw deny 23/tcp   # Telnet

# Ver estado:
ufw status verbose

# Desactivar:
ufw disable</code></pre>

  <h2>7. firewalld (CentOS/RHEL/Fedora)</h2>
  <pre><code># Estado y zona activa:
firewall-cmd --state
firewall-cmd --get-active-zones

# Listar reglas de la zona por defecto:
firewall-cmd --list-all

# Añadir servicios permanentes:
firewall-cmd --permanent --add-service=http
firewall-cmd --permanent --add-service=https
firewall-cmd --permanent --add-port=8080/tcp

# Aplicar cambios:
firewall-cmd --reload</code></pre>
</div>
<?php else: ?>
<div class="prose">
  <p>Practical firewall configuration on Linux with <strong>iptables</strong>, <strong>UFW</strong> and <strong>firewalld</strong>, covering default policies, filtering rules and NAT.</p>

  <h2>1. Default Policy — Deny All</h2>
  <pre><code>iptables -P INPUT DROP
iptables -P FORWARD DROP
iptables -P OUTPUT ACCEPT
iptables -A INPUT -m state --state ESTABLISHED,RELATED -j ACCEPT
iptables -A INPUT -i lo -j ACCEPT</code></pre>

  <h2>2. Common Rules</h2>
  <pre><code>iptables -A INPUT -p tcp --dport 22 -j ACCEPT   # SSH
iptables -A INPUT -p tcp --dport 80 -j ACCEPT   # HTTP
iptables -A INPUT -p tcp --dport 443 -j ACCEPT  # HTTPS
iptables -A INPUT -p icmp --icmp-type echo-request -j ACCEPT  # Ping
iptables -A INPUT -s 192.168.1.100 -j DROP      # Block specific IP</code></pre>

  <h2>3. NAT</h2>
  <pre><code>iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE
echo 1 > /proc/sys/net/ipv4/ip_forward</code></pre>

  <h2>4. Save Rules</h2>
  <pre><code>iptables-save > /etc/iptables/rules.v4
apt install iptables-persistent && netfilter-persistent save</code></pre>

  <h2>5. UFW (Simplified)</h2>
  <pre><code>ufw enable
ufw default deny incoming && ufw default allow outgoing
ufw allow ssh && ufw allow http && ufw allow https
ufw status verbose</code></pre>

  <h2>6. firewalld (CentOS/RHEL)</h2>
  <pre><code>firewall-cmd --permanent --add-service=http
firewall-cmd --permanent --add-service=https
firewall-cmd --permanent --add-port=8080/tcp
firewall-cmd --reload</code></pre>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';
