<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $lang === 'es' ? 'Cloud Enum Cheatsheet — CyberEscudo' : 'Cloud Enum Cheatsheet — CyberEscudo';
$current_page = basename($_SERVER['PHP_SELF']);
require __DIR__ . '/templates/header.php';
?>

<main class="content-page">
  <div class="m-bottom-2">
    <span class="section-label"><?= $lang === 'es' ? '// HERRAMIENTAS' : '// TOOLS' ?></span>
    <h1><?= $lang === 'es' ? 'Herramientas de Seguridad' : 'Security Tools' ?></h1>
  </div>

  <div class="tool-select-wrapper">
    <select id="tool-switcher" class="tool-selector">
      <option value="" disabled>-- <?= $lang === 'es' ? 'Selecciona una herramienta' : 'Select a tool' ?> --</option>
      <!-- Asegúrate de añadir las demás herramientas aquí si las necesitas -->
      <option value="<?= BASE_URL ?>/tool-headers.php">📋 <?= $lang === 'es' ? 'Analizador Headers' : 'Header Analyzer' ?></option>
      <option value="<?= BASE_URL ?>/tool-waf.php">🛡️ WAF Bypass</option>
      <option value="<?= BASE_URL ?>/tool-cloud.php" selected>☁️ Cloud Enum</option>
    </select>
  </div>

  <div class="card">
    <div class="tool-header md-container">
      <h2>☁️ Cloud Enum Cheatsheet</h2>
      <p><?= $lang === 'es'
        ? 'Comandos de enumeración interactivos para Azure, AWS y GCP. Introduce el nombre del tenant/cuenta y genera comandos listos para ejecutar en tu terminal.'
        : 'Interactive enumeration commands for Azure, AWS and GCP. Enter the tenant/account name and generate commands ready to run on your terminal.' ?></p>
    </div>

    <!-- Controles Cloud -->
    <div class="cloud-grid m-bottom-1-5">
      <div>
        <label class="info-card-label"><?= $lang === 'es' ? 'Plataforma' : 'Platform' ?></label>
        <div class="cloud-tabs-container" id="cloud-tabs">
          <button type="button" data-cloud="azure" class="cloud-tab-btn active">Azure</button>
          <button type="button" data-cloud="aws" class="cloud-tab-btn">AWS</button>
          <button type="button" data-cloud="gcp" class="cloud-tab-btn">GCP</button>
        </div>
      </div>
      <div>
        <label class="info-card-label" id="target-label">Tenant / dominio objetivo</label>
        <input type="text" id="cloud-target" class="cyber-input" placeholder="empresa.onmicrosoft.com" style="margin-bottom:0;">
      </div>
    </div>

    <!-- Píldoras de Sección -->
    <div class="cloud-pills-container m-bottom-1-5" id="section-pills">
        <!-- Rellenado por JS -->
    </div>

    <!-- Lista de Comandos -->
    <div id="cloud-cmds"></div>
  </div>
</main>

<script src="<?= BASE_URL ?>/assets/js/tools.js?v=<?= time() ?>"></script>
<?php require __DIR__ . '/templates/footer.php'; ?>