<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Seguridad DNS y DNSSEC — CyberEscudo' : 'DNS Security & DNSSEC — CyberEscudo';
$contentTitle = $lang==='es' ? 'Seguridad DNS y DNSSEC' : 'DNS Security & DNSSEC';
$contentDate  = '2022-03-30';
$contentTags  = ['DNS','DNSSEC','DNS Spoofing','Reconocimiento','Enumeración'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p>Ataques sobre el protocolo <strong>DNS</strong>, enumeración de registros, ataques de spoofing y cache poisoning, y cómo protegerse con <strong>DNSSEC</strong>.</p>

  <h2>1. Reconocimiento DNS (enumeración)</h2>
  <pre><code># Consultas básicas:
nslookup target.com
dig target.com
dig target.com ANY      # Todos los tipos de registro
dig target.com MX       # Servidores de correo
dig target.com NS       # Servidores de nombres
dig target.com TXT      # Registros TXT (SPF, DKIM, DMARC)

# Transferencia de zona (si está mal configurada):
dig axfr @ns1.target.com target.com
# Una transferencia exitosa revela TODOS los registros DNS

# Fuerza bruta de subdominios:
dnsenum --dnsserver 8.8.8.8 target.com
fierce --domain target.com
dnsrecon -d target.com -t brt -D /usr/share/wordlists/dnsmap.txt</code></pre>

  <h2>2. DNS Spoofing y Cache Poisoning</h2>
  <p>El atacante inyecta respuestas DNS falsas en la caché de un servidor DNS para redirigir tráfico a servidores maliciosos.</p>
  <pre><code># Demostración con Ettercap (entorno de laboratorio):
# Editar /etc/ettercap/etter.dns:
# *.target.com  A  192.168.1.100   (IP del servidor malicioso)

ettercap -T -q -P dns_spoof -M arp /192.168.1.1// /192.168.1.10//</code></pre>

  <h2>3. DNSSEC — DNS Security Extensions</h2>
  <p>DNSSEC añade firmas criptográficas a los registros DNS, permitiendo verificar su autenticidad e integridad.</p>
  <pre><code># Verificar si un dominio tiene DNSSEC activo:
dig +dnssec target.com
dig DS target.com @8.8.8.8

# Comprobar la cadena de confianza:
delv target.com

# Si la respuesta incluye "ad" (authenticated data), DNSSEC está activo</code></pre>

  <h2>4. Configurar DNSSEC en BIND</h2>
  <pre><code"># Generar par de claves ZSK (Zone Signing Key):
dnssec-keygen -a RSASHA256 -b 2048 -n ZONE target.com

# Generar par de claves KSK (Key Signing Key):
dnssec-keygen -a RSASHA256 -b 4096 -f KSK -n ZONE target.com

# Firmar la zona:
dnssec-signzone -A -3 $(head -c 1000 /dev/random | sha1sum | cut -b 1-16) \
  -N INCREMENT -o target.com -t target.com.zone

# En /etc/bind/named.conf.local:
zone "target.com" {
    type master;
    file "/etc/bind/zones/target.com.zone.signed";
    auto-dnssec maintain;
    key-directory "/etc/bind/keys";
    inline-signing yes;
};</code></pre>

  <h2>5. Protecciones adicionales</h2>
  <pre><code># Deshabilitar transferencia de zona (solo a servidores secundarios):
# En named.conf:
zone "target.com" {
    allow-transfer { 192.168.1.2; };  # Solo NS secundario
};

# Usar DNS sobre HTTPS (DoH) o DNS sobre TLS (DoT):
# - Cloudflare: 1.1.1.1
# - Google: 8.8.8.8
# Configurar en /etc/systemd/resolved.conf:
DNS=1.1.1.1 1.0.0.1
DNSOverTLS=yes</code></pre>

  <h2>Registros DNS de seguridad</h2>
  <table>
    <thead><tr><th>Registro</th><th>Propósito</th><th>Ejemplo</th></tr></thead>
    <tbody>
      <tr><td>SPF</td><td>Define qué servidores pueden enviar email como el dominio</td><td><code>v=spf1 include:_spf.google.com ~all</code></td></tr>
      <tr><td>DKIM</td><td>Firma criptográfica de emails salientes</td><td>Clave pública en registro TXT</td></tr>
      <tr><td>DMARC</td><td>Política de alineación SPF+DKIM y reporting</td><td><code>v=DMARC1; p=reject; rua=mailto:dmarc@domain.com</code></td></tr>
      <tr><td>CAA</td><td>Limita qué CAs pueden emitir certificados para el dominio</td><td><code>0 issue "letsencrypt.org"</code></td></tr>
    </tbody>
  </table>
</div>
<?php else: ?>
<div class="prose">
  <p>DNS protocol attacks, record enumeration, spoofing/cache poisoning, and protection with <strong>DNSSEC</strong>.</p>

  <h2>1. DNS Enumeration</h2>
  <pre><code>dig target.com ANY          # All record types
dig target.com MX           # Mail servers
dig target.com NS           # Name servers
dig target.com TXT          # SPF, DKIM, DMARC records

# Zone transfer (if misconfigured — reveals ALL records):
dig axfr @ns1.target.com target.com

# Subdomain brute force:
dnsrecon -d target.com -t brt -D /usr/share/wordlists/dnsmap.txt
fierce --domain target.com</code></pre>

  <h2>2. DNS Cache Poisoning</h2>
  <p>Attacker injects forged DNS responses into a resolver cache to redirect traffic to malicious servers.</p>
  <pre><code># Lab demo with Ettercap — edit /etc/ettercap/etter.dns:
# *.target.com  A  192.168.1.100
ettercap -T -q -P dns_spoof -M arp /192.168.1.1// /192.168.1.10//</code></pre>

  <h2>3. DNSSEC Verification</h2>
  <pre><code>dig +dnssec target.com      # Check if DNSSEC is active
dig DS target.com @8.8.8.8  # Check DS record
delv target.com             # Verify chain of trust
# "ad" flag in response = authenticated data (DNSSEC active)</code></pre>

  <h2>4. Security: Disable Zone Transfers</h2>
  <pre><code># BIND — allow transfer only to secondary NS:
zone "target.com" {
    allow-transfer { 192.168.1.2; };
};</code></pre>

  <h2>5. DNS over TLS</h2>
  <pre><code># /etc/systemd/resolved.conf:
DNS=1.1.1.1 1.0.0.1
DNSOverTLS=yes</code></pre>

  <h2>DNS Security Records</h2>
  <table>
    <thead><tr><th>Record</th><th>Purpose</th></tr></thead>
    <tbody>
      <tr><td>SPF</td><td>Defines authorised mail servers for the domain</td></tr>
      <tr><td>DKIM</td><td>Cryptographic signature for outgoing emails</td></tr>
      <tr><td>DMARC</td><td>SPF+DKIM alignment policy and reporting</td></tr>
      <tr><td>CAA</td><td>Limits which CAs can issue certificates for the domain</td></tr>
    </tbody>
  </table>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';
