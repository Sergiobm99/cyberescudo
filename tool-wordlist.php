<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $lang==='es' ? 'Generador de Wordlist — CyberEscudo' : 'Wordlist Generator — CyberEscudo';
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
      <option value="<?= BASE_URL ?>/tool-cve.php" <?= $current_page==='tool-cve.php' ? 'selected' : '' ?>>🐛 <?= $lang==='es' ? 'Buscador CVE y Exploits' : 'CVE & Exploit Finder' ?></option>
      <option value="<?= BASE_URL ?>/tool-takeover.php" <?= $current_page==='tool-takeover.php' ? 'selected' : '' ?>>🏴‍☠️ <?= $lang==='es' ? 'Auditoría / Bug Bounty' : 'Subdomain Takeover' ?></option>
    </select>
  </div>

  <div class="card">
    <div class="tool-header">
      <h2 style="font-size:1.4rem;color:var(--white);margin-bottom:.5rem;">📝 <?= $lang==='es'?'Generador de Wordlist':'Wordlist Generator' ?></h2>
      <p style="color:var(--gray);font-size:.9rem;"><?= $lang==='es'
        ? 'Genera diccionarios personalizados para fuerza bruta. Introduce palabras clave (empresa, nombres) y aplica mutaciones automáticas comunes.'
        : 'Generate custom wordlists for dictionary attacks. Enter keywords (company, names) and apply automatic common mutations.' ?></p>
    </div>

    <div class="wordlist-grid">
      <div style="flex: 1; min-width: 250px;">
        <label class="info-card-label"><?= $lang==='es'?'Palabras clave (una por línea)':'Keywords (one per line)' ?></label>
        <textarea id="wl-keywords" class="cyber-input" rows="12" style="resize:vertical;"
          placeholder="empresa&#10;admin&#10;soporte&#10;verano&#10;madrid"></textarea>
      </div>
      
      <div style="flex: 1; min-width: 250px; display:flex; flex-direction:column; gap:1.25rem;">
        <div>
          <label class="info-card-label"><?= $lang==='es'?'Años a incluir (separados por coma)':'Years to include (comma separated)' ?></label>
          <input type="text" id="wl-years" class="cyber-input" placeholder="2022,2023,2024,2025" value="2023,2024,2025">
        </div>
        <div>
          <label class="info-card-label"><?= $lang==='es'?'Sufijos comunes':'Common suffixes' ?></label>
          <input type="text" id="wl-suffixes" class="cyber-input" placeholder="123,!,@,#,1234" value="123,!,@,1234,#">
        </div>
        <div>
          <label class="info-card-label" style="margin-bottom:0.75rem;"><?= $lang==='es'?'Mutaciones y Variaciones':'Mutations and Variations' ?></label>
          <div class="wl-mutations-box">
            <?php foreach([
              ['wl-m-case',    $lang==='es'?'Capitalizar (Empresa, EMPRESA)':'Capitalise (Company, COMPANY)'],
              ['wl-m-leet',    $lang==='es'?'Leetspeak (a→4, e→3, o→0, i→1)':'Leetspeak (a→4, e→3, o→0, i→1)'],
              ['wl-m-years',   $lang==='es'?'Combinar con años (empresa2024)':'Combine with years (company2024)'],
              ['wl-m-suffixes',$lang==='es'?'Combinar con sufijos (empresa123)':'Combine with suffixes (company123)'],
              ['wl-m-special', $lang==='es'?'Variantes especiales (@empresa, empresa.)':'Special variants (@company, company.)'],
            ] as $m): ?>
            <label class="wl-checkbox-label">
              <input type="checkbox" id="<?= $m[0] ?>" checked>
              <span><?= $m[1] ?></span>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

    <div style="display:flex; gap:1rem; margin-bottom:1.5rem; align-items:center; flex-wrap:wrap;">
      <button type="button" id="btn-wl-generate" class="tool-btn" style="height: 48px;">
        ⚡ <?= $lang==='es'?'Generar Wordlist':'Generate Wordlist' ?>
      </button>
      <button type="button" id="btn-wl-download" class="dns-quick-btn" style="display:none; height: 48px; border-color:var(--cyan) !important;">
        ⬇ <?= $lang==='es'?'Descargar .txt':'Download .txt' ?>
      </button>
      <span id="wl-count" style="font-family:var(--mono); font-size:0.85rem; color:var(--cyan);"></span>
    </div>

    <div id="wl-output-wrap" style="display:none;">
      <label class="info-card-label"><?= $lang==='es'?'Wordlist generada (Previsualización máx. 300)':'Generated wordlist (Preview max 300)' ?></label>
      <textarea id="wl-output" class="cyber-input" rows="12" readonly style="resize:vertical; background:rgba(0,0,0,0.6); color:var(--gray);"></textarea>
    </div>
  </div>
</main>

<script src="<?= BASE_URL ?>/assets/js/tools.js"></script>
<?php require __DIR__ . '/templates/footer.php'; ?>