<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $lang==='es' ? 'OSINT Quick Recon — CyberEscudo' : 'OSINT Quick Recon — CyberEscudo';
$current_page = basename($_SERVER['PHP_SELF']);
require __DIR__ . '/templates/header.php';
?>
<main class="content-page">
  <div class="m-bottom-2">
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
		<option value="<?= BASE_URL ?>/tool-mac.php" <?= $current_page==='tool-mac.php' ? 'selected' : '' ?>>🏷️ <?= $lang==='es' ? 'Buscador de Fabricante MAC' : 'MAC Vendor Lookup' ?></option>
		<option value="<?= BASE_URL ?>/tool-regex.php" <?= $current_page==='tool-regex.php' ? 'selected' : '' ?>>🛡️ <?= $lang==='es' ? 'Generador Regex Contraseñas' : 'Password Regex Generator' ?></option>
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
    <option value="<?= BASE_URL ?>/tool-cve.php" <?= $current_page==='tool-cve.php' ? 'selected' : '' ?>>🐛 <?= $lang==='es' ? 'Buscador CVE y Exploits' : 'CVE & Exploit Finder' ?></option>
    <option value="<?= BASE_URL ?>/tool-takeover.php" <?= $current_page==='tool-takeover.php' ? 'selected' : '' ?>>🏴‍☠️ <?= $lang==='es' ? 'Auditoría / Bug Bounty' : 'Subdomain Takeover' ?></option>
    <option value="<?= BASE_URL ?>/tool-recon.php" <?= $current_page==='tool-recon.php' ? 'selected' : '' ?>>🔍 <?= $lang==='es' ? 'Reconocimiento rápido OSINT' : 'OSINT Quick Recon' ?></option>
    <option value="<?= BASE_URL ?>/tool-ssh.php" <?= $current_page==='/tool-ssh.php' ? 'selected' : '' ?>>🔑 <?= $lang==='es' ? 'Analizador SSH' : 'SSH Analyzer' ?></option>
    <option value="<?= BASE_URL ?>/tool-ports.php" <?= $current_page==='/tool-ports.php' ? 'selected' : '' ?>>📋 <?= $lang==='es' ? 'Puertos de referencia' : 'Port Reference' ?></option>
    </select>
  </div>

  <div class="card">
    <div class="tool-header md-container" style="border-bottom:1px solid var(--border); margin-bottom: 1.5rem; padding-bottom: 1rem;">
      <h2>🔍 OSINT Quick Recon</h2>
      <p><?= $lang==='es'
        ? 'Introduce un dominio o empresa y genera automáticamente todos los comandos de reconocimiento pasivo y activo listos para copiar y ejecutar en Kali.'
        : 'Enter a domain or company name and automatically generate all passive and active reconnaissance commands ready to copy and run on Kali.' ?></p>
    </div>

    <!-- Target input -->
    <div class="recon-grid">
      <div>
        <label class="recon-label"><?= $lang==='es'?'Dominio / empresa objetivo':'Target domain / company' ?></label>
        <input type="text" id="recon-target" class="cyber-input m-bottom-0" placeholder="empresa.com">
      </div>
      <div>
        <label class="recon-label"><?= $lang==='es'?'Tu IP (tun0)':'Your IP (tun0)' ?></label>
        <input type="text" id="recon-myip" class="cyber-input m-bottom-0" placeholder="10.10.14.5" value="10.10.14.5">
      </div>
    </div>

    <!-- Phase filter pills -->
    <div class="recon-pills-wrap" id="phase-pills"></div>

    <!-- Output -->
    <div id="recon-output"></div>

    <!-- Warning -->
    <div class="recon-warning">
      ⚠️ <?= $lang==='es'
        ? 'Reconocimiento activo (Nmap, Nikto, etc.) solo debe realizarse sobre objetivos con permiso escrito. El reconocimiento pasivo (WHOIS, crt.sh) es legal sobre cualquier dominio.'
        : 'Active recon (Nmap, Nikto, etc.) must only be performed on targets with written permission. Passive recon (WHOIS, crt.sh) is legal on any domain.' ?>
    </div>
  </div>
</main>

<script src="<?= BASE_URL ?>/assets/js/tools.js?v=<?= time() ?>"></script>
<?php require __DIR__ . '/templates/footer.php'; ?>