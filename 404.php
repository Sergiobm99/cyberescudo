<?php
require_once __DIR__ . '/bootstrap.php';
http_response_code(404);
$pageTitle       = $lang === 'es' ? 'Página no encontrada — CyberEscudo' : 'Page Not Found — CyberEscudo';
$pageDescription = '';
require __DIR__ . '/templates/header.php';
?>
<div class="content-page" style="text-align:center;padding-top:10rem;">
  <span class="section-label">404</span>
  <h1 style="font-size:3rem;margin-bottom:1rem;">
    <?= $lang === 'es' ? 'Página no encontrada' : 'Page Not Found' ?>
  </h1>
  <p style="color:rgba(255,255,255,.5);margin-bottom:2rem;">
    <?= $lang === 'es'
      ? 'El recurso que buscas no existe o ha sido movido.'
      : 'The resource you are looking for does not exist or has been moved.' ?>
  </p>
  <a href="<?= e(BASE_URL) ?>/index.php" style="color:#00ffff;">
    ← <?= $lang === 'es' ? 'Volver al inicio' : 'Back to home' ?>
  </a>
</div>
<?php require __DIR__ . '/templates/footer.php'; ?>
