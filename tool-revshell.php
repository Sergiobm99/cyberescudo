<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $lang==='es' ? 'Generador de Reverse Shells — CyberEscudo' : 'Reverse Shell Generator — CyberEscudo';
$current_page = basename($_SERVER['PHP_SELF']);
require __DIR__ . '/templates/header.php';
?>

<main class="content-page">
  <div style="margin-bottom:2rem;">
    <span class="section-label"><?= $lang==='es' ? '// HERRAMIENTAS' : '// TOOLS' ?></span>
    <h1><?= $lang==='es' ? 'Herramientas de Seguridad' : 'Security Tools' ?></h1>
  </div>

  <div class="tool-select-wrapper">
    <select id="tool-switcher" class="tool-selector">
      <option value="" disabled>-- <?= $lang==='es' ? 'Selecciona una herramienta' : 'Select a tool' ?> --</option>
      <option value="<?= BASE_URL ?>/tool-ip.php" <?= $current_page==='tool-ip.php' ? 'selected' : '' ?>>🌐 <?= $lang==='es' ? '¿Cuál es mi IP?' : 'What is my IP?' ?></option>
      <option value="<?= BASE_URL ?>/tool-passgen.php" <?= $current_page==='tool-passgen.php' ? 'selected' : '' ?>>🔑 <?= $lang==='es' ? 'Generador de Contraseñas' : 'Password Generator' ?></option>
      <option value="<?= BASE_URL ?>/tool-passcheck.php" <?= $current_page==='tool-passcheck.php' ? 'selected' : '' ?>>🛡️ <?= $lang==='es' ? 'Fortaleza de Contraseña' : 'Password Strength' ?></option>
      <option value="<?= BASE_URL ?>/tool-hash.php" <?= $current_page==='tool-hash.php' ? 'selected' : '' ?>>#️⃣ <?= $lang==='es' ? 'Generador de Hashes' : 'Hash Generator' ?></option>
      <option value="<?= BASE_URL ?>/tool-hashcrack.php" <?= $current_page==='tool-hashcrack.php' ? 'selected' : '' ?>>🔓 <?= $lang==='es' ? 'Analizador/Cracker de Hashes' : 'Hash Analyzer/Cracker' ?></option>
      <option value="<?= BASE_URL ?>/tool-base64.php" <?= $current_page==='tool-base64.php' ? 'selected' : '' ?>>🔄 <?= $lang==='es' ? 'Codificador/Decodificador Base64' : 'Base64 Encoder/Decoder' ?></option>
      <option value="<?= BASE_URL ?>/tool-cidr.php" <?= $current_page==='tool-cidr.php' ? 'selected' : '' ?>>🌍 <?= $lang==='es' ? 'Calculadora de Subredes CIDR' : 'CIDR Subnet Calculator' ?></option>
      <option value="<?= BASE_URL ?>/tool-jwt.php" <?= $current_page==='tool-jwt.php' ? 'selected' : '' ?>>🔓 <?= $lang==='es' ? 'Decodificador JWT' : 'JWT Decoder' ?></option>
      <option value="<?= BASE_URL ?>/tool-url.php" <?= $current_page==='tool-url.php' ? 'selected' : '' ?>>🔗 <?= $lang==='es' ? 'Codificador/Decodificador de URL' : 'URL Encoder/Decoder' ?></option>
      <option value="<?= BASE_URL ?>/tool-chmod.php" <?= $current_page==='tool-chmod.php' ? 'selected' : '' ?>>🐧 <?= $lang==='es' ? 'Calculadora Chmod Linux' : 'Linux Chmod Calculator' ?></option>
      <option value="<?= BASE_URL ?>/tool-regex.php" <?= $current_page==='tool-regex.php' ? 'selected' : '' ?>>🛡️ <?= $lang==='es' ? 'Generador Regex Contraseñas' : 'Password Regex Generator' ?></option>
      <option value="<?= BASE_URL ?>/tool-mac.php" <?= $current_page==='tool-mac.php' ? 'selected' : '' ?>>🏷️ <?= $lang==='es' ? 'Buscador de Fabricante MAC' : 'MAC Vendor Lookup' ?></option>
      <option value="<?= BASE_URL ?>/tool-revshell.php" <?= $current_page==='tool-revshell.php' ? 'selected' : '' ?>>🐚 <?= $lang==='es' ? 'Generador Reverse Shells' : 'Reverse Shell Generator' ?></option>
      <option value="<?= BASE_URL ?>/tool-cron.php" <?= $current_page==='tool-cron.php' ? 'selected' : '' ?>>⏱ <?= $lang==='es' ? 'Analizador Cron' : 'Cron Parser' ?></option>
    <option value="<?= BASE_URL ?>/tool-dns.php" <?= $current_page==='tool-dns.php' ? 'selected' : '' ?>>🔍 DNS Lookup</option>
    <option value="<?= BASE_URL ?>/tool-headers.php" <?= $current_page==='tool-headers.php' ? 'selected' : '' ?>>📋 <?= $lang==='es' ? 'Analizador de Headers HTTP' : 'HTTP Header Analyzer' ?></option>
    <option value="<?= BASE_URL ?>/tool-wordlist.php" <?= $current_page==='tool-wordlist.php' ? 'selected' : '' ?>>📝 <?= $lang==='es' ? 'Generador Wordlist' : 'Wordlist Generator' ?></option>
    <option value="<?= BASE_URL ?>/tool-multidecode.php" <?= $current_page==='tool-multidecode.php' ? 'selected' : '' ?>>🔄 <?= $lang==='es' ? 'Multi Decoder (CTF)' : 'Multi Decoder (CTF)' ?></option>
    <option value="<?= BASE_URL ?>/tool-httpbuilder.php" <?= $current_page==='tool-httpbuilder.php' ? 'selected' : '' ?>>📡 <?= $lang==='es' ? 'Generador de HTTP' : '📡 HTTP Builder' ?></option>
    <option value="<?= BASE_URL ?>/tool-waf.php" <?= $current_page==='tool-waf.php' ? 'selected' : '' ?>>🛡️ <?= $lang==='es' ? 'biblioteca de payloads para evadir WAFs' : '🛡️ WAF Bypass Payloads' ?></option>
    <option value="<?= BASE_URL ?>/tool-cloud.php" <?= $current_page==='tool-cloud.php' ? 'selected' : '' ?>>☁️ <?= $lang==='es' ? 'comandos de enumeración cloud interactivos' : '☁️ Cloud Enum' ?></option>
    <option value="<?= BASE_URL ?>/tool-loganalyzer.php" <?= $current_page==='tool-loganalyzer.php' ? 'selected' : '' ?>>📊 <?= $lang==='es' ? 'Analizador de Logs' : 'Log Analyzer' ?></option>
    </select>
  </div>

  <div class="card">
    <div style="margin-bottom:1.5rem;border-bottom:1px solid var(--border);padding-bottom:1rem;">
      <h2 style="font-size:1.4rem; color:var(--white); margin-bottom:.5rem;">🐚 <?= $lang==='es' ? 'Generador de Reverse Shells' : 'Reverse Shell Generator' ?></h2>
      <p style="color:var(--gray);font-size:.9rem;"><?= $lang==='es'
        ? 'Genera payloads para Reverse Shells en múltiples lenguajes. Modifica la IP y el puerto para adaptarlo a tu listener.'
        : 'Generate reverse shell payloads for multiple languages. Modify the IP and port to match your listener.' ?></p>
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:1rem; margin-bottom:1.5rem;">
      <div>
        <label style="font-family:var(--mono);font-size:.75rem;color:var(--gray-dark);text-transform:uppercase;letter-spacing:.1em;display:block;margin-bottom:.5rem;">
          <?= $lang==='es' ? 'Tu IP (Atacante)' : 'Your IP (Attacker)' ?>
        </label>
        <input type="text" id="rs-ip" class="cyber-input" placeholder="10.10.14.5" value="10.10.14.5" style="margin-bottom:0;">
      </div>
      <div>
        <label style="font-family:var(--mono);font-size:.75rem;color:var(--gray-dark);text-transform:uppercase;letter-spacing:.1em;display:block;margin-bottom:.5rem;">
          <?= $lang==='es' ? 'Puerto' : 'Port' ?>
        </label>
        <input type="number" id="rs-port" class="cyber-input" placeholder="4444" value="4444" min="1" max="65535" style="margin-bottom:0;">
      </div>
    </div>

    <div style="margin-bottom:1.5rem;">
      <label style="font-family:var(--mono);font-size:.75rem;color:var(--gray-dark);text-transform:uppercase;letter-spacing:.1em;display:block;margin-bottom:.75rem;">
        <?= $lang==='es' ? 'Tipo de Payload' : 'Payload Type' ?>
      </label>
      <div id="rs-types" style="display:flex;flex-wrap:wrap;gap:.5rem;"></div>
    </div>

    <div style="position:relative;">
      <div style="font-family:var(--mono);font-size:.7rem;color:var(--gray-dark);text-transform:uppercase;letter-spacing:.12em;margin-bottom:.5rem;">
        <?= $lang==='es' ? 'Comando generado' : 'Generated command' ?>
      </div>
      <div id="rs-output" style="background:rgba(0,0,0,.5);border:1px solid rgba(0,255,255,.15);border-radius:.5rem;padding:1.25rem 4rem 1.25rem 1.25rem;font-family:var(--mono);font-size:.85rem;color:rgba(255,255,255,.85);white-space:pre-wrap;word-break:break-all;min-height:3.5rem;line-height:1.7;"></div>
      <button type="button" id="btn-rs-copy" class="copy-btn" style="top: 1.8rem; right: 0.5rem;">📋 <?= $lang==='es' ? 'Copiar' : 'Copy' ?></button>
    </div>

    <div style="margin-top:1.25rem;padding:1rem 1.25rem;background:rgba(0,255,255,.03);border:1px solid rgba(0,255,255,.08);border-radius:.5rem;">
      <div style="font-family:var(--mono);font-size:.7rem;color:var(--cyan);text-transform:uppercase;letter-spacing:.12em;margin-bottom:.5rem;">
        <?= $lang==='es' ? 'Listener (En tu máquina local)' : 'Listener (On your local machine)' ?>
      </div>
      <div id="rs-listener" style="font-family:var(--mono);font-size:.9rem;color:rgba(255,255,255,.8); font-weight: bold;"></div>
    </div>

    <div style="margin-top:1.25rem;padding:.75rem 1rem;background:rgba(255,160,0,.05);border:1px solid rgba(255,160,0,.15);border-radius:.45rem;font-family:var(--mono);font-size:.78rem;color:#f0a000;">
      ⚠️ <?= $lang==='es'
        ? 'Solo para uso en entornos de laboratorio, CTFs o sistemas con permiso explícito. El uso malintencionado es ilegal.'
        : 'For use in lab environments, CTFs, or systems with explicit permission only. Malicious use is illegal.' ?>
    </div>
  </div>
</main>

<script src="<?= BASE_URL ?>/assets/js/tools.js"></script>
<?php require __DIR__ . '/templates/footer.php'; ?>