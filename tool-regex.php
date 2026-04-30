<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $lang==='es' ? 'Generador Regex de Contraseñas — CyberEscudo' : 'Password Regex Generator — CyberEscudo';
$current_page = basename($_SERVER['PHP_SELF']); 
require __DIR__ . '/templates/header.php';
?>

<main class="content-page">
  <div style="margin-bottom: 2rem;">
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
    </select>
  </div>

  <div class="card">
    <div style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border); padding-bottom: 1rem;">
      <h2 style="font-size: 1.4rem; color: var(--white); margin-bottom: 0.5rem;">🛡️ <?= $lang==='es' ? 'Generador de Políticas Regex' : 'Regex Policy Generator' ?></h2>
      <p style="color: var(--gray); font-size: 0.9rem;"><?= $lang==='es' ? 'Selecciona las reglas de tu contraseña y obtén la Expresión Regular (Regex) exacta para validarla en tu servidor o aplicación.' : 'Select your password rules and get the exact Regular Expression (Regex) to validate it on your server or app.' ?></p>
    </div>

    <div class="passgen-controls">
      <label class="ctrl-row">
        <span><?= $lang==='es' ? 'Longitud Mínima:' : 'Min Length:' ?> <strong id="regex-len-val" class="cyan-text">8</strong></span>
        <input type="range" min="4" max="64" value="8" id="regex-len">
      </label>
      
      <div class="ctrl-checks">
        <label><input type="checkbox" class="rx-rule" id="rx-lower" checked> <?= $lang==='es' ? 'Minúsculas (a-z)' : 'Lowercase (a-z)' ?></label>
        <label><input type="checkbox" class="rx-rule" id="rx-upper" checked> <?= $lang==='es' ? 'Mayúsculas (A-Z)' : 'Uppercase (A-Z)' ?></label>
        <label><input type="checkbox" class="rx-rule" id="rx-num" checked> <?= $lang==='es' ? 'Números (0-9)' : 'Numbers (0-9)' ?></label>
        <label><input type="checkbox" class="rx-rule" id="rx-sym" checked> <?= $lang==='es' ? 'Símbolos (!@#$)' : 'Symbols (!@#$)' ?></label>
        <label><input type="checkbox" class="rx-rule" id="rx-space"> <?= $lang==='es' ? 'Bloquear espacios' : 'No whitespace' ?></label>
      </div>
    </div>

    <div style="position: relative; margin-top: 1.5rem;">
      <div style="font-family: var(--mono); font-size: 0.75rem; color: var(--gray); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.5rem;">
          <?= $lang==='es' ? 'Código Regex Generado:' : 'Generated Regex Code:' ?>
      </div>
      <button type="button" class="copy-btn" id="btn-regex-copy" style="top: 1.8rem; right: 0.5rem;">📋 <?= $lang==='es' ? 'Copiar' : 'Copy' ?></button>
      <div id="regex-out" class="tool-output" style="font-family: var(--mono); color: var(--cyan); padding: 1.5rem; word-break: break-all; min-height: 80px;"></div>
    </div>

    <div style="margin-top: 2.5rem; border-top: 1px dashed var(--border); padding-top: 1.5rem;">
      <h3 style="font-size: 1.1rem; color: var(--white); margin-bottom: 1rem;">🧪 <?= $lang==='es' ? 'Prueba tu Regex' : 'Test your Regex' ?></h3>
      <div class="cyber-input-wrapper">
        <input type="text" id="regex-test" class="cyber-input" placeholder="<?= $lang==='es' ? 'Escribe una contraseña para probar...' : 'Type a password to test...' ?>" autocomplete="off">
        <div id="regex-indicator" style="position: absolute; right: 1.25rem; font-size: 1.5rem; transition: all 0.3s;"></div>
      </div>
      <p id="regex-test-msg" style="font-family: var(--mono); font-size: 0.85rem; color: var(--gray);"></p>
    </div>

  </div>
</main>

<script src="<?= BASE_URL ?>/assets/js/tools.js"></script>
<?php require __DIR__ . '/templates/footer.php'; ?>