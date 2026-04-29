<?php
/**
 * CyberEscudo — PROJECT TEMPLATE
 * ─────────────────────────────────────────────────────────────────────────────
 * HOW TO ADD A NEW PROJECT:
 *   1. Copy this file to projects/my-project.php
 *   2. Set $pageTitle, $contentTitle, $contentDate, $contentTags
 *   3. Write your bilingual content in the if/else block below
 *   4. Register the project in lang/es.php AND lang/en.php under 'projects.items'
 *      using 'link' => 'projects/my-project.php'
 * ─────────────────────────────────────────────────────────────────────────────
 */

require_once __DIR__ . '/../bootstrap.php';

$pageTitle    = $lang === 'es' ? 'Título en español — CyberEscudo' : 'English Title — CyberEscudo';
$contentTitle = $lang === 'es' ? 'Título en español'               : 'English Title';
$contentDate  = '2025-01-01';   // YYYY-MM-DD
$contentTags  = ['Tag1', 'Tag2'];

ob_start();
if ($lang === 'es'): ?>

<div class="prose">
  <p>Introducción en español...</p>

  <h2>Sección 1</h2>
  <p>Contenido...</p>

  <pre><code># Ejemplo de código
comando --opcion valor</code></pre>

</div>

<?php else: ?>

<div class="prose">
  <p>English introduction...</p>

  <h2>Section 1</h2>
  <p>Content...</p>

  <pre><code># Code example
command --option value</code></pre>

</div>

<?php endif;
$contentBody = ob_get_clean();
require __DIR__ . '/../templates/content-page.php';
