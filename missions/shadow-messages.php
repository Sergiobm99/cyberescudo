<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'OP: DEEP STATE — Mission Center' : 'OP: DEEP STATE — Mission Center';
$current_page = 'missions/shadow-messages.php';
require __DIR__ . '/../templates/header.php';
?>

<style>
    .briefing-container {
        max-width: 800px;
        margin: 4rem auto;
        background: #0a0a0a;
        border: 1px solid #333;
        border-top: 4px solid #aa00ff;
        padding: 3rem;
        position: relative;
        box-shadow: 0 10px 30px rgba(0,0,0,0.8);
    }

    .briefing-header {
        border-bottom: 1px dashed #444;
        padding-bottom: 1.5rem;
        margin-bottom: 2rem;
    }

    .classification {
        color: #aa00ff;
        font-family: var(--mono);
        font-weight: bold;
        letter-spacing: 2px;
        display: inline-block;
        border: 1px solid #aa00ff;
        padding: 4px 10px;
        margin-bottom: 1rem;
        font-size: 0.8rem;
    }

    .briefing-title {
        font-size: 2.2rem;
        color: #fff;
        margin: 0;
        text-transform: uppercase;
        font-family: var(--mono);
    }

    .intel-block {
        background: rgba(255, 255, 255, 0.02);
        border-left: 2px solid #aa00ff;
        padding: 1.5rem;
        margin-bottom: 2rem;
        font-family: var(--mono);
        color: #ccc;
        line-height: 1.6;
    }

    .download-section {
        margin-top: 3rem;
        text-align: center;
        padding: 2rem;
        background: #050505;
        border: 1px dashed #333;
    }

    .btn-download {
        display: inline-block;
        background: transparent;
        color: #aa00ff;
        border: 2px solid #aa00ff;
        padding: 12px 30px;
        font-family: var(--mono);
        font-size: 1.1rem;
        font-weight: bold;
        text-decoration: none;
        transition: all 0.3s;
        text-transform: uppercase;
    }

    .btn-download:hover {
        background: #aa00ff;
        color: #000;
        box-shadow: 0 0 15px rgba(170, 0, 255, 0.4);
    }

    .validation-hint {
        margin-top: 2rem;
        font-family: var(--mono);
        font-size: 0.85rem;
        color: #666;
        text-align: center;
    }
</style>

<main class="content-page">
    <div class="briefing-container">
        
        <div class="briefing-header">
            <div class="classification"><?= $lang === 'es' ? 'NIVEL: AVANZADO' : 'LEVEL: ADVANCED' ?></div>
            <h1 class="briefing-title">OP: DEEP STATE</h1>
            <div style="color: #666; font-family: var(--mono); margin-top: 10px;">
                <?= $lang === 'es' ? 'ID EXPEDIENTE:' : 'CASE ID:' ?> #STEG-IMG-004
            </div>
        </div>

        <div class="intel-block">
            <strong style="color: #fff;"><?= $lang === 'es' ? '[ INFORME DE INTELIGENCIA ]' : '[ INTELLIGENCE BRIEF ]' ?></strong><br><br>
            <?= $lang === 'es' ? 
                'Uno de nuestros informantes encubiertos ("Alias: Shadow") fue descubierto. Antes de destruir su equipo, logró subir una fotografía aparentemente inocente a un foro público.<br><br>Sabemos que Shadow utiliza técnicas de esteganografía para ofuscar coordenadas y claves dentro de archivos multimedia. Analiza la imagen y extrae el mensaje de socorro.' : 
                'One of our undercover informants ("Alias: Shadow") was compromised. Before destroying their equipment, they managed to upload a seemingly innocent photograph to a public forum.<br><br>We know Shadow uses steganography techniques to obfuscate coordinates and keys within multimedia files. Analyze the image and extract the distress message.' 
            ?>
        </div>

        <div class="download-section">
            <div style="margin-bottom: 1.5rem; color: #888; font-family: var(--mono);">
                <?= $lang === 'es' ? 'Evidencia interceptada: drop_zone.jpg' : 'Intercepted Evidence: drop_zone.jpg' ?><br>
                <?= $lang === 'es' ? 'Nota: La clave de extracción podría estar vacía (blank password).' : 'Note: The extraction passphrase might be empty (blank password).' ?>
            </div>
            
            <a href="<?= e(BASE_URL) ?>/assets/challenges/drop_zone.jpg" class="btn-download" download="drop_zone.jpg">
                [ <?= $lang === 'es' ? 'DESCARGAR EVIDENCIA' : 'DOWNLOAD EVIDENCE' ?> ]
            </a>
        </div>

        <div class="validation-hint">
            <?= $lang === 'es' ? 'Abre tu terminal favorita (Linux/Kali), usa herramientas como steghide o binwalk, y luego introduce aquí:' : 'Open your favorite terminal (Linux/Kali), use tools like steghide or binwalk, and then enter here:' ?><br>
            <strong style="color: #aa00ff;">submit OP-DEEP-STATE FLAG{...}</strong>
        </div>

    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>