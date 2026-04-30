<?php
// --- PROXY API PARA SALTARSE EL BLOQUEO CORS/ADBLOCK ---
if (isset($_GET['api_mac'])) {
    header('Content-Type: application/json');
    $mac = trim($_GET['api_mac']);
    $url = "https://api.maclookup.app/v2/macs/" . urlencode($mac);
    
    // Fingimos ser un servidor legítimo para que la API nos responda
    $options = [
        'http' => [
            'method' => 'GET',
            'header' => "User-Agent: CyberEscudo-Server/1.0\r\n"
        ]
    ];
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    
    if ($response) {
        echo $response;
    } else {
        echo json_encode(['success' => false, 'error' => 'API Connection failed']);
    }
    exit; // Detenemos la carga para enviar solo los datos JSON puros
}
// -------------------------------------------------------

require_once __DIR__ . '/bootstrap.php';
$pageTitle = $lang==='es' ? 'Buscador de Fabricante MAC — CyberEscudo' : 'MAC Vendor Lookup — CyberEscudo';
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
    <div style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border); padding-bottom: 1rem;">
      <h2 style="font-size: 1.4rem; color: var(--white); margin-bottom: 0.5rem;">🏷️ <?= $lang==='es' ? 'Buscador de Fabricante MAC (OUI)' : 'MAC Vendor Lookup (OUI)' ?></h2>
      <p style="color: var(--gray); font-size: 0.9rem;"><?= $lang==='es' ? 'Introduce una dirección MAC para descubrir qué empresa fabricó la tarjeta de red (Cisco, Apple, Intel...). Ideal para identificar dispositivos desconocidos en tu red.' : 'Enter a MAC address to discover which company manufactured the network card. Great for identifying unknown devices on your network.' ?></p>
    </div>

    <div class="cyber-input-wrapper" style="margin-bottom: 1rem;">
      <input type="text" id="mac-input" class="cyber-input" placeholder="<?= $lang==='es' ? 'Ejemplo: 00:1A:2B:3C:4D:5E' : 'Example: 00:1A:2B:3C:4D:5E' ?>" autocomplete="off" style="text-transform: uppercase;">
    </div>
    
    <button type="button" class="tool-btn" id="btn-mac-search" style="margin-bottom: 2rem;">⚡ <?= $lang==='es' ? 'Buscar Fabricante' : 'Lookup Vendor' ?></button>

    <div id="mac-results" style="display: none; background: rgba(0,0,0,0.4); border: 1px solid var(--border); border-radius: 0.5rem; padding: 1.5rem;">
        <div style="font-family: var(--mono); font-size: 0.75rem; color: var(--gray); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.5rem;">
            <?= $lang==='es' ? 'Resultado de la búsqueda:' : 'Search Result:' ?>
        </div>
        <div id="mac-vendor-name" style="font-family: var(--font); font-size: 1.4rem; color: var(--cyan); margin-bottom: 1.5rem; font-weight: bold;">
        </div>
        
        <div class="info-grid" id="mac-details">
        </div>
    </div>

  </div>
</main>

<script src="<?= BASE_URL ?>/assets/js/tools.js"></script>
<?php require __DIR__ . '/templates/footer.php'; ?>