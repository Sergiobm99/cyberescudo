<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Hardening Nginx y SSL/TLS — CyberEscudo' : 'Nginx Hardening & SSL/TLS — CyberEscudo';
$contentTitle = $lang==='es' ? 'Hardening Nginx y SSL/TLS' : 'Nginx Hardening & SSL/TLS';
$contentDate  = '2022-03-25';
$contentTags  = ['Nginx','SSL','TLS','HTTPS','Hardening'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p>Guía de hardening para <strong>Nginx</strong> con configuración segura de SSL/TLS, cabeceras de seguridad HTTP y protecciones contra ataques comunes.</p>

  <h2>1. Instalación y estructura</h2>
  <pre><code>apt install nginx

# Archivos de configuración:
/etc/nginx/nginx.conf          # Config principal
/etc/nginx/sites-available/    # VirtualHosts disponibles
/etc/nginx/sites-enabled/      # VirtualHosts activos (symlinks)
/etc/nginx/conf.d/             # Configuraciones adicionales</code></pre>

  <h2>2. Ocultar versión de Nginx</h2>
  <pre><code># En /etc/nginx/nginx.conf, dentro del bloque http {}:
server_tokens off;</code></pre>

  <h2>3. Configurar HTTPS con Let's Encrypt</h2>
  <pre><code># Instalar Certbot:
apt install certbot python3-certbot-nginx

# Obtener certificado:
certbot --nginx -d midominio.com -d www.midominio.com

# Renovación automática (ya configurada por Certbot):
# Verificar:
certbot renew --dry-run</code></pre>

  <h2>4. Configuración SSL/TLS segura</h2>
  <pre><code># En el bloque server {} del VirtualHost HTTPS:
listen 443 ssl http2;

ssl_certificate     /etc/letsencrypt/live/midominio.com/fullchain.pem;
ssl_certificate_key /etc/letsencrypt/live/midominio.com/privkey.pem;

# Solo TLS 1.2 y 1.3 (deshabilitar versiones antiguas):
ssl_protocols TLSv1.2 TLSv1.3;

# Ciphers seguros:
ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384;
ssl_prefer_server_ciphers off;

# HSTS (máx. 1 año con subdominos y preload):
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;

# OCSP Stapling:
ssl_stapling on;
ssl_stapling_verify on;
resolver 8.8.8.8 8.8.4.4 valid=300s;</code></pre>

  <h2>5. Cabeceras de seguridad HTTP</h2>
  <pre><code># En el bloque server {} o http {}:
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Permissions-Policy "geolocation=(), microphone=(), camera=()" always;
add_header Content-Security-Policy "default-src 'self'; script-src 'self' https://cdn.trusted.com; style-src 'self' 'unsafe-inline';" always;</code></pre>

  <h2>6. Redirigir HTTP → HTTPS</h2>
  <pre><code">server {
    listen 80;
    server_name midominio.com www.midominio.com;
    return 301 https://$host$request_uri;
}</code></pre>

  <h2>7. Límites y protecciones</h2>
  <pre><code># Limitar tamaño de peticiones (anti-DoS):
client_max_body_size 10M;
client_body_timeout 12;
client_header_timeout 12;
keepalive_timeout 15;
send_timeout 10;

# Deshabilitar métodos HTTP peligrosos:
if ($request_method !~ ^(GET|HEAD|POST)$) {
    return 444;
}

# Bloquear acceso a archivos ocultos (.htaccess, .git):
location ~ /\. {
    deny all;
    return 404;
}</code></pre>

  <h2>8. Verificar configuración</h2>
  <pre><code"># Verificar sintaxis sin reiniciar:
nginx -t

# Aplicar cambios:
systemctl reload nginx

# Comprobar nota SSL en:
# https://www.ssllabs.com/ssltest/</code></pre>
</div>
<?php else: ?>
<div class="prose">
  <p>Security hardening guide for <strong>Nginx</strong> with secure SSL/TLS configuration and HTTP security headers.</p>

  <h2>1. Hide Nginx Version</h2>
  <pre><code># /etc/nginx/nginx.conf inside http {}:
server_tokens off;</code></pre>

  <h2>2. HTTPS with Let's Encrypt</h2>
  <pre><code>apt install certbot python3-certbot-nginx
certbot --nginx -d mydomain.com -d www.mydomain.com
certbot renew --dry-run</code></pre>

  <h2>3. Secure SSL/TLS</h2>
  <pre><code>ssl_protocols TLSv1.2 TLSv1.3;
ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256;
ssl_prefer_server_ciphers off;
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
ssl_stapling on;
ssl_stapling_verify on;</code></pre>

  <h2>4. Security Headers</h2>
  <pre><code>add_header X-Frame-Options          "SAMEORIGIN" always;
add_header X-Content-Type-Options   "nosniff" always;
add_header X-XSS-Protection         "1; mode=block" always;
add_header Referrer-Policy          "strict-origin-when-cross-origin" always;
add_header Content-Security-Policy  "default-src 'self';" always;</code></pre>

  <h2>5. Redirect HTTP → HTTPS</h2>
  <pre><code>server {
    listen 80;
    server_name mydomain.com;
    return 301 https://$host$request_uri;
}</code></pre>

  <h2>6. Rate Limiting & Protections</h2>
  <pre><code>client_max_body_size 10M;
keepalive_timeout    15;
if ($request_method !~ ^(GET|HEAD|POST)$) { return 444; }
location ~ /\. { deny all; return 404; }</code></pre>

  <h2>7. Verify</h2>
  <pre><code>nginx -t
systemctl reload nginx
# Check SSL grade: https://www.ssllabs.com/ssltest/</code></pre>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';
