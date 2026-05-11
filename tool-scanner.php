<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $lang === 'es' ? 'Escáner de Seguridad — CyberEscudo' : 'Security Scanner — CyberEscudo';
require __DIR__ . '/templates/header.php';

$scan_completed = false;
$score = 100;
$issues = [];
$target_url = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['target_url'])) {
    $target_url = filter_var($_POST['target_url'], FILTER_SANITIZE_URL);
    
    // Asegurar que tiene http/https
    if (!preg_match("~^(?:f|ht)tps?://~i", $target_url)) {
        $target_url = "https://" . $target_url;
    }

    $headers = @get_headers($target_url, 1);
    
    if ($headers === false) {
        $issues[] = [
            'type' => 'critical', 
            'msg' => $lang === 'es' ? 'HOST INACCESIBLE: No se pudo conectar con el objetivo.' : 'HOST UNREACHABLE: Could not connect to the target.',
            'sol' => $lang === 'es' ? 'Verifica que el dominio existe, está encendido y no bloquea peticiones automáticas (Firewall/WAF).' : 'Verify that the domain exists, is online, and is not blocking automated requests (Firewall/WAF).'
        ];
        $score = 0;
    } else {
        $headers_lower = array_change_key_case($headers, CASE_LOWER);

        // 1. Check HTTPS
        if (strpos($target_url, 'https://') === false) {
            $score -= 30;
            $issues[] = [
                'type' => 'high', 
                'msg' => $lang === 'es' ? 'Tráfico no cifrado (HTTP). Interceptación posible.' : 'Unencrypted traffic (HTTP). Interception possible.',
                'sol' => $lang === 'es' ? 'Instala un certificado SSL (Let\'s Encrypt) y fuerza la redirección a HTTPS en tu servidor.' : 'Install an SSL certificate (Let\'s Encrypt) and force HTTPS redirection on your server.'
            ];
        }

        // 2. Clickjacking (X-Frame-Options)
        if (!isset($headers_lower['x-frame-options'])) {
            $score -= 15;
            $issues[] = [
                'type' => 'medium', 
                'msg' => $lang === 'es' ? 'Falta X-Frame-Options. Vulnerable a Clickjacking.' : 'Missing X-Frame-Options. Vulnerable to Clickjacking.',
                'sol' => $lang === 'es' ? 'Añade la cabecera <code>Header always set X-Frame-Options "SAMEORIGIN"</code> en tu .htaccess o Nginx. <a href="https://developer.mozilla.org/es/docs/Web/HTTP/Headers/X-Frame-Options" target="_blank">Leer más</a>' : 'Add the header <code>Header always set X-Frame-Options "SAMEORIGIN"</code>. <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options" target="_blank">Read more</a>'
            ];
        }

        // 3. HSTS (Strict-Transport-Security)
        if (!isset($headers_lower['strict-transport-security'])) {
            $score -= 15;
            $issues[] = [
                'type' => 'medium', 
                'msg' => $lang === 'es' ? 'Falta HSTS. Degradación de cifrado posible.' : 'Missing HSTS. SSL stripping possible.',
                'sol' => $lang === 'es' ? 'Añade la cabecera <code>Strict-Transport-Security "max-age=31536000; includeSubDomains"</code> para obligar al navegador a usar siempre HTTPS.' : 'Add the header <code>Strict-Transport-Security "max-age=31536000; includeSubDomains"</code> to force browsers to always use HTTPS.'
            ];
        }

        // 4. XSS Protection (Content-Security-Policy)
        if (!isset($headers_lower['content-security-policy'])) {
            $score -= 20;
            $issues[] = [
                'type' => 'high', 
                'msg' => $lang === 'es' ? 'Falta CSP. Riesgo crítico de inyección XSS.' : 'Missing CSP. Critical risk of XSS injection.',
                'sol' => $lang === 'es' ? 'Implementa una política CSP estricta para controlar qué scripts pueden ejecutarse. <a href="https://developer.mozilla.org/es/docs/Web/HTTP/CSP" target="_blank">Guía CSP de Mozilla</a>' : 'Implement a strict CSP policy to control which scripts can run. <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP" target="_blank">Mozilla CSP Guide</a>'
            ];
        }

        // 5. Fuga de Información (Server / X-Powered-By)
        if (isset($headers_lower['server']) || isset($headers_lower['x-powered-by'])) {
            $score -= 10;
            $info = isset($headers_lower['server']) ? 'Server' : 'X-Powered-By';
            $issues[] = [
                'type' => 'low', 
                'msg' => $lang === 'es' ? "Fuga de información en la cabecera [$info]." : "Information disclosure in [$info] header.",
                'sol' => $lang === 'es' ? 'Oculta la versión de tu servidor. En PHP, cambia <code>expose_php = Off</code> en el php.ini. En el servidor web, usa <code>ServerTokens Prod</code>.' : 'Hide your server version. In PHP, set <code>expose_php = Off</code> in php.ini. On web server, use <code>ServerTokens Prod</code>.'
            ];
        }
    }
    
    // Evitar puntuaciones negativas
    if ($score < 0) $score = 0;
    $scan_completed = true;
}
?>

