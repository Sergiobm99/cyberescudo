<?php
require_once __DIR__ . '/bootstrap.php';
http_response_code(403);
$pageTitle       = $lang === 'es' ? 'Acceso denegado — CyberEscudo' : 'Access Denied — CyberEscudo';
$pageDescription = '';
require __DIR__ . '/templates/header.php';
?>
<div class="content-page" style="text-align:center;padding-top:10rem;">
  <span class="section-label">403</span>
  <h1 style="font-size:3rem;margin-bottom:1rem;">
    <?= $lang === 'es' ? 'Acceso denegado' : 'Access Denied' ?>
  </h1>
  <p style="color:rgba(255,255,255,.5);margin-bottom:2rem;">
    <?= $lang === 'es'
      ? 'No tienes permiso para acceder a este recurso.'
      : 'You do not have permission to access this resource.' ?>
  </p>
  <a href="<?= e(BASE_URL) ?>/index.php" style="color:#00ffff;">
    ← <?= $lang === 'es' ? 'Volver al inicio' : 'Back to home' ?>
  </a>
</div>
<?php require __DIR__ . '/templates/footer.php'; ?>
