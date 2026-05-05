/* CyberEscudo — Main JS */
(function () {
  'use strict';

  // ── Navbar scroll class ────────────────────────────────────────────────────
  var navbar = document.getElementById('navbar');
  if (navbar) {
    function onScroll() {
      navbar.classList.toggle('scrolled', window.scrollY > 40);
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // ── Mobile menu toggle ─────────────────────────────────────────────────────
  var burger = document.getElementById('burger');
  var mobileMenu = document.getElementById('mobile-menu');
  if (burger && mobileMenu) {
    burger.addEventListener('click', function () {
      mobileMenu.classList.toggle('open');
    });
    // Close when clicking a link
    mobileMenu.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        mobileMenu.classList.remove('open');
      });
    });
  }

  // ── Smooth scroll for hero CTA ─────────────────────────────────────────────
  document.querySelectorAll('[data-scroll-to]').forEach(function (el) {
    el.addEventListener('click', function () {
      var target = document.getElementById(el.dataset.scrollTo);
      if (target) target.scrollIntoView({ behavior: 'smooth' });
    });
  });

  // ── Copy-to-clipboard for code blocks ─────────────────────────────────────
  document.querySelectorAll('.prose pre').forEach(function (pre) {
    var code = pre.querySelector('code');
    if (!code) return;

    // Wrap pre in a relative container so the button sits OUTSIDE the
    // overflow-x:auto scroll area and is never clipped
    var wrapper = document.createElement('div');
    wrapper.className = 'code-wrapper';
    pre.parentNode.insertBefore(wrapper, pre);
    wrapper.appendChild(pre);

    var btn = document.createElement('button');
    btn.className = 'copy-btn';
    btn.setAttribute('aria-label', 'Copiar código');
    btn.innerHTML =
      '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
        '<rect x="9" y="9" width="13" height="13" rx="2"/>' +
        '<path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>' +
      '</svg>' +
      '<span>Copiar</span>';

    wrapper.appendChild(btn);

    btn.addEventListener('click', function () {
      var text = code.innerText;
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function () {
          showCopied(btn);
        }).catch(function () { fallbackCopy(text, btn); });
      } else {
        fallbackCopy(text, btn);
      }
    });
  });

  function showCopied(btn) {
    btn.classList.add('copied');
    btn.querySelector('span').textContent = '¡Copiado!';
    setTimeout(function () {
      btn.classList.remove('copied');
      btn.querySelector('span').textContent = 'Copiar';
    }, 2000);
  }

  function fallbackCopy(text, btn) {
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.style.cssText = 'position:fixed;top:-9999px;left:-9999px;opacity:0';
    document.body.appendChild(ta);
    ta.select();
    try { document.execCommand('copy'); showCopied(btn); } catch (e) {}
    document.body.removeChild(ta);
  }

  // ── Reading progress bar ───────────────────────────────────────────────────
  var readingBar = document.getElementById('reading-bar');
  if (readingBar) {
    function updateBar() {
      var total = document.documentElement.scrollHeight - window.innerHeight;
      readingBar.style.width = (total > 0 ? Math.min(window.scrollY / total * 100, 100) : 0) + '%';
    }
    window.addEventListener('scroll', updateBar, { passive: true });
    updateBar();
  }

  // ── Read time estimation ───────────────────────────────────────────────────
  var proseEl = document.querySelector('.prose');
  var readTimeMeta = document.getElementById('read-time-meta');
  if (proseEl && readTimeMeta) {
    var words = proseEl.innerText.trim().split(/\s+/).length;
    var mins  = Math.max(1, Math.round(words / 200));
    var isEs  = document.documentElement.lang === 'es';
    readTimeMeta.textContent = isEs ? mins + ' min de lectura' : mins + ' min read';
  }

  // ── Table of contents ──────────────────────────────────────────────────────
  var tocAnchor = document.getElementById('toc-anchor');
  if (proseEl && tocAnchor) {
    var headings = Array.prototype.slice.call(proseEl.querySelectorAll('h2'));
    if (headings.length > 2) {
      var isEs2 = document.documentElement.lang === 'es';
      var toc   = document.createElement('div');
      toc.id    = 'toc-container';
      toc.innerHTML = '<h4>' + (isEs2 ? 'Contenido' : 'Contents') + '</h4><ul id="toc-list"></ul>';
      var ul = toc.querySelector('ul');
      headings.forEach(function (h, i) {
        if (!h.id) h.id = 'sec-' + i;
        var li = document.createElement('li');
        li.innerHTML = '<span class="toc-arrow">›</span><a href="#' + h.id + '">' + h.textContent + '</a>';
        ul.appendChild(li);
      });
      tocAnchor.appendChild(toc);

      // Highlight active section while scrolling
      if ('IntersectionObserver' in window) {
        var tocLinks = ul.querySelectorAll('a');
        var activeLink = null;
        var secObserver = new IntersectionObserver(function (entries) {
          entries.forEach(function (entry) {
            var link = ul.querySelector('a[href="#' + entry.target.id + '"]');
            if (!link) return;
            if (entry.isIntersecting) {
              if (activeLink) activeLink.classList.remove('toc-active');
              activeLink = link;
              link.classList.add('toc-active');
            }
          });
        }, { rootMargin: '-10% 0px -60% 0px' });
        headings.forEach(function (h) { secObserver.observe(h); });
      }
    }
  }

  // ── Project filter + search (index page only) ──────────────────────────────
  var projSearch  = document.getElementById('proj-search');
  var catFilters  = document.getElementById('cat-filters');
  var diffFilters = document.getElementById('diff-filters');
  var noResults   = document.getElementById('no-results');
  if (projSearch && catFilters) {
    var activeCat  = '';
    var activeDiff = '';

    function applyFilters() {
      var q = projSearch.value.toLowerCase().trim();
      var cards   = document.querySelectorAll('.projects-grid .card');
      var visible = 0;
      cards.forEach(function (card) {
        var catOk    = !activeCat  || card.dataset.cat  === activeCat;
        var diffOk   = !activeDiff || card.dataset.diff === activeDiff;
        var searchOk = !q          || (card.dataset.search || '').indexOf(q) !== -1;
        var show = catOk && diffOk && searchOk;
        card.style.display = show ? '' : 'none';
        if (show) visible++;
      });
      if (noResults) noResults.classList.toggle('visible', visible === 0);
    }

    projSearch.addEventListener('input', applyFilters);

    catFilters.querySelectorAll('.filter-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        catFilters.querySelectorAll('.filter-btn').forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
        activeCat = btn.dataset.cat;
        applyFilters();
      });
    });

    diffFilters.querySelectorAll('.filter-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        diffFilters.querySelectorAll('.filter-btn').forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
        activeDiff = btn.dataset.diff;
        applyFilters();
      });
    });
  }

  // ── Intersection observer: fade-in sections ────────────────────────────────
  if ('IntersectionObserver' in window) {
    var style = document.createElement('style');
    style.textContent = '.fade-in{opacity:0;transform:translateY(24px);transition:opacity .55s ease,transform .55s ease}.fade-in.visible{opacity:1;transform:none}';
    document.head.appendChild(style);

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.card, .manual-card, .profile-card, .contact-card, .donate-card').forEach(function (el) {
      el.classList.add('fade-in');
      observer.observe(el);
    });
  }
  /* ─── LÓGICA DE LA TERMINAL EASTER EGG ─── */
