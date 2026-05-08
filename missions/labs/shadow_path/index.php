<?php
// Asegúrate de incluir aquí tu sistema de idiomas/sesiones
// Ejemplo: include '../../includes/config.php'; 
// Asumimos que $lang ya está definida ('es' o 'en')
if (!isset($lang)) { $lang = 'es'; } // Fallback por defecto
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <title>CyberEscudo - OP: SHADOW PATH</title>
    <style>
        body { background-color: #0d0d0d; color: #00ff41; font-family: monospace; padding: 2rem; }
        .terminal-box { border: 1px solid var(--cyan, #00ffff); padding: 1.5rem; max-width: 600px; margin: 0 auto; box-shadow: 0 0 10px rgba(0, 255, 255, 0.2); }
        h2 { color: #aa00ff; text-transform: uppercase; }
        a.btn-download { color: #0d0d0d; background-color: var(--cyan, #00ffff); padding: 10px 15px; text-decoration: none; font-weight: bold; display: inline-block; margin-top: 15px; }
        a.btn-download:hover { background-color: #fff; }
    </style>
</head>
<body>

<div class="terminal-box">
    <h2>[ OP: SHADOW PATH ]</h2>
    <p>> <?php echo $lang === 'es' ? 'CONECTANDO AL SERVIDOR DE DOCUMENTOS...' : 'CONNECTING TO DOCUMENT SERVER...'; ?></p>
    <p>> <?php echo $lang === 'es' ? 'ESTADO: ONLINE.' : 'STATUS: ONLINE.'; ?></p>
    <br>
    
    <p>
        <?php echo $lang === 'es' 
        ? 'Hemos detectado un servidor de la facción enemiga alojando informes desclasificados. Sabemos que guardan credenciales vitales en una bóveda del sistema llamada <strong>"hidden_vault"</strong>, dos niveles por encima de la carpeta de reportes actual.' 
        : 'We have detected an enemy faction server hosting declassified reports. We know they store vital credentials in a system vault called <strong>"hidden_vault"</strong>, two levels above the current reports folder.'; ?>
    </p>
    <p>
        <?php echo $lang === 'es' 
        ? 'Objetivo: Manipular el sistema de descargas para extraer credentials.txt.' 
        : 'Objective: Manipulate the download system to extract credentials.txt.'; ?>
    </p>
    
    <hr style="border-color: #333;">
    
    <h3><?php echo $lang === 'es' ? 'ARCHIVOS DISPONIBLES:' : 'AVAILABLE FILES:'; ?></h3>
    <ul>
        <li>informe_2026.txt [<?php echo $lang === 'es' ? 'TAMAÑO' : 'SIZE'; ?>: 128 B]</li>
    </ul>

    <a href="download.php?file=informe_2026.txt" class="btn-download" target="_blank">
        [ <?php echo $lang === 'es' ? 'INICIAR DESCARGA' : 'INITIATE DOWNLOAD'; ?> ]
    </a>
</div>
</body>
</html>