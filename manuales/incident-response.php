<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Plan de Respuesta a Incidentes — CyberEscudo' : 'Incident Response Plan — CyberEscudo';
$contentTitle = $lang==='es' ? 'Plan de Respuesta a Incidentes' : 'Incident Response Plan';
$contentDate  = '2022-05-20';
$contentTags  = ['Incidentes','DFIR','Forense','NIST','Respuesta'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p>Framework de <strong>respuesta a incidentes de ciberseguridad</strong> basado en el marco NIST SP 800-61, cubriendo las 4 fases: preparación, detección, contención/erradicación y recuperación.</p>

  <h2>Fases del plan de respuesta (NIST)</h2>

  <h2>Fase 1 — Preparación</h2>
  <ul>
    <li>Definir el equipo de respuesta (CSIRT/SOC) y sus roles.</li>
    <li>Clasificar activos críticos y datos sensibles.</li>
    <li>Establecer canales de comunicación de emergencia.</li>
    <li>Preparar herramientas forenses (Volatility, Autopsy, FTK).</li>
    <li>Implantar SIEM (Splunk, Elastic SIEM, Wazuh) para centralizar logs.</li>
    <li>Definir y probar los procedimientos con simulacros.</li>
  </ul>

  <h2>Fase 2 — Detección y Análisis</h2>
  <pre><code># Fuentes de alertas:
# - IDS/IPS (Snort, Suricata)
# - SIEM
# - Logs del sistema
# - Usuarios finales
# - Threat intelligence feeds

# Comandos de triage inicial en Linux:
# Procesos activos:
ps aux | grep -v "^\[" | sort -k4 -r | head -20

# Conexiones de red activas:
netstat -antp | grep ESTABLISHED
ss -tulnp

# Últimos logins:
last -20
lastb | head -20    # Intentos fallidos

# Archivos modificados recientemente:
find /etc /bin /sbin /usr -mtime -1 -type f 2>/dev/null

# Crontabs (persistencia):
crontab -l
cat /etc/crontab
ls /etc/cron.*</code></pre>

  <h2>Fase 3 — Contención</h2>
  <pre><code># CONTENCIÓN A CORTO PLAZO:
# Aislar el sistema de la red (sin apagarlo para preservar memoria):
ip link set eth0 down
# o desconectar cable físico

# Bloquear IP del atacante en firewall:
iptables -A INPUT -s [IP_ATACANTE] -j DROP
iptables -A OUTPUT -d [IP_ATACANTE] -j DROP

# Cambiar contraseñas comprometidas:
passwd [usuario_comprometido]

# Revocar certificados/tokens comprometidos

# CAPTURA DE EVIDENCIAS (antes de erradicar):
# Volcado de memoria RAM (preserva procesos, conexiones):
avml /media/usb/memory.lime
# o con LiME:
insmod lime.ko "path=/media/usb/ram.lime format=lime"

# Imagen del disco:
dd if=/dev/sda of=/media/usb/disk.img bs=4M status=progress

# Hash de integridad:
sha256sum /media/usb/disk.img > /media/usb/disk.img.sha256</code></pre>

  <h2>Fase 4 — Erradicación</h2>
  <pre><code># Identificar el vector de entrada:
# - Revisar logs de Apache/Nginx para la primera request maliciosa
# - Analizar imagen de disco con Autopsy o Sleuth Kit
# - Buscar webshells:
find /var/www -name "*.php" -newer /var/www/index.php 2>/dev/null
grep -r "eval(base64_decode" /var/www/ 2>/dev/null
grep -r "system\($_" /var/www/ 2>/dev/null

# Eliminar malware y backdoors identificados
# Parchear la vulnerabilidad explotada
# Eliminar cuentas no autorizadas:
cat /etc/passwd | grep "/bin/bash"</code></pre>

  <h2>Fase 5 — Recuperación</h2>
  <pre><code># Restaurar desde backup limpio verificado
# Restablecer contraseñas de todos los usuarios
# Monitorización intensiva post-incidente:
tail -f /var/log/auth.log
tail -f /var/log/apache2/access.log

# Verificar integridad del sistema:
apt install aide
aide --init && cp /var/lib/aide/aide.db.new /var/lib/aide/aide.db
aide --check</code></pre>

  <h2>Fase 6 — Lecciones Aprendidas</h2>
  <p>En las 2 semanas posteriores al incidente, documentar:</p>
  <ul>
    <li>¿Qué pasó? ¿Cuándo se detectó? ¿Cuál fue el impacto?</li>
    <li>¿Qué funcionó bien en la respuesta?</li>
    <li>¿Qué falló? ¿Qué mejorar?</li>
    <li>Acciones concretas para prevenir incidentes similares.</li>
  </ul>

  <h2>Clasificación de incidentes por gravedad</h2>
  <table>
    <thead><tr><th>Nivel</th><th>Descripción</th><th>Tiempo de respuesta</th></tr></thead>
    <tbody>
      <tr><td>P1 — Crítico</td><td>Brecha activa, datos sensibles comprometidos, sistemas caídos</td><td>Inmediato (&lt; 1h)</td></tr>
      <tr><td>P2 — Alto</td><td>Acceso no autorizado, ransomware, exfiltración detectada</td><td>&lt; 4h</td></tr>
      <tr><td>P3 — Medio</td><td>Malware no propagado, fuerza bruta activa, phishing exitoso</td><td>&lt; 24h</td></tr>
      <tr><td>P4 — Bajo</td><td>Escaneo de puertos, phishing bloqueado, anomalía menor</td><td>&lt; 72h</td></tr>
    </tbody>
  </table>
</div>
<?php else: ?>
<div class="prose">
  <p>Incident response framework based on NIST SP 800-61: preparation, detection, containment, eradication and recovery.</p>
  <table>
    <thead><tr><th>Phase</th><th>Key Actions</th></tr></thead>
    <tbody>
      <tr><td>1. Preparation</td><td>CSIRT team, SIEM, runbooks, backups</td></tr>
      <tr><td>2. Detection</td><td>Triage: ps, netstat, last, find modified files</td></tr>
      <tr><td>3. Containment</td><td>Isolate system, block attacker IP, capture evidence</td></tr>
      <tr><td>4. Eradication</td><td>Remove malware, patch CVE, delete unauthorized accounts</td></tr>
      <tr><td>5. Recovery</td><td>Restore clean backup, monitor intensively</td></tr>
      <tr><td>6. Lessons Learned</td><td>Post-mortem report within 2 weeks</td></tr>
    </tbody>
  </table>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';
