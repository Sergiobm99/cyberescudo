<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $lang === 'es' ? 'Arsenal SOC: KQL & Scripts — CyberEscudo' : 'SOC Arsenal: KQL & Scripts — CyberEscudo';
require __DIR__ . '/templates/header.php';
?>

<main class="content-page" style="max-width: 100%;">
    <div class="arsenal-container">
        <div class="arsenal-header">
            <h1 class="arsenal-title"><?= $lang === 'es' ? 'Arsenal SOC' : 'SOC Arsenal' ?></h1>
            <p style="color: #888; font-family: var(--mono);">
                <?= $lang === 'es' ? 'Directorio táctico de consultas KQL (Threat Hunting) y scripts de automatización.' : 'Tactical directory of KQL queries (Threat Hunting) and automation scripts.' ?>
            </p>
        </div>

        <div class="arsenal-grid">
            <div class="arsenal-sidebar">
                <input type="text" class="arsenal-search" id="searchInput" placeholder="<?= $lang === 'es' ? 'Buscar por táctica (ej: Brute Force, Exfiltration)...' : 'Search by tactic...' ?>">
                <ul class="arsenal-list" id="scriptList">
                    </ul>
            </div>

            <div class="arsenal-viewer" id="viewer">
                <div style="text-align:center; color: var(--gray); margin-top: 10rem; font-family: var(--mono);">
                    <div class="cyber-spinner"></div><br><br>
                    <?= $lang === 'es' ? 'SELECCIONA UN SCRIPT DEL PANEL LATERAL' : 'SELECT A SCRIPT FROM THE SIDE PANEL' ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/templates/footer.php'; ?>