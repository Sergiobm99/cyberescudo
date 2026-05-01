<?php
/**
 * CyberEscudo — Home page (index.php)
 * Upload the entire cyberescudo-plesk/ folder to your Plesk httpdocs directory.
 */

require_once __DIR__ . '/bootstrap.php';

// ── Project metadata: category + difficulty per page ────────────────
$projectMeta = [
  'projects/hardening-apache.php'           => ['cat'=>'defensive',  'diff'=>'basic'],
  'projects/inyeccion-comandos-rfi-lfi.php' => ['cat'=>'offensive',  'diff'=>'intermediate'],
  'projects/sqlmap.php'                     => ['cat'=>'offensive',  'diff'=>'intermediate'],
  'projects/xss-practica.php'               => ['cat'=>'offensive',  'diff'=>'intermediate'],
  'projects/sql-injection-manual.php'       => ['cat'=>'offensive',  'diff'=>'intermediate'],
  'projects/android-reversing.php'          => ['cat'=>'android',    'diff'=>'advanced'],
  'projects/diva-auditoria.php'             => ['cat'=>'android',    'diff'=>'intermediate'],
  'projects/insecurebank-analisis.php'      => ['cat'=>'android',    'diff'=>'intermediate'],
  'projects/diva-profundizacion.php'        => ['cat'=>'android',    'diff'=>'advanced'],
  'projects/nmap.php'                       => ['cat'=>'network',    'diff'=>'basic'],
  'projects/metasploit.php'                 => ['cat'=>'offensive',  'diff'=>'advanced'],
  'projects/hydra-brute-force.php'          => ['cat'=>'offensive',  'diff'=>'intermediate'],
  'projects/wireshark.php'                  => ['cat'=>'network',    'diff'=>'basic'],
  'projects/john-hashcat.php'               => ['cat'=>'analysis',   'diff'=>'intermediate'],
  'projects/burpsuite.php'                  => ['cat'=>'offensive',  'diff'=>'intermediate'],
  'projects/csrf-clickjacking.php'          => ['cat'=>'offensive',  'diff'=>'intermediate'],
  'projects/nikto-dirb.php'                 => ['cat'=>'offensive',  'diff'=>'basic'],
  'projects/xxe-path-traversal.php'         => ['cat'=>'offensive',  'diff'=>'advanced'],
  'projects/firewall.php'                   => ['cat'=>'defensive',  'diff'=>'basic'],
  'projects/vuln-scanner.php'               => ['cat'=>'analysis',   'diff'=>'intermediate'],
  'projects/network-monitoring.php'         => ['cat'=>'network',    'diff'=>'intermediate'],
  'projects/secure-dev.php'                 => ['cat'=>'defensive',  'diff'=>'intermediate'],
  'projects/incident-response.php'          => ['cat'=>'defensive',  'diff'=>'advanced'],
  'projects/gobuster.php'                   => ['cat'=>'offensive',  'diff'=>'basic'],
  'projects/privilege-escalation-linux.php' => ['cat'=>'offensive',  'diff'=>'advanced'],
  'projects/docker-hardening.php'           => ['cat'=>'defensive',  'diff'=>'intermediate'],
  'projects/shodan.php'                     => ['cat'=>'network',    'diff'=>'basic'],
];

// Difficulty display labels
$diffLabel = [
  'basic'        => ['es'=>'Básico',      'en'=>'Basic'],
  'intermediate' => ['es'=>'Intermedio',  'en'=>'Intermediate'],
  'advanced'     => ['es'=>'Avanzado',    'en'=>'Advanced'],
];

$pageTitle       = SITE_NAME . ' — ' . ($lang === 'es' ? 'Ciberseguridad' : 'Cybersecurity');
$pageDescription = $lang === 'es'
    ? 'Proyectos, manuales y guías sobre ciberseguridad. Desde hardening de servidores hasta pentesting.'
    : 'Projects, manuals and guides on cybersecurity. From server hardening to penetration testing.';

require __DIR__ . '/templates/header.php';
?>
<section id="hero" class="cyber-grid">
  <div class="hero-glow"></div>
  <div class="hero-content">

    <div class="terminal-badge">
      <span class="prompt">&gt;_</span>
      <?= e($t['hero']['terminal']) ?>
    </div>

    <h1 class="hero-title glitch-text" data-text="<?= e($t['hero']['title1']) ?> <?= e($t['hero']['titleHighlight']) ?>">
      <?= e($t['hero']['title1']) ?>
      <span class="hero-highlight"> <?= e($t['hero']['titleHighlight']) ?></span>
    </h1>

    <p class="hero-subtitle"><?= e($t['hero']['subtitle']) ?></p>
    <p class="hero-command"><?= e($t['hero']['command']) ?></p>

    <button class="hero-cta" data-scroll-to="projects">
      <?= e($t['hero']['cta']) ?> <span class="arrow">↓</span>
    </button>
  </div>
</section>