/* ─── CEREBRO DE LA TERMINAL EASTER EGG (MODO DIOS) ─── */
console.log("🚀 Sistema de terminal iniciado.");

// Como el script se carga al final del body, no necesitamos DOMContentLoaded
const terminal = document.getElementById('cyber-terminal');
const termInput = document.getElementById('term-input');
const termHistory = document.getElementById('term-history');

if(terminal) console.log("✅ HTML de la terminal detectado en la página.");

// 1. ESCUCHAR CLICS EN TODA LA PÁGINA (Delegación de eventos)
document.addEventListener('click', (e) => {
    // Si el clic fue en el botón flotante o dentro de él
    const btnOpen = e.target.closest('#btn-open-terminal');
    // Si el clic fue en la X de cerrar
    const btnClose = e.target.closest('#term-close');

    if (btnOpen) {
        console.log("🖱️ Clic interceptado. Abriendo/Cerrando terminal...");
        if(terminal) {
            terminal.classList.toggle('hidden');
            if (!terminal.classList.contains('hidden') && termInput) termInput.focus();
        }
    }

    if (btnClose && terminal) {
        terminal.classList.add('hidden');
    }
});

// 2. ESCUCHAR TECLAS PARA ABRIR (`, ~, º)
document.addEventListener('keydown', (e) => {
    if (e.key === '`' || e.key === '~' || e.key === 'º') {
        if (document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
            e.preventDefault();
            console.log("⌨️ Tecla detectada. Abriendo terminal...");
            if(terminal) {
                terminal.classList.toggle('hidden');
                if (!terminal.classList.contains('hidden') && termInput) termInput.focus();
            }
        }
    }
});

