<?php
require_once __DIR__ . '/bootstrap.php';

$pageTitle    = $lang === 'es' ? 'Política de Privacidad — CyberEscudo' : 'Privacy Policy — CyberEscudo';
$contentTitle = $lang === 'es' ? 'Política de privacidad' : 'Privacy Policy';
$contentDate  = '2025-01-01';
$contentTags  = [];

ob_start();
if ($lang === 'es'): ?>
<div class="prose">
  <p>Esta política de privacidad describe cómo CyberEscudo maneja la información de sus visitantes.</p>
  <h2>Datos recopilados</h2>
  <p>Este sitio no recopila datos personales directamente. El servidor web puede registrar automáticamente la dirección IP, el navegador y la página visitada con fines estadísticos.</p>
  <h2>Cookies</h2>
  <p>Solo se utiliza una cookie de sesión para recordar el idioma seleccionado. No se utilizan cookies de terceros ni para publicidad.</p>
  <h2>Contacto</h2>
  <p>Para cualquier consulta sobre privacidad: <a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a></p>
</div>
<?php else: ?>
<div class="prose">
  <p>This privacy policy describes how CyberEscudo handles visitor information.</p>
  <h2>Data Collected</h2>
  <p>This site does not directly collect personal data. The web server may automatically log IP addresses, browsers, and visited pages for statistical purposes.</p>
  <h2>Cookies</h2>
  <p>Only a session cookie is used to remember the selected language. No third-party or advertising cookies are used.</p>
  <h2>Contact</h2>
  <p>For any privacy-related questions: <a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a></p>
</div>
<?php endif;
$contentBody = ob_get_clean();
require __DIR__ . '/templates/content-page.php';
