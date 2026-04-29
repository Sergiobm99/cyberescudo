<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Guía de Hardening SSH — CyberEscudo' : 'SSH Hardening Guide — CyberEscudo';
$contentTitle = $lang==='es' ? 'Guía de Hardening SSH' : 'SSH Hardening Guide';
$contentDate  = '2022-03-05';
$contentTags  = ['SSH','Hardening','OpenSSH','Autenticación','Claves'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p>Guía completa para securizar el servicio <strong>SSH</strong> en servidores Linux, reduciendo la superficie de ataque frente a fuerza bruta, acceso no autorizado y escuchas.</p>

  <h2>1. Archivo de configuración</h2>
  <pre><code># Ruta principal:
/etc/ssh/sshd_config

# Tras cada cambio, reiniciar el servicio:
systemctl restart sshd
# o
service ssh restart</code></pre>

  <h2>2. Cambiar el puerto por defecto</h2>
  <pre><code># En /etc/ssh/sshd_config:
Port 2222   # Cambiar de 22 a otro puerto (1024-65535)

# Conectarse con el nuevo puerto:
ssh -p 2222 usuario@servidor</code></pre>

  <h2>3. Deshabilitar login como root</h2>
  <pre><code>PermitRootLogin no
# Opciones: yes | no | without-password | forced-commands-only</code></pre>

  <h2>4. Autenticación por clave pública (sin contraseña)</h2>
  <pre><code># Generar par de claves en el cliente:
ssh-keygen -t ed25519 -C "mi_clave_segura"
# o RSA 4096:
ssh-keygen -t rsa -b 4096

# Copiar clave pública al servidor:
ssh-copy-id -i ~/.ssh/id_ed25519.pub usuario@servidor

# En sshd_config del servidor:
PubkeyAuthentication yes
AuthorizedKeysFile .ssh/authorized_keys

# Deshabilitar autenticación por contraseña (solo claves):
PasswordAuthentication no
ChallengeResponseAuthentication no</code></pre>

  <h2>5. Limitar usuarios y grupos con acceso SSH</h2>
  <pre><code># Solo usuarios específicos:
AllowUsers usuario1 usuario2

# Solo grupos específicos:
AllowGroups sshusers admins

# Denegar usuarios específicos:
DenyUsers root oracle mysql</code></pre>

  <h2>6. Configuraciones de seguridad adicionales</h2>
  <pre><code"># Tiempo máximo para autenticarse:
LoginGraceTime 30

# Máximo de intentos de autenticación:
MaxAuthTries 3

# Máximo de sesiones simultáneas por conexión:
MaxSessions 3

# Desactivar reenvío X11 (si no es necesario):
X11Forwarding no

# Desactivar reenvío de agente SSH:
AllowAgentForwarding no

# Desactivar túneles TCP:
AllowTcpForwarding no

# Deshabilitar autenticación GSSAPI:
GSSAPIAuthentication no

# Banner de advertencia:
Banner /etc/ssh/banner.txt</code></pre>

  <h2>7. Algoritmos criptográficos seguros</h2>
  <pre><code># Solo algoritmos modernos (en sshd_config):
KexAlgorithms curve25519-sha256,diffie-hellman-group14-sha256
Ciphers aes256-gcm@openssh.com,chacha20-poly1305@openssh.com
MACs hmac-sha2-512,hmac-sha2-256

# Verificar configuración sin reiniciar:
sshd -T | grep -E "kexalgorithms|ciphers|macs"</code></pre>

  <h2>8. Fail2Ban — Protección contra fuerza bruta</h2>
  <pre><code>apt install fail2ban

# Configuración en /etc/fail2ban/jail.local:
[sshd]
enabled  = true
port     = 2222          # Puerto SSH configurado
filter   = sshd
logpath  = /var/log/auth.log
maxretry = 3             # Intentos antes de bloquear
bantime  = 3600          # Segundos de bloqueo (1 hora)
findtime = 600           # Ventana de tiempo (10 min)

# Reiniciar Fail2Ban:
systemctl restart fail2ban

# Ver IPs bloqueadas:
fail2ban-client status sshd</code></pre>

  <h2>Checklist de hardening SSH</h2>
  <table>
    <thead><tr><th>Medida</th><th>Parámetro</th><th>Valor recomendado</th></tr></thead>
    <tbody>
      <tr><td>Puerto no estándar</td><td><code>Port</code></td><td>≠ 22</td></tr>
      <tr><td>Sin login root</td><td><code>PermitRootLogin</code></td><td>no</td></tr>
      <tr><td>Solo claves públicas</td><td><code>PasswordAuthentication</code></td><td>no</td></tr>
      <tr><td>Tiempo de gracia</td><td><code>LoginGraceTime</code></td><td>30</td></tr>
      <tr><td>Máx. intentos</td><td><code>MaxAuthTries</code></td><td>3</td></tr>
      <tr><td>Sin X11</td><td><code>X11Forwarding</code></td><td>no</td></tr>
      <tr><td>Fail2Ban activo</td><td>maxretry</td><td>3 intentos / 1h ban</td></tr>
    </tbody>
  </table>
</div>
<?php else: ?>
<div class="prose">
  <p>Complete guide to hardening the <strong>SSH</strong> service on Linux servers, reducing the attack surface against brute force, unauthorised access and eavesdropping.</p>

  <h2>1. Configuration File</h2>
  <pre><code>/etc/ssh/sshd_config
systemctl restart sshd   # Apply changes</code></pre>

  <h2>2. Change the Default Port</h2>
  <pre><code>Port 2222
ssh -p 2222 user@server</code></pre>

  <h2>3. Disable Root Login</h2>
  <pre><code>PermitRootLogin no</code></pre>

  <h2>4. Public Key Authentication</h2>
  <pre><code>ssh-keygen -t ed25519 -C "my_key"
ssh-copy-id -i ~/.ssh/id_ed25519.pub user@server

# sshd_config:
PubkeyAuthentication yes
PasswordAuthentication no
ChallengeResponseAuthentication no</code></pre>

  <h2>5. Restrict SSH Access</h2>
  <pre><code>AllowUsers user1 user2
AllowGroups sshusers
DenyUsers root oracle</code></pre>

  <h2>6. Additional Security Settings</h2>
  <pre><code>LoginGraceTime 30
MaxAuthTries 3
MaxSessions 3
X11Forwarding no
AllowAgentForwarding no
AllowTcpForwarding no
GSSAPIAuthentication no
Banner /etc/ssh/banner.txt</code></pre>

  <h2>7. Secure Cryptographic Algorithms</h2>
  <pre><code>KexAlgorithms curve25519-sha256,diffie-hellman-group14-sha256
Ciphers aes256-gcm@openssh.com,chacha20-poly1305@openssh.com
MACs hmac-sha2-512,hmac-sha2-256</code></pre>

  <h2>8. Fail2Ban — Brute Force Protection</h2>
  <pre><code>apt install fail2ban

# /etc/fail2ban/jail.local:
[sshd]
enabled  = true
port     = 2222
maxretry = 3
bantime  = 3600
findtime = 600

systemctl restart fail2ban
fail2ban-client status sshd</code></pre>

  <h2>SSH Hardening Checklist</h2>
  <table>
    <thead><tr><th>Measure</th><th>Parameter</th><th>Value</th></tr></thead>
    <tbody>
      <tr><td>Non-standard port</td><td><code>Port</code></td><td>≠ 22</td></tr>
      <tr><td>No root login</td><td><code>PermitRootLogin</code></td><td>no</td></tr>
      <tr><td>Key-only auth</td><td><code>PasswordAuthentication</code></td><td>no</td></tr>
      <tr><td>Max attempts</td><td><code>MaxAuthTries</code></td><td>3</td></tr>
      <tr><td>No X11</td><td><code>X11Forwarding</code></td><td>no</td></tr>
      <tr><td>Fail2Ban</td><td>maxretry</td><td>3 / 1h ban</td></tr>
    </tbody>
  </table>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';