// 3. PROCESAR COMANDOS AL PULSAR ENTER
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
    if(!termHistory) return;
    const div = document.createElement('div');
    div.innerHTML = text; 
    if (className) div.className = className;
    termHistory.appendChild(div);
    termHistory.scrollTop = termHistory.scrollHeight;
}

function escapeHTML(str) {
    return str.replace(/[&<>'"]/g, tag => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;'
    }[tag] || tag));
}

function processCommand(cmd) {
    printLine(`$&gt; ${escapeHTML(cmd)}`, 'cmd-echo');
    const args = cmd.split(' ').filter(Boolean);
    const mainCmd = args[0].toLowerCase();

    switch (mainCmd) {
            case 'help':
                printLine("Comandos instalados:");
                printLine("&nbsp;&nbsp;<strong style='color:#fff'>whoami</strong>&nbsp;&nbsp;&nbsp;- Muestra tu identidad");
                printLine("&nbsp;&nbsp;<strong style='color:#fff'>clear</strong>&nbsp;&nbsp;&nbsp;&nbsp;- Limpia la pantalla");
                printLine("&nbsp;&nbsp;<strong style='color:#fff'>osint</strong>&nbsp;&nbsp;&nbsp;&nbsp;- Atajo a OSINT Recon");
                printLine("&nbsp;&nbsp;<strong style='color:#fff'>matrix</strong>&nbsp;&nbsp;&nbsp;- (Clasificado)");
                printLine("&nbsp;&nbsp;<strong style='color:#fff'>submit</strong>&nbsp;&nbsp;&nbsp;- Canjear banderas CTF");
                printLine("&nbsp;&nbsp;<strong style='color:#fff'>exit</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Cierra la terminal");
                break;
        case 'clear':
            if(termHistory) termHistory.innerHTML = '<div style="color: var(--cyan);">CyberEscudo OS v1.0.0</div><div>Escribe <strong style="color: #fff;">help</strong> para ver comandos.</div>';
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
            if(terminal) terminal.classList.add('hidden');
            break;
        case 'submit':
            const flagIngresada = args[1];
            if (!flagIngresada) {
                printLine("Uso: submit FLAG{...}", "cmd-error");
            } else if (flagIngresada === 'FLAG{sql_bypass_master}') {
                printLine("🏆 ¡ENHORABUENA! Has resuelto el CTF de inyección SQL.", "cmd-echo");
                printLine("Otorgando rol de [ SQL_NINJA ] a tu sesión actual...");
                document.body.style.border = "5px solid #00ff00"; // Efecto visual global
                setTimeout(() => printLine("¡Sigue practicando en la sección de manuales!"), 1500);
            } else if (flagIngresada === 'FLAG{default_creds_hunter}') {
                printLine("🏅 Bandera válida. Las credenciales por defecto son el pan de cada día.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{cmd_inj_explorer}') {
                printLine("🏅 Has explotado tu primera Inyección de Comandos.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{rce_root_master}') {
                printLine("🏆 ¡BRUTAL! Ejecución Remota de Código (RCE) conseguida.", "cmd-echo");
                printLine("Otorgando rol de [ ROOT_PIMPER ] a tu sesión...");
                document.body.style.border = "5px solid #ff2a2a"; // Borde Rojo agresivo
            } else {
                printLine("❌ Bandera incorrecta o no reconocida.", "cmd-error");
            }
            break;

        default:
            printLine(`bash: ${escapeHTML(mainCmd)}: comando no encontrado`, 'cmd-error');
    }
}
})();
