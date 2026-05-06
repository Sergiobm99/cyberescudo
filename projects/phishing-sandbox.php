<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Simulador SOC: Análisis de Phishing — CyberEscudo' : 'SOC Simulator: Phishing Analysis — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Herramienta interactiva para detectar amenazas en correos electrónicos.' : 'Interactive tool to detect email threats.';
$current_page = 'projects/phishing-sandbox.php';
require __DIR__ . '/../templates/header.php';
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 900px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: var(--cyan);">// <?= $lang === 'es' ? 'BLUE TEAM SANDBOX' : 'BLUE TEAM SANDBOX' ?></span>
        <h1 style="margin-bottom: 1rem;"><?= $lang === 'es' ? 'Analizador de Phishing' : 'Phishing Analyzer' ?></h1>
        <p style="color: var(--gray); margin-bottom: 2rem;">
            <?= $lang === 'es' ? 'Bienvenido al simulador de Triaje del SOC. Un empleado ha reportado este correo como sospechoso. Tu misión es <strong>hacer clic en las 3 banderas rojas (Red Flags)</strong> que demuestran que es un ataque de Ingeniería Social.' : 'Welcome to the SOC Triage simulator. An employee has reported this email as suspicious. Your mission is to <strong>click on the 3 red flags</strong> that prove this is a Social Engineering attack.' ?>
        </p>

        <!-- Panel de Puntuación -->
        <div class="soc-dashboard">
            <div>
                <span style="color: var(--cyan); font-family: var(--mono);">[ <?= $lang === 'es' ? 'ESTADO: EN INVESTIGACIÓN' : 'STATUS: UNDER INVESTIGATION' ?> ]</span>
            </div>
            <div class="score-badge">
                RED FLAGS: <span id="flags-counter">0</span> / 3
            </div>
        </div>

        <!-- Cliente de Correo Simulado -->
        <div class="email-client">
            <div class="email-header">
                <div class="email-header-row">
                    <span class="email-label"><?= $lang === 'es' ? 'De:' : 'From:' ?></span> 
                    <strong><?= $lang === 'es' ? 'Soporte Técnico' : 'Technical Support' ?></strong> &lt;<span class="suspicious-element" data-info="<?= $lang === 'es' ? 'Dominio suplantado: paypaI-security (Usa una \'i\' mayúscula en lugar de una \'L\')' : 'Spoofed domain: paypaI-security (Uses a capital \'i\' instead of an \'L\')' ?>">soporte@paypaI-security.com</span>&gt;
                </div>
                <div class="email-header-row">
                    <span class="email-label"><?= $lang === 'es' ? 'Para:' : 'To:' ?></span> admin@tuempresa.com
                </div>
                <div class="email-header-row">
                    <span class="email-label"><?= $lang === 'es' ? 'Asunto:' : 'Subject:' ?></span> ⚠️ <?= $lang === 'es' ? 'ALERTA: Su cuenta ha sido restringida temporalmente' : 'ALERT: Your account has been temporarily restricted' ?>
                </div>
            </div>
            
            <div class="email-body">
                <p><?= $lang === 'es' ? 'Estimado cliente,' : 'Dear customer,' ?></p>
                <p><?= $lang === 'es' ? 'Hemos detectado actividad inusual en su cuenta comercial y por motivos de seguridad hemos suspendido sus transacciones temporalmente.' : 'We have detected unusual activity on your business account and for security reasons we have temporarily suspended your transactions.' ?></p>
                <p><?= $lang === 'es' ? 'Para restaurar el acceso inmediato y evitar la suspensión permanente de sus fondos, por favor verifique su identidad en el siguiente enlace seguro:' : 'To restore immediate access and avoid permanent suspension of your funds, please verify your identity at the following secure link:' ?></p>
                
                <p style="text-align: center; margin: 30px 0;">
                    <!-- Red Flag 2: Enlace engañoso -->
                    <a href="#" class="suspicious-element" style="background: #0070ba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;" title="<?= $lang === 'es' ? 'Destino real: http://login-update-192.ru/auth/login.php' : 'Real destination: http://login-update-192.ru/auth/login.php' ?>" onclick="event.preventDefault();"><?= $lang === 'es' ? 'Verificar mi cuenta ahora' : 'Verify my account now' ?></a>
                </p>
                
                <p><?= $lang === 'es' ? 'Si no reconoce esta actividad, por favor descargue y revise el reporte de accesos adjunto a este correo.' : 'If you do not recognize this activity, please download and review the access report attached to this email.' ?></p>
                <p><?= $lang === 'es' ? 'Atentamente,<br>El equipo de seguridad.' : 'Sincerely,<br>The security team.' ?></p>
            </div>
            
            <div class="email-attachment">
                <span>📎</span>
                <!-- Red Flag 3: Doble extensión -->
                <span class="suspicious-element" data-info="<?= $lang === 'es' ? 'Doble extensión de archivo. Finge ser PDF pero es un ejecutable.' : 'Double file extension. Pretends to be a PDF but is an executable.' ?>"><?= $lang === 'es' ? 'reporte_accesos.pdf.exe' : 'access_report.pdf.exe' ?></span>
                <span style="color: #888; font-size: 0.85rem;">(245 KB)</span>
            </div>
        </div>
        <!-- Mensaje de Éxito y Explicación -->
        <div id="success-message">
            <h3>🛡️ <?= $lang === 'es' ? '¡AMENAZA NEUTRALIZADA!' : 'THREAT NEUTRALIZED!' ?> 🛡️</h3>
            <p><?= $lang === 'es' ? 'Excelente trabajo analítico. Has identificado correctamente los vectores de ataque:' : 'Excellent analytical work. You correctly identified the attack vectors:' ?></p>
            <ul style="text-align: left; max-width: 600px; margin: 15px auto; line-height: 1.6;">
                <li>
                    <strong><?= $lang === 'es' ? 'Spoofing Visual (Typosquatting):' : 'Visual Spoofing (Typosquatting):' ?></strong> 
                    <?= $lang === 'es' ? 'El atacante usó "paypaI" (con \'i\' mayúscula) para engañar al ojo humano.' : 'The attacker used "paypaI" (with a capital \'i\') to deceive the human eye.' ?>
                </li>
                <li>
                    <strong><?= $lang === 'es' ? 'Enlace Malicioso:' : 'Malicious Link:' ?></strong> 
                    <?= $lang === 'es' ? 'El texto del botón parecía legítimo, pero ocultaba una redirección a un servidor ruso HTTP.' : 'The button text appeared legitimate but hid a redirect to a Russian HTTP server.' ?>
                </li>
                <li>
                    <strong><?= $lang === 'es' ? 'Malware Adjunto:' : 'Attached Malware:' ?></strong> 
                    <?= $lang === 'es' ? 'El archivo usaba una doble extensión (.pdf.exe) para ocultar su verdadera naturaleza ejecutable.' : 'The file used a double extension (.pdf.exe) to hide its true executable nature.' ?>
                </li>
            </ul>
            <p style="margin-top: 20px; color: var(--cyan);">
                <?= $lang === 'es' ? 'Valida tu auditoría en la terminal principal con la flag:' : 'Validate your audit in the main terminal with the flag:' ?> <strong>submit FLAG{phishing_triage_expert}</strong>
            </p>
        </div>

    </div>
</main>

<script src="/assets/js/phishing.js"></script>
<?php require __DIR__ . '/../templates/footer.php'; ?>