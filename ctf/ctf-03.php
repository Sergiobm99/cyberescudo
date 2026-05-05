<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Reto CTF #03: Reflected XSS — CyberEscudo' : 'CTF Challenge #03: Reflected XSS — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Simulador de vulnerabilidad Cross-Site Scripting.' : 'Cross-Site Scripting vulnerability simulator.';
$current_page = 'ctf/ctf-03.php';
require __DIR__ . '/../templates/header.php';

$busqueda = "";
$flag = "";
$esExito = false;
$tipoXSS = "";

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['q'])) {
    $busqueda = $_GET['q'];
    $busqueda_lower = strtolower($busqueda);
    
    // 100% SEGURO: Verificamos el payload desde PHP, NO imprimimos el script real.
    
    // Ruta 1: Robo de Cookies
    if (strpos($busqueda_lower, 'document.cookie') !== false && (strpos($busqueda_lower, '<script>') !== false || strpos($busqueda_lower, 'onerror=') !== false)) {
        $esExito = true;
        $tipoXSS = $lang === 'es' ? "¡Exfiltración de Cookies conseguida!" : "Cookie Exfiltration successful!";
        $flag = "FLAG{xss_cookie_thief}";
    } 
    // Ruta 2: Alerta básica (PoC)
    elseif (strpos($busqueda_lower, '<script>') !== false && strpos($busqueda_lower, 'alert(') !== false) {
        $esExito = true;
        $tipoXSS = $lang === 'es' ? "¡Ejecución de JavaScript (PoC) conseguida!" : "JavaScript execution (PoC) successful!";
        $flag = "FLAG{xss_alert_master}";
    }
}
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 700px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// <?= $lang === 'es' ? 'RETO CTF 03' : 'CTF CHALLENGE 03' ?></span>
        <h1 style="text-align: center; margin-bottom: 2rem;"><?= $lang === 'es' ? 'Buscador de Artículos' : 'Article Search Engine' ?></h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <p style="text-align: center; color: var(--gray); margin-bottom: 2rem;">
                <?= $lang === 'es' ? 'Busca cualquier término en nuestra base de datos corporativa.' : 'Search for any term in our corporate database.' ?>
            </p>
            
            <form method="GET" action="" style="display: flex; gap: 10px; margin-bottom: 2rem;">
                <input type="text" name="q" class="cyber-input" style="flex: 1;" placeholder="<?= $lang === 'es' ? 'Ej: ciberseguridad, redes...' : 'Ex: cybersecurity, networking...' ?>" autocomplete="off">
                <button type="submit" style="background: var(--cyan); border: none; color: #000; padding: 0 20px; font-family: var(--mono); font-weight: bold; cursor: pointer;">
                    <?= $lang === 'es' ? 'BUSCAR' : 'SEARCH' ?>
                </button>
            </form>

            <?php if($busqueda !== "" && !$esExito): ?>
                <div style="padding: 1rem; border-left: 3px solid var(--cyan); color: var(--white);">
                    <?= $lang === 'es' ? 'Resultados para:' : 'Results for:' ?> <strong style="color: var(--cyan);"><?= htmlspecialchars($busqueda) ?></strong>
                    <br><br>
                    <span style="color: var(--gray); font-size: 0.9rem;"><?= $lang === 'es' ? '0 artículos encontrados.' : '0 articles found.' ?></span>
                </div>
            <?php endif; ?>

            <?php if($esExito): ?>
                <!-- SIMULACIÓN DEL POP-UP DEL NAVEGADOR -->
                <div style="margin: 2rem auto; width: 80%; background: #ddd; border-radius: 6px; border: 1px solid #aaa; color: #000; font-family: sans-serif; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.5); animation: pulse 1s 1;">
                    <div style="background: #eee; padding: 5px 10px; border-bottom: 1px solid #ccc; font-size: 0.8rem; font-weight: bold;">
                        Mensaje de la página web
                    </div>
                    <div style="padding: 20px; text-align: center;">
                        <p style="margin-bottom: 15px; font-size: 1rem;"><?= $tipoXSS ?></p>
                        <button style="background: #0078D7; color: white; border: none; padding: 5px 20px; border-radius: 3px; cursor: pointer;">Aceptar</button>
                    </div>
                </div>

                <div style="margin-top: 2rem; padding: 1rem; background: rgba(0,255,0,0.1); border: 1px dashed #00ff00; text-align: center;">
                    <h3 style="color: #00ff00; margin: 0.5rem 0; font-family: var(--mono); letter-spacing: 2px;"><?= $flag ?></h3>
                    <p style="margin:0; font-size: 0.8rem; color: var(--cyan);">
                        <?= $lang === 'es' ? 'Canjea esta bandera en la terminal secreta:' : 'Redeem this flag in the secret terminal:' ?> <strong>submit <?= $flag ?></strong>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>