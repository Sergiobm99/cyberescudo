<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $lang==='es' ? 'HTTP Request Builder — CyberEscudo' : 'HTTP Request Builder — CyberEscudo';
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
    </select>
  </div>

  <div class="card">
    <div class="tool-header md-container">
      <h2>📡 HTTP Request Builder</h2>
      <p><?= $lang==='es'
        ? 'Construye peticiones HTTP a medida: método, headers, body y auth. Genera el comando curl equivalente listo para copiar.'
        : 'Build custom HTTP requests: method, headers, body and auth. Generates the equivalent curl command ready to copy.' ?></p>
    </div>

    <!-- URL + Method -->
    <div class="hb-grid">
      <div>
        <label class="info-card-label"><?= $lang==='es'?'Método':'Method' ?></label>
        <select id="hb-method" class="cyber-input" style="margin-bottom:0; cursor:pointer;">
          <option>GET</option><option>POST</option><option>PUT</option><option>PATCH</option>
          <option>DELETE</option><option>HEAD</option><option>OPTIONS</option>
        </select>
      </div>
      <div>
        <label class="info-card-label">URL</label>
        <input type="text" id="hb-url" class="cyber-input" placeholder="https://api.target.com/v1/users" style="margin-bottom:0;">
      </div>
    </div>

    <!-- Tabs -->
    <div class="hb-tabs" id="hb-tabs-nav">
      <button type="button" data-tab="headers" class="hb-tab-btn active"><?= $lang==='es'?'Headers':'Headers' ?></button>
      <button type="button" data-tab="auth" class="hb-tab-btn"><?= $lang==='es'?'Auth':'Auth' ?></button>
      <button type="button" data-tab="body" class="hb-tab-btn"><?= $lang==='es'?'Body':'Body' ?></button>
    </div>

    <!-- Headers Tab -->
    <div id="tab-headers" class="hb-tab-content active">
      <div id="hb-headers-list" class="m-bottom-1"></div>
      <button type="button" id="btn-hb-add-header" class="tool-btn" style="background:rgba(255,255,255,.04); border-style:dashed; color:var(--gray); width:100%; height:40px;">
        + <?= $lang==='es'?'Añadir header':'Add header' ?>
      </button>

      <div style="margin-top:1.5rem;">
        <label class="info-card-label md-label-mb"><?= $lang==='es'?'Headers comunes':'Common headers' ?></label>
        <div class="md-flex-wrap" id="hb-common-headers">
          <?php foreach([
            ['Content-Type','application/json'],
            ['Content-Type','application/x-www-form-urlencoded'],
            ['Accept','application/json'],
            ['User-Agent','CyberEscudo-Agent/1.0'],
            ['X-Forwarded-For','127.0.0.1'],
          ] as $h): ?>
          <button type="button" class="dns-quick-btn hb-preset-header" data-key="<?= htmlspecialchars($h[0]) ?>" data-val="<?= htmlspecialchars($h[1]) ?>">
            <?= htmlspecialchars($h[0]) ?>
          </button>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Auth Tab -->
    <div id="tab-auth" class="hb-tab-content">
      <label class="info-card-label"><?= $lang==='es'?'Tipo de autenticación':'Auth type' ?></label>
      <select id="hb-auth-type" class="cyber-input">
        <option value="none"><?= $lang==='es'?'Sin autenticación':'No authentication' ?></option>
        <option value="bearer">Bearer Token (JWT)</option>
        <option value="basic">Basic Auth (user:pass)</option>
        <option value="apikey">API Key (Header)</option>
        <option value="custom"><?= $lang==='es'?'Header personalizado':'Custom header' ?></option>
      </select>

      <div id="auth-bearer" class="hb-auth-section" style="display:none;">
        <input type="text" id="hb-bearer" class="cyber-input" placeholder="eyJhbGci...">
      </div>
      <div id="auth-basic" class="hb-auth-section hb-auth-grid" style="display:none;">
        <input type="text" id="hb-basic-user" class="cyber-input" placeholder="<?= $lang==='es'?'Usuario':'Username' ?>">
        <input type="password" id="hb-basic-pass" class="cyber-input" placeholder="<?= $lang==='es'?'Contraseña':'Password' ?>">
      </div>
      <div id="auth-apikey" class="hb-auth-section hb-auth-grid" style="display:none;">
        <input type="text" id="hb-ak-name" class="cyber-input" placeholder="X-API-Key" value="X-API-Key">
        <input type="text" id="hb-ak-val" class="cyber-input" placeholder="sk-abc123...">
      </div>
      <div id="auth-custom" class="hb-auth-section hb-auth-grid" style="display:none;">
        <input type="text" id="hb-custom-h" class="cyber-input" placeholder="Authorization">
        <input type="text" id="hb-custom-v" class="cyber-input" placeholder="Token xyz...">
      </div>
    </div>

    <!-- Body Tab -->
    <div id="tab-body" class="hb-tab-content">
      <div class="md-flex-wrap m-bottom-1" id="hb-body-types">
        <?php foreach(['JSON','Form','XML','Raw'] as $bt): ?>
        <button type="button" data-btype="<?= $bt ?>" class="dns-quick-btn hb-btype-btn <?= $bt==='JSON' ? 'active' : '' ?>"><?= $bt ?></button>
        <?php endforeach; ?>
      </div>
      <textarea id="hb-body" class="cyber-input md-textarea" rows="8" placeholder='{"username":"admin","password":"123456"}'></textarea>
    </div>

    <!-- Output cURL -->
    <div class="md-examples-section">
      <div class="info-card-label md-label-mb">cURL <?= $lang==='es'?'(Listo para copiar)':'(Ready to copy)' ?></div>
      <div class="hb-curl-box">
        <div id="hb-curl-output">curl -s -i "https://api.target.com/v1/users"</div>
        <button type="button" id="btn-hb-copy" class="copy-btn-mini" style="position:absolute; top:1rem; right:1rem; font-size:0.8rem; padding:0.4rem 0.8rem;">📋 <?= $lang==='es'?'Copiar':'Copy' ?></button>
      </div>
    </div>
  </div>
</main>

<script src="<?= BASE_URL ?>/assets/js/tools.js?v=<?= time() ?>"></script>
<?php require __DIR__ . '/templates/footer.php'; ?>