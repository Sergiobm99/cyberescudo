<?php
require_once __DIR__ . '/bootstrap.php';

// --- INICIO DEL MOTOR DNS Y DETECCIÓN (API PROXY) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['api'])) {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    $domains = $input['domains'] ?? [];
    $results = [];

    // Base de datos de firmas de Takeover (CNAMEs vulnerables)
    $providers = [
        'github.io' => ['name' => 'GitHub Pages', 'severity' => 'high'],
        'herokuapp.com' => ['name' => 'Heroku', 'severity' => 'high'],
        's3.amazonaws.com' => ['name' => 'AWS S3 Bucket', 'severity' => 'high'],
        'azurewebsites.net' => ['name' => 'Azure Web App', 'severity' => 'high'],
        'cloudapp.net' => ['name' => 'Azure CloudApp', 'severity' => 'high'],
        'trafficmanager.net' => ['name' => 'Azure Traffic Manager', 'severity' => 'high'],
        'myshopify.com' => ['name' => 'Shopify', 'severity' => 'medium'],
        'domains.tumblr.com' => ['name' => 'Tumblr', 'severity' => 'medium'],
        'cargocollective.com' => ['name' => 'Cargo', 'severity' => 'medium'],
        'wpengine.com' => ['name' => 'WP Engine', 'severity' => 'medium']
    ];

    foreach ($domains as $domain) {
        $domain = trim($domain);
        if (empty($domain)) continue;
        
        // Validación básica de dominio para evitar inyecciones
        if (!preg_match('/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i', $domain)) {
            $results[] = ['domain' => $domain, 'error' => 'Formato inválido'];
            continue;
        }

        // Consultamos el registro CNAME
        $records = @dns_get_record($domain, DNS_CNAME);
        if ($records && count($records) > 0) {
            $cname = $records[0]['target'];
            $vulnerable = false;
            $providerName = '';
            $severity = '';

            // Comprobamos si el CNAME apunta a un servicio Cloud conocido
            foreach ($providers as $sig => $info) {
                if (str_ends_with(strtolower($cname), $sig)) {
                    $vulnerable = true;
                    $providerName = $info['name'];
                    $severity = $info['severity'];
                    break;
                }
            }

            $results[] = [
                'domain' => $domain,
                'cname' => $cname,
                'vulnerable' => $vulnerable,
                'provider' => $providerName,
                'severity' => $severity
            ];
        } else {
            $results[] = [
                'domain' => $domain,
                'cname' => 'Sin registro CNAME',
                'vulnerable' => false
            ];
        }
    }
    echo json_encode($results);
    exit;
}
// --- FIN DEL MOTOR DNS ---

$pageTitle = $lang==='es' ? 'Subdomain Takeover — CyberEscudo' : 'Subdomain Takeover — CyberEscudo';
$current_page = basename($_SERVER['PHP_SELF']);
require __DIR__ . '/templates/header.php';
?>

<main class="content-page">
  <div class="m-bottom-2">
    <span class="section-label"><?= $lang==='es' ? '// AUDITORÍA CLOUD' : '// CLOUD AUDIT' ?></span>
    <h1><?= $lang==='es' ? 'Subdomain Takeover Assistant' : 'Subdomain Takeover Assistant' ?></h1>
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
    <div class="tool-header md-container">
      <h2>🏴‍☠️ Subdomain Takeover Assistant</h2>
      <p><?= $lang==='es'
        ? 'Analiza una lista de subdominios para extraer sus registros CNAME y cruza los datos con firmas de proveedores Cloud (AWS, Azure, GitHub Pages) en busca de posibles secuestros de infraestructura.'
        : 'Analyzes a list of subdomains to extract their CNAME records and cross-references the data with Cloud provider signatures (AWS, Azure, GitHub Pages) looking for potential infrastructure hijacks.' ?></p>
    </div>

    <!-- Interfaz de Entrada -->
    <div class="m-bottom-2">
      <label class="info-card-label"><?= $lang==='es' ? 'Lista de subdominios (Uno por línea)' : 'Subdomain list (One per line)' ?></label>
      <textarea id="takeover-input" class="cyber-input" rows="6" placeholder="blog.tu-empresa.com&#10;shop.tu-empresa.com&#10;dev.tu-empresa.com"></textarea>
      
      <div style="display: flex; gap: 1rem; margin-top: 1rem; flex-wrap: wrap;">
        <button type="button" id="btn-takeover-scan" class="tool-btn">
          🔍 <?= $lang==='es' ? 'Analizar Subdominios' : 'Scan Subdomains' ?>
        </button>
        <button type="button" id="btn-takeover-example" class="tool-btn" style="background: transparent; border: 1px solid var(--border);">
          📋 <?= $lang==='es' ? 'Cargar Ejemplo' : 'Load Example' ?>
        </button>
      </div>
    </div>

    <!-- Panel de Resultados -->
    <div id="takeover-status" class="m-bottom-1" style="font-family: var(--mono); font-size: 0.85rem; color: var(--gray);"></div>
    <div id="takeover-results" class="takeover-grid"></div>
  </div>
</main>

<script src="<?= BASE_URL ?>/assets/js/tools.js?v=<?= time() ?>"></script>
<?php require __DIR__ . '/templates/footer.php'; ?>