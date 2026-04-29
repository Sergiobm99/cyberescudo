<?php
/**
 * CyberEscudo — MANUAL TEMPLATE
 * ─────────────────────────────────────────────────────────────────────────────
 * HOW TO ADD A NEW MANUAL:
 *   1. Copy this file to manuales/my-manual.php
 *   2. Fill in $pageTitle, $contentTitle, $contentDate, $contentTags
 *   3. Write bilingual content in the if/else block
 *   4. Register in lang/es.php AND lang/en.php under 'manuals.categories[*].items'
 *      using 'link' => 'manuales/my-manual.php'
 * ─────────────────────────────────────────────────────────────────────────────
 */

require_once __DIR__ . '/../bootstrap.php';

$pageTitle    = $lang === 'es' ? 'Título en español — CyberEscudo' : 'English Title — CyberEscudo';
$contentTitle = $lang === 'es' ? 'Título en español'               : 'English Title';
$contentDate  = '2025-01-01';
$contentTags  = ['Tag1', 'Tag2'];

ob_start();
if ($lang === 'es'): ?>

<div class="prose">
  <p>Introducción del manual...</p>

  <h2>Paso 1 — Preparación</h2>
  <p>...</p>

  <h2>Paso 2 — Instalación</h2>
  <pre><code>sudo apt update && sudo apt install -y paquete</code></pre>
</div>

<?php else: ?>

<div class="prose">
  <p>Manual introduction...</p>

  <h2>Step 1 — Preparation</h2>
  <p>...</p>

  <h2>Step 2 — Installation</h2>
  <pre><code>sudo apt update && sudo apt install -y package</code></pre>
</div>

<?php endif;
$contentBody = ob_get_clean();
require __DIR__ . '/../templates/content-page.php';
