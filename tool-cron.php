<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $lang==='es' ? 'Analizador de Expresiones Cron — CyberEscudo' : 'Cron Expression Parser — CyberEscudo';
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
    <option value="<?= BASE_URL ?>/tool-recon.php" <?= $current_page==='tool-recon.php' ? 'selected' : '' ?>>🔍 <?= $lang==='es' ? 'Reconocimiento rápido OSINT' : 'OSINT Quick Recon' ?></option>
    <option value="<?= BASE_URL ?>/tool-ssh.php" <?= $current_page==='/tool-ssh.php' ? 'selected' : '' ?>>🔑 <?= $lang==='es' ? 'Analizador SSH' : 'SSH Analyzer' ?></option>
    <option value="<?= BASE_URL ?>/tool-ports.php" <?= $current_page==='/tool-ports.php' ? 'selected' : '' ?>>📋 <?= $lang==='es' ? 'Puertos de referencia' : 'Port Reference' ?></option>
    </select>
  </div>

  <div class="card">
    <div style="margin-bottom:1.5rem;border-bottom:1px solid var(--border);padding-bottom:1rem;">
      <h2 style="font-size:1.4rem;color:var(--white);margin-bottom:.5rem;">⏱ <?= $lang==='es' ? 'Analizador de Expresiones Cron' : 'Cron Expression Parser' ?></h2>
      <p style="color:var(--gray);font-size:.9rem;"><?= $lang==='es'
        ? 'Interpreta expresiones cron en lenguaje humano, muestra las próximas ejecuciones y detecta comandos potencialmente peligrosos.'
        : 'Parse cron expressions into human language, show next executions and detect dangerous cron patterns.' ?></p>
    </div>

    <div style="margin-bottom:1.5rem;">
      <label style="font-family:var(--mono);font-size:.75rem;color:var(--gray-dark);text-transform:uppercase;letter-spacing:.1em;display:block;margin-bottom:.5rem;"><?= $lang==='es' ? 'Expresión Cron (minuto hora día mes día_semana)' : 'Cron Expression (minute hour day month weekday)' ?></label>
      <div class="cyber-input-wrapper">
        <input type="text" id="cron-input" class="cyber-input" placeholder="*/5 * * * *" value="*/5 * * * *"
          style="font-family:var(--mono);font-size:1.1rem;letter-spacing:.05em;margin-bottom:0;">
      </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:.5rem;margin-bottom:1.75rem;" id="cron-fields">
      <?php foreach([
        [$lang==='es' ? 'MIN' : 'MIN', '0-59', '*/1-59'],
        [$lang==='es' ? 'HORA' : 'HOUR', '0-23', '0,12'],
        [$lang==='es' ? 'DÍA' : 'DAY', '1-31', '1,15'],
        [$lang==='es' ? 'MES' : 'MONTH', '1-12', '1-6'],
        [$lang==='es' ? 'SEM' : 'WEEK', '0-7', '1-5'],
      ] as $f): ?>
      <div style="text-align:center;">
        <div style="font-family:var(--mono);font-size:.65rem;color:var(--cyan);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.3rem;"><?= $f[0] ?></div>
        <div class="cron-field-box" style="background:rgba(0,255,255,.05);border:1px solid rgba(0,255,255,.15);border-radius:.4rem;padding:.5rem;font-family:var(--mono);font-size:.85rem;color:var(--white);">*</div>
        <div style="font-family:var(--mono);font-size:.62rem;color:var(--gray-dark);margin-top:.25rem;"><?= $f[1] ?></div>
      </div>
      <?php endforeach; ?>
    </div>

    <div style="padding:1rem 1.25rem;background:rgba(0,255,255,.04);border:1px solid rgba(0,255,255,.12);border-radius:.5rem;margin-bottom:1.25rem;">
      <div style="font-family:var(--mono);font-size:.7rem;color:var(--cyan);text-transform:uppercase;letter-spacing:.12em;margin-bottom:.4rem;"><?= $lang==='es' ? 'Descripción' : 'Description' ?></div>
      <div id="cron-desc" style="font-size:1rem;color:var(--white);font-weight:500;"></div>
    </div>

    <div style="margin-bottom:1.5rem;">
      <div style="font-family:var(--mono);font-size:.7rem;color:var(--gray-dark);text-transform:uppercase;letter-spacing:.12em;margin-bottom:.75rem;"><?= $lang==='es' ? 'Próximas 5 ejecuciones' : 'Next 5 executions' ?></div>
      <div id="cron-next" style="display:flex;flex-direction:column;gap:.4rem;"></div>
    </div>

    <div id="cron-alert" style="display:none;margin-bottom:1.25rem;padding:.75rem 1rem;background:rgba(255,80,80,.06);border:1px solid rgba(255,80,80,.2);border-radius:.45rem;font-family:var(--mono);font-size:.8rem;color:#ff6060;"></div>

    <div id="cron-error" style="display:none;padding:.75rem 1rem;background:rgba(255,160,0,.06);border:1px solid rgba(255,160,0,.2);border-radius:.45rem;font-family:var(--mono);font-size:.8rem;color:#f0a000;margin-bottom:1.25rem;"></div>

    <div style="padding-top:1.25rem;border-top:1px solid var(--border);">
      <div style="font-family:var(--mono);font-size:.7rem;color:var(--gray-dark);text-transform:uppercase;letter-spacing:.12em;margin-bottom:.75rem;"><?= $lang==='es' ? 'Ejemplos comunes' : 'Common examples' ?></div>
      <div style="display:flex;flex-wrap:wrap;gap:.5rem;">
        <?php
        $examples = [
          ['* * * * *',    $lang==='es'?'Cada minuto':'Every minute'],
          ['*/5 * * * *',  $lang==='es'?'Cada 5 minutos':'Every 5 minutes'],
          ['0 * * * *',    $lang==='es'?'Cada hora':'Every hour'],
          ['0 0 * * *',    $lang==='es'?'Diario a medianoche':'Daily at midnight'],
          ['0 9 * * 1-5',  $lang==='es'?'Lun-Vie a las 9:00':'Mon-Fri at 9:00'],
          ['0 0 * * 0',    $lang==='es'?'Domingos a medianoche':'Sundays at midnight'],
          ['0 0 1 * *',    $lang==='es'?'El 1 de cada mes':'1st of each month'],
          ['*/15 * * * *', $lang==='es'?'Cada 15 minutos':'Every 15 minutes'],
          ['0 2 * * *',    $lang==='es'?'Backup diario 2am':'Daily backup 2am'],
          ['* * * * * root rm -rf /', $lang==='es'?'⚠ PELIGROSO':'⚠ DANGEROUS'],
        ];
        foreach($examples as $ex):
        ?>
        <button type="button" class="cron-preset-btn" data-cron="<?= htmlspecialchars($ex[0]) ?>" 
          style="background:rgba(255,255,255,.04);border:1px solid var(--border);color:var(--gray);font-family:var(--mono);font-size:.73rem;padding:.4rem .7rem;border-radius:.3rem;cursor:pointer;transition:all .2s;">
          <span style="color:var(--cyan); pointer-events: none;"><?= htmlspecialchars($ex[0]) ?></span> — <span style="pointer-events: none;"><?= htmlspecialchars($ex[1]) ?></span>
        </button>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</main>

<script src="<?= BASE_URL ?>/assets/js/tools.js"></script>
<?php require __DIR__ . '/templates/footer.php'; ?>