<section id="projects" class="section">
  <div class="section-inner">
    <div class="section-header">
      <span class="section-label"><?= e($t['projects']['sectionLabel']) ?></span>
      <h2><?= e($t['projects']['title']) ?></h2>
      <p><?= e($t['projects']['subtitle']) ?></p>
    </div>

    <div class="projects-grid">
      <?php
      // Filter button labels
      $catLabels = $lang==='es'
        ? [''=>'Todos','defensive'=>'Defensivo','offensive'=>'Ofensivo','android'=>'Android','network'=>'Red','analysis'=>'Análisis']
        : [''=>'All',  'defensive'=>'Defensive','offensive'=>'Offensive','android'=>'Android','network'=>'Network','analysis'=>'Analysis'];
      $diffLabels2 = $lang==='es'
        ? [''=>'Todos','basic'=>'Básico','intermediate'=>'Intermedio','advanced'=>'Avanzado']
        : [''=>'All',  'basic'=>'Basic','intermediate'=>'Intermediate','advanced'=>'Advanced'];
      ?>

      <!-- Se ha eliminado el style en línea problemático de la filter-bar -->
      <div class="filter-bar">
        <input type="search" class="filter-search" id="proj-search"
               placeholder="<?= $lang==='es' ? 'Buscar proyecto...' : 'Search project...' ?>">
        <div class="filter-sep"></div>
        <span class="filter-label"><?= $lang==='es' ? 'Cat:' : 'Cat:' ?></span>
        <div class="filter-group" id="cat-filters">
          <?php foreach ($catLabels as $val => $label): ?>
          <button class="filter-btn <?= $val==='' ? 'active' : '' ?>" data-cat="<?= e($val) ?>"><?= e($label) ?></button>
          <?php endforeach; ?>
        </div>
        <div class="filter-sep"></div>
        <span class="filter-label"><?= $lang==='es' ? 'Nivel:' : 'Level:' ?></span>
        <div class="filter-group" id="diff-filters">
          <?php foreach ($diffLabels2 as $val => $label): ?>
          <button class="filter-btn <?= $val==='' ? 'active' : '' ?>" data-diff="<?= e($val) ?>"><?= e($label) ?></button>
          <?php endforeach; ?>
        </div>
      </div>

      <p class="no-results" id="no-results">
        <?= $lang==='es' ? '🔍 Sin resultados. Prueba otro filtro.' : '🔍 No results. Try another filter.' ?>
      </p>

      <?php foreach ($t['projects']['items'] as $item):
        $meta  = $projectMeta[$item['link']] ?? ['cat'=>'', 'diff'=>''];
        $dl    = $diffLabel[$meta['diff']] ?? null;
        $dText = $dl ? $dl[$lang] : '';
      ?>
      <a href="<?= e(BASE_URL . '/' . $item['link']) ?>" class="card"
         data-cat="<?= e($meta['cat']) ?>"
         data-diff="<?= e($meta['diff']) ?>"
         data-search="<?= e(strtolower($item['title'] . ' ' . $item['desc'])) ?>">
        <div class="card-icon"><?= icon($item['icon']) ?></div>
        <?php if ($dText): ?><span class="diff-badge <?= e($meta['diff']) ?>"><?= e($dText) ?></span><?php endif; ?>
        <h3><?= e($item['title']) ?></h3>
        <p><?= e($item['desc']) ?></p>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section id="manuals" class="section alt">
  <div class="section-inner">
    <div class="section-header">
      <span class="section-label"><?= e($t['manuals']['sectionLabel']) ?></span>
      <h2><?= e($t['manuals']['title']) ?></h2>
      <p><?= e($t['manuals']['subtitle']) ?></p>
    </div>

    <div class="manuals-grid">
      <?php foreach ($t['manuals']['categories'] as $cat): ?>
      <div class="manual-card">
        <div class="manual-cat-header">
          <div class="manual-cat-icon"><?= icon($cat['icon']) ?></div>
          <h3><?= e($cat['title']) ?></h3>
        </div>
        <ul class="manual-list">
          <?php foreach ($cat['items'] as $item): ?>
          <li>
            <span class="arrow-r">›</span>
            <a href="<?= e(BASE_URL . '/' . $item['link']) ?>"><?= e($item['label']) ?></a>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section id="about" class="section">
  <div class="section-inner">
    <div class="section-header">
      <span class="section-label"><?= e($t['about']['sectionLabel']) ?></span>
      <h2><?= e($t['about']['title']) ?></h2>
    </div>

    <div class="about-wrap">

      <div class="profile-card">
        <div class="profile-header">
          <div class="profile-avatar"><?= icon('logo') ?></div>
          <div>
            <p class="profile-name"><?= e($t['about']['name']) ?></p>
            <p class="profile-role"><?= e($t['about']['role']) ?></p>
          </div>
        </div>
        <p class="profile-bio"><?= e($t['about']['bio']) ?></p>
        <p class="profile-bio"><?= e($t['about']['bio2']) ?></p>
      </div>

      <div class="contact-card">
        <div class="contact-icon"><?= icon('email') ?></div>
        <div>
          <p class="contact-label"><?= e($t['about']['contact']) ?></p>
          <a class="contact-email" href="mailto:<?= e(SITE_EMAIL) ?>"><?= e($t['about']['email']) ?></a>
        </div>
      </div>

      <div class="donate-card" id="donate">
        <?= icon('heart') ?>
        <h3><?= e($t['donate']['title']) ?></h3>
        <p class="donate-subtitle"><?= e($t['donate']['subtitle']) ?></p>
        <a href="<?= e(safeUrl(PAYPAL_LINK)) ?>" target="_blank" rel="noopener noreferrer" class="donate-btn">
          ♥ <?= e($t['donate']['btn']) ?>
        </a>
        <p class="donate-note"><?= e($t['donate']['note']) ?> ℹ️</p>
      </div>

    </div>
  </div>
</section>

<?php require __DIR__ . '/templates/footer.php'; ?>