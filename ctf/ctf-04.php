<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Reto CTF #04: CSRF Forgery — CyberEscudo' : 'CTF Challenge #04: CSRF Forgery — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Simulador de explotación de Cross-Site Request Forgery.' : 'Cross-Site Request Forgery exploitation simulator.';
$current_page = 'ctf/ctf-04.php';
require __DIR__ . '/../templates/header.php';

$payload = "";
$flag = "";
$esExito = false;
$feedback = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['html_payload'])) {
    $payload = $_POST['html_payload'];
    $payload_lower = strtolower($payload);
    
    // 100% SEGURO: Analizamos estáticamente si el payload construiría un ataque CSRF válido.
    $hasForm = strpos($payload_lower, '<form') !== false;
    $hasMethodPost = strpos($payload_lower, 'method="post"') !== false || strpos($payload_lower, "method='post'") !== false;
    $hasAction = strpos($payload_lower, 'action=') !== false;
    $hasAmount = strpos($payload_lower, 'name="amount"') !== false || strpos($payload_lower, "name='amount'") !== false;
    $hasSubmit = strpos($payload_lower, 'type="submit"') !== false || strpos($payload_lower, 'onload=') !== false || strpos($payload_lower, '.submit()') !== false;

    if ($hasForm && $hasMethodPost && $hasAction && $hasAmount && $hasSubmit) {
        $esExito = true;
        $feedback = $lang === 'es' ? "¡Vulnerabilidad confirmada! El payload forjaría con éxito una petición cruzada." : "Vulnerability confirmed! The payload successfully forges a cross-site request.";
        $flag = "FLAG{csrf_forgery_expert}";
    } else {
        $faltan = [];
        if (!$hasForm) $faltan[] = "<form>";
        if (!$hasMethodPost) $faltan[] = 'method="POST"';
        if (!$hasAction) $faltan[] = 'action=""';
        if (!$hasAmount) $faltan[] = 'input name="amount"';
        if (!$hasSubmit) $faltan[] = 'Auto-submit trigger (JS) o submit button';
        
        $feedback = $lang === 'es' ? "Payload fallido. Te faltan estos elementos clave para un CSRF válido: " . implode(", ", $faltan) : "Payload failed. You are missing these key elements for a valid CSRF: " . implode(", ", $faltan);
    }
}
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 800px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// <?= $lang === 'es' ? 'RETO CTF 04' : 'CTF CHALLENGE 04' ?></span>
        <h1 style="text-align: center; margin-bottom: 2rem;"><?= $lang === 'es' ? 'CSRF PoC Generator' : 'CSRF PoC Generator' ?></h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <p style="color: var(--gray); margin-bottom: 1rem; line-height: 1.6;">
                <strong><?= $lang === 'es' ? 'Misión:' : 'Mission:' ?></strong> <?= $lang === 'es' ? 'El servidor del banco <code>https://banco.local/transfer</code> no tiene protección CSRF y acepta peticiones POST. Escribe el código HTML que alojarías en tu web maliciosa para forzar una transferencia enviando un campo <code>name="amount"</code> y que se envíe.' : 'The bank server <code>https://bank.local/transfer</code> has no CSRF protection and accepts POST requests. Write the HTML code you would host on your malicious site to force a transfer sending an <code>name="amount"</code> field.' ?>
            </p>

            <?php if($feedback !== "" && !$esExito): ?>
                <div style="color: #ff2a2a; border: 1px solid #ff2a2a; padding: 10px; margin-bottom: 1.5rem; background: rgba(255,42,42,0.1); font-family: var(--mono); font-size: 0.9rem;">
                    [ERROR] <?= $feedback ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <textarea name="html_payload" class="cyber-input" style="width: 100%; height: 200px; font-family: var(--mono); font-size: 0.9rem; margin-bottom: 1rem; resize: vertical; box-sizing: border-box;" placeholder="<!-- Escribe tu HTML aquí -->"><?= htmlspecialchars($_POST['html_payload'] ?? '') ?></textarea>
                
                <button type="submit" style="width: 100%; background: var(--cyan); border: none; color: #000; padding: 15px; font-family: var(--mono); font-weight: bold; cursor: pointer; font-size: 1.1rem;">
                    <?= $lang === 'es' ? 'TESTEAR PAYLOAD (PoC)' : 'TEST PAYLOAD (PoC)' ?>
                </button>
            </form>

            <?php if($esExito): ?>
                <div style="margin-top: 2rem; padding: 1.5rem; background: rgba(0,255,0,0.1); border: 1px dashed #00ff00; text-align: center; animation: pulse 2s infinite;">
                    <h3 style="color: #00ff00; margin: 0; font-family: var(--mono); letter-spacing: 1px;"><?= $feedback ?></h3>
                    <h2 style="color: #fff; margin: 1rem 0; font-family: var(--mono); letter-spacing: 3px; font-size: 1.8rem;"><?= $flag ?></h2>
                    <p style="margin:0; font-size: 0.9rem; color: var(--cyan);">
                        <?= $lang === 'es' ? 'Abre la terminal de CyberEscudo y ejecuta:' : 'Open the CyberEscudo terminal and execute:' ?> <strong>submit <?= $flag ?></strong>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>