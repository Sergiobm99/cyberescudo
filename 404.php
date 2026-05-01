<?php
// Enviar el código de error oficial al navegador
http_response_code(404);

// FIX: Rescatar los parámetros de la URL (como ?lang=en) que Apache borra en el ErrorDocument
if (isset($_SERVER['REQUEST_URI'])) {
    $parsedUrl = parse_url($_SERVER['REQUEST_URI']);
    if (isset($parsedUrl['query'])) {
        parse_str($parsedUrl['query'], $reclaimed_get);
        $_GET = array_merge($_GET, $reclaimed_get);
    }
}

require_once __DIR__ . '/bootstrap.php';

$pageTitle = $lang==='es' ? '404 - Objetivo no encontrado — CyberEscudo' : '404 - Target Not Found — CyberEscudo';
$pageDescription = $lang==='es' ? 'Error 404. La página o herramienta que buscas no existe.' : '404 Error. The page or tool you are looking for does not exist.';

require __DIR__ . '/templates/header.php';
?>

<main class="content-page not-found-page">
  <div class="m-bottom-2">
    <span class="section-label error-label">// ERROR 404</span>
    <h1 class="error-title">Target Not Found</h1>
  </div>

  <div class="terminal-404">
    <p class="term-cmd">> ping <span class="term-highlight"><?= htmlspecialchars($_SERVER['REQUEST_URI']) ?></span></p>
    <p>> <?= $lang==='es' ? 'Resolviendo DNS del objetivo...' : 'Resolving target DNS...' ?></p>
    <p>> <?= $lang==='es' ? 'Intentando establecer conexión TCP...' : 'Attempting TCP connection...' ?></p>
    <p class="term-error">> [! ERROR] HTTP 404: <?= $lang==='es' ? 'El recurso solicitado ha sido purgado, clasificado o nunca existió.' : 'Requested resource has been purged, classified, or never existed.' ?></p>
    <p class="term-prompt">> <?= $lang==='es' ? 'Iniciando protocolo de evacuación a la ruta:' : 'Initiating evacuation protocol to route:' ?> <a href="<?= BASE_URL ?>/index.php" class="term-link">/home/cyberescudo</a></p>
  </div>
  
  <a href="<?= BASE_URL ?>/index.php" class="tool-btn return-btn">
     🏠 <?= $lang==='es' ? 'Volver a la Base' : 'Return to Base' ?>
  </a>
</main>

<?php require __DIR__ . '/templates/footer.php'; ?>