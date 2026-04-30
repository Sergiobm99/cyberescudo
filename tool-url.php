<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $lang==='es' ? 'Codificador de URL — CyberEscudo' : 'URL Encoder — CyberEscudo';
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
    </select>
  </div>

  <div class="card">
    <div style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border); padding-bottom: 1rem;">
      <h2 style="font-size: 1.4rem; color: var(--white); margin-bottom: 0.5rem;">URL <?= $lang==='es' ? 'Codificador y Decodificador' : 'Encoder & Decoder' ?></h2>
      <p style="color: var(--gray); font-size: 0.9rem;"><?= $lang==='es' ? 'Codifica caracteres especiales en formato URL (ej. espacios a %20) o decodifica cadenas de vuelta a texto normal. El procesamiento se realiza de forma segura en tu navegador.' : 'Encode special characters into URL format (e.g. spaces to %20) or decode strings back to normal text. Processing is done safely in your browser.' ?></p>
    </div>

    <div class="cyber-input-wrapper" style="align-items: flex-start; margin-bottom: 1rem;">
      <textarea id="url-in" class="cyber-input" style="min-height:120px; resize:vertical;" placeholder="<?= $lang==='es' ? 'Escribe o pega aquí la URL o el texto...' : 'Type or paste URL or text here...' ?>"></textarea>
    </div>

    <div style="display:flex; gap:0.75rem; flex-wrap:wrap; margin-bottom: 2rem;">
      <button type="button" class="tool-btn" id="btn-url-enc">🔒 <?= $lang==='es' ? 'Codificar URL' : 'Encode URL' ?></button>
      <button type="button" class="tool-btn" id="btn-url-dec" style="background: rgba(255,255,255,0.05); border-color: var(--border); color: var(--white);">🔓 <?= $lang==='es' ? 'Decodificar URL' : 'Decode URL' ?></button>
    </div>

    <div style="position: relative;">
      <div style="font-family: var(--mono); font-size: 0.75rem; color: var(--gray); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.5rem;">
          <?= $lang==='es' ? 'Resultado:' : 'Output:' ?>
      </div>
      
      <button type="button" class="copy-btn" id="btn-url-copy" style="top: 1.8rem; right: 0.5rem;">📋 <?= $lang==='es' ? 'Copiar' : 'Copy' ?></button>
      
      <div id="url-out" class="tool-output" style="text-align: left; font-size: 1rem; min-height: 100px; padding-top: 2.5rem; word-break: break-all; white-space: pre-wrap;"></div>
    </div>

  </div>
</main>

<script nonce="<?= e($cspNonce) ?>">
document.addEventListener('DOMContentLoaded', function() {
    var toolSwitcher = document.getElementById('tool-switcher');
    if (toolSwitcher) {
        toolSwitcher.addEventListener('change', function() {
            if (this.value) window.location.href = this.value;
        });
    }
});
</script>
<script src="<?= BASE_URL ?>/assets/js/tools.js"></script>
<?php require __DIR__ . '/templates/footer.php'; ?>