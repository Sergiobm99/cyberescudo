<?php
require_once __DIR__ . '/bootstrap.php';

// --- INICIO DEL PROXY API PARA NIST ---
if (isset($_GET['api_cve'])) {
    header('Content-Type: application/json');
    $query = urlencode($_GET['api_cve']);
    $url = "https://services.nvd.nist.gov/rest/json/cves/2.0?keywordSearch={$query}&resultsPerPage=15";
    
    // Configuramos la petición para que parezca un servidor legítimo
    $options = [
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: CyberEscudo-Security-Tool/1.0',
                'Accept: application/json'
            ]
        ]
    ];
    $context = stream_context_create($options);
    
    // Hacemos la petición de forma silenciosa (@) para controlar los errores
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        http_response_code(502); // Bad Gateway
        echo json_encode(['error' => 'El servidor del NIST rechazó la conexión o está saturado.']);
        exit;
    }
    
    echo $response;
    exit;
}
// --- FIN DEL PROXY API ---

$pageTitle = $lang==='es' ? 'Buscador de CVE y Exploits — CyberEscudo' : 'CVE & Exploit Finder — CyberEscudo';
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
      <option value="<?= BASE_URL ?>/tool-cloud.php" <?= $current_page==='tool-cloud.php' ? 'selected' : '' ?>>☁️ Cloud Enum</option>
      <option value="<?= BASE_URL ?>/tool-loganalyzer.php" <?= $current_page==='tool-loganalyzer.php' ? 'selected' : '' ?>>📊 Log Analyzer</option>
      <option value="<?= BASE_URL ?>/tool-cve.php" <?= $current_page==='tool-cve.php' ? 'selected' : '' ?>>🐛 <?= $lang==='es' ? 'Buscador CVE y Exploits' : 'CVE & Exploit Finder' ?></option>
    </select>
  </div>

  <div class="card">
    <div class="tool-header md-container">
      <h2>🐛 CVE & Exploit Finder</h2>
      <p><?= $lang==='es'
        ? 'Busca vulnerabilidades conocidas por software o versión en la base de datos oficial del NIST (NVD). Genera enlaces directos a exploits y Pruebas de Concepto (PoC).'
        : 'Search for known vulnerabilities by software or version in the official NIST database (NVD). Generates direct links to exploits and Proofs of Concept (PoC).' ?></p>
    </div>

    <!-- Buscador -->
    <div class="cve-search-box m-bottom-2">
      <div style="flex: 1;">
        <label class="info-card-label"><?= $lang==='es'?'Software o versión (Ej: Apache 2.4.49, Log4j)':'Software or version (e.g. Apache 2.4.49, Log4j)' ?></label>
        <input type="text" id="cve-input" class="cyber-input" placeholder="Apache 2.4.49..." style="margin-bottom:0;">
      </div>
      <button type="button" id="btn-cve-search" class="tool-btn" style="height: 42px; margin-top: auto;">
        🔍 <?= $lang==='es'?'Buscar Vulnerabilidades':'Search Vulnerabilities' ?>
      </button>
    </div>

    <!-- Resultados -->
    <div id="cve-status" class="m-bottom-1" style="font-family: var(--mono); font-size: 0.85rem; color: var(--gray);"></div>
    <div id="cve-results"></div>
  </div>
</main>

<script src="<?= BASE_URL ?>/assets/js/tools.js?v=<?= time() ?>"></script>
<?php require __DIR__ . '/templates/footer.php'; ?>