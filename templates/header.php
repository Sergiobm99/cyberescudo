<!DOCTYPE html>
<html lang="<?= e($lang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= e($pageDescription ?? 'Proyectos, manuales y guías sobre ciberseguridad. Desde hardening de servidores hasta pentesting.') ?>">
  <title><?= e($pageTitle ?? SITE_NAME . ' — Ciberseguridad') ?></title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">

  <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/img/logo-cyberescudo.png">
  <script>
    // Definimos las variables globales para que los archivos .js las entiendan
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
      
      <li class="nav-dropdown">
        <a href="<?= BASE_URL ?>/tools.php"><?= e($t['nav']['tools']) ?> ▾</a>
        <ul class="dropdown-menu">
          <li><a href="<?= BASE_URL ?>/tool-ip.php">🌐 What is my IP?</a></li>
          <li><a href="<?= BASE_URL ?>/tool-passgen.php">🔑 Password Generator</a></li>
          <li><a href="<?= BASE_URL ?>/tool-passcheck.php">🛡️ Password Strength</a></li>
          <li><a href="<?= BASE_URL ?>/tool-hash.php">#️⃣ Hash Generator</a></li>
          <li><a href="<?= BASE_URL ?>/tool-hashcrack.php">🔓 Hash Analyzer</a></li>
          <li><a href="<?= BASE_URL ?>/tool-base64.php">🔄 Base64</a></li>
          <li><a href="<?= BASE_URL ?>/tool-cidr.php">🌍 CIDR Calculator</a></li>
          <li><a href="<?= BASE_URL ?>/tool-jwt.php">🔓 JWT Decoder</a></li>
          <li><a href="<?= BASE_URL ?>/tool-url.php">🔗 URL Encoder</a></li>
          <li><a href="<?= BASE_URL ?>/tool-chmod.php">🐧 Linux Chmod</a></li>
          <li><a href="<?= BASE_URL ?>/tool-regex.php">🛡️ Password Regex Generator</a></li>
          <li><a href="<?= BASE_URL ?>/tool-mac.php">🏷️ MAC Vendor Lookup</a></li>
          <li><a href="<?= BASE_URL ?>/tool-revshell.php">🐚 Reverse Shell Generator</a></li>
          <li><a href="<?= BASE_URL ?>/tool-cron.php">⏱ Cron Parser</a></li>
          <li><a href="<?= BASE_URL ?>/tool-headers.php">📋 HTTP Header Analyzer</a></li>
        </ul>
      </li>

      <li><a href="<?= BASE_URL ?>/index.php#about"><?= e($t['nav']['about']) ?></a></li>
      <li>
        <a href="<?= BASE_URL ?>/index.php#donate" class="nav-support-btn">
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
        <li><a href="<?= BASE_URL ?>/index.php#projects"><?= e($t['nav']['projects']) ?></a></li>
        <li><a href="<?= BASE_URL ?>/index.php#manuals"><?= e($t['nav']['manuals']) ?></a></li>
        
        <li class="has-submenu">
            <input type="checkbox" id="tools-toggle" class="submenu-checkbox" style="display: none;">
            
            <label for="tools-toggle" class="submenu-toggle">
                <?= $lang === 'es' ? 'Herramientas' : 'Tools' ?>
                <span class="chevron">▼</span>
            </label>
            
            <ul class="mobile-submenu">
                <li><a href="<?= BASE_URL ?>/tool-ip.php">🌐 What is my IP?</a></li>
                <li><a href="<?= BASE_URL ?>/tool-passgen.php">🔑 Password Generator</a></li>
                <li><a href="<?= BASE_URL ?>/tool-passcheck.php">🛡️ Password Strength</a></li>
                <li><a href="<?= BASE_URL ?>/tool-hash.php">#️⃣ Hash Generator</a></li>
                <li><a href="<?= BASE_URL ?>/tool-hashcrack.php">🔓 Hash Analyzer</a></li>
                <li><a href="<?= BASE_URL ?>/tool-base64.php">🔄 Base64</a></li>
                <li><a href="<?= BASE_URL ?>/tool-cidr.php">🌍 CIDR Calculator</a></li>
                <li><a href="<?= BASE_URL ?>/tool-jwt.php">🔓 JWT Decoder</a></li>
                <li><a href="<?= BASE_URL ?>/tool-url.php">🔗 URL Encoder</a></li>
                <li><a href="<?= BASE_URL ?>/tool-chmod.php">🐧 Linux Chmod</a></li>
                <li><a href="<?= BASE_URL ?>/tool-regex.php">🛡️ Password Regex Generator</a></li>
                <li><a href="<?= BASE_URL ?>/tool-mac.php">🏷️ MAC Vendor Lookup</a></li>
                <li><a href="<?= BASE_URL ?>/tool-revshell.php">🐚 Reverse Shell Generator</a></li>
                <li><a href="<?= BASE_URL ?>/tool-cron.php">⏱ Cron Parser</a></li>
                <li><a href="<?= BASE_URL ?>/tool-headers.php">📋 HTTP Header Analyzer</a></li>
            </ul>
        </li>

        <li><a href="<?= BASE_URL ?>/index.php#about"><?= e($t['nav']['about']) ?></a></li>
        
        <li style="margin-top: 1.5rem; text-align: center;">
            <a href="<?= BASE_URL ?>/index.php#donate" class="nav-support-btn" style="display:inline-flex;">
                ♥ <?= $lang === 'es' ? 'Apóyame' : 'Support' ?> 
            </a>
        </li>
    </ul>
  </div>
</nav>