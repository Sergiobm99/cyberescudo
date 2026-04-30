<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $lang==='es' ? 'Multi Decoder — CyberEscudo' : 'Multi Decoder — CyberEscudo';
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
      <option value="<?= BASE_URL ?>/tool-cve.php" <?= $current_page==='tool-cve.php' ? 'selected' : '' ?>>🐛 <?= $lang==='es' ? 'Buscador CVE y Exploits' : 'CVE & Exploit Finder' ?></option>
    </select>
  </div>

  <div class="card">
    <div class="tool-header md-container">
      <h2 style="font-size:1.4rem;color:var(--white);margin-bottom:.5rem;">🔄 Multi Decoder</h2>
      <p style="color:var(--gray);font-size:.9rem;"><?= $lang==='es'
        ? 'Detecta y decodifica encodings encadenados automáticamente. Útil en CTFs, análisis de WAF bypass y ofuscación de malware.'
        : 'Automatically detects and decodes chained encodings. Useful for CTFs, WAF bypass analysis and malware obfuscation.' ?></p>
    </div>

    <!-- Input -->
    <div class="md-container">
      <label class="info-card-label"><?= $lang==='es'?'Texto a decodificar':'Text to decode' ?></label>
      <textarea id="md-input" class="cyber-input md-textarea" rows="5" 
        placeholder="JTNDc2NyaXB0JTNFYWxlcnQoMSklM0MlMkZzY3JpcHQlM0U="></textarea>
    </div>

    <!-- Mode buttons -->
    <div class="md-container">
      <label class="info-card-label md-label-mb"><?= $lang==='es'?'Modo de Decodificación':'Decoding Mode' ?></label>
      <div class="md-flex-wrap" id="md-modes">
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

    <div class="md-flex-wrap-lg">
      <button type="button" id="btn-md-decode" class="tool-btn">
        🔍 <?= $lang==='es'?'Decodificar':'Decode' ?>
      </button>
      <button type="button" id="btn-md-autochain" class="tool-btn md-btn-autochain">
        ⛓ <?= $lang==='es'?'Auto-desencadenar (máx. 10 capas)':'Auto-unchain (max 10 layers)' ?>
      </button>
    </div>

    <!-- Steps chain -->
    <div id="md-chain"></div>

    <!-- Quick payloads -->
    <div class="md-examples-section">
      <div class="info-card-label md-label-mb"><?= $lang==='es'?'Payloads de ejemplo (haz clic para cargar)':'Example payloads (click to load)' ?></div>
      <div class="md-flex-wrap">
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

<script src="<?= BASE_URL ?>/assets/js/tools.js?v=<?= time() ?>"></script>
<?php require __DIR__ . '/templates/footer.php'; ?>