<?php
require_once __DIR__ . '/bootstrap.php';

$pageTitle = $lang === 'es' ? 'Buscador CVE y Exploits — CyberEscudo' : 'CVE & Exploit Finder — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Rastrea y busca vulnerabilidades CVE en la base de datos global en tiempo real.' : 'Track and search CVE vulnerabilities in the global database in real-time.';
$current_page = basename($_SERVER['PHP_SELF']);
require __DIR__ . '/templates/header.php';
?>

<main class="content-page">
  <div style="margin-bottom: 2rem;">
    <span class="section-label"><?= $lang==='es' ? '// VULNERABILIDADES' : '// VULNERABILITIES' ?></span>
    <h1><?= $lang==='es' ? 'Buscador de CVE y Exploits' : 'CVE & Exploit Finder' ?></h1>
    <p style="color: var(--gray); font-size: 0.95rem; margin-top: 0.5rem;">
      <?= $lang==='es' ? 'Consulta la base de datos oficial (Conexión Cliente-a-API) para descubrir detalles y pruebas de concepto (PoC).' : 'Query the official database (Client-to-API Connection) to discover details and Proofs of Concept (PoC).' ?>
    </p>
  </div>

  <div class="tool-select-wrapper">
    <!-- Tu selector habitual de herramientas -->
    <select id="tool-switcher" class="tool-selector">
        <option value="<?= BASE_URL ?>/tool-cve.php" selected>🐛 <?= $lang==='es' ? 'Buscador CVE y Exploits' : 'CVE & Exploit Finder' ?></option>
        <option value="<?= BASE_URL ?>/tool-ip.php">🌐 <?= $lang==='es' ? '¿Cuál es mi IP?' : 'What is my IP?' ?></option>
        <!-- Puedes añadir el resto de options aquí abajo como los tenías -->
    </select>
  </div>

  <div class="card">
    <div class="cve-search-box" style="margin-bottom: 1.5rem;">
      <input type="text" id="cve-input" class="cyber-input" placeholder="<?= $lang==='es' ? 'Ej: CVE-2024-12345 o apache...' : 'E.g: CVE-2024-12345 or apache...' ?>" style="flex: 1;">
      <button id="btn-cve-search" class="tool-btn">🔍 <?= $lang==='es' ? 'BUSCAR' : 'SEARCH' ?></button>
    </div>

    <!-- AQUÍ ESTÁ EL TEXTO INICIAL POR DEFECTO -->
    <div id="cve-status" style="font-family: var(--mono); font-size: 0.85rem; margin-bottom: 1.5rem; color: var(--cyan);">
      ⏳ <?= $lang==='es' ? 'Iniciando sistema y a la espera...' : 'System starting, please wait...' ?>
    </div>

    <div id="cve-results">
      <!-- Los resultados de JavaScript aparecerán aquí -->
    </div>
  </div>
</main>

<!-- IMPORTANTE: Usa el time() en desarrollo para romper la maldita caché de Nginx -->
<script src="<?= BASE_URL ?>/assets/js/tools.js?v=<?= time() ?>" nonce="<?= e($cspNonce) ?>"></script>

<?php require __DIR__ . '/templates/footer.php'; ?>
