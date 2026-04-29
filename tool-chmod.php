<?php
require_once __DIR__ . '/bootstrap.php';

$pageTitle = SITE_NAME . ' — Calculadora de Permisos (Chmod)';
$pageDescription = 'Calculadora interactiva de permisos Linux/Unix. Genera comandos chmod y convierte entre notación octal y simbólica.';

// Variable para que el desplegable sepa en qué página estamos
$current_page = basename($_SERVER['PHP_SELF']); 

require __DIR__ . '/templates/header.php';
?>

<section class="section tool-page">
    <div class="section-inner" style="max-width: 800px; margin: 0 auto;">
        
        <div class="tool-select-wrapper">
            <select class="tool-selector" id="tool-switcher">
                <option value="" disabled>-- <?= $lang==='es' ? 'Selecciona una herramienta' : 'Select a tool' ?> --</option>
                <option value="<?= BASE_URL ?>/tool-ip.php" <?= $current_page==='tool-ip.php' ? 'selected' : '' ?>>🌐 <?= $lang==='es' ? '¿Cuál es mi IP?' : 'What is my IP?' ?></option>
                <option value="<?= BASE_URL ?>/tool-passgen.php" <?= $current_page==='tool-passgen.php' ? 'selected' : '' ?>>🔑 <?= $lang==='es' ? 'Generador de Contraseñas' : 'Password Generator' ?></option>
                <option value="<?= BASE_URL ?>/tool-passcheck.php" <?= $current_page==='tool-passcheck.php' ? 'selected' : '' ?>>🛡️ <?= $lang==='es' ? 'Fortaleza de Contraseña' : 'Password Strength' ?></option>
                <option value="<?= BASE_URL ?>/tool-hash.php" <?= $current_page==='tool-hash.php' ? 'selected' : '' ?>>#️⃣ <?= $lang==='es' ? 'Generador de Hashes' : 'Hash Generator' ?></option>
                <option value="<?= BASE_URL ?>/tool-hashcrack.php" <?= $current_page==='tool-hashcrack.php' ? 'selected' : '' ?>>🔓 <?= $lang==='es' ? 'Analizador/Cracker de Hashes' : 'Hash Analyzer/Cracker' ?></option>
                <option value="<?= BASE_URL ?>/tool-base64.php" <?= $current_page==='tool-base64.php' ? 'selected' : '' ?>>🔄 <?= $lang==='es' ? 'Codificador/Decodificador Base64' : 'Base64 Encoder/Decoder' ?></option>
                <option value="<?= BASE_URL ?>/tool-cidr.php" <?= $current_page==='tool-cidr.php' ? 'selected' : '' ?>>🌍 <?= $lang==='es' ? 'Calculadora de Subredes CIDR' : 'CIDR Subnet Calculator' ?></option>
                <option value="<?= BASE_URL ?>/tool-jwt.php" <?= $current_page==='tool-jwt.php' ? 'selected' : '' ?>>🔓 <?= $lang==='es' ? 'Decodificador JWT' : 'JWT Decoder' ?></option>
                <option value="<?= BASE_URL ?>/tool-url.php" <?= $current_page==='tool-url.php' ? 'selected' : '' ?>>🔗 <?= $lang==='es' ? 'Codificador/Decodificador de URL' : 'URL Encoder/Decoder' ?></option>
                <option value="<?= BASE_URL ?>/tool-chmod.php" <?= $current_page==='tool-chmod.php' ? 'selected' : '' ?>>🐧 <?= $lang==='es' ? 'Calculadora Chmod Linux' : 'Linux Chmod Calculator' ?></option>
				<option value="<?= BASE_URL ?>/tool-regex.php" <?= $current_page==='tool-regex.php' ? 'selected' : '' ?>>🛡️ <?= $lang==='es' ? 'Generador Regex Contraseñas' : 'Password Regex Generator' ?></option>
      <option value="<?= BASE_URL ?>/tool-mac.php" <?= $current_page==='tool-mac.php' ? 'selected' : '' ?>>🏷️ <?= $lang==='es' ? 'Buscador de Fabricante MAC' : 'MAC Vendor Lookup' ?></option>
				<option value="<?= BASE_URL ?>/tool-revshell.php" <?= $current_page==='tool-revshell.php' ? 'selected' : '' ?>>🐚 <?= $lang==='es' ? 'Generador Reverse Shells' : 'Reverse Shell Generator' ?></option>
                <option value="<?= BASE_URL ?>/tool-cron.php" <?= $current_page==='tool-cron.php' ? 'selected' : '' ?>>⏱ <?= $lang==='es' ? 'Analizador Cron' : 'Cron Parser' ?></option>
            </select>
        </div>
        
        <div class="chmod-tool">
            <div class="chmod-panel">
                <h2><?= $lang==='es' ? 'Calculadora de permisos Linux' : 'Linux Permissions Calculator' ?></h2>

                <div class="chmod-result">
                    <div class="chmod-number" id="chmodNumber">000</div>
                    <div class="chmod-symbolic" id="chmodSymbolic">---------</div>
                    <div class="chmod-command" id="chmodCommand">chmod 000 <?= $lang==='es' ? 'archivo' : 'file' ?></div>
                </div>

                <div class="chmod-grid">
                    <div class="chmod-col">
                        <h3><?= $lang==='es' ? 'Propietario' : 'Owner' ?></h3>
                        <label class="chmod-btn">
                            <input class="perm-cb" type="checkbox" data-group="owner" data-sym="r">
                            <span class="btn-text"><?= $lang==='es' ? 'Leer' : 'Read' ?></span>
                            <span class="btn-value">4</span>
                        </label>
                        <label class="chmod-btn">
                            <input class="perm-cb" type="checkbox" data-group="owner" data-sym="w">
                            <span class="btn-text"><?= $lang==='es' ? 'Escribir' : 'Write' ?></span>
                            <span class="btn-value">2</span>
                        </label>
                        <label class="chmod-btn">
                            <input class="perm-cb" type="checkbox" data-group="owner" data-sym="x">
                            <span class="btn-text"><?= $lang==='es' ? 'Ejecutar' : 'Execute' ?></span>
                            <span class="btn-value">1</span>
                        </label>
                    </div>

                    <div class="chmod-col">
                        <h3><?= $lang==='es' ? 'Grupo' : 'Group' ?></h3>
                        <label class="chmod-btn">
                            <input class="perm-cb" type="checkbox" data-group="group" data-sym="r">
                            <span class="btn-text"><?= $lang==='es' ? 'Leer' : 'Read' ?></span>
                            <span class="btn-value">4</span>
                        </label>
                        <label class="chmod-btn">
                            <input class="perm-cb" type="checkbox" data-group="group" data-sym="w">
                            <span class="btn-text"><?= $lang==='es' ? 'Escribir' : 'Write' ?></span>
                            <span class="btn-value">2</span>
                        </label>
                        <label class="chmod-btn">
                            <input class="perm-cb" type="checkbox" data-group="group" data-sym="x">
                            <span class="btn-text"><?= $lang==='es' ? 'Ejecutar' : 'Execute' ?></span>
                            <span class="btn-value">1</span>
                        </label>
                    </div>

                    <div class="chmod-col">
                        <h3><?= $lang==='es' ? 'Otros' : 'Public' ?></h3>
                        <label class="chmod-btn">
                            <input class="perm-cb" type="checkbox" data-group="others" data-sym="r">
                            <span class="btn-text"><?= $lang==='es' ? 'Leer' : 'Read' ?></span>
                            <span class="btn-value">4</span>
                        </label>
                        <label class="chmod-btn">
                            <input class="perm-cb" type="checkbox" data-group="others" data-sym="w">
                            <span class="btn-text"><?= $lang==='es' ? 'Escribir' : 'Write' ?></span>
                            <span class="btn-value">2</span>
                        </label>
                        <label class="chmod-btn">
                            <input class="perm-cb" type="checkbox" data-group="others" data-sym="x">
                            <span class="btn-text"><?= $lang==='es' ? 'Ejecutar' : 'Execute' ?></span>
                            <span class="btn-value">1</span>
                        </label>
                    </div>
                </div>

                <button type="button" id="chmodReset" class="chmod-reset">
                    <?= $lang==='es' ? 'Reiniciar' : 'Reset' ?>
                </button>
            </div>
        </div>
    </div>
</section>

<script src="<?= BASE_URL ?>/assets/js/tools.js"></script>
<?php require __DIR__ . '/templates/footer.php'; ?>