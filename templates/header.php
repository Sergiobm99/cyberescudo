<!DOCTYPE html>
<html lang="<?= e($lang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <title><?= e($pageTitle ?? 'CyberEscudo — Herramientas de Ciberseguridad y Pentesting') ?></title>
  <meta name="description" content="<?= e($pageDescription ?? ($lang === 'es' ? 'Plataforma gratuita de herramientas para pentesting y OSINT. Genera reverse shells, analiza claves SSH, descubre vulnerabilidades y aprende hacking ético.' : 'Free platform for pentesting and OSINT tools. Generate reverse shells, analyze SSH keys, discover vulnerabilities and learn ethical hacking.')) ?>">

  <meta property="og:type" content="website">
  <meta property="og:url" content="<?= e('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>">
  <meta property="og:title" content="<?= e($pageTitle ?? 'CyberEscudo — Herramientas de Ciberseguridad') ?>">
  <meta property="og:description" content="<?= e($pageDescription ?? ($lang === 'es' ? 'Plataforma gratuita de herramientas para pentesting y OSINT.' : 'Free platform for pentesting and OSINT tools.')) ?>">
  <meta property="og:image" content="<?= BASE_URL ?>/assets/img/logo-cyberescudo.jpg">

  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= e($pageTitle ?? 'CyberEscudo') ?>">
  <meta name="twitter:description" content="<?= e($pageDescription ?? ($lang === 'es' ? 'Herramientas de ciberseguridad gratuitas.' : 'Free cybersecurity tools.')) ?>">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=<?= time() ?>">

  <link rel="icon" href="<?= BASE_URL ?>/assets/img/logo-cyberescudo.png" sizes="192x192" type="image/png">

  <link rel="apple-touch-icon" href="<?= BASE_URL ?>/assets/img/logo-cyberescudo.png">
  
  <script nonce="<?= e($cspNonce) ?>" async src="https://www.googletagmanager.com/gtag/js?id=G-T1D83JWZV1"></script>
  <script nonce="<?= e($cspNonce) ?>">
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-T1D83JWZV1');
  </script>
</head>
<body class="<?= $lang ?>">

