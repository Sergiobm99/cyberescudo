<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'John the Ripper y Hashcat: Cracking de Contraseñas — CyberEscudo' : 'John the Ripper & Hashcat: Password Cracking — CyberEscudo';
$contentTitle = $lang==='es' ? 'John the Ripper y Hashcat: Cracking de Contraseñas' : 'John the Ripper & Hashcat: Password Cracking';
$contentDate  = '2022-04-01';
$contentTags  = ['John the Ripper','Hashcat','Cracking','MD5','SHA','Contraseñas'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p>Práctica de <strong>cracking de hashes de contraseñas</strong> con John the Ripper (CPU) y Hashcat (GPU), cubriendo ataques de diccionario, fuerza bruta y reglas.</p>

  <h2>1. Identificar el tipo de hash</h2>
  <pre><code># Con hashid:
hashid 5f4dcc3b5aa765d61d8327deb882cf99

# Con hash-identifier:
hash-identifier

# Tipos comunes:
# MD5:    32 caracteres hex
# SHA1:   40 caracteres hex
# SHA256: 64 caracteres hex
# bcrypt: $2b$... (empieza con $2)
# NTLM:  32 caracteres hex (Windows)</code></pre>

  <h2>2. John the Ripper</h2>
  <pre><code># Ataque con diccionario:
john --wordlist=/usr/share/wordlists/rockyou.txt hashes.txt

# Especificar formato del hash:
john --format=md5crypt --wordlist=rockyou.txt hashes.txt
john --format=sha256 --wordlist=rockyou.txt hashes.txt
john --format=bcrypt --wordlist=rockyou.txt hashes.txt
john --format=NT --wordlist=rockyou.txt hashes.txt  # NTLM Windows

# Ataque con reglas (mutaciones de palabras del diccionario):
john --wordlist=rockyou.txt --rules hashes.txt

# Ataque incremental (fuerza bruta pura):
john --incremental hashes.txt

# Ver contraseñas ya crackeadas:
john --show hashes.txt

# Crackear /etc/shadow de Linux:
unshadow /etc/passwd /etc/shadow > combined.txt
john combined.txt</code></pre>

  <h2>3. Hashcat</h2>
  <pre><code># Ataque de diccionario (-a 0):
hashcat -a 0 -m 0 hashes.txt rockyou.txt       # MD5
hashcat -a 0 -m 100 hashes.txt rockyou.txt     # SHA1
hashcat -a 0 -m 1400 hashes.txt rockyou.txt    # SHA256
hashcat -a 0 -m 1000 hashes.txt rockyou.txt    # NTLM (Windows)
hashcat -a 0 -m 3200 hashes.txt rockyou.txt    # bcrypt

# Ataque de fuerza bruta (-a 3):
hashcat -a 3 -m 0 hash.txt ?d?d?d?d?d?d   # 6 dígitos

# Máscaras de fuerza bruta:
# ?l = minúscula | ?u = MAYÚSCULA | ?d = dígito | ?s = símbolo | ?a = todo

# Combinaciones comunes:
hashcat -a 3 -m 0 hash.txt ?u?l?l?l?d?d?d?s  # Formato Password1!

# Ataque con reglas (mejora el diccionario):
hashcat -a 0 -m 0 hash.txt rockyou.txt -r /usr/share/hashcat/rules/best64.rule

# Ver progreso en tiempo real:
hashcat -a 0 -m 0 hash.txt rockyou.txt --status</code></pre>

  <h2>4. Modos de hash de Hashcat (más comunes)</h2>
  <table>
    <thead><tr><th>Modo (-m)</th><th>Tipo de hash</th></tr></thead>
    <tbody>
      <tr><td>0</td><td>MD5</td></tr>
      <tr><td>100</td><td>SHA1</td></tr>
      <tr><td>1400</td><td>SHA256</td></tr>
      <tr><td>1700</td><td>SHA512</td></tr>
      <tr><td>1000</td><td>NTLM (Windows)</td></tr>
      <tr><td>3200</td><td>bcrypt</td></tr>
      <tr><td>500</td><td>md5crypt (Unix $1$)</td></tr>
      <tr><td>1800</td><td>sha512crypt (Unix $6$)</td></tr>
      <tr><td>2500</td><td>WPA/WPA2 (WiFi)</td></tr>
    </tbody>
  </table>

  <h2>5. Preparar wordlists</h2>
  <pre><code># Descomprimir rockyou.txt (Kali):
gunzip /usr/share/wordlists/rockyou.txt.gz

# Crear wordlist personalizada con cewl (basada en una web):
cewl https://www.target.com -d 2 -m 6 -w wordlist_target.txt

# Crear mutaciones con crunch:
crunch 8 10 abcdefghijklmnopqrstuvwxyz0123456789 -o wordlist.txt</code></pre>
</div>
<?php else: ?>
<div class="prose">
  <p>Password hash cracking practice using <strong>John the Ripper</strong> (CPU-based) and <strong>Hashcat</strong> (GPU-based), covering dictionary attacks, brute force and rule-based mutations.</p>

  <h2>1. Identifying Hash Types</h2>
  <pre><code># With hashid:
hashid 5f4dcc3b5aa765d61d8327deb882cf99

# Common hash lengths:
# MD5:    32 hex chars
# SHA1:   40 hex chars
# SHA256: 64 hex chars
# bcrypt: starts with $2b$
# NTLM:  32 hex chars (Windows)</code></pre>

  <h2>2. John the Ripper</h2>
  <pre><code># Dictionary attack:
john --wordlist=/usr/share/wordlists/rockyou.txt hashes.txt

# Specify hash format:
john --format=md5crypt  --wordlist=rockyou.txt hashes.txt
john --format=sha256    --wordlist=rockyou.txt hashes.txt
john --format=bcrypt    --wordlist=rockyou.txt hashes.txt
john --format=NT        --wordlist=rockyou.txt hashes.txt  # NTLM (Windows)

# Rule-based attack (mutates dictionary words):
john --wordlist=rockyou.txt --rules hashes.txt

# Incremental (pure brute force):
john --incremental hashes.txt

# Show cracked passwords:
john --show hashes.txt

# Crack Linux /etc/shadow:
unshadow /etc/passwd /etc/shadow > combined.txt
john combined.txt</code></pre>

  <h2>3. Hashcat</h2>
  <pre><code># Dictionary attack (-a 0):
hashcat -a 0 -m 0    hashes.txt rockyou.txt   # MD5
hashcat -a 0 -m 100  hashes.txt rockyou.txt   # SHA1
hashcat -a 0 -m 1400 hashes.txt rockyou.txt   # SHA256
hashcat -a 0 -m 1000 hashes.txt rockyou.txt   # NTLM (Windows)
hashcat -a 0 -m 3200 hashes.txt rockyou.txt   # bcrypt

# Brute force (-a 3) with mask:
hashcat -a 3 -m 0 hash.txt ?d?d?d?d?d?d  # 6 digits

# Mask characters:
# ?l = lowercase | ?u = UPPERCASE | ?d = digit | ?s = symbol | ?a = all

# Common pattern (e.g. Password1!):
hashcat -a 3 -m 0 hash.txt ?u?l?l?l?l?d?d?d?s

# Rule-based attack (enhances the dictionary):
hashcat -a 0 -m 0 hash.txt rockyou.txt -r /usr/share/hashcat/rules/best64.rule</code></pre>

  <h2>4. Hashcat Mode Reference</h2>
  <table>
    <thead><tr><th>Mode (-m)</th><th>Hash Type</th></tr></thead>
    <tbody>
      <tr><td>0</td><td>MD5</td></tr>
      <tr><td>100</td><td>SHA1</td></tr>
      <tr><td>1400</td><td>SHA256</td></tr>
      <tr><td>1700</td><td>SHA512</td></tr>
      <tr><td>1000</td><td>NTLM (Windows)</td></tr>
      <tr><td>3200</td><td>bcrypt</td></tr>
      <tr><td>500</td><td>md5crypt (Unix $1$)</td></tr>
      <tr><td>1800</td><td>sha512crypt (Unix $6$)</td></tr>
      <tr><td>22000</td><td>WPA/WPA2 (WiFi)</td></tr>
    </tbody>
  </table>

  <h2>5. Preparing Wordlists</h2>
  <pre><code># Decompress rockyou.txt (Kali):
gunzip /usr/share/wordlists/rockyou.txt.gz

# Generate website-specific wordlist with CeWL:
cewl https://www.target.com -d 2 -m 6 -w target_wordlist.txt

# Generate custom wordlist with Crunch:
crunch 8 10 abcdefghijklmnopqrstuvwxyz0123456789 -o wordlist.txt</code></pre>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';
