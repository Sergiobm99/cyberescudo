<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $lang==='es' ? 'Multi Decoder — CyberEscudo' : 'Multi Decoder — CyberEscudo';
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
    </select>
  </div>

  <div class="card">
    <div class="tool-header">
      <h2 style="font-size:1.4rem;color:var(--white);margin-bottom:.5rem;">🔄 Multi Decoder</h2>
      <p style="color:var(--gray);font-size:.9rem;"><?= $lang==='es'
        ? 'Detecta y decodifica encodings encadenados automáticamente. Útil en CTFs, análisis de WAF bypass y ofuscación de malware.'
        : 'Automatically detects and decodes chained encodings. Useful for CTFs, WAF bypass analysis and malware obfuscation.' ?></p>
    </div>

    <div style="margin-bottom:1.5rem;">
      <label class="info-card-label"><?= $lang==='es'?'Texto a decodificar':'Text to decode' ?></label>
      <textarea id="md-input" class="cyber-input" rows="5" style="resize:vertical;"
        placeholder="JTNDc2NyaXB0JTNFYWxlcnQoMSklM0MlMkZzY3JpcHQlM0U="></textarea>
    </div>

    <div style="margin-bottom:1.5rem;">
      <label class="info-card-label" style="margin-bottom:0.75rem;"><?= $lang==='es'?'Modo de Decodificación':'Decoding Mode' ?></label>
      <div style="display:flex;flex-wrap:wrap;gap:0.5rem;" id="md-modes">
        <?php foreach([
          ['auto',   $lang==='es'?'🤖 Auto-detectar':'🤖 Auto-detect'],
          ['base64', '🔄 Base64'],
          ['url',    '🔗 URL'],
          ['html',   '🌐 HTML Entities'],
          ['hex',    '🔢 Hex'],
          ['rot13',  '🔤 ROT13'],
          ['unicode','✨ Unicode Escape'],
          ['jwt',    '🔓 JWT'],
        ] as $m): ?>
        <button type="button" data-mode="<?= $m[0] ?>" class="dns-quick-btn md-mode-btn <?= $m[0]==='auto' ? 'active' : '' ?>">
          <?= $m[1] ?>
        </button>
        <?php endforeach; ?>
      </div>
    </div>

    <div style="display:flex; gap:1rem; margin-bottom:2rem; flex-wrap:wrap;">
      <button type="button" id="btn-md-decode" class="tool-btn">
        🔍 <?= $lang==='es'?'Decodificar':'Decode' ?>
      </button>
      <button type="button" id="btn-md-autochain" class="tool-btn" style="background:rgba(0,255,255,.08); border-color:var(--cyan);">
        ⛓ <?= $lang==='es'?'Auto-desencadenar (máx. 10 capas)':'Auto-unchain (max 10 layers)' ?>
      </button>
    </div>

    <div id="md-chain"></div>

    <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px solid var(--border);">
      <div class="info-card-label" style="margin-bottom:1rem;"><?= $lang==='es'?'Payloads de ejemplo (haz clic para cargar)':'Example payloads (click to load)' ?></div>
      <div style="display:flex; flex-wrap:wrap; gap:0.5rem;">
        <?php
        $examples = [
          ['XSS Base64+URL', 'JTNDc2NyaXB0JTNFYWxlcnQoJ1hTUycpJTNDJTJGc2NyaXB0JTNF'],
          ['HTML Entity XSS', '&lt;script&gt;alert(1)&lt;/script&gt;'],
          ['Double URL Enc.', '%2527%2520OR%25201%253D1'],
          ['Hex string', '48656c6c6f20576f726c64'],
          ['Unicode escape', '\u003cscript\u003ealert(1)\u003c/script\u003e'],
          ['ROT13', 'Uryyb, jbeyq! Guvf vf n grfg.'],
          ['JWT (decode)',  'eyJhbGciOiJub25lIiwidHlwIjoiSldUIn0.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkFkbWluIiwicm9sZSI6ImFkbWluIn0.'],
        ];
        foreach($examples as $ex): ?>
        <button type="button" class="dns-quick-btn md-example-btn" data-payload="<?= htmlspecialchars($ex[1]) ?>">
            <?= htmlspecialchars($ex[0]) ?>
        </button>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</main>

<script src="<?= BASE_URL ?>/assets/js/tools.js"></script>
<?php require __DIR__ . '/templates/footer.php'; ?>