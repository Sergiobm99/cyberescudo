<!DOCTYPE html>
<html lang="<?= e($lang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- SEO Básico -->
  <title><?= e($pageTitle ?? 'CyberEscudo — Herramientas de Ciberseguridad y Pentesting') ?></title>
  <meta name="description" content="<?= e($pageDescription ?? ($lang === 'es' ? 'Plataforma gratuita de herramientas para pentesting y OSINT. Genera reverse shells, analiza claves SSH, descubre vulnerabilidades y aprende hacking ético.' : 'Free platform for pentesting and OSINT tools. Generate reverse shells, analyze SSH keys, discover vulnerabilities and learn ethical hacking.')) ?>">

  <!-- Open Graph / Facebook / WhatsApp / LinkedIn -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?= e('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>">
  <meta property="og:title" content="<?= e($pageTitle ?? 'CyberEscudo — Herramientas de Ciberseguridad') ?>">
  <meta property="og:description" content="<?= e($pageDescription ?? ($lang === 'es' ? 'Plataforma gratuita de herramientas para pentesting y OSINT.' : 'Free platform for pentesting and OSINT tools.')) ?>">
  <meta property="og:image" content="<?= BASE_URL ?>/assets/img/logo-cyberescudo.jpg">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= e($pageTitle ?? 'CyberEscudo') ?>">
  <meta name="twitter:description" content="<?= e($pageDescription ?? ($lang === 'es' ? 'Herramientas de ciberseguridad gratuitas.' : 'Free cybersecurity tools.')) ?>">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=<?= time() ?>">

  <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/img/logo-cyberescudo.png">
  
  <!-- Google tag (gtag.js) -->
  <script nonce="<?= e($cspNonce) ?>" async src="https://www.googletagmanager.com/gtag/js?id=G-T1D83JWZV1"></script>
  <script nonce="<?= e($cspNonce) ?>">
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-T1D83JWZV1');
  </script>
  
  <script nonce="<?= e($cspNonce) ?>">
    window.LANG = '<?= $lang ?>';
    window.BASE_URL = '<?= BASE_URL ?>';
  </script>
</head>
<body class="<?= $lang ?>">

<nav class="navbar" id="navbar">
  <div class="nav-inner">

    <a href="<?= BASE_URL ?>/index.php" class="logo">
      <img src="<?= BASE_URL ?>/assets/img/logo-cyberescudo.jpg" alt="CyberEscudo Logo" class="nav-logo-img">
      <span>Cyber<span class="accent">Escudo</span></span>
    </a>

    <ul class="nav-links">
      <li><a href="<?= BASE_URL ?>/index.php#projects"><?= e($t['nav']['projects']) ?></a></li>
      <li><a href="<?= BASE_URL ?>/index.php#manuals"><?= e($t['nav']['manuals']) ?></a></li>
      
      <!-- INICIO DEL MEGA MENÚ -->
      <li class="nav-dropdown">
        <a href="<?= BASE_URL ?>/tools.php"><?= e($t['nav']['tools']) ?> ▾</a>
        <ul class="dropdown-menu">
            
          <!-- Buscador de Escritorio -->
          <div class="megamenu-search-wrap">
            <input type="text" id="megamenu-search" class="cyber-input" placeholder="<?= $lang==='es' ? '🔍 Busca tu herramienta más rápido...' : '🔍 Search your tool faster...' ?>">
          </div>

          <!-- COLUMNA 1: Auditoría y Reconocimiento -->
          <li class="megamenu-col">
            <span class="megamenu-title"><?= $lang==='es'?'🔍 Auditoría & OSINT':'🔍 Audit & OSINT' ?></span>
            <ul class="megamenu-sublist">
              <li><a href="<?= BASE_URL ?>/tool-recon.php">🔍 OSINT Quick Recon</a></li>
              <li><a href="<?= BASE_URL ?>/tool-takeover.php">🏴‍☠️ Subdomain Takeover</a></li>
              <li><a href="<?= BASE_URL ?>/tool-cloud.php">☁️ Cloud Enum</a></li>
              <li><a href="<?= BASE_URL ?>/tool-loganalyzer.php">📊 Log Analyzer</a></li>
              <li><a href="<?= BASE_URL ?>/tool-headers.php">📋 HTTP Header Analyzer</a></li>
              <li><a href="<?= BASE_URL ?>/tool-ssh.php">🔑 SSH Analyzer</a></li>
              <li><a href="<?= BASE_URL ?>/tool-ports.php">📋 Port Reference</a></li>
            </ul>
          </li>

          <!-- COLUMNA 2: Explotación y Redes -->
          <li class="megamenu-col">
            <span class="megamenu-title"><?= $lang==='es'?'🏴‍☠️ Pentesting & Explotación':'🏴‍☠️ Pentesting & Exploits' ?></span>
            <ul class="megamenu-sublist">
              <li><a href="<?= BASE_URL ?>/tool-revshell.php">🐚 Reverse Shell Generator</a></li>
              <li><a href="<?= BASE_URL ?>/tool-waf.php">🛡️ WAF Bypass Payloads</a></li>
              <li><a href="<?= BASE_URL ?>/tool-cve.php">🐛 CVE & Exploit Finder</a></li>
              <li><a href="<?= BASE_URL ?>/tool-wordlist.php">📝 Wordlist Generator</a></li>
              <li><a href="<?= BASE_URL ?>/tool-httpbuilder.php">📡 HTTP Builder</a></li>
            </ul>

            <span class="megamenu-title" style="margin-top: 1.5rem;"><?= $lang==='es'?'🛡️ Redes & Sistemas':'🛡️ Network & Systems' ?></span>
            <ul class="megamenu-sublist">
              <li><a href="<?= BASE_URL ?>/tool-ip.php">🌐 What is my IP?</a></li>
              <li><a href="<?= BASE_URL ?>/tool-cidr.php">🌍 CIDR Calculator</a></li>
              <li><a href="<?= BASE_URL ?>/tool-mac.php">🏷️ MAC Vendor Lookup</a></li>
              <li><a href="<?= BASE_URL ?>/tool-chmod.php">🐧 Linux Chmod</a></li>
              <li><a href="<?= BASE_URL ?>/tool-cron.php">⏱ Cron Parser</a></li>
            </ul>
          </li>

          <!-- COLUMNA 3: Criptografía y Auth -->
          <li class="megamenu-col">
            <span class="megamenu-title"><?= $lang==='es'?'🔐 Cripto & Hashes':'🔐 Crypto & Hashes' ?></span>
            <ul class="megamenu-sublist">
              <li><a href="<?= BASE_URL ?>/tool-hash.php">#️⃣ Hash Generator</a></li>
              <li><a href="<?= BASE_URL ?>/tool-hashcrack.php">🔓 Hash Analyzer</a></li>
              <li><a href="<?= BASE_URL ?>/tool-base64.php">🔄 Base64</a></li>
              <li><a href="<?= BASE_URL ?>/tool-multidecode.php">🔄 Multi Decoder (CTF)</a></li>
              <li><a href="<?= BASE_URL ?>/tool-jwt.php">🔓 JWT Decoder</a></li>
            </ul>

            <span class="megamenu-title" style="margin-top: 1.5rem;"><?= $lang==='es'?'⚙️ Utilidades & Auth':'⚙️ Utilities & Auth' ?></span>
            <ul class="megamenu-sublist">
              <li><a href="<?= BASE_URL ?>/tool-passgen.php">🔑 Password Generator</a></li>
              <li><a href="<?= BASE_URL ?>/tool-passcheck.php">🛡️ Password Strength</a></li>
              <li><a href="<?= BASE_URL ?>/tool-regex.php">🛡️ Password Regex Generator</a></li>
              <li><a href="<?= BASE_URL ?>/tool-url.php">🔗 URL Encoder</a></li>
            </ul>
          </li>

        </ul>
      </li>
      <!-- FIN DEL MEGA MENÚ -->

      <li><a href="<?= BASE_URL ?>/index.php#about"><?= e($t['nav']['about']) ?></a></li>
      <li>
        <a href="<?= BASE_URL ?>/index.php#donate" class="nav-support-btn">
            ♥ <?= $lang === 'es' ? 'Apóyame' : 'Support' ?>
        </a></li>
        <li>
        <a href="<?= BASE_URL ?>/tool-osint-report.php" class="nav-link <?= ($current_page === 'tool-osint-report.php') ? 'active' : '' ?>" style="color: var(--cyan); font-weight: bold; text-shadow: 0 0 8px rgba(0,255,255,0.4);">
    📄 <?= $lang==='es' ? 'Reporte OSINT' : 'OSINT Report' ?>
</a>
      </li>
    </ul>
    
    <div class="lang-toggle">
      <a href="<?= e(langUrl('es')) ?>" class="lang-btn <?= $lang === 'es' ? 'active' : '' ?>">ES</a>
      <a href="<?= e(langUrl('en')) ?>" class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>">EN</a>
    </div>

    <button class="burger" id="burger" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
  </div>

  <div class="mobile-menu" id="mobile-menu">
    <ul class="mobile-nav-list">
        <li><a href="<?= BASE_URL ?>/index.php#projects"><?= e($t['nav']['projects']) ?></a></li>
        <li><a href="<?= BASE_URL ?>/index.php#manuals"><?= e($t['nav']['manuals']) ?></a></li>
        
        <li class="has-submenu">
            <input type="checkbox" id="tools-toggle" class="submenu-checkbox" hidden>
            <label for="tools-toggle" class="submenu-toggle">
                <?= $lang === 'es' ? 'Herramientas' : 'Tools' ?>
                <span class="chevron">▼</span>
            </label>
            
            <ul class="mobile-submenu">
                <!-- Buscador Móvil -->
                <div class="mobile-search-wrap">
                  <input type="text" id="mobile-menu-search" class="cyber-input" placeholder="<?= $lang==='es' ? '🔍 Buscar herramienta...' : '🔍 Search tool...' ?>">
                </div>

                <li class="mobile-menu-title"><?= $lang==='es'?'🔍 Auditoría & OSINT':'🔍 Audit & OSINT' ?></li>
                <li><a href="<?= BASE_URL ?>/tool-recon.php">🔍 OSINT Quick Recon</a></li>
                <li><a href="<?= BASE_URL ?>/tool-takeover.php">🏴‍☠️ Subdomain Takeover</a></li>
                <li><a href="<?= BASE_URL ?>/tool-cloud.php">☁️ Cloud Enum</a></li>
                <li><a href="<?= BASE_URL ?>/tool-loganalyzer.php">📊 Log Analyzer</a></li>
                <li><a href="<?= BASE_URL ?>/tool-headers.php">📋 HTTP Header Analyzer</a></li>
                <li><a href="<?= BASE_URL ?>/tool-ssh.php">🔑 SSH Analyzer</a></li>
                <li><a href="<?= BASE_URL ?>/tool-ports.php">📋 Port Reference</a></li>

                <li class="mobile-menu-title"><?= $lang==='es'?'🏴‍☠️ Pentesting & Explotación':'🏴‍☠️ Pentesting & Exploits' ?></li>
                <li><a href="<?= BASE_URL ?>/tool-revshell.php">🐚 Reverse Shell Generator</a></li>
                <li><a href="<?= BASE_URL ?>/tool-waf.php">🛡️ WAF Bypass Payloads</a></li>
                <li><a href="<?= BASE_URL ?>/tool-cve.php">🐛 CVE & Exploit Finder</a></li>
                <li><a href="<?= BASE_URL ?>/tool-wordlist.php">📝 Wordlist Generator</a></li>
                <li><a href="<?= BASE_URL ?>/tool-httpbuilder.php">📡 HTTP Builder</a></li>

                <li class="mobile-menu-title"><?= $lang==='es'?'🛡️ Redes & Sistemas':'🛡️ Network & Systems' ?></li>
                <li><a href="<?= BASE_URL ?>/tool-ip.php">🌐 What is my IP?</a></li>
                <li><a href="<?= BASE_URL ?>/tool-cidr.php">🌍 CIDR Calculator</a></li>
                <li><a href="<?= BASE_URL ?>/tool-mac.php">🏷️ MAC Vendor Lookup</a></li>
                <li><a href="<?= BASE_URL ?>/tool-chmod.php">🐧 Linux Chmod</a></li>
                <li><a href="<?= BASE_URL ?>/tool-cron.php">⏱ Cron Parser</a></li>

                <li class="mobile-menu-title"><?= $lang==='es'?'🔐 Cripto & Hashes':'🔐 Crypto & Hashes' ?></li>
                <li><a href="<?= BASE_URL ?>/tool-hash.php">#️⃣ Hash Generator</a></li>
                <li><a href="<?= BASE_URL ?>/tool-hashcrack.php">🔓 Hash Analyzer</a></li>
                <li><a href="<?= BASE_URL ?>/tool-base64.php">🔄 Base64</a></li>
                <li><a href="<?= BASE_URL ?>/tool-multidecode.php">🔄 Multi Decoder (CTF)</a></li>
                <li><a href="<?= BASE_URL ?>/tool-jwt.php">🔓 JWT Decoder</a></li>

                <li class="mobile-menu-title"><?= $lang==='es'?'⚙️ Utilidades & Auth':'⚙️ Utilities & Auth' ?></li>
                <li><a href="<?= BASE_URL ?>/tool-passgen.php">🔑 Password Generator</a></li>
                <li><a href="<?= BASE_URL ?>/tool-passcheck.php">🛡️ Password Strength</a></li>
                <li><a href="<?= BASE_URL ?>/tool-regex.php">🛡️ Password Regex Generator</a></li>
                <li><a href="<?= BASE_URL ?>/tool-url.php">🔗 URL Encoder</a></li>
            </ul>
        </li>

        <li><a href="<?= BASE_URL ?>/index.php#about"><?= e($t['nav']['about']) ?></a></li>
        <li class="nav-osint-item">
            <a href="<?= BASE_URL ?>/tool-osint-report.php" style="color: var(--cyan); font-weight: 600;">
                📄 <?= $lang === 'es' ? 'Reporte OSINT' : 'Osint Report' ?>
            </a>
        </li>
        <li class="nav-donate-item">
            <a href="<?= BASE_URL ?>/index.php#donate" class="nav-support-btn">
                ♥ <?= $lang === 'es' ? 'Apóyame' : 'Support' ?> 
            </a>
        </li>
    </ul>
  </div>
</nav>

<!-- Lógica del Buscador del Menú -->
<script nonce="<?= e($cspNonce) ?>">
document.addEventListener('DOMContentLoaded', () => {
    const setupMenuSearch = (inputId, listSelector) => {
        const input = document.getElementById(inputId);
        if (!input) return;
        
        input.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase();
            const items = document.querySelectorAll(listSelector);
            
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(term)) {
                    item.classList.remove('hidden-tool');
                } else {
                    item.classList.add('hidden-tool');
                }
            });
        });
        
        // Evitar que el menú se cierre al hacer clic en el buscador (Escritorio)
        input.addEventListener('click', (e) => e.stopPropagation());
    };

    // Aplicar al menú de PC
    setupMenuSearch('megamenu-search', '.megamenu-sublist li');
    // Aplicar al menú de móvil (ignorando los títulos)
    setupMenuSearch('mobile-menu-search', '.mobile-submenu li:not(.mobile-menu-title):not(.mobile-search-wrap)');
});
</script>