<style>
    .scanner-container { max-width: 800px; margin: 4rem auto; padding: 2rem; background: #0a0a0a; border: 1px solid #333; border-radius: 8px; font-family: var(--mono); }
    .scan-input { width: 100%; padding: 15px; background: #111; border: 1px solid var(--cyan); color: var(--cyan); font-family: var(--mono); font-size: 1.1rem; border-radius: 4px; margin-bottom: 20px; outline: none;}
    .scan-input:focus { box-shadow: 0 0 15px rgba(0,255,255,0.2); }
    .btn-scan { width: 100%; background: var(--cyan); color: #000; padding: 15px; font-weight: bold; border: none; cursor: pointer; font-size: 1.2rem; transition: 0.3s; text-transform: uppercase;}
    .btn-scan:hover { box-shadow: 0 0 20px var(--cyan); }
    
    .results-box { margin-top: 3rem; border: 1px solid #333; padding: 2rem; background: #050505; position: relative; }
    .score-circle { width: 120px; height: 120px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: bold; margin: 0 auto 2rem auto; border: 4px solid; }
    
    .score-good { color: #00ff41; border-color: #00ff41; box-shadow: 0 0 20px rgba(0,255,65,0.2); }
    .score-warn { color: #ffcc00; border-color: #ffcc00; box-shadow: 0 0 20px rgba(255,204,0,0.2); }
    .score-bad { color: #ff2a2a; border-color: #ff2a2a; box-shadow: 0 0 20px rgba(255,42,42,0.2); }
    
    .issue-item { padding: 15px; border-left: 3px solid; margin-bottom: 15px; background: #111; border-radius: 0 4px 4px 0;}
    .issue-high { border-color: #ff2a2a; }
    .issue-medium { border-color: #ffcc00; }
    .issue-low { border-color: #00ffff; }
    
    .issue-title { font-weight: bold; margin-bottom: 8px; font-size: 1.05rem; }
    .issue-high .issue-title { color: #ff2a2a; }
    .issue-medium .issue-title { color: #ffcc00; }
    .issue-low .issue-title { color: #00ffff; }

    .issue-solution { font-size: 0.85rem; color: #aaa; margin-left: 20px; position: relative; line-height: 1.4;}
    .issue-solution::before { content: "↳ FIX: "; color: var(--terminal-green); font-weight: bold; position: absolute; left: -50px; }
    .issue-solution code { background: #222; padding: 2px 5px; color: #fff; border-radius: 3px; font-size: 0.8rem; }
    .issue-solution a { color: var(--cyan); text-decoration: none; border-bottom: 1px dotted var(--cyan); }
    .issue-solution a:hover { color: #fff; }

    /* ANIMACIÓN DE CARGA */
    .loader-container { display: none; text-align: center; margin-top: 2rem; }
    .spinner { width: 60px; height: 60px; border: 4px solid rgba(0, 255, 255, 0.1); border-top-color: var(--cyan); border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 1rem auto; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .loader-text { color: var(--cyan); font-family: var(--mono); font-weight: bold; letter-spacing: 2px; animation: pulse 1.5s infinite; }
    @keyframes pulse { 0% { opacity: 0.5; } 50% { opacity: 1; } 100% { opacity: 0.5; } }
</style>

<main class="content-page">
    <div class="scanner-container">
        <h1 style="color: var(--cyan); text-align: center; margin-bottom: 1rem; text-transform: uppercase;">
            <?= $lang === 'es' ? 'Escáner Perimetral' : 'Perimeter Scanner' ?>
        </h1>
        <p style="text-align: center; color: #888; margin-bottom: 2rem;">
            <?= $lang === 'es' ? 'Analiza las defensas pasivas y cabeceras de seguridad de cualquier objetivo.' : 'Analyze passive defenses and security headers of any target.' ?>
        </p>

        <form id="scanner-form" method="POST" action="">
            <input type="text" name="target_url" class="scan-input" placeholder="ej: https://target-website.com" value="<?= htmlspecialchars($target_url) ?>" required>
            <button type="submit" id="btn-scan" class="btn-scan">⚡ <?= $lang === 'es' ? 'Iniciar Escaneo' : 'Initiate Scan' ?></button>
        </form>

        <div id="loading-zone" class="loader-container">
            <div class="spinner"></div>
            <div class="loader-text"><?= $lang === 'es' ? 'ESTABLECIENDO CONEXIÓN CON EL OBJETIVO...' : 'ESTABLISHING CONNECTION TO TARGET...' ?></div>
        </div>

        <?php if ($scan_completed): ?>
            <?php 
                $score_class = 'score-good';
                if ($score < 80) $score_class = 'score-warn';
                if ($score < 50) $score_class = 'score-bad';
            ?>
            <div class="results-box" id="results-box">
                <div style="position: absolute; top: -10px; left: 20px; background: #050505; padding: 0 10px; color: #666; font-size: 0.8rem;">
                    [ RESULTADOS DEL ANÁLISIS ]
                </div>
                
                <div class="score-circle <?= $score_class ?>">
                    <?= $score ?>
                </div>

                <?php if (empty($issues) && $score == 100): ?>
                    <div class="issue-item issue-low" style="border-color: #00ff41;">
                        <div class="issue-title" style="color: #00ff41;">[+] <?= $lang === 'es' ? 'OBJETIVO SEGURO.' : 'TARGET SECURE.' ?></div>
                        <div style="color: #888;"><?= $lang === 'es' ? 'No se detectaron vulnerabilidades perimetrales.' : 'No perimeter vulnerabilities detected.' ?></div>
                    </div>
                <?php else: ?>
                    <h3 style="color: #fff; margin-bottom: 1rem; font-size: 1rem;">
                        <?= $lang === 'es' ? 'Brechas Detectadas y Mitigación:' : 'Detected Breaches & Mitigation:' ?>
                    </h3>
                    <?php foreach ($issues as $issue): ?>
                        <div class="issue-item issue-<?= htmlspecialchars($issue['type']) ?>">
                            <div class="issue-title">[!] <?= htmlspecialchars($issue['msg']) ?></div>
                            <div class="issue-solution"><?= $issue['sol'] ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require __DIR__ . '/templates/footer.php'; ?>