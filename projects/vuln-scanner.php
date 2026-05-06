<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Escáner de Vulnerabilidades en Python — CyberEscudo' : 'Python Vulnerability Scanner — CyberEscudo';
$contentTitle = $lang==='es' ? 'Escáner de Vulnerabilidades Custom (Python)' : 'Custom Vulnerability Scanner (Python)';
$contentDate  = '2024-10-05';
$contentDiff  = 'intermediate';
$contentTags  = ['Python','Sockets','Requests','Automation','CVE','DevSecOps'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p>Crear tu propio escáner de vulnerabilidades te otorga un control absoluto sobre tus auditorías. A diferencia de soluciones comerciales pesadas (como Nessus o Qualys), un escáner en Python puede ser ligero, sigiloso y estar programado para buscar exactamente los vectores de ataque que te interesan. En esta guía, construiremos la arquitectura de un escáner modular.</p>

  <h2>1. Fundamentos de Red: Escaneo de Puertos con Sockets</h2>
  <p>El núcleo de cualquier escáner es descubrir qué puertas están abiertas. Para ello, usamos la librería nativa <code>socket</code> de Python, intentando realizar el <em>TCP 3-Way Handshake</em>.</p>
  <pre><code>import socket
from concurrent.futures import ThreadPoolExecutor

def scan_port(ip, port):
    # AF_INET para IPv4, SOCK_STREAM para TCP
    try:
        with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
            s.settimeout(1.0) # Evitar quedarse colgado infinitamente
            if s.connect_ex((ip, port)) == 0:
                print(f"[+] Puerto {port} ABIERTO")
                return port
    except Exception:
        pass
    return None

# Usar hilos paralelos para escanear rápido
target = "10.10.10.50"
with ThreadPoolExecutor(max_workers=50) as executor:
    for port in range(1, 1024):
        executor.submit(scan_port, target, port)</code></pre>

  <h2>2. Banner Grabbing (Extracción de Versiones)</h2>
  <p>Saber que el puerto 22 está abierto no es suficiente. Necesitamos saber qué versión de SSH corre ahí para cruzarla con bases de datos de vulnerabilidades. El <em>Banner Grabbing</em> lee los primeros bytes que el servidor envía al conectarnos.</p>
  <pre><code>def grab_banner(ip, port):
    try:
        with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
            s.settimeout(2.0)
            s.connect((ip, port))
            # Algunos servicios requieren que enviemos datos primero (como HTTP)
            if port == 80:
                s.send(b"HEAD / HTTP/1.1\r\nHost: {ip}\r\n\r\n")
            banner = s.recv(1024).decode().strip()
            print(f"[+] Banner en {port}: {banner}")
    except Exception as e:
        print(f"[-] No se pudo obtener banner en {port}")</code></pre>

  <h2>3. Análisis Web con Requests y BeautifulSoup</h2>
  <p>Si el escáner detecta servicios HTTP/HTTPS, delegamos el análisis a un módulo específico. Usaremos <code>requests</code> para interactuar con la web y buscar cabeceras inseguras o directorios expuestos.</p>
  <pre><code>import requests

def analyze_headers(url):
    try:
        # verify=False útil en entornos locales con certificados autofirmados (cuidado en prod)
        response = requests.get(url, timeout=5, verify=False)
        headers = response.headers
        
        # Comprobar cabeceras de seguridad críticas
        security_headers = [
            'Strict-Transport-Security',
            'X-Frame-Options',
            'X-Content-Type-Options',
            'Content-Security-Policy'
        ]
        
        for header in security_headers:
            if header not in headers:
                print(f"[!] ALERTA: Falta la cabecera {header}")
                
        # Detectar tecnologías expuestas
        if 'Server' in headers:
            print(f"[*] Servidor detectado: {headers['Server']}")
            
    except requests.exceptions.RequestException as e:
        print(f"Error conectando a {url}: {e}")</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 12 (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Simulador de Desarrollo Seguro
      </h3>
      <p style="margin-bottom: 1.5rem;">El módulo HTTP de nuestro escáner escrito en Python está fallando en producción. Como Lead Developer, debes debugear el código, identificar los parámetros correctos de la librería <code>requests</code> y de <code>sockets</code> para asegurar que la herramienta audita correctamente.</p>
      <a href="/ctf/ctf-12.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 12
      </a>
  </div>

  <h2>4. Fuzzing de Directorios Integrado</h2>
  <p>Podemos dotar a nuestro escáner Python de las mismas capacidades que Gobuster, leyendo un diccionario y paralelizando peticiones GET para encontrar paneles ocultos (como <code>/admin</code> o <code>.env</code>).</p>
  <pre><code>def fuzz_directories(base_url, wordlist_path):
    with open(wordlist_path, 'r') as file:
        directories = file.read().splitlines()
        
    def check_dir(directory):
        url = f"{base_url}/{directory}"
        res = requests.get(url)
        if res.status_code != 404:
            print(f"[+] Encontrado: {url} (Status: {res.status_code})")
            
    with ThreadPoolExecutor(max_workers=20) as executor:
        executor.map(check_dir, directories)</code></pre>

  <h2>5. Integración con Bases de Datos CVE (NVD API)</h2>
  <p>El paso final para un escáner profesional es automatizar la búsqueda de exploits. Una vez que nuestro Banner Grabbing detecta, por ejemplo, "Apache 2.4.49", el escáner hace una petición a la API del <em>National Vulnerability Database (NVD)</em> para listar los CVEs críticos (como el CVE-2021-41773 de Path Traversal).</p>

  <h2>6. Recomendaciones de Rendimiento y Arquitectura</h2>
  <ul>
    <li><strong>Asincronismo (asyncio):</strong> Aunque <code>ThreadPoolExecutor</code> es fácil de usar, la librería <code>asyncio</code> junto con <code>aiohttp</code> es infinitamente más rápida para escaneos masivos en red porque evita el bloqueo de hilos (I/O Bound).</li>
    <li><strong>Manejo de Errores:</strong> Los firewalls cortarán tus conexiones de forma abrupta. Utiliza siempre bloques <code>try/except</code> genéricos alrededor de las peticiones para que el escáner no se cierre (crash) a la mitad del proceso.</li>
    <li><strong>Evasión:</strong> Añade un <code>User-Agent</code> aleatorio en la librería requests para evitar ser bloqueado inmediatamente por los WAF.</li>
  </ul>
</div>

<?php else: ?>
<div class="prose">
  <p>Creating your own vulnerability scanner gives you absolute control over your audits. Unlike heavy commercial solutions, a Python scanner can be lightweight, stealthy, and tailored to search for specific attack vectors. In this guide, we will build a modular scanner architecture.</p>

  <h2>1. Networking Basics: Port Scanning with Sockets</h2>
  <p>The core of any scanner is discovering open doors. We use Python's native <code>socket</code> library to attempt a <em>TCP 3-Way Handshake</em>.</p>
  <pre><code>import socket
from concurrent.futures import ThreadPoolExecutor

def scan_port(ip, port):
    # AF_INET for IPv4, SOCK_STREAM for TCP
    try:
        with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
            s.settimeout(1.0) # Avoid hanging indefinitely
            if s.connect_ex((ip, port)) == 0:
                print(f"[+] Port {port} OPEN")
                return port
    except Exception:
        pass
    return None

# Use multithreading for speed
target = "10.10.10.50"
with ThreadPoolExecutor(max_workers=50) as executor:
    for port in range(1, 1024):
        executor.submit(scan_port, target, port)</code></pre>

  <h2>2. Banner Grabbing (Version Extraction)</h2>
  <p>Knowing port 22 is open isn't enough; we need the SSH version to cross-reference with CVE databases.</p>
  <pre><code>def grab_banner(ip, port):
    try:
        with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
            s.settimeout(2.0)
            s.connect((ip, port))
            banner = s.recv(1024).decode().strip()
            print(f"[+] Banner on {port}: {banner}")
    except Exception as e:
        pass</code></pre>

  <h2>3. Web Analysis with Requests</h2>
  <p>We delegate HTTP/HTTPS scanning to the <code>requests</code> library to search for insecure headers or exposed paths.</p>
  <pre><code>import requests

def analyze_headers(url):
    try:
        # verify=False is useful for local self-signed certs
        response = requests.get(url, timeout=5, verify=False)
        headers = response.headers
        
        security_headers = ['Strict-Transport-Security', 'X-Frame-Options']
        for header in security_headers:
            if header not in headers:
                print(f"[!] ALERT: Missing {header} header")
    except requests.exceptions.RequestException:
        pass</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 12 (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Secure Development Simulator
      </h3>
      <p style="margin-bottom: 1.5rem;">Our Python scanner's HTTP module is crashing in production. As the Lead Developer, you must debug the code, identify the correct <code>requests</code> and <code>sockets</code> parameters, and ensure the tool audits correctly.</p>
      <a href="/ctf/ctf-12.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 12 CHALLENGE
      </a>
  </div>

  <h2>4. Architecture & Performance</h2>
  <ul>
    <li><strong>Asyncio:</strong> While <code>ThreadPoolExecutor</code> is good, combining <code>asyncio</code> with <code>aiohttp</code> is infinitely faster for mass network scanning because it doesn't block threads (I/O Bound).</li>
    <li><strong>Evasion:</strong> Always inject a randomized <code>User-Agent</code> header in your requests to avoid immediate WAF blocks.</li>
  </ul>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';