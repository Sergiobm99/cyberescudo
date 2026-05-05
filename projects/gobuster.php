<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Gobuster y ffuf: Fuzzing Web y Subdominios — CyberEscudo' : 'Gobuster & ffuf: Web & Subdomain Fuzzing — CyberEscudo';
$contentTitle = $lang==='es' ? 'Gobuster y ffuf: Fuzzing Web y Subdominios' : 'Gobuster & ffuf: Web & Subdomain Fuzzing';
$contentDate  = '2024-06-10';
$contentDiff  = 'basic';
$contentTags  = ['Gobuster','ffuf','Fuzzing','Directorios','Subdominios','Reconocimiento'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p><strong>Gobuster</strong> y <strong>ffuf</strong> son dos de las herramientas de fuzzing más usadas en pentesting web. Permiten descubrir directorios ocultos, archivos, parámetros y subdominios mediante ataques de diccionario. Son rápidas, modulares y esenciales en la fase de reconocimiento.</p>

  <h2>1. Instalación y Wordlists</h2>
  <pre><code># Gobuster (Go):
apt install gobuster

# ffuf (Go — más flexible y rápido):
apt install ffuf

# Wordlists recomendadas (incluidas en Kali/SecLists):
apt install seclists
# Ruta principal: /usr/share/seclists/</code></pre>

  <h2>2. Códigos HTTP Clave en Fuzzing</h2>
  <p>Al hacer fuzzing, el servidor responde con códigos de estado. Saber interpretarlos te indica qué hacer a continuación:</p>
  <ul>
      <li><strong>200 OK:</strong> El recurso existe y es accesible.</li>
      <li><strong>301 / 302 Redirect:</strong> El recurso existe, pero te redirige. (Conviene explorar hacia dónde).</li>
      <li><strong>401 Unauthorized:</strong> Requiere autenticación HTTP básica.</li>
      <li><strong>403 Forbidden:</strong> El recurso existe, pero no tienes permisos (¡Interesante para intentar bypassear!).</li>
      <li><strong>404 Not Found:</strong> El recurso no existe (Ruido).</li>
      <li><strong>500 Internal Error:</strong> Has roto algo o el servidor no sabe manejar tu payload (Interesante en inyecciones).</li>
  </ul>

  <h2>3. Gobuster — Enumeración de directorios</h2>
  <pre><code># Escaneo básico de directorios:
gobuster dir -u http://objetivo.com -w /usr/share/seclists/Discovery/Web-Content/common.txt

# Buscar archivos con extensiones específicas:
gobuster dir -u http://objetivo.com -w wordlist.txt -x php,html,txt,bak,zip

# Ignorar códigos de respuesta concretos (eliminar ruido):
gobuster dir -u http://objetivo.com -w wordlist.txt -b 404,403</code></pre>

  <h2>4. ffuf — Fuzzing avanzado (El rey actual)</h2>
  <p>A diferencia de Gobuster, <code>ffuf</code> usa la palabra clave <strong>FUZZ</strong> para inyectar el diccionario en cualquier parte de la petición (URL, Cabeceras, POST data).</p>
  <pre><code># Fuzzing de directorios:
ffuf -u http://objetivo.com/FUZZ -w dicc.txt

# Fuzzing de parámetros GET (Ej: id=1, file=algo):
ffuf -u "http://objetivo.com/page.php?FUZZ=test" -w parametros.txt

# Fuzzing POST (formularios de login, APIs JSON):
ffuf -u http://objetivo.com/login \
  -w /usr/share/wordlists/rockyou.txt \
  -X POST \
  -d "username=admin&password=FUZZ" \
  -H "Content-Type: application/x-www-form-urlencoded"</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 07 (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Simulador de Fuzzing
      </h3>
      <p style="margin-bottom: 1.5rem;">Te enfrentas a un servidor configurado como "Catch-all" (miente y siempre devuelve código 200 OK). Demuestra tus habilidades construyendo el comando ffuf exacto para filtrar la basura y encontrar el panel oculto.</p>
      <a href="/ctf/ctf-07.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 07
      </a>
  </div>

  <h2>5. El problema del "Catch-all" (Filtrando resultados)</h2>
  <p>A veces, los servidores están mal configurados y devuelven un <strong>200 OK incluso si la página no existe</strong> (mostrando una página de error personalizada). Esto inunda nuestro fuzzing de miles de falsos positivos.</p>
  <p>Para solucionarlo, analizamos el tamaño (Size) o la cantidad de palabras (Words) de la página de error genérica y le decimos a <code>ffuf</code> que <strong>filtre y oculte</strong> las respuestas que coincidan con ese tamaño exacto.</p>

  <pre><code># Filtrar por código HTTP:
ffuf -u http://objetivo.com/FUZZ -w dicc.txt -fc 404,403

# Filtrar por tamaño de respuesta en Bytes (evadir Catch-all):
ffuf -u http://objetivo.com/FUZZ -w dicc.txt -fs 512

# Filtrar por número de palabras o líneas:
ffuf -u http://objetivo.com/FUZZ -w dicc.txt -fw 10 -fl 25</code></pre>

  <h2>6. Enumeración de Subdominios y VHosts</h2>
  <pre><code># Subdominios DNS (Gobuster):
gobuster dns -d objetivo.com -w subdomains.txt

# Virtual Hosts con ffuf (Inyectando en la cabecera Host):
ffuf -u http://10.10.10.10 \
  -H "Host: FUZZ.objetivo.com" \
  -w subdomains.txt \
  -fs 4242  # Muy importante excluir el tamaño de la respuesta por defecto</code></pre>
</div>

<?php else: ?>
<div class="prose">
  <p><strong>Gobuster</strong> and <strong>ffuf</strong> are two of the most popular fuzzing tools in web penetration testing. They discover hidden directories, files, parameters, and subdomains through dictionary-based attacks.</p>

  <h2>1. Installation & Wordlists</h2>
  <pre><code># Gobuster (Go):
apt install gobuster

# ffuf (Go — faster and more flexible):
apt install ffuf

# Recommended wordlists (SecLists):
apt install seclists</code></pre>

  <h2>2. Key HTTP Codes in Fuzzing</h2>
  <ul>
      <li><strong>200 OK:</strong> Resource exists.</li>
      <li><strong>301 / 302:</strong> Redirect (Explore where it leads).</li>
      <li><strong>401 Unauthorized:</strong> Requires basic auth.</li>
      <li><strong>403 Forbidden:</strong> Exists, but access denied (Target for bypasses!).</li>
      <li><strong>404 Not Found:</strong> Doesn't exist (Noise).</li>
      <li><strong>500 Internal Error:</strong> Server crashed handling your payload.</li>
  </ul>

  <h2>3. Gobuster — Directory Enumeration</h2>
  <pre><code>gobuster dir -u http://target.com -w common.txt -x php,txt,bak
gobuster dir -u http://target.com -w wordlist.txt -b 404,403</code></pre>

  <h2>4. ffuf — Advanced Fuzzing (The Modern King)</h2>
  <p><code>ffuf</code> uses the <strong>FUZZ</strong> keyword to inject the wordlist anywhere in the request.</p>
  <pre><code># Directory fuzzing:
ffuf -u http://target.com/FUZZ -w dict.txt

# GET parameter name fuzzing:
ffuf -u "http://target.com/page.php?FUZZ=test" -w params.txt

# POST fuzzing:
ffuf -u http://target.com/login \
  -w rockyou.txt \
  -X POST -d "username=admin&password=FUZZ" \
  -H "Content-Type: application/x-www-form-urlencoded"</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 07 (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Fuzzing Simulator
      </h3>
      <p style="margin-bottom: 1.5rem;">You are facing a "Catch-all" server (it lies and always returns 200 OK). Prove your skills by crafting the exact ffuf command to filter the garbage and find the hidden panel.</p>
      <a href="/ctf/ctf-07.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 07 CHALLENGE
      </a>
  </div>

  <h2>5. The "Catch-all" Problem (Filtering)</h2>
  <p>Sometimes servers return <strong>200 OK for everything</strong>, flooding our results with false positives. We analyze the default error page size and tell <code>ffuf</code> to filter it out.</p>
  <pre><code># Filter by size in Bytes (-fs):
ffuf -u http://target.com/FUZZ -w dict.txt -fs 512

# Filter by word (-fw) or line count (-fl):
ffuf -u http://target.com/FUZZ -w dict.txt -fw 10 -fl 25</code></pre>

  <h2>6. Subdomains & VHosts</h2>
  <pre><code># VHost fuzzing via Host header:
ffuf -u http://SERVER_IP -H "Host: FUZZ.target.com" -w subdomains.txt -fs 4242</code></pre>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';