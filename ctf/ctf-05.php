<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Reto CTF #05: API XXE — CyberEscudo' : 'CTF Challenge #05: API XXE — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Explotación de Entidades Externas XML en un entorno controlado.' : 'XML External Entity exploitation in a controlled environment.';
$current_page = 'ctf/ctf-05.php';
require __DIR__ . '/../templates/header.php';

$xmlInput = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<stockCheck>\n    <productId>1</productId>\n</stockCheck>";
$resultado = "";
$flag = "";
$esExito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['xml_data'])) {
    $xmlInput = $_POST['xml_data'];
    
    // 100% SEGURO: Análisis Regex para buscar el patrón exacto de un ataque XXE.
    // Buscamos: <!ENTITY algo SYSTEM "file:///etc/passwd">
    $tieneEntitySystem = preg_match('/<!ENTITY\s+[a-zA-Z0-9_]+\s+SYSTEM\s+[\'"]file:\/\/\/etc\/passwd[\'"]\s*>/i', $xmlInput, $matchesEntity);
    
    // Buscamos si la entidad creada se está llamando dentro del XML (ej: &xxe;)
    $llamadaEntidad = preg_match('/&[a-zA-Z0-9_]+;/i', $xmlInput);

    if ($tieneEntitySystem && $llamadaEntidad) {
        $esExito = true;
        $resultado = "root:x:0:0:root:/root:/bin/bash\ndaemon:x:1:1:daemon:/usr/sbin:/usr/sbin/nologin\nbin:x:2:2:bin:/bin:/usr/sbin/nologin\nsys:x:3:3:sys:/dev:/usr/sbin/nologin\nwww-data:x:33:33:www-data:/var/www:/usr/sbin/nologin";
        $flag = "FLAG{xxe_xml_parser_pwned}";
    } else {
        // Simulamos un comportamiento normal de API
        preg_match('/<productId>(.*?)<\/productId>/is', $xmlInput, $matchesProd);
        $prodId = $matchesProd[1] ?? 'Desconocido';
        $resultado = "El producto ID [ " . htmlspecialchars($prodId) . " ] tiene 45 unidades en stock.";
    }
}
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 800px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// <?= $lang === 'es' ? 'RETO CTF 05' : 'CTF CHALLENGE 05' ?></span>
        <h1 style="text-align: center; margin-bottom: 2rem;"><?= $lang === 'es' ? 'API de Inventario (XML)' : 'Inventory API (XML)' ?></h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <p style="color: var(--gray); margin-bottom: 1.5rem; text-align: center;">
                <?= $lang === 'es' ? 'Nuestra API antigua procesa las peticiones en formato XML para comprobar el stock. Modifica el payload interceptado e intenta leer el archivo <code>/etc/passwd</code>.' : 'Our legacy API processes stock requests in XML format. Modify the intercepted payload and try to read the <code>/etc/passwd</code> file.' ?>
            </p>
            
            <form method="POST" action="">
                <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                    <?= $lang === 'es' ? 'Cuerpo de la Petición HTTP (XML):' : 'HTTP Request Body (XML):' ?>
                </label>
                <textarea name="xml_data" class="cyber-input" style="width: 100%; height: 180px; font-family: var(--mono); font-size: 0.9rem; margin-bottom: 1rem; resize: vertical; box-sizing: border-box; background: #0a0a0a; color: #00ff00;"><?= htmlspecialchars($xmlInput) ?></textarea>
                
                <button type="submit" style="width: 100%; background: var(--cyan); border: none; color: #000; padding: 15px; font-family: var(--mono); font-weight: bold; cursor: pointer; font-size: 1rem; transition: background 0.3s;">
                    <?= $lang === 'es' ? 'ENVIAR XML A LA API' : 'SEND XML TO API' ?>
                </button>
            </form>

            <?php if($resultado !== ""): ?>
                <div style="margin-top: 2rem;">
                    <span style="color: var(--gray); font-family: var(--mono); font-size: 0.8rem;"><?= $lang === 'es' ? 'Respuesta del Servidor:' : 'Server Response:' ?></span>
                    <div style="background: #000; padding: 1rem; border-left: 4px solid <?= $esExito ? '#ff2a2a' : 'var(--cyan)' ?>; font-family: var(--mono); font-size: 0.9rem; color: <?= $esExito ? '#ff2a2a' : 'var(--white)' ?>; white-space: pre-wrap; margin-top: 0.5rem; word-break: break-all;">
<?= $resultado ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($esExito): ?>
                <div style="margin-top: 2rem; padding: 1rem; background: rgba(0,255,0,0.1); border: 1px dashed #00ff00; text-align: center; animation: pulse 2s infinite;">
                    <p style="color: #00ff00; margin: 0; font-weight: bold;">✔ <?= $lang === 'es' ? 'Archivo exfiltrado con éxito a través del parser XML.' : 'File successfully exfiltrated through the XML parser.' ?></p>
                    <h3 style="color: #00ff00; margin: 1rem 0; font-family: var(--mono); letter-spacing: 2px;"><?= $flag ?></h3>
                    <p style="margin:0; font-size: 0.85rem; color: var(--cyan);">
                        <?= $lang === 'es' ? 'Terminal oculta. Ejecuta:' : 'Hidden terminal. Run:' ?> <strong>submit <?= $flag ?></strong>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>