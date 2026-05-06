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
  'projects/sql-injection-manual.php'       => ['cat'=>'offensive',  'diff'=>'intermediate' ],
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
  <!-- AVISO DE THREAT INTEL EN LA CABECERA -->
<div style="text-align: center; margin-top: 3rem; margin-bottom: 2rem;">
    <a href="#threat-intel" style="color: var(--cyan); text-decoration: none; font-size: 0.85rem; font-family: var(--mono); border: 1px solid rgba(0,255,255,0.3); padding: 6px 16px; border-radius: 20px; background: rgba(0,255,255,0.05); transition: all 0.3s;" onmouseover="this.style.background='rgba(0,255,255,0.1)'; this.style.borderColor='var(--cyan)';" onmouseout="this.style.background='rgba(0,255,255,0.05)'; this.style.borderColor='rgba(0,255,255,0.3)';">
        <span style="display:inline-block; margin-right:5px; animation: pulse 2s infinite;">🔴</span> 
        <?= $lang === 'es' ? 'Ver Últimas Alertas y 0-Days ↓' : 'View Latest Alerts & 0-Days ↓' ?>
    </a>
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
        
        <!-- Contenedor flex para que las etiquetas queden alineadas -->
        <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 8px; margin-bottom: 0.8rem;">
            
            <!-- 1. Etiqueta de dificultad normal (Básico, Intermedio...) -->
            <?php if ($dText): ?>
                <span class="diff-badge <?= e($meta['diff']) ?>" style="margin: 0;"><?= e($dText) ?></span>
            <?php endif; ?>
            
           <!-- Insignia del CTF -->
            <?php if ($item['link'] === 'projects/sql-injection-manual.php' || $item['link'] === 'projects/inyeccion-comandos-rfi-lfi.php' || $item['link'] === 'projects/xss-practica.php' || $item['link'] === 'projects/csrf-clickjacking.php' || $item['link'] === 'projects/xxe-path-traversal.php' || $item['link'] === 'projects/privilege-escalation-linux.php' || $item['link'] === 'projects/gobuster.php' || $item['link'] === 'projects/incident-response.php' || $item['link'] === 'projects/secure-dev.php' || $item['link'] === 'projects/nmap.php' || $item['link'] === 'projects/hydra-brute-force.php' || $item['link'] === 'projects/vuln-scanner.php' || $item['link'] === 'projects/firewall.php' || $item['link'] === 'projects/network-monitoring.php' || $item['link'] === 'projects/shodan.php'): ?>
                <span style="background: rgba(0, 255, 255, 0.1); color: var(--cyan); border: 1px solid var(--cyan); padding: 2px 8px; border-radius: 4px; font-size: 0.65rem; font-family: var(--mono); font-weight: bold; letter-spacing: 1px; display: inline-flex; align-items: center; gap: 5px; animation: pulse 2s infinite;">
                    <span style="font-size: 0.8rem;">🎯</span> <?= $lang === 'es' ? 'RETO CTF INCLUIDO' : 'CTF INSIDE' ?>
                </span>
            <?php endif; ?>
            
        </div>

        <h3 style="margin-top: 0;"><?= e($item['title']) ?></h3>
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
<!-- ─── THREAT INTELLIGENCE DASHBOARD ─── -->
<div id="threat-intel" class="md-container" style="max-width: 900px; margin-left: auto; margin-right: auto; padding-top: 2rem; margin-bottom: 6rem;">
    <div class="section-label">// THREAT INTEL FEED</div>
    <h2 style="margin-top: 0.5rem; margin-bottom: 1.5rem;">
        <?= $lang === 'es' ? 'Últimas Amenazas y 0-Days' : 'Latest Threats & 0-Days' ?>
    </h2>
    
    <div class="cyber-rss-grid">
        <?php
        // Función segura para leer el RSS
        function getThreatNews($url, $limit = 4) {
            $context = stream_context_create(['http' => ['timeout' => 2]]);
            $xmlString = @file_get_contents($url, false, $context);
            if (!$xmlString) return false;
            
            $xml = @simplexml_load_string($xmlString);
            if (!$xml) return false;

            $news = [];
            $count = 0;
            foreach ($xml->channel->item as $item) {
                if ($count >= $limit) break;
                $news[] = [
                    'title' => (string)$item->title,
                    'link'  => (string)$item->link,
                    'date'  => date('d M Y', strtotime((string)$item->pubDate))
                ];
                $count++;
            }
            return $news;
        }

        $rssUrl = 'https://feeds.feedburner.com/TheHackersNews';
        $noticias = getThreatNews($rssUrl, 4);

        // Textos traducidos
        $sourceText = $lang === 'es' ? 'Fuente: The Hacker News' : 'Source: The Hacker News';
        $errorText = $lang === 'es' ? 'No se pudo conectar con los satélites de inteligencia. Reintentando...' : 'Could not connect to intelligence satellites. Retrying...';

        if ($noticias && count($noticias) > 0) {
            foreach ($noticias as $n) {
                echo '<a href="'. htmlspecialchars($n['link']) .'" target="_blank" rel="noopener noreferrer" class="cyber-rss-card">';
                echo '  <div class="rss-date">'. htmlspecialchars($n['date']) .'</div>';
                echo '  <h4 class="rss-title">'. htmlspecialchars($n['title']) .'</h4>';
                echo '  <div class="rss-source">'. $sourceText .'</div>';
                echo '</a>';
            }
        } else {
            echo '<div style="color: var(--gray);">'. $errorText .'</div>';
        }
        ?>
    </div>
</div>
<?php require __DIR__ . '/templates/footer.php'; ?>