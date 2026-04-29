<?php
/**
 * CyberEscudo — Content page template
 *
 * Usage: include this template AFTER setting:
 *   $pageTitle       = 'Page title';
 *   $pageDescription = 'Meta description';
 *   $contentTitle    = 'Displayed title';
 *   $contentDate     = '2025-01-15';   // optional
 *   $contentTags     = ['Apache','SSL'];  // optional array of tags
 *
 * Then echo your HTML content into $contentBody before including.
 *
 * Example usage:
 *   ob_start();
 *   // write your <div class="prose">...</div> here
 *   $contentBody = ob_get_clean();
 *   require __DIR__ . '/../templates/content-page.php';
 */

require_once __DIR__ . '/../bootstrap.php';
require __DIR__ . '/header.php';
?>

<!-- Reading progress bar (JS-driven) -->
<div id="reading-bar"></div>

<div class="content-page">

  <!-- Back link -->
  <a href="<?= BASE_URL ?>/index.php" class="back">← <?= $lang === 'es' ? 'Volver al inicio' : 'Back to home' ?></a>

  <!-- Tags -->
  <?php if (!empty($contentTags)): ?>
  <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1rem;">
    <?php foreach ($contentTags as $tag): ?>
    <span class="tag"><?= e($tag) ?></span>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- Difficulty badge (if provided) -->
  <?php if (!empty($contentDiff)): ?>
  <?php
    $diffLabels = ['basic'=>['Básico','Basic'],'intermediate'=>['Intermedio','Intermediate'],'advanced'=>['Avanzado','Advanced']];
    $dl = $diffLabels[$contentDiff] ?? null;
    if ($dl): ?>
  <span class="diff-badge <?= e($contentDiff) ?>"><?= $lang==='es' ? $dl[0] : $dl[1] ?></span>
  <?php endif; endif; ?>

  <!-- Title -->
  <h1><?= e($contentTitle ?? $pageTitle) ?></h1>

  <!-- Meta: date + read time -->
  <div class="meta">
    <?php if (!empty($contentDate)): ?>
    <span><?= date($lang === 'es' ? 'd/m/Y' : 'M j, Y', strtotime($contentDate)) ?></span>
    <?php endif; ?>
    <span class="read-time" id="read-time-meta"></span>
  </div>

  <!-- TOC injected here by JS -->
  <div id="toc-anchor"></div>

  <!-- Body -->
  <?= $contentBody ?? '' ?>

</div>

<?php require __DIR__ . '/footer.php'; ?>
