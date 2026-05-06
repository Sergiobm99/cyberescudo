<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'John the Ripper y Hashcat: Cracking de Contraseñas — CyberEscudo' : 'John the Ripper & Hashcat: Password Cracking — CyberEscudo';
$contentTitle = $lang==='es' ? 'John the Ripper y Hashcat: Cracking' : 'John the Ripper & Hashcat: Cracking';
$contentDate  = '2022-04-01';
$contentDiff  = 'advanced';
$contentTags  = ['Hashcat','John The Ripper','Cracking','MD5','SHA','GPU'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p>El <strong>Cracking de contraseñas</strong> no es adivinar contraseñas al azar contra un servidor web (eso es fuerza bruta online o <em>Hydra</em>). El cracking es un proceso <strong>offline</strong>: primero robas el archivo de la base de datos que contiene los "Hashes" y luego usas toda la potencia de tu CPU o tarjeta gráfica (GPU) para intentar revertirlos en tu propia máquina.</p>

  <h2>1. Teoría: Hashing vs Encriptación y el uso de "Salts"</h2>
  <p>A diferencia de la encriptación (que es reversible si tienes la llave), una función de Hash (como MD5 o SHA-256) es un camino de un solo sentido. Convierte cualquier texto en una cadena de longitud fija. Para "crackear" un hash, tienes que hashear millones de palabras por segundo hasta que una coincida con el hash robado.</p>
  <p><strong>El problema del Salt:</strong> Para evitar que usemos tablas precalculadas (Rainbow Tables), los sistemas operativos modernos añaden un "Salt" (una cadena aleatoria) a la contraseña antes de hashearla.</p>
  <pre><code># Formato clásico de Linux (/etc/shadow):
$id$salt$hashed_string

# Los IDs indican el algoritmo usado:
$1$ = MD5
$2a$ o $2b$ = Bcrypt
$5$ = SHA-256
$6$ = SHA-512
$y$ = Yescrypt (Por defecto en Debian 11+ y Ubuntu 22.04+)</code></pre>

  <h2>2. Extracción de Hashes (Windows y Linux)</h2>
  <p>Antes de crackear, necesitas obtener los hashes del sistema comprometido.</p>
  <pre><code># LINUX: Necesitas ser root para leer /etc/shadow
# Debes combinar /etc/passwd y /etc/shadow para que John lo entienda:
unshadow /etc/passwd /etc/shadow > hashes_linux.txt

# WINDOWS: Los hashes NTLM se guardan en el archivo SAM. 
# Si eres Administrador o System, puedes volcarlo con Mimikatz, CrackMapExec o Impacket:
impacket-secretsdump ADMINISTRATOR:Password@192.168.1.50</code></pre>

  <h2>3. John the Ripper (Jumbo Edition)</h2>
  <p><em>John the Ripper</em> es una herramienta basada principalmente en CPU. Es excelente para cracking de bajo volumen o cuando no estás seguro del tipo de hash, ya que John intenta autodetectarlo maravillosamente.</p>
  <pre><code># 1. Ataque de Diccionario (Wordlist):
john --wordlist=/usr/share/wordlists/rockyou.txt hashes_linux.txt

# 2. Especificar formato manualmente (acelera el proceso):
john --format=NT --wordlist=rockyou.txt hashes_windows.txt    # NTLM
john --format=sha512crypt --wordlist=rockyou.txt hashes.txt

# 3. Ataque Single Crack Mode (El modo más inteligente):
# Usa el nombre de usuario y datos GECOS (info del usuario) mutados como contraseñas.
john --single hashes_linux.txt

# 4. Ataque con Reglas de Mutación (Wordlist Manging):
# Toma "password" y prueba "Password1!", "p@ssword", etc.
john --wordlist=rockyou.txt --rules hashes.txt

# 5. Ver contraseñas crackeadas:
john --show hashes.txt</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 19 (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Operador de Hashcat
      </h3>
      <p style="margin-bottom: 1.5rem;">Acabas de extraer la línea del usuario <code>root</code> del archivo <em>shadow</em> de un servidor Ubuntu. Necesitas construir el comando exacto de <strong>Hashcat</strong> para reventarlo utilizando tu tarjeta gráfica, un ataque de diccionario y reglas de mutación.</p>
      <a href="/ctf/ctf-19.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 19
      </a>
  </div>

  <h2>4. Hashcat — La Bestia del GPU Cracking</h2>
  <p><strong>Hashcat</strong> es la herramienta de cracking más rápida del mundo. Utiliza el poder de procesamiento paralelo masivo de las tarjetas gráficas (NVIDIA/AMD) para probar miles de millones de hashes por segundo.</p>
  
  <h3>Tipos de Ataque (Attack Modes <code>-a</code>)</h3>
  <ul>
      <li><code>-a 0</code>: Straight (Ataque de diccionario normal).</li>
      <li><code>-a 1</code>: Combination (Junta dos diccionarios: palabra1+palabra2).</li>
      <li><code>-a 3</code>: Brute-force (Fuerza bruta pura usando máscaras).</li>
  </ul>

  <pre><code># Ataque de Diccionario Básico (-a 0):
# Sintaxis: hashcat -a [ataque] -m [modo_hash] [fichero_hashes] [diccionario]
hashcat -a 0 -m 1000 hashes.txt rockyou.txt      # NTLM Windows

# Ataque de Fuerza Bruta (-a 3) con Máscaras:
# ?l = minúscula, ?u = MAYÚSCULA, ?d = dígito, ?s = símbolo, ?a = todos
# Crackear cualquier contraseña de 8 letras minúsculas:
hashcat -a 3 -m 0 hash.txt ?l?l?l?l?l?l?l?l

# Ataque Híbrido (Diccionario + Máscara a la derecha):
# Palabra de rockyou + 4 números (ej: password2024)
hashcat -a 6 -m 0 hash.txt rockyou.txt ?d?d?d?d</code></pre>

  <h2>5. Modos de Hash de Hashcat (Los más críticos)</h2>
  <p>Hashcat no autodetecta los hashes. Debes especificar el módulo exacto usando <code>-m</code>. Consulta <code>hashcat --help</code> para ver los más de 300 módulos.</p>
  <table>
    <thead><tr><th>Modo (-m)</th><th>Tipo de hash</th><th>Contexto típico</th></tr></thead>
    <tbody>
      <tr><td>0</td><td>MD5</td><td>Bases de datos antiguas web (WordPress viejo)</td></tr>
      <tr><td>1000</td><td>NTLM</td><td>Windows SAM o volcado de Active Directory (NTDS.dit)</td></tr>
      <tr><td>5600</td><td>NetNTLMv2</td><td>Robado interceptando tráfico de red (Responder)</td></tr>
      <tr><td>1800</td><td>sha512crypt ($6$)</td><td>Contraseñas locales en Linux (Ubuntu/CentOS)</td></tr>
      <tr><td>3200</td><td>bcrypt ($2a$)</td><td>Aplicaciones web modernas. MUY LENTO de crackear.</td></tr>
      <tr><td>22000</td><td>WPA/WPA2</td><td>Handshakes de redes WiFi interceptadas.</td></tr>
    </tbody>
  </table>

  <h2>6. Hardware Tuning y Optimización</h2>
  <p>Para aprovechar al máximo tu tarjeta gráfica (Rig de minería o Gaming PC) con Hashcat:</p>
  <pre><code># Optimizar la carga de trabajo (-w 3 o -w 4):
# -w 3 bloquea el escritorio pero maximiza el rendimiento del GPU.
hashcat -a 0 -m 1000 hashes.txt rockyou.txt -w 3

# Forzar optimización del kernel de la GPU (-O):
# Limita la longitud máxima de contraseña a 31 caracteres, pero aumenta la velocidad un 20%.
hashcat -a 0 -m 1000 hashes.txt rockyou.txt -O

# Continuar una sesión interrumpida:
hashcat --session mi_ataque --restore</code></pre>

  <h2>7. Preparar Diccionarios (Wordlists)</h2>
  <p>Rockyou.txt es legendario, pero si estás auditando una empresa española, rockyou (basado en fugas americanas) no será tan efectivo.</p>
  <pre><code># Descomprimir rockyou en Kali Linux:
sudo gzip -d /usr/share/wordlists/rockyou.txt.gz

# CeWL: Crear un diccionario scrapeando las palabras de la web de tu objetivo.
cewl -d 2 -m 6 -w dicc_empresa.txt https://www.objetivo.com

# Crunch: Crear todas las combinaciones posibles de longitud específica:
crunch 8 8 0123456789 -o 8_digits.txt</code></pre>
</div>

<?php else: ?>
<div class="prose">
  <p><strong>Password Cracking</strong> is not guessing passwords online against a web server. It is an <strong>offline</strong> process: you first steal the database file containing the "Hashes" and then use the full power of your CPU or GPU to attempt to reverse them on your own machine.</p>

  <h2>1. Theory: Hashing vs Encryption & Salts</h2>
  <p>Unlike encryption (which is reversible if you have the key), a Hash function (like MD5 or SHA-256) is a one-way path. To "crack" a hash, you must hash millions of words per second until one matches the stolen hash.</p>
  <p><strong>The Salt Problem:</strong> To prevent precomputed tables (Rainbow Tables), modern OS append a "Salt" (random string) to the password before hashing.</p>
  <pre><code># Classic Linux format (/etc/shadow):
$id$salt$hashed_string

# IDs indicate the algorithm:
$1$ = MD5
$5$ = SHA-256
$6$ = SHA-512</code></pre>

  <h2>2. Extracting Hashes</h2>
  <pre><code># LINUX: Combine /etc/passwd and /etc/shadow for John:
unshadow /etc/passwd /etc/shadow > hashes_linux.txt

# WINDOWS: Dump NTLM hashes using Impacket:
impacket-secretsdump ADMINISTRATOR:Password@192.168.1.50</code></pre>

  <h2>3. John the Ripper (Jumbo Edition)</h2>
  <p><em>John the Ripper</em> is primarily CPU-based. It is excellent for low-volume cracking or when you are unsure of the hash type.</p>
  <pre><code># Dictionary Attack:
john --wordlist=/usr/share/wordlists/rockyou.txt hashes_linux.txt

# Single Crack Mode (Smartest mode using GECOS info):
john --single hashes_linux.txt

# Wordlist Manging Rules (e.g., "password" -> "Password1!"):
john --wordlist=rockyou.txt --rules hashes.txt</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 19 (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Hashcat Rig Operator
      </h3>
      <p style="margin-bottom: 1.5rem;">You just extracted the <code>root</code> user line from an Ubuntu server's shadow file. You need to construct the exact <strong>Hashcat</strong> command to crack it using your GPU, a dictionary attack, and mutation rules.</p>
      <a href="/ctf/ctf-19.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 19 CHALLENGE
      </a>
  </div>

  <h2>4. Hashcat — The GPU Cracking Beast</h2>
  <p><strong>Hashcat</strong> is the world's fastest password cracker, utilizing massive parallel processing from GPUs (NVIDIA/AMD).</p>
  
  <h3>Attack Modes (<code>-a</code>)</h3>
  <pre><code># Straight Dictionary Attack (-a 0):
hashcat -a 0 -m 1000 hashes.txt rockyou.txt

# Pure Brute-force (-a 3) with Masks:
# Crack any 8 lowercase letter password:
hashcat -a 3 -m 0 hash.txt ?l?l?l?l?l?l?l?l

# Hybrid Attack (Wordlist + Mask):
hashcat -a 6 -m 0 hash.txt rockyou.txt ?d?d?d?d</code></pre>

  <h2>5. Critical Hashcat Modes</h2>
  <p>Hashcat doesn't auto-detect hashes. You must specify the exact module using <code>-m</code>.</p>
  <ul>
      <li><code>-m 0</code>: MD5</li>
      <li><code>-m 1000</code>: Windows NTLM</li>
      <li><code>-m 5600</code>: NetNTLMv2 (Responder captures)</li>
      <li><code>-m 1800</code>: sha512crypt (Linux $6$)</li>
      <li><code>-m 22000</code>: WPA/WPA2 WiFi Handshakes</li>
  </ul>

  <h2>6. Hardware Tuning</h2>
  <pre><code># Maximize GPU performance (locks desktop):
hashcat -a 0 -m 1000 hashes.txt rockyou.txt -w 3 -O

# Restore interrupted session:
hashcat --session my_attack --restore</code></pre>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';