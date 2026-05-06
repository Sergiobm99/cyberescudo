<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Reto CTF #15: Shodan Dorking — CyberEscudo' : 'CTF Challenge #15: Shodan Dorking — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Simulador de inteligencia de fuentes abiertas con Shodan.' : 'Open source intelligence simulator with Shodan.';
$current_page = 'ctf/ctf-15.php';
require __DIR__ . '/../templates/header.php';

$feedback = "";
$flag = "";
$esExito = false;
$query_ingresada = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['query'])) {
    $query_ingresada = trim($_POST['query']);
    
    // Validar los 3 componentes del Dork de Shodan sin importar el orden
    
    // 1. Debe incluir la vulnerabilidad vuln:CVE-2021-44228
    $hasVuln = preg_match('/vuln:\s*CVE-2021-44228/i', $query_ingresada);
    
    // 2. Debe incluir el producto Tomcat (product:Tomcat o product:"Apache Tomcat")
    $hasProduct = preg_match('/product:\s*["\']?(Apache\s)?Tomcat["\']?/i', $query_ingresada);
    
    // 3. Debe incluir el país US (country:US o country:"US")
    $hasCountry = preg_match('/country:\s*["\']?US["\']?/i', $query_ingresada);

    if ($hasVuln && $hasProduct && $hasCountry) {
        $esExito = true;
        $feedback = $lang === 'es' 
            ? "¡Excelente Dork! Shodan ha devuelto 4,302 resultados coincidentes. Tienes la foto completa de la exposición." 
            : "Excellent Dork! Shodan returned 4,302 matching results. You have the full picture of the exposure.";
        $flag = "FLAG{shodan_osint_master}";
    } else {
        $errores = [];
        if (!$hasVuln) $errores[] = $lang === 'es' ? "Falta el filtro de vulnerabilidad (vuln:CVE-2021-44228)" : "Missing vulnerability filter (vuln:CVE-2021-44228)";
        if (!$hasProduct) $errores[] = $lang === 'es' ? "Falta el filtro de producto (product:Tomcat)" : "Missing product filter (product:Tomcat)";
        if (!$hasCountry) $errores[] = $lang === 'es' ? "Falta el filtro geográfico (country:US)" : "Missing geographic filter (country:US)";
        
        $feedback = "[SHODAN ERROR] " . implode(" | ", $errores);
    }
}
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 900px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// <?= $lang === 'es' ? 'RETO CTF 15' : 'CTF CHALLENGE 15' ?></span>
        <h1 style="text-align: center; margin-bottom: 2rem;"><?= $lang === 'es' ? 'OSINT: Threat Hunting' : 'OSINT: Threat Hunting' ?></h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <div style="background: #111; padding: 1.5rem; border-left: 4px solid var(--cyan); margin-bottom: 2rem;">
                <h3 style="color: var(--cyan); margin-top: 0;">🌐 <?= $lang === 'es' ? 'Operación de Inteligencia' : 'Intelligence Operation' ?></h3>
                <p style="color: var(--white); font-size: 0.95rem; line-height: 1.6; margin-bottom: 0;">
                    <?= $lang === 'es' ? 'Tu equipo SOC necesita evaluar el riesgo global de un 0-day reciente. Construye una única consulta de búsqueda (Dork) de Shodan que filtre los servidores combinando estos 3 criterios exactos:<br><br>1. Que el producto sea <strong>Tomcat</strong>.<br>2. Que estén geolocalizados en los Estados Unidos (código <strong>US</strong>).<br>3. Que estén etiquetados con la vulnerabilidad Log4Shell (<strong>CVE-2021-44228</strong>).' : 'Your SOC team needs to evaluate the global risk of a recent 0-day. Construct a single Shodan search query (Dork) that filters servers combining these 3 exact criteria:<br><br>1. The product must be <strong>Tomcat</strong>.<br>2. They must be geolocated in the United States (code <strong>US</strong>).<br>3. They must be tagged with the Log4Shell vulnerability (<strong>CVE-2021-44228</strong>).' ?>
                </p>
            </div>
            
            <form method="POST" action="">
                <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                    <?= $lang === 'es' ? 'Shodan Search Query:' : 'Shodan Search Query:' ?>
                </label>
                <div style="display: flex; align-items: center; background: #050505; border: 1px solid #333; padding: 0.5rem; border-radius: 4px; margin-bottom: 1.5rem;">
                    <span style="color: #4a90e2; margin-right: 10px; font-family: var(--mono);">🔍</span>
                    <input type="text" name="query" class="cyber-input" style="flex: 1; border: none; background: transparent; padding: 0; box-shadow: none;" placeholder="filtro:valor filtro2:valor2 ..." autocomplete="off" value="<?= htmlspecialchars($query_ingresada) ?>">
                </div>
                
                <button type="submit" style="width: 100%; background: var(--cyan); border: none; color: #000; padding: 15px; font-family: var(--mono); font-weight: bold; cursor: pointer; font-size: 1rem; transition: background 0.3s;">
                    <?= $lang === 'es' ? 'EJECUTAR BÚSQUEDA' : 'EXECUTE SEARCH' ?>
                </button>
            </form>

            <?php if($feedback !== ""): ?>
                <div style="margin-top: 2rem; padding: 1rem; background: <?= $esExito ? 'rgba(0,255,0,0.1)' : 'rgba(255,42,42,0.1)' ?>; border: 1px solid <?= $esExito ? '#00ff00' : '#ff2a2a' ?>; font-family: var(--mono); font-size: 0.9rem; color: <?= $esExito ? '#00ff00' : '#ff2a2a' ?>; line-height: 1.5;">
                    <?= $feedback ?>
                </div>
            <?php endif; ?>

            <?php if($esExito): ?>
                <div style="margin-top: 1.5rem; text-align: center; animation: pulse 2s infinite;">
                    <h3 style="color: #00ff00; margin: 1rem 0; font-family: var(--mono); letter-spacing: 2px;"><?= $flag ?></h3>
                    <p style="margin:0; font-size: 0.85rem; color: var(--cyan);">
                        <?= $lang === 'es' ? 'Ve a la terminal del sistema y canjea tu código:' : 'Go to the system terminal and redeem your code:' ?> <strong>submit <?= $flag ?></strong>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>