<nav class="navbar" id="navbar">
  <div class="nav-inner">

    <a href="<?= BASE_URL ?>/index" class="logo">
      <img src="<?= BASE_URL ?>/assets/img/logo-cyberescudo.jpg" alt="CyberEscudo Logo" class="nav-logo-img">
      <span>Cyber<span class="accent">Escudo</span></span>
    </a>

    <ul class="nav-links">
      
      <li class="nav-dropdown">
        <a href="#"><?= $lang === 'es' ? 'Explorar' : 'Explore' ?> <span class="chevron"></span></a>
        <div class="dropdown-menu">
            <a href="<?= BASE_URL ?>/index#projects" class="dropdown-item">📁 <?= e($t['nav']['projects']) ?></a>
            <a href="<?= BASE_URL ?>/index#manuals" class="dropdown-item">📖 <?= e($t['nav']['manuals']) ?></a>
            <a href="<?= BASE_URL ?>/projects/skill-tree" class="dropdown-item">🌳 <?= $lang === 'es' ? 'Árbol de Habilidades' : 'Skill Tree' ?></a>
            <a href="<?= BASE_URL ?>/projects/ransomware-tabletop" style="color: #ff2a2a; font-weight: bold;">🚨 <?= $lang === 'es' ? 'Simulador IR: Ransomware' : 'IR Simulator: Ransomware' ?></a>
            <a href="<?= BASE_URL ?>/projects/mitre-mapper" style="color: #b400ff; font-weight: bold; text-shadow: 0 0 8px rgba(180,0,255,0.4);">🗺️ <?= $lang==='es' ? 'MITRE ATT&CK Mapper' : 'MITRE ATT&CK Mapper' ?></a>
        </div>
      </li>
      
      <li class="nav-dropdown">
        <a href="<?= BASE_URL ?>/tools.php"><?= e($t['nav']['tools']) ?> <span class="chevron"></span></a>
        <ul class="dropdown-menu mega">
            
          <div class="megamenu-search-wrap">
            <input type="text" id="megamenu-search" class="cyber-input" placeholder="<?= $lang==='es' ? '🔍 Busca tu herramienta más rápido...' : '🔍 Search your tool faster...' ?>">
          </div>

          <li class="megamenu-col">
            <span class="megamenu-title"><?= $lang==='es'?'🔍 Auditoría & OSINT':'🔍 Audit & OSINT' ?></span>
            <ul class="megamenu-sublist">
              <li><a href="<?= BASE_URL ?>/tool-recon">🔍 OSINT Quick Recon</a></li>
              <li><a href="<?= BASE_URL ?>/tool-takeover">🏴‍☠️ Subdomain Takeover</a></li>
              <li><a href="<?= BASE_URL ?>/tool-cloud">☁️ Cloud Enum</a></li>
              <li><a href="<?= BASE_URL ?>/tool-loganalyzer">📊 Log Analyzer</a></li>
              <li><a href="<?= BASE_URL ?>/tool-headers">📋 HTTP Header Analyzer</a></li>
              <li><a href="<?= BASE_URL ?>/tool-ssh">🔑 SSH Analyzer</a></li>
              <li><a href="<?= BASE_URL ?>/tool-ports">📋 Port Reference</a></li>
              <li><a href="<?= BASE_URL ?>/tool-dns">🔍 DNS Lookup</a></li>
              <li><a href="<?= BASE_URL ?>/tool-osint-report" style="color: var(--cyan); font-weight: bold; text-shadow: 0 0 8px rgba(0,255,255,0.4);">📄 <?= $lang==='es' ? 'Reporte OSINT' : 'OSINT Report' ?></a></li>
            </ul>
          </li>

          <li class="megamenu-col">
            <span class="megamenu-title"><?= $lang==='es'?'🏴‍☠️ Pentesting & Defensa':'🏴‍☠️ Pentesting & Defense' ?></span>
            <ul class="megamenu-sublist">
              <li><a href="<?= BASE_URL ?>/soc-arsenal" style="color: #00d45a; font-weight: bold;">🛡️ <?= $lang==='es' ? 'Arsenal SOC (KQL)' : 'SOC Arsenal (KQL)' ?></a></li>
              <li><a href="<?= BASE_URL ?>/projects/phishing-sandbox" style="color: #ff2a2a; font-weight: bold;">🎣 <?= $lang==='es' ? 'Simulador SOC' : 'SOC Simulator' ?></a></li>
              <li><a href="<?= BASE_URL ?>/tool-scanner">🎯 <?= $lang==='es' ? 'Escáner Perimetral' : 'Perimeter Scanner' ?></a></li>
              <li><a href="<?= BASE_URL ?>/tool-revshell">🐚 Reverse Shell Generator</a></li>
              <li><a href="<?= BASE_URL ?>/tool-waf">🛡️ WAF Bypass Payloads</a></li>
              <li><a href="<?= BASE_URL ?>/tool-cve">🐛 CVE & Exploit Finder</a></li>
              <li><a href="<?= BASE_URL ?>/tool-wordlist">📝 Wordlist Generator</a></li>
              <li><a href="<?= BASE_URL ?>/tool-httpbuilder">📡 HTTP Builder</a></li>
            </ul>
          </li>

          <li class="megamenu-col">
            <span class="megamenu-title"><?= $lang==='es'?'🔐 Cripto & Redes':'🔐 Crypto & Network' ?></span>
            <ul class="megamenu-sublist">
              <li><a href="<?= BASE_URL ?>/tool-ip">🌐 What is my IP?</a></li>
              <li><a href="<?= BASE_URL ?>/tool-cidr">🌍 CIDR Calculator</a></li>
              <li><a href="<?= BASE_URL ?>/tool-mac">🏷️ MAC Vendor Lookup</a></li>
              <li><a href="<?= BASE_URL ?>/tool-hash">#️⃣ Hash Generator</a></li>
              <li><a href="<?= BASE_URL ?>/tool-hashcrack">🔓 Hash Analyzer</a></li>
              <li><a href="<?= BASE_URL ?>/tool-base64">🔄 Base64</a></li>
              <li><a href="<?= BASE_URL ?>/tool-multidecode">🔄 Multi Decoder (CTF)</a></li>
              <li><a href="<?= BASE_URL ?>/tool-jwt">🔓 JWT Decoder</a></li>
              <li><a href="<?= BASE_URL ?>/tool-passgen">🔑 Password Generator</a></li>
            </ul>
          </li>

        </ul>
      </li>

      <li class="nav-dropdown">
        <a href="#"><?= $lang === 'es' ? 'Comunidad' : 'Community' ?> <span class="chevron"></span></a>
        <div class="dropdown-menu">
            <a href="<?= BASE_URL ?>/missions/" class="dropdown-item" style="color: #ff2a2a; font-weight: bold; font-family: var(--mono);">🎯 [ CTF Challenges ]</a>
            <a href="<?= BASE_URL ?>/sobre-mi" class="dropdown-item">👤 <?= $lang === 'es' ? 'Sobre Mí' : 'About Me' ?></a>
        </div>
      </li>

      <li class="nav-donate-item">
        <a href="<?= BASE_URL ?>/index#donate" class="nav-support-btn">
            ♥ <?= $lang === 'es' ? 'Apóyame' : 'Support' ?>
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
      
      <li class="has-submenu">
          <input type="checkbox" id="explore-toggle" class="submenu-checkbox" hidden>
          <label for="explore-toggle" class="submenu-toggle">
              <?= $lang === 'es' ? 'Explorar' : 'Explore' ?> <span class="chevron"></span>
          </label>
          <ul class="mobile-submenu">
              <li><a href="<?= BASE_URL ?>/index#projects">📁 <?= e($t['nav']['projects']) ?></a></li>
              <li><a href="<?= BASE_URL ?>/index#manuals">📖 <?= e($t['nav']['manuals']) ?></a></li>
              <li><a href="<?= BASE_URL ?>/projects/skill-tree">🌳 <?= $lang === 'es' ? 'Árbol de Habilidades' : 'Skill Tree' ?></a></li>
              <li><a href="<?= BASE_URL ?>/projects/ransomware-tabletop" style="color: #ff2a2a; font-weight: bold;">🚨 <?= $lang === 'es' ? 'Simulador IR: Ransomware' : 'IR Simulator: Ransomware' ?></a></li>
              <li><a href="<?= BASE_URL ?>/projects/mitre-mapper" style="color: #b400ff; font-weight: bold; text-shadow: 0 0 8px rgba(180,0,255,0.4);">🗺️ <?= $lang==='es' ? 'MITRE ATT&CK Mapper' : 'MITRE ATT&CK Mapper' ?></a></li>
          </ul>
      </li>
        
      <li class="has-submenu">
          <input type="checkbox" id="tools-toggle" class="submenu-checkbox" hidden>
          <label for="tools-toggle" class="submenu-toggle">
              <?= $lang === 'es' ? 'Herramientas' : 'Tools' ?> <span class="chevron"></span>
          </label>
          
          <ul class="mobile-submenu">
              <div class="mobile-search-wrap">
                <input type="text" id="mobile-menu-search" class="cyber-input" placeholder="<?= $lang==='es' ? '🔍 Buscar herramienta...' : '🔍 Search tool...' ?>">
              </div>

              <li class="mobile-menu-title"><?= $lang==='es'?'🛡️ Defensa & SOC':'🛡️ Defense & SOC' ?></li>
              <li><a href="<?= BASE_URL ?>/soc-arsenal" style="color: #00d45a;">🛡️ <?= $lang==='es' ? 'Arsenal SOC (KQL)' : 'SOC Arsenal (KQL)' ?></a></li>
              <li><a href="<?= BASE_URL ?>/projects/phishing-sandbox" style="color: #ff2a2a;">🎣 <?= $lang==='es' ? 'Simulador SOC' : 'SOC Simulator' ?></a></li>
              
              <li class="mobile-menu-title"><?= $lang==='es'?'🔍 Auditoría & OSINT':'🔍 Audit & OSINT' ?></li>
              <li><a href="<?= BASE_URL ?>/tool-recon">🔍 OSINT Quick Recon</a></li>
              <li><a href="<?= BASE_URL ?>/tool-takeover">🏴‍☠️ Subdomain Takeover</a></li>
              <li><a href="<?= BASE_URL ?>/tool-cloud">☁️ Cloud Enum</a></li>
              <li><a href="<?= BASE_URL ?>/tool-osint-report">📄 <?= $lang==='es' ? 'Reporte OSINT' : 'OSINT Report' ?></a></li>

              <li class="mobile-menu-title"><?= $lang==='es'?'🏴‍☠️ Pentesting & Explotación':'🏴‍☠️ Pentesting & Exploits' ?></li>
              <li><a href="<?= BASE_URL ?>/tool-scanner">🎯 <?= $lang==='es' ? 'Escáner Perimetral' : 'Perimeter Scanner' ?></a></li>
              <li><a href="<?= BASE_URL ?>/tool-revshell">🐚 Reverse Shell Generator</a></li>
              <li><a href="<?= BASE_URL ?>/tool-waf">🛡️ WAF Bypass Payloads</a></li>
              <li><a href="<?= BASE_URL ?>/tool-cve">🐛 CVE & Exploit Finder</a></li>

              <li class="mobile-menu-title"><?= $lang==='es'?'🔐 Cripto, Redes & Auth':'🔐 Crypto, Network & Auth' ?></li>
              <li><a href="<?= BASE_URL ?>/tool-hash">#️⃣ Hash Generator</a></li>
              <li><a href="<?= BASE_URL ?>/tool-multidecode">🔄 Multi Decoder (CTF)</a></li>
              <li><a href="<?= BASE_URL ?>/tool-jwt">🔓 JWT Decoder</a></li>
              <li><a href="<?= BASE_URL ?>/tool-ip">🌐 What is my IP?</a></li>
              <li><a href="<?= BASE_URL ?>/tool-passgen">🔑 Password Generator</a></li>
          </ul>
      </li>

      <li class="has-submenu">
          <input type="checkbox" id="community-toggle" class="submenu-checkbox" hidden>
          <label for="community-toggle" class="submenu-toggle">
              <?= $lang === 'es' ? 'Comunidad' : 'Community' ?> <span class="chevron"></span>
          </label>
          <ul class="mobile-submenu">
              <li><a href="<?= BASE_URL ?>/missions/" style="color: #ff2a2a;">🎯 <?= $lang === 'es' ? 'Misiones CTF' : 'CTF Missions' ?></a></li>
              <li><a href="<?= BASE_URL ?>/sobre-mi">👤 <?= $lang === 'es' ? 'Sobre Mí' : 'About Me' ?></a></li>
          </ul>
      </li>

      <li class="nav-donate-item" style="margin-top: 1rem;">
          <a href="<?= BASE_URL ?>/index#donate" class="nav-support-btn" style="display: block; text-align: center;">
              ♥ <?= $lang === 'es' ? 'Apóyame' : 'Support' ?> 
          </a>
      </li>
    </ul>
  </div>
</nav>

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