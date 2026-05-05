<footer>
  <div class="footer-inner">
    <a href="<?= BASE_URL ?>/index.php" class="logo">
      <img src="<?= BASE_URL ?>/assets/img/logo-cyberescudo.jpg" alt="CyberEscudo Logo" class="nav-logo-img">
      <span>Cyber<span class="accent">Escudo</span></span>
    </a>
    <p class="footer-tagline"><?= e($t['footer']['tagline']) ?></p>
    <div class="footer-links">
      <span><?= e(SITE_EMAIL) ?></span>
      <span class="sep">·</span>
      <a href="<?= BASE_URL ?>/privacidad.php"><?= e($t['footer']['privacy']) ?></a>
    </div>
    <p class="footer-copy">© <?= SITE_YEAR ?> CyberEscudo — <?= e($t['footer']['built']) ?></p>
  </div>
</footer>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
<script src="<?= BASE_URL ?>/assets/js/html2pdf.min.js"></script>
<!-- MODAL DE DONACIÓN / DESCARGA -->
<div id="donation-modal" class="cyber-modal hidden">
    <div class="cyber-modal-content">
        <h3 style="color: var(--cyan); margin-top: 0;">🛡️ Generar Reporte OSINT</h3>
        <p style="color: var(--gray); font-size: 0.95rem; margin-bottom: 1rem;">
            Generar y mantener esta herramienta consume recursos del servidor. <strong>CyberEscudo es 100% gratuito y sin publicidad.</strong>
        </p>
        <p style="color: var(--white); font-size: 0.95rem; margin-bottom: 1.5rem;">
            Si este reporte te ayuda a proteger tu empresa o te ahorra tiempo de trabajo, ¿considerarías apoyarme para mantener el proyecto vivo?
        </p>
        <div class="modal-actions">
            <!-- Cambia el href por el enlace real a tu página de Ko-fi, PayPal o donaciones -->
            <a href="https://cyberescudo.com/donar" target="_blank" class="cyber-btn-donate">☕ Invitar a un café</a>
            <button id="btn-download-free" class="cyber-btn-free">Descargar PDF gratis</button>
        </div>
    </div>
</div>
<!-- ─── BOTÓN FLOTANTE PARA ABRIR LA TERMINAL ─── -->
<button id="btn-open-terminal" class="cyber-term-float-btn" title="Iniciar Terminal OS">
    &gt;_
</button>

<!-- Aquí debajo ya tienes tu <div id="cyber-terminal"... del paso anterior -->
<!-- ─── TERMINAL EASTER EGG ─── -->
<div id="cyber-terminal" class="cyber-terminal hidden">
    <div class="terminal-header">
        <span>guest@cyberescudo: ~</span>
        <button id="term-close" title="Cerrar terminal">X</button>
    </div>
    <div id="term-history" class="term-history">
        <div style="color: var(--cyan);">CyberEscudo OS v1.0.0</div>
        <div>Escribe <strong style="color: #fff;">help</strong> para ver los comandos disponibles.</div>
    </div>
    <div class="term-input-line">
        <span class="term-prompt">$&gt;</span>
        <input type="text" id="term-input" autocomplete="off" spellcheck="false" autofocus>
    </div>
</div>
</body>
</html>