<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $lang==='es' ? 'Analizador de Logs de Seguridad — CyberEscudo' : 'Security Log Analyzer — CyberEscudo';
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
      <!-- Menú estandarizado -->
      <option value="<?= BASE_URL ?>/tool-headers.php" <?= $current_page==='tool-headers.php' ? 'selected' : '' ?>>📋 <?= $lang==='es' ? 'Analizador Headers' : 'Header Analyzer' ?></option>
      <option value="<?= BASE_URL ?>/tool-waf.php" <?= $current_page==='tool-waf.php' ? 'selected' : '' ?>>🛡️ WAF Bypass Payloads</option>
      <option value="<?= BASE_URL ?>/tool-cloud.php" <?= $current_page==='tool-cloud.php' ? 'selected' : '' ?>>☁️ Cloud Enum</option>
      <option value="<?= BASE_URL ?>/tool-loganalyzer.php" <?= $current_page==='tool-loganalyzer.php' ? 'selected' : '' ?>>📊 <?= $lang==='es' ? 'Analizador de Logs' : 'Log Analyzer' ?></option>
    </select>
  </div>

  <div class="card">
    <div class="tool-header md-container">
      <h2>📊 <?= $lang==='es' ? 'Analizador de Logs de Seguridad' : 'Security Log Analyzer' ?></h2>
      <p><?= $lang==='es'
        ? 'Pega logs de Apache, Nginx, auth.log o cualquier log web y detecta automáticamente ataques: SQLi, XSS, bruteforce, escaneos, LFI/RFI, bots y mucho más.'
        : 'Paste Apache, Nginx, auth.log or any web log and automatically detect attacks: SQLi, XSS, bruteforce, scans, LFI/RFI, bots and more.' ?></p>
    </div>

    <!-- Filtros de Tipo de Log -->
    <div class="md-flex-wrap m-bottom-1">
      <?php foreach([
        ['apache','Apache/Nginx'],
        ['auth','auth.log (SSH)'],
        ['iis','IIS'],
        ['auto', $lang==='es'?'Auto-detectar':'Auto-detect'],
      ] as $t): ?>
      <button type="button" data-ltype="<?= $t[0] ?>" class="dns-quick-btn log-type-btn <?= $t[0]==='auto' ? 'active' : '' ?>">
        <?= $t[1] ?>
      </button>
      <?php endforeach; ?>
    </div>

    <!-- Área de Input -->
    <textarea id="log-input" class="cyber-input md-textarea" rows="12" style="font-size:0.8rem; line-height:1.6; margin-bottom:1.5rem;"
      placeholder='192.168.1.100 - - [29/Apr/2026:10:23:15 +0000] "GET /admin/login.php?id=1 UNION SELECT 1,2,3-- HTTP/1.1" 200 4523 "-" "sqlmap/1.7.8"&#10;10.0.0.5 - - [29/Apr/2026:10:23:18 +0000] "GET /../../../etc/passwd HTTP/1.1" 404 217&#10;45.33.32.156 - - [29/Apr/2026:10:23:20 +0000] "POST /wp-login.php HTTP/1.1" 200 2718 "-" "python-requests/2.28"'></textarea>

    <!-- Botones de Acción -->
    <div class="md-flex-wrap-lg" style="align-items: center;">
      <button type="button" id="btn-log-analyze" class="tool-btn">
        🔍 <?= $lang==='es'?'Analizar Logs':'Analyze Logs' ?>
      </button>
      <button type="button" id="btn-log-example" class="dns-quick-btn">
        <?= $lang==='es'?'Cargar ejemplo':'Load example' ?>
      </button>
      <span id="log-stats" class="md-text-layer" style="font-size: 0.85rem; margin-left: 0.5rem;"></span>
    </div>

    <!-- Resultados -->
    <div id="log-results"></div>
  </div>
</main>

<script src="<?= BASE_URL ?>/assets/js/tools.js?v=<?= time() ?>"></script>
<?php require __DIR__ . '/templates/footer.php'; ?>