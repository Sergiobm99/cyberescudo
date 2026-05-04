<?php
require_once __DIR__ . '/bootstrap.php';

$pageTitle = $lang==='es' ? 'Generador de Reporte OSINT — CyberEscudo' : 'OSINT Report Generator — CyberEscudo';
$pageDescription = $lang==='es' 
    ? 'Genera un reporte de inteligencia pública (OSINT) 100% legal y no intrusivo sobre cualquier dominio.' 
    : 'Generate a 100% legal and non-intrusive public intelligence (OSINT) report on any domain.';

$current_page = basename($_SERVER['PHP_SELF']);
require __DIR__ . '/templates/header.php';
?>
<main class="content-page">
  <div class="m-bottom-2">
    <span class="section-label"><?= $lang==='es' ? '// AUDITORÍA COMERCIAL' : '// COMMERCIAL AUDIT' ?></span>
    <h1><?= $lang==='es' ? 'Generador de Reportes OSINT' : 'OSINT Report Generator' ?></h1>
  </div>
  
  <div class="tool-select-wrapper">
    <select id="tool-switcher" class="tool-selector">
      <option value="" disabled>-- <?= $lang==='es' ? 'Selecciona una herramienta' : 'Select a tool' ?> --</option>
      <!-- AQUÍ ESTÁN LAS DOS HERRAMIENTAS CONVIVIENDO -->
      <option value="<?= BASE_URL ?>/tool-recon.php" <?= $current_page==='tool-recon.php' ? 'selected' : '' ?>>🔍 <?= $lang==='es' ? 'OSINT Quick Recon (Técnico)' : 'OSINT Quick Recon (Technical)' ?></option>
      <option value="<?= BASE_URL ?>/tool-osint-report.php" <?= $current_page==='tool-osint-report.php' ? 'selected' : '' ?>>📄 <?= $lang==='es' ? 'Generador Reporte PDF (Comercial)' : 'PDF Report Generator (Commercial)' ?></option>
      <!-- ... (puedes añadir el resto de tus herramientas aquí si quieres que salgan en el menú) ... -->
    </select>
  </div>

  <div class="card">
    <div class="tool-header md-container" style="border-bottom:1px solid var(--border); margin-bottom: 1.5rem; padding-bottom: 1rem;">
      <h2>📄 Reporte de Inteligencia Pública</h2>
      <p><?= $lang==='es'
        ? 'Introduce un dominio y obtén un resumen de la información pública expuesta en internet. Proceso 100% legal y sin interacción con los servidores del objetivo.'
        : 'Enter a domain and get a summary of public information exposed on the internet. 100% legal process with no interaction with target servers.' ?></p>
    </div>

    <!-- Buscador -->
    <div class="cyber-input-wrapper" style="display:flex; gap:10px; margin-bottom: 2rem;">
      <input type="text" id="osint-target" class="cyber-input" placeholder="empresa.com" style="flex:1;">
      <button id="btn-run-osint" class="copy-btn" style="position:static; padding: 0 1.5rem;">
        <?= $lang==='es'?'Escanear':'Scan' ?>
      </button>
    </div>

  <!-- EL CONTENEDOR QUE SE EXPORTARÁ A PDF (12 CAJAS MULTI-PÁGINA) -->
    <div id="osint-results" style="display:none; padding: 40px; background: #0a0f14; border-radius: 0px; color: #ffffff; width: 100%; box-sizing: border-box;">
        
        <!-- Cabecera del PDF -->
        <div style="border-bottom: 2px solid #00ffff; margin-bottom: 1.5rem; padding-bottom: 1rem; text-align: center;">
            <h1 style="color: #00ffff; font-family: monospace; margin: 0; font-size: 2.2rem;">REPORTE DE INTELIGENCIA (OSINT)</h1>
            <h3 id="report-domain" style="color: #ffffff; margin: 0.5rem 0; font-size: 1.2rem;"></h3>
            <span style="color: #aaaaaa; font-size: 0.85rem;">Generado por la plataforma CyberEscudo</span>
        </div>

        <!-- Fila 1 -->
        <div style="display: flex; justify-content: space-between; gap: 1rem; margin-bottom: 1rem; break-inside: avoid; page-break-inside: avoid;">
            <div style="background: rgba(255,255,255,0.03); padding: 1.2rem; border: 1px solid #333333; border-radius: 6px; width: 48%; box-sizing: border-box;">
                <h4 style="color: #00ffff; margin-top:0; font-size: 1.1rem;">🌍 Infraestructura y Servidor</h4>
                <ul id="osint-geo" style="list-style: none; padding: 0; color: #dddddd; font-family: monospace; font-size:0.9rem; line-height: 1.8;"><li>Cargando...</li></ul>
            </div>
            <div style="background: rgba(255,255,255,0.03); padding: 1.2rem; border: 1px solid #333333; border-radius: 6px; width: 48%; box-sizing: border-box;">
                <h4 style="color: #00ffff; margin-top:0; font-size: 1.1rem;">🏷️ Información del Dominio</h4>
                <ul id="osint-whois" style="list-style: none; padding: 0; color: #dddddd; font-family: monospace; font-size:0.9rem; line-height: 1.8;"><li>Cargando...</li></ul>
            </div>
        </div>

        <!-- Fila 2 -->
        <div style="display: flex; justify-content: space-between; gap: 1rem; margin-bottom: 1rem; break-inside: avoid; page-break-inside: avoid;">
            <div style="background: rgba(255,255,255,0.03); padding: 1.2rem; border: 1px solid #333333; border-radius: 6px; width: 48%; box-sizing: border-box;">
                <h4 style="color: #00ffff; margin-top:0; font-size: 1.1rem;">📧 Servidores de Correo (MX)</h4>
                <ul id="osint-mx" style="list-style: none; padding: 0; color: #dddddd; font-family: monospace; font-size:0.9rem; line-height: 1.8;"><li>Cargando...</li></ul>
            </div>
            <div style="background: rgba(255,255,255,0.03); padding: 1.2rem; border: 1px solid #333333; border-radius: 6px; width: 48%; box-sizing: border-box;">
                <h4 style="color: #00ffff; margin-top:0; font-size: 1.1rem;">🛡️ Postura de Seguridad (TXT)</h4>
                <ul id="osint-txt" style="list-style: none; padding: 0; color: #dddddd; font-family: monospace; font-size:0.9rem; line-height: 1.8;"><li>Cargando...</li></ul>
            </div>
        </div>

        <!-- Fila 3 -->
        <div style="display: flex; justify-content: space-between; gap: 1rem; margin-bottom: 1rem; break-inside: avoid; page-break-inside: avoid;">
            <div style="background: rgba(255,255,255,0.03); padding: 1.2rem; border: 1px solid #333333; border-radius: 6px; width: 48%; box-sizing: border-box;">
                <h4 style="color: #00ffff; margin-top:0; font-size: 1.1rem;">🗄️ Gestión DNS (NS)</h4>
                <ul id="osint-ns" style="list-style: none; padding: 0; color: #dddddd; font-family: monospace; font-size:0.9rem; line-height: 1.8;"><li>Cargando...</li></ul>
            </div>
            <div style="background: rgba(255,255,255,0.03); padding: 1.2rem; border: 1px solid #333333; border-radius: 6px; width: 48%; box-sizing: border-box;">
                <h4 style="color: #00ffff; margin-top:0; font-size: 1.1rem;">🕰️ Historial Web (Archive)</h4>
                <ul id="osint-archive" style="list-style: none; padding: 0; color: #dddddd; font-family: monospace; font-size:0.9rem; line-height: 1.8;"><li>Cargando...</li></ul>
            </div>
        </div>

        <!-- Fila 4: NUEVA (Shodan) -->
        <div style="display: flex; justify-content: space-between; gap: 1rem; margin-bottom: 1rem; break-inside: avoid; page-break-inside: avoid;">
            <div style="background: rgba(255,255,255,0.03); padding: 1.2rem; border: 1px solid #333333; border-radius: 6px; width: 48%; box-sizing: border-box;">
                <h4 style="color: #00ffff; margin-top:0; font-size: 1.1rem;">🖧 Puertos Expuestos (Pasivo)</h4>
                <ul id="osint-ports" style="list-style: none; padding: 0; color: #dddddd; font-family: monospace; font-size:0.9rem; line-height: 1.8;"><li>Cargando...</li></ul>
            </div>
            <div style="background: rgba(255,255,255,0.03); padding: 1.2rem; border: 1px solid #333333; border-radius: 6px; width: 48%; box-sizing: border-box;">
                <h4 style="color: #00ffff; margin-top:0; font-size: 1.1rem;">🐞 Vulnerabilidades Conocidas</h4>
                <ul id="osint-vulns" style="list-style: none; padding: 0; color: #dddddd; font-family: monospace; font-size:0.9rem; line-height: 1.8;"><li>Cargando...</li></ul>
            </div>
        </div>

        <!-- Fila 5: NUEVA (DMARC y SOA) -->
        <div style="display: flex; justify-content: space-between; gap: 1rem; margin-bottom: 1rem; break-inside: avoid; page-break-inside: avoid;">
            <div style="background: rgba(255,255,255,0.03); padding: 1.2rem; border: 1px solid #333333; border-radius: 6px; width: 48%; box-sizing: border-box;">
                <h4 style="color: #00ffff; margin-top:0; font-size: 1.1rem;">🛡️ Política Antifraude (DMARC)</h4>
                <ul id="osint-dmarc" style="list-style: none; padding: 0; color: #dddddd; font-family: monospace; font-size:0.9rem; line-height: 1.8;"><li>Cargando...</li></ul>
            </div>
            <div style="background: rgba(255,255,255,0.03); padding: 1.2rem; border: 1px solid #333333; border-radius: 6px; width: 48%; box-sizing: border-box;">
                <h4 style="color: #00ffff; margin-top:0; font-size: 1.1rem;">👑 Autoridad de Zona (SOA)</h4>
                <ul id="osint-soa" style="list-style: none; padding: 0; color: #dddddd; font-family: monospace; font-size:0.9rem; line-height: 1.8;"><li>Cargando...</li></ul>
            </div>
        </div>

        <!-- Fila 6: NUEVA (CAA y IPv6) -->
        <div style="display: flex; justify-content: space-between; gap: 1rem; break-inside: avoid; page-break-inside: avoid;">
            <div style="background: rgba(255,255,255,0.03); padding: 1.2rem; border: 1px solid #333333; border-radius: 6px; width: 48%; box-sizing: border-box;">
                <h4 style="color: #00ffff; margin-top:0; font-size: 1.1rem;">🔐 Autorización SSL (CAA)</h4>
                <ul id="osint-caa" style="list-style: none; padding: 0; color: #dddddd; font-family: monospace; font-size:0.9rem; line-height: 1.8;"><li>Cargando...</li></ul>
            </div>
            <div style="background: rgba(255,255,255,0.03); padding: 1.2rem; border: 1px solid #333333; border-radius: 6px; width: 48%; box-sizing: border-box;">
                <h4 style="color: #00ffff; margin-top:0; font-size: 1.1rem;">🌐 Soporte Moderno (IPv6)</h4>
                <ul id="osint-ipv6" style="list-style: none; padding: 0; color: #dddddd; font-family: monospace; font-size:0.9rem; line-height: 1.8;"><li>Cargando...</li></ul>
            </div>
        </div>

        <!-- Recomendaciones Finales -->
        <div style="background: rgba(0, 255, 255, 0.05); padding: 1.5rem; border: 1px solid #00ffff; border-radius: 6px; margin-top: 2rem; break-inside: avoid; page-break-inside: avoid;">
            <h4 style="color: #00ffff; margin-top:0; font-size: 1.1rem;">⚠️ Resumen Ejecutivo de CyberEscudo</h4>
            <p style="color: #dddddd; font-size: 0.95rem; line-height: 1.6; margin-bottom: 0;">
                Análisis pasivo finalizado. Revise especialmente el apartado de <strong>Puertos Expuestos</strong> y <strong>Vulnerabilidades</strong> (CVE) extraídos de registros públicos. Se encarece aplicar políticas de seguridad si no hay registros <strong>DMARC</strong> o <strong>CAA</strong> configurados, previniendo así suplantación de identidad y emisión fraudulenta de certificados.
            </p>
        </div>

    </div>

    <!-- Botón de Exportar -->
    <div id="export-container" style="display:none; text-align: center; margin-top: 2rem;">
        <button id="btn-export-pdf" class="copy-btn" style="position:static; padding: 0.8rem 2rem; background: var(--cyan); color: #000; font-weight: bold;">
            📄 Exportar a PDF Completo
        </button>
    </div>
</main>
<script src="<?= BASE_URL ?>/assets/js/osint.js?v=<?= time() ?>"></script>
<?php require __DIR__ . '/templates/footer.php'; ?>