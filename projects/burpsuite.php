<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Burp Suite: Interceptación y Testing Web — CyberEscudo' : 'Burp Suite: Web Interception & Testing — CyberEscudo';
$contentTitle = $lang==='es' ? 'Burp Suite: Interceptación y Testing Web' : 'Burp Suite: Web Interception & Testing';
$contentDate  = '2022-04-05';
$contentTags  = ['BurpSuite','Web','Proxy','Interceptación','OWASP'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p><strong>Burp Suite</strong> es la suite de testing de seguridad web más utilizada en pentesting. Actúa como proxy HTTP/HTTPS entre el navegador y el servidor, permitiendo interceptar, modificar y analizar todas las peticiones.</p>

  <h2>1. Configuración inicial</h2>
  <h3>Configurar el proxy en el navegador</h3>
  <pre><code># Burp escucha por defecto en:
127.0.0.1:8080

# En Firefox: Preferences → Network Settings → Manual Proxy
# HTTP Proxy: 127.0.0.1 | Puerto: 8080</code></pre>

  <h3>Instalar certificado SSL de Burp</h3>
  <pre><code># Con el proxy activo, navegar a:
http://burpsuite
# Descargar e instalar el certificado CA para interceptar HTTPS</code></pre>

  <h2>2. Proxy — Interceptar peticiones</h2>
  <pre><code># Activar intercepción: Proxy → Intercept → Intercept is ON
# Modificar cualquier campo de la petición antes de enviarla
# Forward: enviar al servidor | Drop: descartar

# Ver historial de peticiones:
Proxy → HTTP History</code></pre>

  <h2>3. Repeater — Repetir y modificar peticiones</h2>
  <pre><code># Pasos:
# 1. Interceptar una petición
# 2. Clic derecho → Send to Repeater (Ctrl+R)
# 3. Modificar parámetros manualmente
# 4. Pulsar "Send" y ver la respuesta

# Ideal para:
# - Probar SQL injection manualmente
# - Modificar IDs o tokens en peticiones
# - Probar parámetros ocultos</code></pre>

  <h2>4. Intruder — Ataques automáticos</h2>
  <pre><code># Enviar petición al Intruder: clic derecho → Send to Intruder (Ctrl+I)
# Configurar posiciones (§parámetro§) y tipo de ataque:

# Tipos de ataque:
# Sniper:       un payload, una posición a la vez
# Battering ram: mismo payload en todas las posiciones
# Pitchfork:    payloads diferentes en cada posición (sincronizado)
# Cluster bomb: producto cartesiano de todos los payloads

# Uso típico — fuerza bruta en login:
# Posición: password=§PASS§
# Payload: lista de contraseñas
# En Burp Community: ataque lento (sin throttle en versión Pro)</code></pre>

  <h2>5. Scanner (Burp Pro) — Detección automática</h2>
  <pre><code># Activo: Burp lanza peticiones de prueba para detectar:
# - SQL Injection
# - XSS reflejado y almacenado
# - XXE
# - SSRF
# - Command Injection
# - Path Traversal

# Pasivo: analiza el tráfico que pasa por el proxy
# sin enviar peticiones adicionales</code></pre>

  <h2>6. Decoder — Codificación/Decodificación</h2>
  <pre><code># Soporta: Base64, URL encoding, HTML, Hex, Gzip, etc.
# Decoder → pegar texto → seleccionar operación

# Ejemplo: decodificar token Base64
# Input: dXNlcjphZG1pbg==
# Output: user:admin</code></pre>

  <h2>7. Comparer — Comparar respuestas</h2>
  <pre><code># Útil para:
# - Detectar diferencias entre respuestas de login (usuario válido vs inválido)
# - Comparar respuestas con/sin payload inyectado
# - Análisis de blind SQL injection</code></pre>

  <h2>8. Flujo típico de testing OWASP</h2>
  <table>
    <thead><tr><th>Vulnerabilidad</th><th>Técnica en Burp</th></tr></thead>
    <tbody>
      <tr><td>SQL Injection</td><td>Repeater: añadir <code>\'</code> o <code>1=1</code> en parámetros</td></tr>
      <tr><td>XSS Reflejado</td><td>Repeater: inyectar <code>&lt;script&gt;alert(1)&lt;/script&gt;</code></td></tr>
      <tr><td>IDOR</td><td>Repeater: cambiar IDs de recursos</td></tr>
      <tr><td>Fuerza bruta</td><td>Intruder: Sniper sobre campo password</td></tr>
      <tr><td>CSRF</td><td>Proxy: capturar token y reutilizarlo</td></tr>
      <tr><td>Path Traversal</td><td>Repeater: <code>../../../etc/passwd</code></td></tr>
    </tbody>
  </table>
</div>
<?php else: ?>
<div class="prose">
  <p><strong>Burp Suite</strong> is the most widely used web security testing toolkit. It acts as an HTTP/HTTPS proxy between your browser and the server, letting you intercept, modify and analyze every request and response.</p>

  <h2>1. Initial Setup</h2>
  <h3>Configure the browser proxy</h3>
  <pre><code># Burp listens by default on:
127.0.0.1:8080

# Firefox: Preferences → Network Settings → Manual Proxy Configuration
# HTTP Proxy: 127.0.0.1 | Port: 8080</code></pre>

  <h3>Install Burp's SSL Certificate</h3>
  <pre><code"># With the proxy active, navigate to:
http://burpsuite
# Download and install the CA certificate to intercept HTTPS traffic</code></pre>

  <h2>2. Proxy — Intercepting Requests</h2>
  <pre><code># Enable interception: Proxy → Intercept → Intercept is ON
# Modify any field in the request before forwarding
# Forward: send to server | Drop: discard the request

# View request history:
Proxy → HTTP History</code></pre>

  <h2>3. Repeater — Replaying & Modifying Requests</h2>
  <pre><code># Steps:
# 1. Intercept a request
# 2. Right-click → Send to Repeater (Ctrl+R)
# 3. Modify parameters manually
# 4. Click "Send" and inspect the response

# Ideal for:
# - Manual SQL injection testing
# - Modifying IDs or tokens in requests
# - Testing hidden parameters</code></pre>

  <h2>4. Intruder — Automated Attacks</h2>
  <pre><code"># Send request to Intruder: right-click → Send to Intruder (Ctrl+I)
# Mark positions with §parameter§ and choose attack type:

# Attack types:
# Sniper:       one payload list, one position at a time
# Battering ram: same payload in all positions simultaneously
# Pitchfork:    different payload lists per position (synchronized)
# Cluster bomb: Cartesian product of all payload lists

# Typical use — brute force login:
# Position: password=§PASS§
# Payload: password wordlist
# Note: throttled in Burp Community (unlimited in Pro)</code></pre>

  <h2>5. Scanner (Burp Pro) — Automated Vulnerability Detection</h2>
  <pre><code"># Active scanning: Burp sends crafted requests to detect:
# - SQL Injection
# - Reflected and stored XSS
# - XXE
# - SSRF
# - Command Injection
# - Path Traversal

# Passive scanning: analyzes traffic flowing through the proxy
# without sending additional requests</code></pre>

  <h2>6. Decoder — Encode / Decode</h2>
  <pre><code># Supports: Base64, URL encoding, HTML entities, Hex, Gzip, etc.
# Decoder tab → paste text → select operation

# Example: decode a Base64 token
# Input:  dXNlcjphZG1pbg==
# Output: user:admin</code></pre>

  <h2>7. OWASP Testing Workflow</h2>
  <table>
    <thead><tr><th>Vulnerability</th><th>Burp Technique</th></tr></thead>
    <tbody>
      <tr><td>SQL Injection</td><td>Repeater: add <code>'</code> or <code>1=1</code> to parameters</td></tr>
      <tr><td>Reflected XSS</td><td>Repeater: inject <code>&lt;script&gt;alert(1)&lt;/script&gt;</code></td></tr>
      <tr><td>IDOR</td><td>Repeater: change resource IDs</td></tr>
      <tr><td>Brute Force</td><td>Intruder: Sniper on password field</td></tr>
      <tr><td>CSRF</td><td>Proxy: capture token and reuse it</td></tr>
      <tr><td>Path Traversal</td><td>Repeater: <code>../../../etc/passwd</code></td></tr>
    </tbody>
  </table>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';
