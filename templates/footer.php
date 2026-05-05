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
<!-- ─── CEREBRO DE LA TERMINAL (INCRUSTADO PARA EVITAR CACHÉ JS) ─── -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const terminal = document.getElementById('cyber-terminal');
    const termInput = document.getElementById('term-input');
    const termHistory = document.getElementById('term-history');
    const termClose = document.getElementById('term-close');
    const btnOpenTerm = document.getElementById('btn-open-terminal');

    if(!terminal) return;

    // Abrir/Cerrar con botón flotante
    if (btnOpenTerm) {
        btnOpenTerm.addEventListener('click', () => {
            terminal.classList.toggle('hidden');
            if (!terminal.classList.contains('hidden')) termInput.focus();
        });
    }

    // Abrir/Cerrar con teclado (`) o (~)
    document.addEventListener('keydown', (e) => {
        if (e.key === '`' || e.key === '~' || e.key === 'º') {
            if (document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
                e.preventDefault();
                terminal.classList.toggle('hidden');
                if (!terminal.classList.contains('hidden')) termInput.focus();
            }
        }
    });

    // Cerrar con la X
    if(termClose) {
        termClose.addEventListener('click', () => terminal.classList.add('hidden'));
    }

    // Procesar comandos al pulsar Enter
    if(termInput) {
        termInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                const command = termInput.value.trim();
                termInput.value = '';
                if (command) processCommand(command);
            }
        });
    }

    function printLine(text, className = '') {
        const div = document.createElement('div');
        div.innerHTML = text; 
        if (className) div.className = className;
        termHistory.appendChild(div);
        termHistory.scrollTop = termHistory.scrollHeight; // Auto-scroll
    }

    function processCommand(cmd) {
        printLine(`$&gt; ${cmd}`, 'cmd-echo');
        const args = cmd.split(' ').filter(Boolean);
        const mainCmd = args[0].toLowerCase();

        switch (mainCmd) {
            case 'help':
                printLine("Comandos instalados:");
                printLine("&nbsp;&nbsp;<strong style='color:#fff'>whoami</strong>&nbsp;&nbsp;&nbsp;- Muestra tu identidad");
                printLine("&nbsp;&nbsp;<strong style='color:#fff'>clear</strong>&nbsp;&nbsp;&nbsp;&nbsp;- Limpia la pantalla");
                printLine("&nbsp;&nbsp;<strong style='color:#fff'>osint</strong>&nbsp;&nbsp;&nbsp;&nbsp;- Atajo a OSINT Recon");
                printLine("&nbsp;&nbsp;<strong style='color:#fff'>matrix</strong>&nbsp;&nbsp;&nbsp;- (Clasificado)");
                printLine("&nbsp;&nbsp;<strong style='color:#fff'>exit</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Cierra la terminal");
                break;
            case 'clear':
                termHistory.innerHTML = '<div style="color: var(--cyan);">CyberEscudo OS v1.0.0</div><div>Escribe <strong style="color: #fff;">help</strong> para ver comandos.</div>';
                break;
            case 'whoami':
                printLine("guest@cyberescudo - Nivel de privilegio: bajo");
                break;
            case 'sudo':
                printLine("¿En serio? Tu intento de escalada de privilegios ha sido registrado.", "cmd-error");
                break;
            case 'matrix':
                printLine("Despierta, Neo...", "cmd-echo");
                document.body.style.filter = "hue-rotate(90deg)"; 
                setTimeout(() => printLine("Sigue al conejo blanco."), 2000);
                break;
            case 'osint':
                printLine("Redirigiendo a OSINT...");
                setTimeout(() => window.location.href = "/tool-osint-report.php", 1000);
                break;
            case 'exit':
                terminal.classList.add('hidden');
                break;
            default:
                printLine(`bash: ${mainCmd}: comando no encontrado`, 'cmd-error');
        }
    }
});
</script>
</body>
</html>