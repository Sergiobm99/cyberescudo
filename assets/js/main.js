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
  console.log("🚀 Sistema de terminal iniciado.");

  const terminal = document.getElementById('cyber-terminal');
  const termInput = document.getElementById('term-input');
  const termHistory = document.getElementById('term-history');

  if(terminal) console.log("✅ HTML de la terminal detectado en la página.");

  // 1. ESCUCHAR CLICS EN TODA LA PÁGINA
  document.addEventListener('click', (e) => {
    const btnOpen = e.target.closest('#btn-open-terminal');
    const btnClose = e.target.closest('#term-close');

    if (btnOpen) {
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
            // Verificamos el formato del comando
            if (args.length < 2) {
                printLine("❌ Uso: submit [ID_MISION] [FLAG] o submit [FLAG]", "cmd-error");
                break;
            }

            // --- NUEVO SISTEMA (CON API PHP) PARA LAS MISIONES ---
            if (args.length >= 3) {
                const missionId = args[1];
                const flag = args[2];

                printLine(`Validando credenciales para ${missionId}...`, "cmd-echo");

                fetch('/api/validate-mission.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ missionId: missionId, flag: flag })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        printLine("========================================", "cmd-echo");
                        printLine("✔️ " + data.message, "cmd-echo");
                        printLine(`[ XP AÑADIDA: +${data.xp} ]`, "cmd-echo");
                        printLine("========================================", "cmd-echo");
                        
                        // --- ESTA ES LA LÍNEA MÁGICA NUEVA ---
                        grantXP(missionId, data.xp);
                        // -------------------------------------
                        
                    } else {
                        printLine("❌ " + data.message, "cmd-error");
                    }
                })
                .catch(err => {
                    printLine("❌ Error de conexión con el validador API.", "cmd-error");
                });
                break;
            }

            // --- SISTEMA ANTIGUO (HARDCODEADO) ---
            const flagIngresada = args[1];
            if (flagIngresada === 'FLAG{sql_bypass_master}') {
                printLine("🏆 ¡ENHORABUENA! Has resuelto el CTF de inyección SQL.", "cmd-echo");
                printLine("Otorgando rol de [ SQL_NINJA ] a tu sesión actual...");
                document.body.style.border = "5px solid #00ff00";
            } else if (flagIngresada === 'FLAG{default_creds_hunter}') {
                printLine("🏅 Bandera válida. Las credenciales por defecto son el pan de cada día.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{cmd_inj_explorer}') {
                printLine("🏅 Has explotado tu primera Inyección de Comandos.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{rce_root_master}') {
                printLine("🏆 ¡BRUTAL! Ejecución Remota de Código (RCE) conseguida.", "cmd-echo");
                printLine("Otorgando rol de [ ROOT_PIMPER ] a tu sesión...");
                document.body.style.border = "5px solid #ff2a2a";
            } else if (flagIngresada === 'FLAG{xss_alert_master}') {
                printLine("🏅 Ejecución de JavaScript simulada con éxito.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{xss_cookie_thief}') {
                printLine("🏆 ¡BRUTAL! Has conseguido robar las cookies de sesión mediante XSS.", "cmd-echo");
                document.body.style.border = "5px dashed #00ffff";
            } else if (flagIngresada === 'FLAG{csrf_forgery_expert}') {
                printLine("🏅 Payload validado. Acabas de vaciar las cuentas del banco simulado.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{xxe_xml_parser_pwned}') {
                printLine("🏅 ¡Excelente! Has manipulado un parser XML usando Entidades Externas.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{sudo_find_root_shell}') {
                printLine("🏆 ¡SISTEMA COMPROMETIDO! Has escalado privilegios a ROOT abusando de sudo.", "cmd-echo");
                document.body.style.border = "5px solid #ff00ff";
            } else if (flagIngresada === 'FLAG{ffuf_filter_ninja}') {
                printLine("🏅 ¡Comando perfecto! Has dominado la evasión de falsos positivos en Fuzzing.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{ir_containment_expert}') {
                printLine("🛡️ ¡Excelente Triage! Has contenido la brecha de seguridad con éxito.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{secure_code_reviewer}') {
                printLine("🏅 ¡Aprobado! Sabes aplicar el parche adecuado a cada vulnerabilidad web.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{nmap_recon_analyst}') {
                printLine("🏅 ¡Aprobado! Sabes leer entre líneas y encontrar vulnerabilidades ocultas.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{hydra_syntax_master}') {
                printLine("🏅 ¡Aprobado! Has dominado la infernal sintaxis web de Hydra.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{python_scanner_architect}') {
                printLine("🏅 ¡Aprobado! Has programado los cimientos de una herramienta ofensiva en Python.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{iptables_defender_wall}') {
                printLine("🏅 ¡Aprobado! Sabes usar Netfilter para fortificar el perímetro.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{snort_rule_engineer}') {
                printLine("🏅 ¡Aprobado! Has creado una firma de red capaz de detener a los atacantes.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{shodan_osint_master}') {
                printLine("🏅 ¡Aprobado! Eres capaz de encontrar una aguja cibernética en un pajar global.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{docker_socket_pwned}') {
                printLine("🏅 ¡Aprobado! Conoces la vulnerabilidad más devastadora en entornos Cloud.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{nikto_recon_expert}') {
                printLine("🏅 ¡Aprobado! Sabes separar el ruido de las vulnerabilidades críticas reales.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{burp_suite_jedi}') {
                printLine("🏅 ¡Aprobado! Tienes un conocimiento profundo de la arquitectura de Burp Suite.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{gpu_hashcat_operator}') {
                printLine("🏅 ¡Aprobado! Sabes usar la potencia gráfica bruta para romper criptografía.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{wireshark_pcap_hunter}') {
                printLine("🏅 ¡Aprobado! Los paquetes no mienten, y tú sabes cómo leerlos.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{msfvenom_payload_crafter}') {
                printLine("🏅 ¡Aprobado! Entiendes a fondo la arquitectura de payloads en MSF.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{sqlmap_tamper_wizard}') {
                printLine("🏅 ¡Aprobado! Has burlado las defensas perimetrales ofuscando tus payloads.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{apache_hardening_master}') {
                printLine("🏅 ¡Aprobado! Sabes cómo cerrar las puertas antes de que los atacantes lleguen.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{android_activity_bypassed}') {
                printLine("🏅 ¡Aprobado! Has saltado los controles lógicos explotando el Manifest de Android.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{diva_android_auditor}') {
                printLine("🏅 ¡Aprobado! Conoces a la perfección el sistema de archivos de Android y ADB.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{android_ipc_pwned}') {
                printLine("🏅 ¡Aprobado! Has abusado de la comunicación inter-procesos del sistema Android.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{smali_patching_ninja}') {
                printLine("🏅 ¡Aprobado! Sabes cómo reescribir las reglas alterando el ADN de las aplicaciones.", "cmd-echo");
            } else if (flagIngresada === 'FLAG{phishing_triage_expert}') {
                printLine("🏅 ¡Aprobado! Tienes un ojo clínico para detectar Ingeniería Social.", "cmd-echo");
            } else {
                printLine("❌ Bandera incorrecta o no reconocida.", "cmd-error");
            }
            break;

        default:
            printLine(`bash: ${escapeHTML(mainCmd)}: comando no encontrado`, 'cmd-error');
    }
  }
/* ─── SISTEMA DE PROGRESO Y XP (LOCALSTORAGE) ─── */
  const MAX_MISSIONS = 17;

  function updateHUD() {
      const xpElement = document.getElementById('user-xp');
      const barElement = document.getElementById('xp-bar');
      const rankElement = document.getElementById('user-rank');
      const countElement = document.getElementById('missions-count');
      
      let currentXP = parseInt(localStorage.getItem('cyber_xp')) || 0;
      let completedMissions = JSON.parse(localStorage.getItem('cyber_missions')) || [];

      // Si estamos en la página de misiones, actualizamos los gráficos
      if (xpElement && barElement) {
          xpElement.innerText = currentXP;
          countElement.innerText = completedMissions.length + ' / ' + MAX_MISSIONS + ' MISSIONS COMPLETED';
          
          let percentage = (completedMissions.length / MAX_MISSIONS) * 100;
          barElement.style.width = percentage + '%';

          // 2. ESTILOS POR DEFECTO (¡AQUÍ ESTÁ LA SOLUCIÓN!)
          rankElement.style.color = "";
          rankElement.style.textShadow = "";
          barElement.style.background = "var(--cyan)"; // Le devolvemos el color a la barra
          barElement.style.boxShadow = "none";

          // 3. ACTUALIZAR RANGO
          let progress = completedMissions.length;
          
          // En tu función updateHUD(), ajusta los rangos así:
if (progress === 0) {
    rankElement.innerText = "RECRUIT";
} 
else if (progress > 0 && progress <= 5) {
    rankElement.innerText = "OPERATOR";
    rankElement.style.color = "var(--cyan)";
} 
else if (progress > 5 && progress <= 12) {
    rankElement.innerText = "SPECIALIST";
    rankElement.style.color = "#aa00ff";
    barElement.style.background = "#aa00ff";
} 
else if (progress >= 13) {
    rankElement.innerText = "GHOST_HACKER";
    rankElement.style.color = "#ff2a2a";
    rankElement.style.textShadow = "0 0 10px red";
    barElement.style.background = "#ff2a2a";
    barElement.style.boxShadow = "0 0 15px red";
}
      }
  }

  function grantXP(missionId, xpGained) {
      let completedMissions = JSON.parse(localStorage.getItem('cyber_missions')) || [];
      
      // Solo damos XP si no había completado esta misión antes
      if (!completedMissions.includes(missionId)) {
          completedMissions.push(missionId);
          localStorage.setItem('cyber_missions', JSON.stringify(completedMissions));
          
          let currentXP = parseInt(localStorage.getItem('cyber_xp')) || 0;
          localStorage.setItem('cyber_xp', currentXP + xpGained);
          
          updateHUD();

          // COMPROBAR SI SE HA PASADO EL JUEGO (EL EFECTO HACKER)
          if (completedMissions.length === MAX_MISSIONS) {
              setTimeout(triggerSystemTakeover, 1500);
          }
      } else {
          printLine(`[INFO] La misión ${missionId} ya estaba completada. Sin cambios en XP.`, "cmd-echo");
      }
  }

  function triggerSystemTakeover() {
      // Ocultar terminal temporalmente
      if(terminal) terminal.classList.add('hidden');

      // Crear pantalla de Glitch
      const glitchOverlay = document.createElement('div');
      glitchOverlay.style.cssText = `
          position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
          background: #000; z-index: 999999; display: flex; flex-direction: column;
          justify-content: center; align-items: center; font-family: monospace;
          color: #00ff00; overflow: hidden;
      `;
      
      glitchOverlay.innerHTML = `
          <h1 style="font-size: 4rem; color: #ff2a2a; text-shadow: 0 0 20px red; margin-bottom: 20px; animation: glitch 0.2s infinite;">SYSTEM COMPROMISED</h1>
          <p style="font-size: 1.5rem;">ALL BLACK OPS MISSIONS COMPLETED.</p>
          <p style="color: #fff; margin-top: 30px;">AUTHORIZATION LEVEL: <strong style="color: #ff2a2a;">ROOT</strong></p>
          <div style="margin-top: 50px; font-size: 0.8rem; opacity: 0.5;">REBOOTING SECURE ENVIRONMENT IN 5 SECONDS...</div>
      `;
      
      // Inyectar animación CSS para el Glitch
      const style = document.createElement('style');
      style.innerHTML = `@keyframes glitch { 0% { transform: translate(2px, 2px); } 20% { transform: translate(-2px, -2px); } 40% { transform: translate(2px, -2px); } 60% { transform: translate(-2px, 2px); } 100% { transform: translate(0); } }`;
      document.head.appendChild(style);
      
      document.body.appendChild(glitchOverlay);

      // Quitar el efecto después de 6 segundos
      setTimeout(() => {
          glitchOverlay.style.transition = "opacity 1s";
          glitchOverlay.style.opacity = "0";
          setTimeout(() => glitchOverlay.remove(), 1000);
      }, 6000);
  }

  // Inicializar la HUD al cargar la página
  document.addEventListener('DOMContentLoaded', updateHUD);
  // ==========================================
// SISTEMA DE SPOILERS PARA LOS WRITE-UPS
// ==========================================
document.addEventListener("DOMContentLoaded", function() {
    // Buscamos todos los botones que tengan la clase .flag-spoiler
    const spoilers = document.querySelectorAll('.flag-spoiler');
    
    spoilers.forEach(function(btn) {
        btn.addEventListener('click', function() {
            // Leemos la flag oculta en el atributo data-flag
            let secretFlag = this.getAttribute('data-flag');
            
            if (secretFlag) {
                // Cambiamos el texto
                this.innerText = secretFlag;
                
                // Le damos el estilo cian de "Hackeado"
                this.style.color = "var(--cyan)";
                this.style.borderColor = "var(--cyan)";
                this.style.cursor = "default";
                
                // Borramos el dato para mayor limpieza
                this.removeAttribute('data-flag');
            }
        });
    });
});
// ==========================================
// SISTEMA DE PROGRESO DE MISIONES (CTF)
// ==========================================
document.addEventListener('DOMContentLoaded', () => {
    // Leemos las misiones guardadas en la memoria del navegador
    let completedMissions = JSON.parse(localStorage.getItem('cyber_missions')) || [];
    
    // Solo actualizamos la barra de misiones si estamos en la página correcta
    let missionsCountDisplay = document.getElementById('missions-count');
    if (missionsCountDisplay) {
        missionsCountDisplay.innerText = `${completedMissions.length} / 17 MISSIONS COMPLETED`;
    }
    
    completedMissions.forEach(missionId => {
        let card = document.getElementById('card-' + missionId);
        
        if (card) {
            // Añadimos la clase CSS que oscurece y activa el "CLEARED"
            card.classList.add('mission-completed');
            
            // Cambiamos el botón para que diga "REVISAR"
            let btn = card.querySelector('.btn-deploy');
            if (btn) {
                // Detectamos el idioma actual mirando si la URL o el HTML tiene el tag 'es'
                let isSpanish = document.documentElement.lang === 'es' || document.body.classList.contains('es');
                btn.innerText = isSpanish ? 'REVISAR' : 'REVIEW';
                btn.style.color = 'var(--terminal-green)';
                btn.style.borderColor = 'var(--terminal-green)';
            }
        }
    });
});
 
document.addEventListener('DOMContentLoaded', () => {
    // ==========================================
    // 2. ANIMACIÓN DEL ESCÁNER PERIMETRAL (AJAX)
    // ==========================================
    let scannerForm = document.getElementById('scanner-form');
    
    if (scannerForm) {
        scannerForm.addEventListener('submit', function(e) {
            e.preventDefault(); // 🔥 Esto evita que la página se recargue de golpe

            let btnScan = document.getElementById('btn-scan');
            let loadingZone = document.getElementById('loading-zone');
            
            // Ocultar botón y caja de resultados vieja (si la hay), mostrar animación
            btnScan.style.display = 'none';
            let oldResults = document.getElementById('results-box');
            if (oldResults) oldResults.remove();
            
            loadingZone.style.display = 'block';

            // Recoger la URL escrita por el usuario
            let formData = new FormData(scannerForm);

            // Enviar petición oculta al servidor (sin recargar)
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(html => {
                // ⏱️ Forzamos 1.5 segundos de animación para crear suspense
                setTimeout(() => {
                    // Ocultar radar y volver a mostrar el botón
                    loadingZone.style.display = 'none';
                    btnScan.style.display = 'block';

                    // Extraer los resultados del código que nos devuelve PHP
                    let parser = new DOMParser();
                    let doc = parser.parseFromString(html, 'text/html');
                    let newResults = doc.getElementById('results-box');
                    
                    if (newResults) {
                        // Inyectar la caja de resultados mágicamente bajo el radar
                        loadingZone.insertAdjacentElement('afterend', newResults);
                    } else {
                        alert("Error: No se pudo generar el reporte del objetivo.");
                    }
                }, 1500); 
            })
            .catch(error => {
                loadingZone.style.display = 'none';
                btnScan.style.display = 'block';
                alert("Fallo de conexión con el servidor.");
            });
        });
    }
});
// ==========================================
    // 3. SOC SIMULATOR (BLUE TEAM)
    // ==========================================
    const logWindow = document.getElementById('log-window');
    
    // Solo se ejecuta si estamos en la página del simulador
    if (logWindow) {
        const btnBlock = document.getElementById('btn-block');
        const selectedInfo = document.getElementById('selected-info');
        const scoreDisplay = document.getElementById('score-display');
        const alertBox = document.getElementById('alert-box');
        const victoryBox = document.getElementById('victory-box');

        let score = 0;
        const WIN_SCORE = 5;
        let selectedLogData = null;
        let isGameOver = false;
        let isHovering = false; // Detiene el auto-scroll al apuntar

        // Control de Scroll de forma segura (CSP compliant)
        logWindow.addEventListener('mouseenter', () => isHovering = true);
        logWindow.addEventListener('mouseleave', () => isHovering = false);

        const normalTraffic = [
            "GET /index.php HTTP/1.1",
            "GET /assets/style.css HTTP/1.1",
            "GET /images/logo.png HTTP/1.1",
            "GET /robots.txt HTTP/1.1",
            "POST /api/v1/heartbeat HTTP/1.1",
            "GET /dashboard/user_profile HTTP/1.1",
            "GET /favicon.ico HTTP/1.1",
            "POST /login.php HTTP/1.1"
        ];

        const attackTraffic = [
            "GET /index.php?id=1' OR '1'='1 HTTP/1.1",
            "POST /login.php?user=admin'-- HTTP/1.1",
            "GET /search?q=<script>alert(1)</script> HTTP/1.1",
            "GET /download.php?file=../../../../etc/passwd HTTP/1.1",
            "GET /.git/config HTTP/1.1",
            "POST /api/upload (filename=shell.php) HTTP/1.1",
            "GET /wp-admin/admin-ajax.php?action=revslider_show_image&img=../wp-config.php HTTP/1.1"
        ];

        function getRandomIP() {
            return `${Math.floor(Math.random() * 255)}.${Math.floor(Math.random() * 255)}.${Math.floor(Math.random() * 255)}.${Math.floor(Math.random() * 255)}`;
        }
        
        function getCurrentTime() {
            const now = new Date();
            return `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}:${now.getSeconds().toString().padStart(2, '0')}`;
        }

        function showAlert(msg, isSuccess) {
            alertBox.innerText = msg;
            alertBox.style.display = 'block';
            alertBox.style.background = isSuccess ? 'rgba(0,255,65,0.1)' : 'rgba(255,42,42,0.1)';
            alertBox.style.color = isSuccess ? '#00ff41' : '#ff2a2a';
            alertBox.style.border = `1px solid ${isSuccess ? '#00ff41' : '#ff2a2a'}`;
            setTimeout(() => { alertBox.style.display = 'none'; }, 2500);
        }

        function generateLog() {
            if (isGameOver) return;

            const isAttack = Math.random() < 0.20; 
            const reqList = isAttack ? attackTraffic : normalTraffic;
            const request = reqList[Math.floor(Math.random() * reqList.length)];
            const ip = getRandomIP();
            const time = getCurrentTime();
            const status = isAttack ? (Math.random() > 0.5 ? '200' : '403') : '200';

            const logDiv = document.createElement('div');
            logDiv.className = 'log-line';
            logDiv.innerHTML = `
                <span class="l-time">[${time}]</span>
                <span class="l-ip">${ip}</span>
                <span class="l-req">"${request}"</span>
                <span class="l-status">${status}</span>
            `;

            logDiv.dataset.ip = ip;
            logDiv.dataset.req = request;
            logDiv.dataset.isAttack = isAttack;

            logDiv.addEventListener('click', () => {
                if (isGameOver) return;
                document.querySelectorAll('.log-line').forEach(el => el.classList.remove('selected'));
                logDiv.classList.add('selected');
                selectedLogData = { ip: ip, req: request, isAttack: isAttack, element: logDiv };
                
                let isEs = document.documentElement.lang === 'es' || document.body.classList.contains('es');
                selectedInfo.innerHTML = `
                    <div style="color:#888; margin-bottom:5px;">[ TARGET LOCKED ]</div>
                    <div style="color:#fff;">IP: <span style="color:#ff2a2a">${ip}</span></div>
                    <div style="color:#aaa; margin-top:5px; font-size: 0.75rem;">PAYLOAD: ${request}</div>
                `;
                btnBlock.disabled = false;
            });

            logWindow.appendChild(logDiv);

            if (logWindow.children.length > 50) logWindow.removeChild(logWindow.firstChild);
            if (!isHovering) logWindow.scrollTop = logWindow.scrollHeight;

            setTimeout(generateLog, Math.random() * 1000 + 400);
        }

        btnBlock.addEventListener('click', () => {
            if (!selectedLogData || isGameOver) return;
            let isEs = document.documentElement.lang === 'es' || document.body.classList.contains('es');

            if (selectedLogData.isAttack === 'true' || selectedLogData.isAttack === true) {
                score++;
                showAlert(isEs ? '[+] AMENAZA NEUTRALIZADA' : '[+] THREAT NEUTRALIZED', true);
                selectedLogData.element.style.color = '#ff2a2a';
                selectedLogData.element.style.textDecoration = 'line-through';
            } else {
                score = Math.max(0, score - 1);
                showAlert(isEs ? '[-] FALSO POSITIVO. Tráfico legítimo bloqueado.' : '[-] FALSE POSITIVE. Legitimate traffic blocked.', false);
            }

            scoreDisplay.innerText = score;

            if (score >= WIN_SCORE) {
                isGameOver = true;
                let statusLive = document.querySelector('.status-live');
                if(statusLive) {
                    statusLive.innerText = '● SECURED';
                    statusLive.style.color = '#aa00ff';
                    statusLive.style.animation = 'none';
                }
                btnBlock.disabled = true;
                victoryBox.style.display = 'block';
                
                let completedMissions = JSON.parse(localStorage.getItem('cyber_missions')) || [];
                if (!completedMissions.includes('OP-SOC-SIM')) {
                    completedMissions.push('OP-SOC-SIM');
                    localStorage.setItem('cyber_missions', JSON.stringify(completedMissions));
                }
            }
            
            selectedLogData = null;
            btnBlock.disabled = true;
            selectedInfo.innerHTML = isEs ? '[ ANALIZANDO TRÁFICO... ]' : '[ ANALYZING TRAFFIC... ]';
            document.querySelectorAll('.log-line').forEach(el => el.classList.remove('selected'));
        });

        setTimeout(generateLog, 1000);
    }
// ==========================================
    // 6. FULL THREAT INTEL PAGE (threat-intel.php)
    // ==========================================
    const fullCveFeed = document.getElementById('full-cve-feed');
    const fullNewsFeed = document.getElementById('full-news-feed');

    if (fullCveFeed && fullNewsFeed) {
        const cveUrl = 'https://api.rss2json.com/v1/api.json?rss_url=https://cvefeed.io/rssfeed/latest.xml';
        const newsUrl = 'https://api.rss2json.com/v1/api.json?rss_url=https://feeds.feedburner.com/TheHackersNews';
        const isSpanish = document.documentElement.lang === 'es' || document.body.classList.contains('es');

        // Función para calcular "Hace X horas/días"
        function timeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const seconds = Math.round((now - date) / 1000);
            const minutes = Math.round(seconds / 60);
            const hours = Math.round(minutes / 60);
            const days = Math.round(hours / 24);

            if (isSpanish) {
                if (seconds < 60) return "Hace un momento";
                if (minutes < 60) return `Hace ${minutes} minutos`;
                if (hours < 24) return `Hace ${hours} horas`;
                return `Hace ${days} días`;
            } else {
                if (seconds < 60) return "Just now";
                if (minutes < 60) return `${minutes} minutes ago`;
                if (hours < 24) return `${hours} hours ago`;
                return `${days} days ago`;
            }
        }

        // Cargar 15 CVEs
        fetch(cveUrl)
            .then(res => res.json())
            .then(data => {
                fullCveFeed.innerHTML = ''; 
                const items = data.items.slice(0, 15); // Mostramos los 15 últimos
                items.forEach(item => {
                    fullCveFeed.innerHTML += `
                        <li class="feed-item cve-item">
                            <span class="feed-date">${timeAgo(item.pubDate)}</span>
                            <a href="${item.link}" target="_blank" class="feed-title">${item.title}</a>
                            <span class="feed-badge badge-cve">NVD DISCLOSURE</span>
                        </li>`;
                });
            })
            .catch(() => fullCveFeed.innerHTML = `<li class="feed-item" style="color:#ff2a2a;">Error de conexión.</li>`);

        // Cargar 15 Noticias
        fetch(newsUrl)
            .then(res => res.json())
            .then(data => {
                fullNewsFeed.innerHTML = ''; 
                const items = data.items.slice(0, 15);
                items.forEach(item => {
                    fullNewsFeed.innerHTML += `
                        <li class="feed-item">
                            <span class="feed-date">${timeAgo(item.pubDate)}</span>
                            <a href="${item.link}" target="_blank" class="feed-title">${item.title}</a>
                            <span class="feed-badge badge-news">INTEL REPORT</span>
                        </li>`;
                });
            })
            .catch(() => fullNewsFeed.innerHTML = `<li class="feed-item" style="color:#ff2a2a;">Error de conexión.</li>`);
    }
    // ==========================================
    // 7. SOC ARSENAL (soc-arsenal.php)
    // ==========================================
    const arsenalListContainer = document.getElementById('scriptList');
    const arsenalViewer = document.getElementById('viewer');
    const arsenalSearch = document.getElementById('searchInput');

    if (arsenalListContainer && arsenalViewer) {
        // Detectar el idioma actual de la web
        const isSpanish = document.documentElement.lang === 'es' || document.body.classList.contains('es');

        // Base de datos Bilingüe del Arsenal SOC
        // Base de datos Bilingüe del Arsenal SOC (Comentarios de código incluidos)
        const arsenalData = [
            {
                id: 1,
                title: isSpanish ? "Ataque de Fuerza Bruta Exitoso (Azure AD)" : "Successful Brute Force Attack (Azure AD)",
                desc: isSpanish 
                    ? "Detecta cuando un atacante ha fallado múltiples intentos de inicio de sesión en un corto periodo de tiempo y finalmente consigue acceder con éxito a la cuenta." 
                    : "Detects when an attacker has failed multiple login attempts in a short period of time and finally successfully accesses the account.",
                lang: "KQL",
                tags: ["KQL", "Azure Sentinel"],
                tagClasses: ["tag-kql", "tag-kql"],
                code: `SigninLogs\n| where TimeGenerated > ago(1d)\n| where ResultType != 0\n| summarize FailedCount = count() by UserPrincipalName, IPAddress\n| where FailedCount > 5\n| join kind=inner (\n    SigninLogs\n    | where TimeGenerated > ago(1d)\n    | where ResultType == 0\n) on UserPrincipalName, IPAddress\n| project TimeGenerated, UserPrincipalName, IPAddress, FailedCount, AppDisplayName\n| sort by TimeGenerated desc`
            },
            {
                id: 2,
                title: isSpanish ? "Exfiltración Masiva de Archivos (SharePoint)" : "Massive File Exfiltration (SharePoint)",
                desc: isSpanish 
                    ? "Monitoriza eventos de Office 365 para identificar usuarios descargando una cantidad inusualmente alta de archivos (posible robo de datos)." 
                    : "Monitors Office 365 events to identify users downloading an unusually high amount of files (possible data theft).",
                lang: "KQL",
                tags: ["KQL", "M365 Defender"],
                tagClasses: ["tag-kql", "tag-mde"],
                code: `OfficeActivity\n| where TimeGenerated > ago(1h)\n| where OfficeWorkload == "SharePoint"\n| where Operation in ("FileDownloaded", "FileSyncDownloadedFull")\n| summarize DownloadCount = count() by UserId, ClientIP\n| where DownloadCount > 50\n| project UserId, ClientIP, DownloadCount\n| order by DownloadCount desc`
            },
            {
                id: 3,
                title: isSpanish ? "Ejecución de PowerShell Codificado (Base64)" : "Encoded PowerShell Execution (Base64)",
                desc: isSpanish 
                    ? "Busca ejecuciones de procesos (Living off the Land) donde se utilice PowerShell con parámetros para ofuscar comandos." 
                    : "Looks for process executions (Living off the Land) where PowerShell is used with parameters to obfuscate commands.",
                lang: "KQL",
                tags: ["KQL", "M365 Defender"],
                tagClasses: ["tag-kql", "tag-mde"],
                code: `DeviceProcessEvents\n| where TimeGenerated > ago(7d)\n| where FileName =~ "powershell.exe" or FileName =~ "pwsh.exe"\n| where ProcessCommandLine has_any ("-e", "-en", "-enc", "-encodedcommand")\n| parse ProcessCommandLine with * "-e " EncodedPayload\n| project TimeGenerated, DeviceName, AccountName, ProcessCommandLine, InitiatingProcessFileName\n| order by TimeGenerated desc`
            },
            {
                id: 4,
                title: isSpanish ? "Aislamiento Automático de Endpoint (API MDE)" : "Automatic Endpoint Isolation (MDE API)",
                desc: isSpanish 
                    ? "Script en PowerShell que interactúa con la API de Microsoft Defender for Endpoint para aislar una máquina comprometida." 
                    : "PowerShell script that interacts with the Microsoft Defender for Endpoint API to isolate a compromised machine.",
                lang: "PowerShell",
                tags: ["PowerShell", isSpanish ? "Automatización" : "Automation"],
                tagClasses: ["tag-ps", "tag-ps"],
                code: `$MachineId = "${isSpanish ? 'INGRESA_AQUI_EL_MACHINE_ID' : 'ENTER_MACHINE_ID_HERE'}"\n$IsolationType = "Full" # ${isSpanish ? 'o' : 'or'} "Selective"\n$Comment = "${isSpanish ? 'Aislamiento preventivo ejecutado por Agente SOC' : 'Preventive isolation executed by SOC Agent'}"\n\n$Uri = "https://api.securitycenter.microsoft.com/api/machines/$MachineId/isolate"\n$Body = @{\n    Comment = $Comment\n    IsolationType = $IsolationType\n} | ConvertTo-Json\n\nInvoke-RestMethod -Method Post -Uri $Uri -Headers $Headers -Body $Body`
            },
            {
                id: 5,
                title: isSpanish ? "Abuso de OAuth (Illicit Consent Grant)" : "OAuth Abuse (Illicit Consent Grant)",
                desc: isSpanish 
                    ? "Detecta cuando un usuario concede permisos a una aplicación OAuth de terceros maliciosa o sospechosa para leer sus correos o archivos de M365." 
                    : "Detects when a user grants permissions to a malicious or suspicious third-party OAuth application to read their M365 emails or files.",
                lang: "KQL",
                tags: ["KQL", "Cloud Security"],
                tagClasses: ["tag-kql", "tag-ps"],
                code: `AuditLogs\n| where TimeGenerated > ago(14d)\n| where OperationName == "Consent to application"\n| extend AppDisplayName = tostring(TargetResources[0].displayName)\n| extend AppId = tostring(TargetResources[0].id)\n| extend UserPrincipalName = tostring(InitiatedBy.user.userPrincipalName)\n| extend PermissionsGranted = tostring(TargetResources[0].modifiedProperties[0].newValue)\n// ${isSpanish ? 'Buscamos permisos críticos que los atacantes suelen solicitar' : 'Look for critical permissions commonly requested by attackers'}\n| where PermissionsGranted has_any ("Mail.Read", "Files.ReadWrite.All", "Contacts.Read")\n| project TimeGenerated, UserPrincipalName, AppDisplayName, AppId, PermissionsGranted\n| sort by TimeGenerated desc`
            },
            {
                id: 6,
                title: isSpanish ? "Regla de Reenvío Oculta (BEC / Persistencia)" : "Hidden Forwarding Rule (BEC / Persistence)",
                desc: isSpanish 
                    ? "Busca atacantes que, tras comprometer una cuenta, crean reglas de bandeja de entrada en Exchange para reenviar correos a dominios externos." 
                    : "Looks for attackers who, after compromising an account, create Exchange inbox rules to forward emails to external domains.",
                lang: "KQL",
                tags: ["KQL", "M365 Defender"],
                tagClasses: ["tag-kql", "tag-mde"],
                code: `OfficeActivity\n| where TimeGenerated > ago(7d)\n| where OfficeWorkload == "Exchange"\n| where Operation in ("New-InboxRule", "Set-InboxRule")\n| extend RuleName = tostring(parse_json(Parameters)[1].Value)\n| extend ForwardTo = tostring(parse_json(Parameters)[2].Value)\n// ${isSpanish ? 'Extraemos cuentas de correo usando regex' : 'Extract email accounts using regex'}\n| where ForwardTo matches regex @"(?i)[a-z0-9._%+-]+@[a-z0-9.-]+\\.[a-z]{2,}"\n// ${isSpanish ? 'EXCLUIR TU DOMINIO CORPORATIVO AQUÍ:' : 'EXCLUDE YOUR CORPORATE DOMAIN HERE:'}\n| where ForwardTo !endswith "@tu-empresa.com"\n| project TimeGenerated, UserId, ClientIP, Operation, RuleName, ForwardTo\n| sort by TimeGenerated desc`
            },
            {
                id: 7,
                title: isSpanish ? "Heurística de Ransomware (Mass File Modification)" : "Ransomware Heuristics (Mass File Mod)",
                desc: isSpanish 
                    ? "Detecta picos anómalos de modificaciones masivas por un solo proceso en una ventana de 5 minutos, indicador clave de cifrado por ransomware." 
                    : "Detects anomalous spikes in massive file modifications by a single process within a 5-minute window, a key indicator of ransomware.",
                lang: "KQL",
                tags: ["KQL", "Endpoint"],
                tagClasses: ["tag-kql", "tag-mde"],
                code: `DeviceFileEvents\n| where TimeGenerated > ago(1d)\n| where ActionType in ("FileRenamed", "FileModified")\n// ${isSpanish ? 'Agrupamos en bloques de 5 minutos para buscar picos agresivos' : 'Group in 5-minute bins to look for aggressive spikes'}\n| summarize FileCount = count() by bin(TimeGenerated, 5m), DeviceName, InitiatingProcessFileName, InitiatingProcessCommandLine\n// ${isSpanish ? 'Umbral de alerta: más de 100 archivos en 5 mins' : 'Alert threshold: more than 100 files in 5 mins'}\n| where FileCount > 100\n// ${isSpanish ? 'Filtramos falsos positivos conocidos (indexadores, antivirus)' : 'Filter known false positives (indexers, antivirus)'}\n| where InitiatingProcessFileName !in~ ("ntoskrnl.exe", "msmpeng.exe", "searchindexer.exe")\n| order by FileCount desc\n| project TimeGenerated, DeviceName, InitiatingProcessFileName, FileCount, InitiatingProcessCommandLine`
            },
            {
                id: 8,
                title: isSpanish ? "Fatiga de MFA (MFA Spamming / Fatigue)" : "MFA Fatigue Attack (Spamming)",
                desc: isSpanish 
                    ? "Detecta múltiples solicitudes de MFA denegadas por el usuario en poco tiempo, seguidas de un acceso exitoso." 
                    : "Detects multiple MFA requests denied by the user in a short time, followed by a successful login.",
                lang: "KQL",
                tags: ["KQL", "Azure AD", "Identity"],
                tagClasses: ["tag-kql", "tag-kql", "tag-ps"],
                code: `SigninLogs\n| where TimeGenerated > ago(1d)\n| where ResultType == "500121" // ${isSpanish ? 'MFA Denegado' : 'MFA Denied'}\n| summarize DeniedCount = count() by UserPrincipalName, bin(TimeGenerated, 10m)\n| where DeniedCount >= 3\n| join kind=inner (\n    SigninLogs\n    | where TimeGenerated > ago(1d)\n    | where ResultType == 0 // ${isSpanish ? 'Éxito' : 'Success'}\n) on UserPrincipalName\n| project TimeGenerated, UserPrincipalName, DeniedCount, IPAddress, AppDisplayName\n| sort by TimeGenerated desc`
            },
            {
                id: 9,
                title: isSpanish ? "Backdoor en Service Principal (Azure AD)" : "Service Principal Backdoor (Azure AD)",
                desc: isSpanish 
                    ? "Detecta cuando un atacante añade una nueva credencial (secreto o certificado) a una aplicación de Azure AD existente para mantener persistencia." 
                    : "Detects when an attacker adds a new credential (secret or certificate) to an existing Azure AD application to maintain persistence.",
                lang: "KQL",
                tags: ["KQL", "Cloud Security"],
                tagClasses: ["tag-kql", "tag-ps"],
                code: `AuditLogs\n| where TimeGenerated > ago(7d)\n| where OperationName in ("Update application - Certificates and secrets management", "Add service principal credentials")\n| extend Actor = tostring(InitiatedBy.user.userPrincipalName)\n| extend TargetApp = tostring(TargetResources[0].displayName)\n| project TimeGenerated, OperationName, Actor, TargetApp, Result\n| sort by TimeGenerated desc`
            },
            {
                id: 10,
                title: isSpanish ? "Volcado de Memoria LSASS (Credential Access)" : "LSASS Memory Dump (Credential Access)",
                desc: isSpanish 
                    ? "Identifica procesos sospechosos intentando acceder y volcar la memoria del proceso LSASS.exe para robar contraseñas." 
                    : "Identifies suspicious processes attempting to access and dump the memory of the LSASS.exe process to steal passwords.",
                lang: "KQL",
                tags: ["KQL", "M365 Defender", "Endpoint"],
                tagClasses: ["tag-kql", "tag-mde", "tag-mde"],
                code: `DeviceEvents\n| where TimeGenerated > ago(1d)\n| where ActionType == "ProcessAccessed"\n| extend TargetProcess = tostring(parse_json(AdditionalFields).TargetProcessName)\n| where TargetProcess =~ "lsass.exe"\n// ${isSpanish ? 'Filtramos falsos positivos normales del sistema' : 'Filter normal system false positives'}\n| where InitiatingProcessFileName !in~ ("svchost.exe", "csrss.exe", "wininit.exe", "msmpeng.exe")\n| project TimeGenerated, DeviceName, InitiatingProcessFileName, InitiatingProcessCommandLine, TargetProcess\n| sort by TimeGenerated desc`
            },
            {
                id: 11,
                title: isSpanish ? "Eliminación de Shadow Copies (Ransomware)" : "Shadow Copy Deletion (Ransomware)",
                desc: isSpanish 
                    ? "Busca comandos nativos de Windows (vssadmin, wmic, bcdedit) utilizados por ransomware para borrar copias de seguridad locales antes de cifrar." 
                    : "Looks for native Windows commands (vssadmin, wmic, bcdedit) used by ransomware to delete local backups before encrypting.",
                lang: "KQL",
                tags: ["KQL", "Endpoint", "Ransomware"],
                tagClasses: ["tag-kql", "tag-mde", "tag-ps"],
                code: `DeviceProcessEvents\n| where TimeGenerated > ago(7d)\n| where FileName in~ ("vssadmin.exe", "wmic.exe", "bcdedit.exe", "wbadmin.exe", "powershell.exe")\n| where ProcessCommandLine has_any ("delete shadows", "shadowcopy delete", "recoveryenabled no", "delete catalog -quiet")\n| project TimeGenerated, DeviceName, AccountName, FileName, ProcessCommandLine, InitiatingProcessFileName\n| sort by TimeGenerated desc`
            },
            {
                id: 12,
                title: isSpanish ? "Ofimática Generando Procesos Sospechosos" : "Office Apps Spawning Suspicious Processes",
                desc: isSpanish 
                    ? "Identifica macros maliciosas detectando aplicaciones de Office (Word, Excel) lanzando consolas de comandos o ejecutables inusuales." 
                    : "Identifies malicious macros by detecting Office applications (Word, Excel) launching command consoles or unusual executables.",
                lang: "KQL",
                tags: ["KQL", "M365 Defender", "Phishing"],
                tagClasses: ["tag-kql", "tag-mde", "tag-ps"],
                code: `DeviceProcessEvents\n| where TimeGenerated > ago(7d)\n| where InitiatingProcessFileName in~ ("winword.exe", "excel.exe", "powerpnt.exe", "outlook.exe")\n| where FileName in~ ("cmd.exe", "powershell.exe", "pwsh.exe", "wscript.exe", "cscript.exe", "mshta.exe", "rundll32.exe")\n| project TimeGenerated, DeviceName, AccountName, InitiatingProcessFileName, FileName, ProcessCommandLine\n| sort by TimeGenerated desc`
            },
            {
                id: 13,
                title: isSpanish ? "Borrado de Registros de Eventos (Evasión)" : "Event Log Clearing (Defense Evasion)",
                desc: isSpanish 
                    ? "Detecta atacantes intentando borrar las huellas de su intrusión limpiando los logs de seguridad de Windows." 
                    : "Detects attackers attempting to cover their tracks by clearing Windows security logs.",
                lang: "KQL",
                tags: ["KQL", "Endpoint", "Evasión"],
                tagClasses: ["tag-kql", "tag-mde", "tag-ps"],
                code: `DeviceProcessEvents\n| where TimeGenerated > ago(14d)\n| where (FileName =~ "wevtutil.exe" and ProcessCommandLine has_any ("cl", "clear-log"))\n   or (FileName =~ "powershell.exe" and ProcessCommandLine has_any ("Clear-EventLog", "Remove-EventLog"))\n| project TimeGenerated, DeviceName, AccountName, FileName, ProcessCommandLine\n| sort by TimeGenerated desc`
            },
            {
                id: 14,
                title: isSpanish ? "Patrón de Beaconing a Servidor C2" : "C2 Beaconing Pattern Detection",
                desc: isSpanish 
                    ? "Analiza conexiones de red salientes buscando patrones repetitivos y periódicos (beaconing) típicos de malware comunicándose con su C2." 
                    : "Analyzes outbound network connections looking for repetitive and periodic patterns (beaconing) typical of malware communicating with C2.",
                lang: "KQL",
                tags: ["KQL", "Network", "C2"],
                tagClasses: ["tag-kql", "tag-ps", "tag-mde"],
                code: `DeviceNetworkEvents\n| where TimeGenerated > ago(1d)\n| where ActionType == "ConnectionSuccess"\n| where RemoteIPType == "Public"\n// ${isSpanish ? 'Filtramos IPs comunes (Microsoft, Google) para reducir ruido' : 'Filter common IPs (Microsoft, Google) to reduce noise'}\n| summarize ConnectionCount = count(), StartTime = min(TimeGenerated), EndTime = max(TimeGenerated) by DeviceName, RemoteIP, RemoteUrl\n| extend Duration = EndTime - StartTime\n| where ConnectionCount > 50 and Duration > 1h\n| extend ConnectionsPerHour = ConnectionCount / (Duration / 1h)\n| where ConnectionsPerHour > 10\n| sort by ConnectionsPerHour desc`
            },
            {
                id: 15,
                title: isSpanish ? "Descarga de Archivos via LOLBins (Certutil)" : "File Download via LOLBins (Certutil)",
                desc: isSpanish 
                    ? "Detecta el uso de binarios nativos del sistema operativo (Living off the Land) como certutil.exe para descargar malware evadiendo detecciones." 
                    : "Detects the use of native OS binaries (Living off the Land) like certutil.exe to download malware, evading detections.",
                lang: "KQL",
                tags: ["KQL", "Endpoint", "LOLBins"],
                tagClasses: ["tag-kql", "tag-mde", "tag-ps"],
                code: `DeviceProcessEvents\n| where TimeGenerated > ago(7d)\n| where FileName =~ "certutil.exe"\n| where ProcessCommandLine has_any ("-urlcache", "-split", "-f")\n| project TimeGenerated, DeviceName, AccountName, ProcessCommandLine, InitiatingProcessFileName\n| sort by TimeGenerated desc`
            },
            {
                id: 16,
                title: isSpanish ? "Creación de Cuenta Administrador Local" : "Local Admin Account Creation",
                desc: isSpanish 
                    ? "Busca actividad de comandos (net user, net localgroup) utilizados para crear cuentas locales y añadirlas a Administradores para persistencia." 
                    : "Looks for command activity (net user, net localgroup) used to create local accounts and add them to Administrators for persistence.",
                lang: "KQL",
                tags: ["KQL", "Endpoint", "PrivEsc"],
                tagClasses: ["tag-kql", "tag-mde", "tag-ps"],
                code: `DeviceProcessEvents\n| where TimeGenerated > ago(7d)\n| where FileName =~ "net.exe" or FileName =~ "net1.exe"\n| where ProcessCommandLine has "localgroup administrators" and ProcessCommandLine has "/add"\n| project TimeGenerated, DeviceName, AccountName, ProcessCommandLine, InitiatingProcessFileName\n| sort by TimeGenerated desc`
            },
            {
                id: 17,
                title: isSpanish ? "Escalada de Privilegios en Azure AD" : "Azure AD Privilege Escalation",
                desc: isSpanish 
                    ? "Alerta cuando a un usuario estándar se le asigna un rol altamente privilegiado en Azure AD (como Global Administrator)." 
                    : "Alerts when a highly privileged role (like Global Administrator) is assigned to a standard user in Azure AD.",
                lang: "KQL",
                tags: ["KQL", "Azure AD", "Identity"],
                tagClasses: ["tag-kql", "tag-kql", "tag-ps"],
                code: `AuditLogs\n| where TimeGenerated > ago(7d)\n| where OperationName == "Add member to role"\n| extend RoleName = tostring(parse_json(tostring(ModifiedProperties[1].newValue)))\n| where RoleName has_any ("Global Administrator", "Privileged Role Administrator", "Security Administrator")\n| extend TargetUser = tostring(TargetResources[0].userPrincipalName)\n| extend InitiatedBy = tostring(InitiatedBy.user.userPrincipalName)\n| project TimeGenerated, InitiatedBy, TargetUser, RoleName\n| sort by TimeGenerated desc`
            },
            {
                id: 18,
                title: isSpanish ? "Detección de Web Shell (IIS/Apache)" : "Web Shell Detection (IIS/Apache)",
                desc: isSpanish 
                    ? "Detecta posible ejecución de Web Shell monitoreando procesos de servidores web (w3wp.exe, httpd.exe) lanzando consolas de comandos interactivas." 
                    : "Detects possible Web Shell execution by monitoring web server processes (w3wp.exe, httpd.exe) spawning interactive command consoles.",
                lang: "KQL",
                tags: ["KQL", "Web", isSpanish ? "Persistencia" : "Persistence"],
                tagClasses: ["tag-kql", "tag-ps", "tag-mde"],
                code: `DeviceProcessEvents\n| where TimeGenerated > ago(7d)\n| where InitiatingProcessFileName in~ ("w3wp.exe", "httpd.exe", "nginx.exe", "tomcat.exe", "php-cgi.exe")\n| where FileName in~ ("cmd.exe", "powershell.exe", "bash", "sh", "whoami.exe")\n| project TimeGenerated, DeviceName, InitiatingProcessFileName, FileName, ProcessCommandLine\n| sort by TimeGenerated desc`
            },
            {
                id: 19,
                title: isSpanish ? "Ataque Pass-the-Hash (NTLM Anomaly)" : "Pass-the-Hash Attack (NTLM Anomaly)",
                desc: isSpanish 
                    ? "Identifica patrones de movimiento lateral rastreando inicios de sesión NTLM explícitos (Event ID 4624 Logon Type 9)." 
                    : "Identifies lateral movement patterns by tracking explicit NTLM logins (Event ID 4624 Logon Type 9).",
                lang: "KQL",
                tags: ["KQL", "Identity", "Lateral Mvt"],
                tagClasses: ["tag-kql", "tag-ps", "tag-mde"],
                code: `SecurityEvent\n| where TimeGenerated > ago(1d)\n| where EventID == 4624\n| where LogonType == 9 // ${isSpanish ? 'NewCredentials (típico de RunAs /netonly o Mimikatz)' : 'NewCredentials (typical of RunAs /netonly or Mimikatz)'}\n| where AuthenticationPackageName =~ "Negotiate"\n| project TimeGenerated, Computer, Account, IpAddress, ProcessName, LogonProcessName\n| sort by TimeGenerated desc`
            },
            {
                id: 20,
                title: isSpanish ? "Persistencia en Claves Run del Registro" : "Registry Run Key Persistence",
                desc: isSpanish 
                    ? "Busca modificaciones sospechosas en las claves de registro Run/RunOnce de Windows, un método clásico para ejecutar malware al inicio." 
                    : "Looks for suspicious modifications in Windows Run/RunOnce registry keys, a classic method for executing malware on startup.",
                lang: "KQL",
                tags: ["KQL", "Endpoint", isSpanish ? "Persistencia" : "Persistence"],
                tagClasses: ["tag-kql", "tag-mde", "tag-ps"],
                code: `DeviceRegistryEvents\n| where TimeGenerated > ago(7d)\n| where ActionType in ("RegistryValueSet", "RegistryKeyCreated")\n| where RegistryKey contains @"\\Software\\Microsoft\\Windows\\CurrentVersion\\Run"\n   or RegistryKey contains @"\\Software\\Microsoft\\Windows\\CurrentVersion\\RunOnce"\n// ${isSpanish ? 'Filtramos binarios limpios firmados' : 'Filter clean signed binaries'}\n| where InitiatingProcessFileName !endswith "explorer.exe"\n| project TimeGenerated, DeviceName, InitiatingProcessFileName, RegistryKey, RegistryValueName, RegistryValueData\n| sort by TimeGenerated desc`
            },
            {
                id: 21,
                title: isSpanish ? "Posible Kerberoasting (Ticket Granting Service)" : "Possible Kerberoasting (TGS Request)",
                desc: isSpanish 
                    ? "Detecta solicitudes excesivas de tickets TGS de Kerberos con cifrado RC4 (Event ID 4769), indicando intentos de crackear contraseñas offline." 
                    : "Detects excessive Kerberos TGS ticket requests using RC4 encryption (Event ID 4769), indicating attempts to crack passwords offline.",
                lang: "KQL",
                tags: ["KQL", "Identity", "AD"],
                tagClasses: ["tag-kql", "tag-ps", "tag-kql"],
                code: `SecurityEvent\n| where TimeGenerated > ago(1d)\n| where EventID == 4769\n| where TicketEncryptionType == "0x17" // RC4-HMAC\n| summarize TgsCount = count() by TargetUserName, IpAddress, bin(TimeGenerated, 10m)\n| where TgsCount > 15 // ${isSpanish ? 'Umbral de múltiples cuentas solicitadas rápidamente' : 'Threshold for multiple accounts requested rapidly'}\n| project TimeGenerated, TargetUserName, IpAddress, TgsCount\n| sort by TgsCount desc`
            },
            {
                id: 22,
                title: isSpanish ? "Abuso de Tareas Programadas (SchTasks)" : "Scheduled Task Abuse (SchTasks)",
                desc: isSpanish 
                    ? "Detecta la creación de tareas programadas mediante línea de comandos para ejecutar scripts de PowerShell o binarios ocultos." 
                    : "Detects the creation of scheduled tasks via command line to execute PowerShell scripts or hidden binaries.",
                lang: "KQL",
                tags: ["KQL", "Endpoint", isSpanish ? "Persistencia" : "Persistence"],
                tagClasses: ["tag-kql", "tag-mde", "tag-ps"],
                code: `DeviceProcessEvents\n| where TimeGenerated > ago(7d)\n| where FileName =~ "schtasks.exe"\n| where ProcessCommandLine has_any ("/create", "/change")\n| where ProcessCommandLine has_any ("powershell", "cmd", "rundll32", "regsvr32", "wscript", "cscript")\n| project TimeGenerated, DeviceName, AccountName, ProcessCommandLine, InitiatingProcessFileName\n| sort by TimeGenerated desc`
            },
            {
                id: 23,
                title: isSpanish ? "Intento de Explotación de Log4j (WAF)" : "Log4j Exploitation Attempt (WAF)",
                desc: isSpanish 
                    ? "Busca cadenas específicas (jndi:ldap, jndi:rmi) en los registros de WAF que indican intentos de explotación de la vulnerabilidad Log4Shell." 
                    : "Looks for specific strings (jndi:ldap) in WAF logs indicating Log4Shell vulnerability exploitation attempts.",
                lang: "KQL",
                tags: ["KQL", "Network", "WAF"],
                tagClasses: ["tag-kql", "tag-ps", "tag-kql"],
                code: `AzureDiagnostics\n| where TimeGenerated > ago(7d)\n| where Category == "ApplicationGatewayFirewallLog" or Category == "FrontdoorWebApplicationFirewallLog"\n| where requestUri_s has_any ("jndi:ldap", "jndi:rmi", "jndi:dns") \n   or userAgent_s has_any ("jndi:ldap", "jndi:rmi", "jndi:dns")\n| project TimeGenerated, clientIP_s, requestUri_s, userAgent_s, action_s, Message\n| sort by TimeGenerated desc`
            },
            {
                id: 24,
                title: isSpanish ? "Descarga Oculta con BITSAdmin" : "Hidden Download via BITSAdmin",
                desc: isSpanish 
                    ? "Identifica el uso del servicio BITS para transferir archivos maliciosos de forma asíncrona y evadir cortafuegos." 
                    : "Identifies the use of the BITS service to transfer malicious files asynchronously and evade firewalls.",
                lang: "KQL",
                tags: ["KQL", "Endpoint", "LOLBins"],
                tagClasses: ["tag-kql", "tag-mde", "tag-ps"],
                code: `DeviceProcessEvents\n| where TimeGenerated > ago(7d)\n| where FileName =~ "bitsadmin.exe"\n| where ProcessCommandLine has_any ("/transfer", "/create", "/addfile", "/setnotifyflags", "/resume")\n| project TimeGenerated, DeviceName, AccountName, ProcessCommandLine, InitiatingProcessFileName\n| sort by TimeGenerated desc`
            },
            {
                id: 25,
                title: isSpanish ? "Inicio de Sesión desde Nodos Tor (Anonymity)" : "Login from Tor Nodes (Anonymity)",
                desc: isSpanish 
                    ? "Cruza los registros de inicio de sesión con Threat Intelligence para detectar usuarios autenticándose desde redes Tor." 
                    : "Cross-references sign-in logs with Threat Intelligence to detect users authenticating from Tor networks.",
                lang: "KQL",
                tags: ["KQL", "Azure AD", "Threat Intel"],
                tagClasses: ["tag-kql", "tag-kql", "tag-mde"],
                code: `SigninLogs\n| where TimeGenerated > ago(1d)\n| where ResultType == 0\n// ${isSpanish ? 'Comprobamos la etiqueta de RiskEvent para IPs anónimas' : 'Check the RiskEvent tag for anonymous IPs'}\n| where RiskEventTypes_V2 has "anonymousIP" or NetworkLocationDetails has "Tor"\n| project TimeGenerated, UserPrincipalName, IPAddress, Location, AppDisplayName, RiskEventTypes_V2\n| sort by TimeGenerated desc`
            },
            {
                id: 26,
                title: isSpanish ? "Sabotaje en la Nube (Destrucción Masiva)" : "Cloud Sabotage (Massive Destruction)",
                desc: isSpanish 
                    ? "Detecta a un administrador (o atacante) intentando eliminar múltiples recursos críticos o máquinas virtuales en Azure en poco tiempo." 
                    : "Detects an admin (or attacker) attempting to delete multiple critical resources or VMs in Azure in a short time.",
                lang: "KQL",
                tags: ["KQL", "Cloud Security", "Sabotage"],
                tagClasses: ["tag-kql", "tag-ps", "tag-kql"],
                code: `AzureActivity\n| where TimeGenerated > ago(1d)\n| where OperationNameValue endswith "/delete"\n| summarize DeleteCount = count(), ResourcesDeleted = make_set(Resource) by Caller, bin(TimeGenerated, 15m)\n| where DeleteCount >= 5 // ${isSpanish ? 'Umbral de múltiples borrados rápidos' : 'Threshold for multiple rapid deletions'}\n| project TimeGenerated, Caller, DeleteCount, ResourcesDeleted\n| sort by DeleteCount desc`
            },
            {
                id: 27,
                title: isSpanish ? "Ejecución de Código en Memoria (Reflective DLL)" : "In-Memory Code Execution (Reflective DLL)",
                desc: isSpanish 
                    ? "Apunta a inyecciones de código en memoria rastreando procesos usando métodos para saltarse el análisis AMSI." 
                    : "Targets in-memory code injections by tracking processes using methods to bypass AMSI scanning.",
                lang: "KQL",
                tags: ["KQL", "M365 Defender", isSpanish ? "Evasión" : "Evasion"],
                tagClasses: ["tag-kql", "tag-mde", "tag-ps"],
                code: `DeviceEvents\n| where TimeGenerated > ago(7d)\n| where ActionType == "AmsiBypass" or ActionType == "SuspiciousMemoryAllocation"\n| project TimeGenerated, DeviceName, InitiatingProcessFileName, ActionType, AdditionalFields\n| sort by TimeGenerated desc`
            },
            {
                id: 28,
                title: isSpanish ? "ALERTA: Infección MDE + Movimiento Lateral" : "ALERT: MDE Infection + Lateral Movement",
                desc: isSpanish 
                    ? "[REGLA ANALÍTICA] Cruza alertas de Defender for Endpoint (MDE) con conexiones de red (RDP/SMB) hacia otros equipos en los siguientes 30 minutos." 
                    : "[ANALYTICS RULE] Correlates MDE alerts with network connections (RDP/SMB) to other hosts within 30 minutes.",
                lang: "Sentinel Rule",
                tags: ["Alert Rule", "MDE", "Correlación"],
                tagClasses: ["tag-alert", "tag-mde", "tag-kql"],
                code: `// ==========================================\n// ${isSpanish ? 'CONFIGURACIÓN DE LA REGLA EN AZURE SENTINEL' : 'AZURE SENTINEL RULE CONFIGURATION'}\n// ==========================================\n// ${isSpanish ? 'Frecuencia: Cada 1 hora' : 'Frequency: Every 1 hour'}\n// ${isSpanish ? 'Búsqueda de datos: Última 1 hora' : 'Data lookup: Last 1 hour'}\n// ${isSpanish ? 'Tácticas MITRE: Lateral Movement (TA0008)' : 'MITRE Tactics: Lateral Movement (TA0008)'}\n// ${isSpanish ? 'Mapeo de Entidades:' : 'Entity Mapping:'} \n//   - Account -> CompromisedUser\n//   - Host -> TargetDevice\n//   - IP -> TargetIP\n// ==========================================\n\nlet TimeFrame = 1h;\nSecurityAlert\n| where TimeGenerated > ago(TimeFrame)\n| where ProviderName == "MDATP"\n| where AlertName has_any ("Malware", "Ransomware", "Backdoor", "Cobalt Strike")\n| extend CompromisedHost = tostring(parse_json(ExtendedProperties).MachineName)\n| extend CompromisedUser = tostring(parse_json(ExtendedProperties).UserName)\n| join kind=inner (\n    DeviceNetworkEvents\n    | where TimeGenerated > ago(TimeFrame)\n    | where ActionType == "ConnectionSuccess"\n    | where RemotePort in (3389, 445)\n    | project NetworkTime=TimeGenerated, CompromisedHost=DeviceName, TargetIP=RemoteIP, TargetDevice=RemoteUrl, AccountName=InitiatingProcessAccountName\n) on CompromisedHost\n| where NetworkTime > TimeGenerated // ${isSpanish ? 'El movimiento fue DESPUÉS de la alerta de MDE' : 'Movement was AFTER the MDE alert'}\n| project TimeGenerated, AlertName, CompromisedHost, CompromisedUser, TargetIP, TargetDevice, NetworkTime\n| sort by NetworkTime desc`
            },
            {
                id: 29,
                title: isSpanish ? "ALERTA: Viaje Imposible + Descarga Masiva" : "ALERT: Impossible Travel + Mass Download",
                desc: isSpanish 
                    ? "[REGLA ANALÍTICA] Correlación multi-nube. Detecta un inicio de sesión desde un país inusual seguido inmediatamente por descargas masivas en SharePoint/OneDrive." 
                    : "[ANALYTICS RULE] Multi-cloud correlation. Detects a login from an unusual country immediately followed by mass downloads in SharePoint/OneDrive.",
                lang: "Sentinel Rule",
                tags: ["Alert Rule", "Azure AD", "Exfiltration"],
                tagClasses: ["tag-alert", "tag-kql", "tag-ps"],
                code: `// ==========================================\n// ${isSpanish ? 'CONFIGURACIÓN DE LA REGLA' : 'RULE CONFIGURATION'}\n// ==========================================\n// ${isSpanish ? 'Frecuencia: Cada 30 minutos' : 'Frequency: Every 30 minutes'}\n// ${isSpanish ? 'Mapeo de Entidades:' : 'Entity Mapping:'} \n//   - Account -> UserPrincipalName\n//   - IP -> SuspiciousIP\n// ==========================================\n\nlet ImpossibleTravel = SigninLogs\n| where TimeGenerated > ago(1h)\n| where ResultType == 0\n| where RiskEventTypes_V2 has "impossibleTravel" or RiskLevelDuringSignIn in ("High", "Medium")\n| project SignInTime=TimeGenerated, UserPrincipalName, SuspiciousIP=IPAddress, Location;\nImpossibleTravel\n| join kind=inner (\n    OfficeActivity\n    | where TimeGenerated > ago(1h)\n    | where RecordType == "SharePointFileOperation"\n    | where Operation in ("FileDownloaded", "FileSyncDownloadedFull")\n    | summarize DownloadCount = count() by UserId, ClientIP\n    | where DownloadCount > 20 // ${isSpanish ? 'Umbral de descarga' : 'Download threshold'}\n) on $left.UserPrincipalName == $right.UserId\n| project SignInTime, UserPrincipalName, SuspiciousIP, Location, DownloadCount\n| sort by DownloadCount desc`
            },
            {
                id: 30,
                title: isSpanish ? "ALERTA: Tampering de Defender (Apagado)" : "ALERT: Defender Tampering (Shutdown)",
                desc: isSpanish 
                    ? "[REGLA ANALÍTICA] Monitoriza los endpoints a través de MDE para detectar intentos de apagar servicios críticos de seguridad (Tampering)." 
                    : "[ANALYTICS RULE] Monitors endpoints via MDE to detect attempts to shut down critical security services (Tampering).",
                lang: "Sentinel Rule",
                tags: ["Alert Rule", "MDE", "Evasión"],
                tagClasses: ["tag-alert", "tag-mde", "tag-ps"],
                code: `// ==========================================\n// ${isSpanish ? 'CONFIGURACIÓN DE LA REGLA' : 'RULE CONFIGURATION'}\n// ==========================================\n// ${isSpanish ? 'Frecuencia: Cada 15 minutos' : 'Frequency: Every 15 minutes'}\n// ${isSpanish ? 'Severidad: ALTA (Creación Auto de Incidente)' : 'Severity: HIGH (Auto Incident Creation)'}\n// ==========================================\n\nDeviceProcessEvents\n| where TimeGenerated > ago(15m)\n| where (\n    (FileName =~ "net.exe" and ProcessCommandLine has_any ("stop WinDefend", "stop mpssvc", "stop wscsvc"))\n    or\n    (FileName =~ "sc.exe" and ProcessCommandLine has_any ("stop WinDefend", "config WinDefend start= disabled"))\n    or\n    (FileName =~ "powershell.exe" and ProcessCommandLine has "Set-MpPreference -DisableRealtimeMonitoring $true")\n)\n| project TimeGenerated, DeviceName, InitiatingProcessAccountName, FileName, ProcessCommandLine\n| sort by TimeGenerated desc`
            },
            {
                id: 31,
                title: isSpanish ? "ALERTA: Escalada Rápida a Global Admin" : "ALERT: Rapid Escalation to Global Admin",
                desc: isSpanish 
                    ? "[REGLA ANALÍTICA] Alerta crítica si una cuenta recién creada en Azure AD recibe permisos de Global Admin en menos de 4 horas." 
                    : "[ANALYTICS RULE] Critical alert if a newly created Azure AD account receives Global Admin permissions in less than 4 hours.",
                lang: "Sentinel Rule",
                tags: ["Alert Rule", "Azure AD", "PrivEsc"],
                tagClasses: ["tag-alert", "tag-kql", "tag-kql"],
                code: `// ==========================================\n// ${isSpanish ? 'CONFIGURACIÓN DE LA REGLA' : 'RULE CONFIGURATION'}\n// ==========================================\n// ${isSpanish ? 'Mapeo de Entidades:' : 'Entity Mapping:'} \n//   - Account -> TargetUser\n//   - Account (Atacante) -> InitiatedBy\n// ==========================================\n\nlet NewUsers = AuditLogs\n| where TimeGenerated > ago(24h)\n| where OperationName == "Add user"\n| extend TargetUser = tostring(TargetResources[0].userPrincipalName)\n| extend CreationTime = TimeGenerated\n| project TargetUser, CreationTime;\nAuditLogs\n| where TimeGenerated > ago(24h)\n| where OperationName == "Add member to role"\n| extend RoleName = tostring(parse_json(tostring(ModifiedProperties[1].newValue)))\n| where RoleName == "Global Administrator"\n| extend TargetUser = tostring(TargetResources[0].userPrincipalName)\n| extend InitiatedBy = tostring(InitiatedBy.user.userPrincipalName)\n| extend RoleAssignTime = TimeGenerated\n| join kind=inner NewUsers on TargetUser\n| extend TimeDifference = RoleAssignTime - CreationTime\n| where TimeDifference < 4h // ${isSpanish ? 'Escalada en menos de 4h desde creación' : 'Escalation in less than 4h since creation'}\n| project RoleAssignTime, InitiatedBy, TargetUser, RoleName, TimeDifference\n| sort by TimeDifference asc`
            },
            {
                id: 32,
                title: isSpanish ? "ALERTA: Password Spraying Masivo (Azure AD)" : "ALERT: Massive Password Spraying (Azure AD)",
                desc: isSpanish 
                    ? "[REGLA ANALÍTICA] Detecta una única IP intentando iniciar sesión en más de 20 cuentas distintas de la organización con fallos consecutivos." 
                    : "[ANALYTICS RULE] Detects a single IP attempting to log into more than 20 different organizational accounts with consecutive failures.",
                lang: "Sentinel Rule",
                tags: ["Alert Rule", "Azure AD", "Credential Access"],
                tagClasses: ["tag-alert", "tag-kql", "tag-kql"],
                code: `// ==========================================\n// ${isSpanish ? 'Mapeo: IP -> SuspiciousIP' : 'Mapping: IP -> SuspiciousIP'}\n// ==========================================\nSigninLogs\n| where TimeGenerated > ago(1h)\n| where ResultType != 0 // ${isSpanish ? 'Inicios fallidos' : 'Failed logins'}\n| summarize AccountsTargeted = dcount(UserPrincipalName), FailedAttempts = count() by IPAddress, bin(TimeGenerated, 15m)\n| where AccountsTargeted >= 20\n| project TimeGenerated, IPAddress, AccountsTargeted, FailedAttempts\n| sort by AccountsTargeted desc`
            },
            {
                id: 33,
                title: isSpanish ? "ALERTA: RDP Brute Force + Éxito (MDE)" : "ALERT: RDP Brute Force + Success (MDE)",
                desc: isSpanish 
                    ? "[REGLA ANALÍTICA] Detecta múltiples conexiones de red fallidas por RDP seguidas de un inicio de sesión interactivo exitoso en el mismo servidor." 
                    : "[ANALYTICS RULE] Detects multiple failed RDP network connections followed by a successful interactive login on the same server.",
                lang: "Sentinel Rule",
                tags: ["Alert Rule", "MDE", "Lateral Mvt"],
                tagClasses: ["tag-alert", "tag-mde", "tag-kql"],
                code: `// ==========================================\n// ${isSpanish ? 'Mapeo: Host -> TargetDevice, IP -> AttackerIP' : 'Mapping: Host -> TargetDevice, IP -> AttackerIP'}\n// ==========================================\nlet FailedRDP = DeviceNetworkEvents\n| where TimeGenerated > ago(1h)\n| where ActionType == "InboundConnectionAccepted"\n| where LocalPort == 3389\n| summarize ConnectionCount = count() by DeviceName, RemoteIP\n| where ConnectionCount > 50;\nFailedRDP\n| join kind=inner (\n    DeviceLogonEvents\n    | where TimeGenerated > ago(1h)\n    | where ActionType == "LogonSuccess"\n    | where LogonType == "RemoteInteractive"\n    | project DeviceName, RemoteIP, AccountName, LogonTime=TimeGenerated\n) on DeviceName, RemoteIP\n| project LogonTime, DeviceName, RemoteIP, AccountName, ConnectionCount\n| sort by LogonTime desc`
            },
            {
                id: 34,
                title: isSpanish ? "ALERTA: Regla de Bandeja Financiera (M365)" : "ALERT: Financial Inbox Rule Creation (M365)",
                desc: isSpanish 
                    ? "[REGLA ANALÍTICA] Detecta la creación de reglas de Outlook que mueven correos con palabras como 'factura', 'pago' o 'banco' a carpetas ocultas." 
                    : "[ANALYTICS RULE] Detects the creation of Outlook rules that move emails with words like 'invoice', 'payment', or 'bank' to hidden folders.",
                lang: "Sentinel Rule",
                tags: ["Alert Rule", "M365", "BEC"],
                tagClasses: ["tag-alert", "tag-mde", "tag-kql"],
                code: `// ==========================================\n// ${isSpanish ? 'Tácticas MITRE: Collection (TA0009)' : 'MITRE Tactics: Collection (TA0009)'}\n// ==========================================\nOfficeActivity\n| where TimeGenerated > ago(1h)\n| where OfficeWorkload == "Exchange"\n| where Operation in ("New-InboxRule", "Set-InboxRule")\n| extend RuleParameters = tostring(parse_json(Parameters))\n| where RuleParameters matches regex @"(?i)(invoice|factura|payment|pago|bank|banco|transfer|iban)"\n| where RuleParameters has_any ("MoveToFolder", "DeleteMessage", "MarkAsRead")\n| project TimeGenerated, UserId, ClientIP, Operation, RuleParameters\n| sort by TimeGenerated desc`
            },
            {
                id: 35,
                title: isSpanish ? "ALERTA: Borrado Masivo de Usuarios (Azure AD)" : "ALERT: Mass User Deletion (Azure AD)",
                desc: isSpanish 
                    ? "[REGLA ANALÍTICA] Alerta crítica si un administrador comprometido intenta sabotear el tenant eliminando más de 10 usuarios en menos de 10 minutos." 
                    : "[ANALYTICS RULE] Critical alert if a compromised admin attempts to sabotage the tenant by deleting more than 10 users in under 10 minutes.",
                lang: "Sentinel Rule",
                tags: ["Alert Rule", "Azure AD", "Impact"],
                tagClasses: ["tag-alert", "tag-kql", "tag-ps"],
                code: `// ==========================================\n// ${isSpanish ? 'Severidad: CRÍTICA' : 'Severity: CRITICAL'}\n// ==========================================\nAuditLogs\n| where TimeGenerated > ago(1h)\n| where OperationName == "Delete user"\n| extend Actor = tostring(InitiatedBy.user.userPrincipalName)\n| summarize DeletedUsers = make_set(TargetResources[0].userPrincipalName), DeleteCount = count() by Actor, bin(TimeGenerated, 10m)\n| where DeleteCount >= 10\n| project TimeGenerated, Actor, DeleteCount, DeletedUsers\n| sort by DeleteCount desc`
            },
            {
                id: 36,
                title: isSpanish ? "ALERTA: Ejecución de Archivo Descargado (MDE)" : "ALERT: Execution of Downloaded File (MDE)",
                desc: isSpanish 
                    ? "[REGLA ANALÍTICA] Detecta cuando un navegador descarga un archivo ejecutable (.exe, .scr, .vbs) y este es ejecutado inmediatamente por el usuario." 
                    : "[ANALYTICS RULE] Detects when a browser downloads an executable file (.exe, .scr, .vbs) and it is immediately executed by the user.",
                lang: "Sentinel Rule",
                tags: ["Alert Rule", "MDE", "Execution"],
                tagClasses: ["tag-alert", "tag-mde", "tag-kql"],
                code: `// ==========================================\n// ${isSpanish ? 'Mapeo: Host -> DeviceName, FileHash -> SHA1' : 'Mapping: Host -> DeviceName, FileHash -> SHA1'}\n// ==========================================\nlet BrowserDownloads = DeviceFileEvents\n| where TimeGenerated > ago(1h)\n| where ActionType == "FileCreated"\n| where InitiatingProcessFileName in~ ("chrome.exe", "msedge.exe", "firefox.exe")\n| where FileName endswith ".exe" or FileName endswith ".vbs" or FileName endswith ".ps1" or FileName endswith ".scr"\n| project DownloadTime=TimeGenerated, DeviceName, FileName, SHA1, InitiatingProcessFileName;\nBrowserDownloads\n| join kind=inner (\n    DeviceProcessEvents\n    | where TimeGenerated > ago(1h)\n    | project ExecutionTime=TimeGenerated, DeviceName, FileName, SHA1, ProcessCommandLine, AccountName\n) on DeviceName, SHA1\n| where ExecutionTime > DownloadTime // ${isSpanish ? 'Ejecutado después de descargar' : 'Executed after download'}\n| project DownloadTime, ExecutionTime, DeviceName, AccountName, FileName, ProcessCommandLine, Browser=InitiatingProcessFileName\n| sort by ExecutionTime desc`
            },
            {
                id: 37,
                title: isSpanish ? "ALERTA: Cradle de Descarga PowerShell (MDE)" : "ALERT: PowerShell Download Cradle (MDE)",
                desc: isSpanish 
                    ? "[REGLA ANALÍTICA] Detecta comandos de PowerShell usando Net.WebClient o Invoke-WebRequest para descargar código directamente a memoria (Fileless)." 
                    : "[ANALYTICS RULE] Detects PowerShell commands using Net.WebClient or Invoke-WebRequest to download code directly into memory (Fileless).",
                lang: "Sentinel Rule",
                tags: ["Alert Rule", "MDE", "Command & Control"],
                tagClasses: ["tag-alert", "tag-mde", "tag-ps"],
                code: `// ==========================================\n// ${isSpanish ? 'Tácticas: Command and Control' : 'Tactics: Command and Control'}\n// ==========================================\nDeviceProcessEvents\n| where TimeGenerated > ago(1h)\n| where FileName in~ ("powershell.exe", "pwsh.exe")\n| where ProcessCommandLine has_any ("Net.WebClient", "DownloadString", "Invoke-WebRequest", "iwr", "wget")\n| where ProcessCommandLine has_any ("http://", "https://")\n| where ProcessCommandLine has_any ("iex", "Invoke-Expression")\n| project TimeGenerated, DeviceName, AccountName, ProcessCommandLine, InitiatingProcessFileName\n| sort by TimeGenerated desc`
            },
            {
                id: 38,
                title: isSpanish ? "ALERTA: VM de Azure Creada y Borrada Rápido" : "ALERT: Azure VM Created & Deleted Rapidly",
                desc: isSpanish 
                    ? "[REGLA ANALÍTICA] Patrón clásico de criptominado. Un atacante compromete la nube, crea VMs gigantes para minar y las borra para evadir facturación/detección." 
                    : "[ANALYTICS RULE] Classic cryptomining pattern. An attacker compromises the cloud, creates giant VMs to mine, and deletes them to evade billing/detection.",
                lang: "Sentinel Rule",
                tags: ["Alert Rule", "Azure", "Impact"],
                tagClasses: ["tag-alert", "tag-ps", "tag-kql"],
                code: `// ==========================================\n// ${isSpanish ? 'Mapeo: Account -> Caller, Resource -> VMName' : 'Mapping: Account -> Caller, Resource -> VMName'}\n// ==========================================\nlet VMCreations = AzureActivity\n| where TimeGenerated > ago(24h)\n| where OperationNameValue == "MICROSOFT.COMPUTE/VIRTUALMACHINES/WRITE"\n| where ActivityStatusValue == "Success"\n| project CreateTime=TimeGenerated, Caller, VMName=Resource, ResourceGroup;\nVMCreations\n| join kind=inner (\n    AzureActivity\n    | where TimeGenerated > ago(24h)\n    | where OperationNameValue == "MICROSOFT.COMPUTE/VIRTUALMACHINES/DELETE"\n    | where ActivityStatusValue == "Success"\n    | project DeleteTime=TimeGenerated, Caller, VMName=Resource\n) on VMName\n| extend Lifespan = DeleteTime - CreateTime\n| where Lifespan < 12h // ${isSpanish ? 'VM vivió menos de 12 horas' : 'VM lived less than 12 hours'}\n| project CreateTime, DeleteTime, Lifespan, Caller, VMName, ResourceGroup\n| sort by Lifespan asc`
            },
            {
                id: 39,
                title: isSpanish ? "ALERTA: Modificación de Conditional Access" : "ALERT: Conditional Access Policy Mod",
                desc: isSpanish 
                    ? "[REGLA ANALÍTICA] Alerta altamente crítica. Un atacante o Insider Threat ha modificado las reglas de acceso condicional para debilitar la seguridad (ej. desactivar MFA)." 
                    : "[ANALYTICS RULE] Highly critical alert. An attacker or Insider Threat modified conditional access rules to weaken security (e.g. disabling MFA).",
                lang: "Sentinel Rule",
                tags: ["Alert Rule", "Azure AD", "Defense Evasion"],
                tagClasses: ["tag-alert", "tag-kql", "tag-ps"],
                code: `// ==========================================\n// ${isSpanish ? 'Severidad: CRÍTICA' : 'Severity: CRITICAL'}\n// ==========================================\nAuditLogs\n| where TimeGenerated > ago(1h)\n| where Category == "Policy"\n| where OperationName in ("Update policy", "Delete policy", "Add policy")\n| extend PolicyName = tostring(TargetResources[0].displayName)\n| extend Actor = tostring(InitiatedBy.user.userPrincipalName)\n// ${isSpanish ? 'Extraer el cambio exacto (valor antiguo vs nuevo)' : 'Extract exact change (old vs new value)'}\n| extend ModifiedProperties = tostring(TargetResources[0].modifiedProperties)\n| project TimeGenerated, OperationName, PolicyName, Actor, ModifiedProperties\n| sort by TimeGenerated desc`
            },
            {
                id: 40,
                title: isSpanish ? "ALERTA: Acceso Sospechoso a Key Vault" : "ALERT: Suspicious Key Vault Access",
                desc: isSpanish 
                    ? "[REGLA ANALÍTICA] Detecta accesos exitosos a Azure Key Vault (donde se guardan contraseñas/certificados) desde IPs que no pertenecen a la red corporativa." 
                    : "[ANALYTICS RULE] Detects successful accesses to Azure Key Vault (where passwords/certificates are stored) from IPs outside the corporate network.",
                lang: "Sentinel Rule",
                tags: ["Alert Rule", "Azure", "Credential Access"],
                tagClasses: ["tag-alert", "tag-ps", "tag-kql"],
                code: `// ==========================================\n// ${isSpanish ? 'Requiere habilitar logs de Diagnóstico en Key Vault' : 'Requires enabling Diagnostics logs on Key Vault'}\n// ==========================================\nAzureDiagnostics\n| where TimeGenerated > ago(1h)\n| where ResourceProvider == "MICROSOFT.KEYVAULT"\n| where OperationName in ("SecretGet", "KeyGet", "VaultGet")\n| extend ClientIP = CallerIPAddress\n// ${isSpanish ? 'Excluir subredes corporativas (EJEMPLO)' : 'Exclude corporate subnets (EXAMPLE)'}\n| where ClientIP !startswith "192.168." and ClientIP !startswith "10."\n// ${isSpanish ? 'Excluir servicios internos de Azure' : 'Exclude internal Azure services'}\n| where identity_claim_appid_g != ""\n| project TimeGenerated, Resource, OperationName, ClientIP, identity_claim_upn_s\n| sort by TimeGenerated desc`
            },
            {
                id: 41,
                title: isSpanish ? "ALERTA: Vaciado de Papelera en O365" : "ALERT: O365 Mailbox Purge (Destruction)",
                desc: isSpanish 
                    ? "[REGLA ANALÍTICA] Detecta cuando un usuario purga permanentemente (HardDelete) correos. Común cuando un atacante borra correos de phishing para ocultar el rastro." 
                    : "[ANALYTICS RULE] Detects when a user permanently purges (HardDelete) emails. Common when an attacker deletes phishing emails to hide their tracks.",
                lang: "Sentinel Rule",
                tags: ["Alert Rule", "M365", "Defense Evasion"],
                tagClasses: ["tag-alert", "tag-mde", "tag-ps"],
                code: `// ==========================================\n// ${isSpanish ? 'Mapeo: Account -> UserId' : 'Mapping: Account -> UserId'}\n// ==========================================\nOfficeActivity\n| where TimeGenerated > ago(1h)\n| where OfficeWorkload == "Exchange"\n| where Operation in ("HardDelete", "Purge")\n| summarize PurgeCount = count() by UserId, ClientIP, bin(TimeGenerated, 15m)\n| where PurgeCount > 10 // ${isSpanish ? 'Purgar 10 correos seguidos es sospechoso' : 'Purging 10 emails in a row is suspicious'}\n| project TimeGenerated, UserId, ClientIP, PurgeCount\n| sort by PurgeCount desc`
            },
            {
                id: 42,
                title: isSpanish ? "ALERTA: Explotación PrintNightmare (MDE)" : "ALERT: PrintNightmare Exploitation (MDE)",
                desc: isSpanish 
                    ? "[REGLA ANALÍTICA] Detecta la explotación de la vulnerabilidad PrintNightmare (CVE-2021-1675) buscando el servicio Spooler cargando DLLs sospechosas." 
                    : "[ANALYTICS RULE] Detects exploitation of the PrintNightmare vulnerability (CVE-2021-1675) by looking for the Spooler service loading suspicious DLLs.",
                lang: "Sentinel Rule",
                tags: ["Alert Rule", "MDE", "PrivEsc"],
                tagClasses: ["tag-alert", "tag-mde", "tag-kql"],
                code: `// ==========================================\n// ${isSpanish ? 'Tácticas MITRE: Privilege Escalation' : 'MITRE Tactics: Privilege Escalation'}\n// ==========================================\nDeviceImageLoadEvents\n| where TimeGenerated > ago(1h)\n| where InitiatingProcessFileName =~ "spoolsv.exe"\n| where FolderPath has @"\\Windows\\System32\\spool\\drivers\\x64\\3"\n| where FileName endswith ".dll"\n// ${isSpanish ? 'Filtramos drivers legítimos firmados por Microsoft' : 'Filter legitimate Microsoft-signed drivers'}\n| where SignatureStatus != "Valid" or IsSigned == 0\n| project TimeGenerated, DeviceName, InitiatingProcessFileName, FileName, FolderPath, SHA1\n| sort by TimeGenerated desc`
            }
        ];

        // Función para renderizar la lista
        function renderArsenalList(items) {
            arsenalListContainer.innerHTML = '';
            items.forEach(item => {
                const li = document.createElement('li');
                li.className = 'arsenal-item';
                
                li.addEventListener('click', () => showArsenalScript(item, li));
                
                let tagsHtml = '';
                item.tags.forEach((tag, index) => {
                    tagsHtml += `<span class="tag ${item.tagClasses[index]}">${tag}</span>`;
                });

                li.innerHTML = `
                    <div class="item-title">${item.title}</div>
                    <div class="item-tags">${tagsHtml}</div>
                `;
                arsenalListContainer.appendChild(li);
            });
        }

        // Mostrar el script en el panel derecho
        function showArsenalScript(item, element) {
            document.querySelectorAll('.arsenal-item').forEach(el => el.classList.remove('active'));
            element.classList.add('active');

            let tagsHtml = '';
            item.tags.forEach((tag, index) => {
                tagsHtml += `<span class="tag ${item.tagClasses[index]}">${tag}</span>`;
            });

            const copyText = isSpanish ? 'COPIAR' : 'COPY';

            // Inyectar HTML
            arsenalViewer.innerHTML = `
                <div style="display:flex; gap: 0.5rem; margin-bottom: 0.5rem;">${tagsHtml}</div>
                <h2 class="viewer-title">${item.title}</h2>
                <p class="viewer-desc">${item.desc}</p>
                
                <div class="code-container">
                    <div class="code-header">
                        <span class="code-lang">${item.lang} SCRIPT</span>
                        <button class="btn-copy" id="copyArsenalBtn">${copyText}</button>
                    </div>
                    <pre class="code-content" id="codeBlock">${item.code}</pre>
                </div>
            `;

            // Bilingüe para la alerta de Copiado
            const copiedSuccessText = isSpanish ? '¡COPIADO!' : 'COPIED!';

            document.getElementById('copyArsenalBtn').addEventListener('click', function() {
                const codeText = document.getElementById('codeBlock').innerText;
                navigator.clipboard.writeText(codeText).then(() => {
                    this.innerText = copiedSuccessText;
                    this.style.background = 'var(--cyan)';
                    this.style.color = '#000';
                    setTimeout(() => {
                        this.innerText = copyText;
                        this.style.background = 'transparent';
                        this.style.color = 'var(--cyan)';
                    }, 2000);
                });
            });
        }

        // ==========================================
        // FILTROS AVANZADOS Y BÚSQUEDA
        // ==========================================
        
        // 1. Inyectar los botones de filtro en el HTML dinámicamente
        const filterHTML = `
            <div class="arsenal-filters" style="display:flex; flex-wrap:wrap; gap:0.5rem; padding: 0.5rem 1.2rem 1.2rem; border-bottom: 1px solid var(--border);">
                <button class="filter-btn active" data-type="all">${isSpanish ? 'Todo' : 'All'}</button>
                <button class="filter-btn" data-type="hunting">${isSpanish ? '🔎 Consultas' : '🔎 Hunting'}</button>
                <button class="filter-btn" data-type="alert">${isSpanish ? '🚨 Alertas' : '🚨 Alerts'}</button>
            </div>
        `;
        arsenalSearch.insertAdjacentHTML('afterend', filterHTML);

        let currentType = 'all';
        let currentSearchTerm = '';

        // Función centralizada para filtrar la base de datos
        function applyFilters() {
            let filtered = arsenalData;

            // 1. Filtrar por Tipo (Hunting vs Alertas)
            if (currentType === 'hunting') {
                filtered = filtered.filter(item => !item.tags.includes('Alert Rule'));
            } else if (currentType === 'alert') {
                filtered = filtered.filter(item => item.tags.includes('Alert Rule'));
            }

            // 2. Filtrar por texto de búsqueda
            if (currentSearchTerm !== '') {
                filtered = filtered.filter(item => 
                    item.title.toLowerCase().includes(currentSearchTerm) || 
                    item.desc.toLowerCase().includes(currentSearchTerm) ||
                    item.tags.some(tag => tag.toLowerCase().includes(currentSearchTerm))
                );
            }

            renderArsenalList(filtered);
        }

        // Evento: Buscador de texto
        arsenalSearch.addEventListener('keyup', (e) => {
            currentSearchTerm = e.target.value.toLowerCase();
            applyFilters();
        });

        // Evento: Botones de filtro de categoría
        const filterButtons = document.querySelectorAll('.arsenal-filters .filter-btn');
        filterButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                // Quitar clase activa de todos
                filterButtons.forEach(b => b.classList.remove('active'));
                // Añadir activa al clicado
                e.target.classList.add('active');
                
                // Actualizar filtro y aplicar
                currentType = e.target.getAttribute('data-type');
                applyFilters();
            });
        });

        // Iniciar la herramienta por primera vez
        renderArsenalList(arsenalData);
    }
   /* ═══════════════════════════════════════════════════════
   IR TABLETOP SIMULATOR (RANSOMWARE)
═══════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    // 1. Escudo protector: Solo se ejecuta si estamos en la página del simulador
    const container = document.getElementById('ir-tabletop');
    if (!container) return; 

    // 2. Variables iniciales y configuración de idioma
    const currentLang = container.getAttribute('data-lang') || 'es';
    const textMin = currentLang === 'es' ? 'min' : 'min';

    let budget = 1000000;
    let timeLeft = 3600; // 60 minutos
    let timerInterval;
    let typeWriterInterval;
    let logGeneratorInterval;

    // 3. Elementos del DOM
    const elStartScreen = document.getElementById('start-screen');
    const elSimInterface = document.getElementById('sim-interface');
    const elAarScreen = document.getElementById('aar-screen');
    const elBudget = document.getElementById('hud-budget');
    const elTimer = document.getElementById('hud-timer');
    const elPhase = document.getElementById('hud-phase');
    const elStoryText = document.getElementById('story-text');
    const elChoices = document.getElementById('choices-container');
    const elLogStream = document.getElementById('log-stream');
    
    // Botones principales
    const btnStart = document.getElementById('btn-start-sim');
    const btnRestart = document.getElementById('btn-restart-sim');

    // 4. Base de Datos de Logs Falsos (Para el panel lateral)
    const logMessages = [
        { type: 'info', text: '[INFO] Port scan blocked from 192.168.1.45' },
        { type: 'warn', text: '[WARN] Multiple failed logins for AD_Admin' },
        { type: 'crit', text: '[CRIT] High volume of file modifications in E: Drive' },
        { type: 'info', text: '[INFO] Azure AD Sync successful' },
        { type: 'warn', text: '[WARN] Outbound traffic to Tor node detected' },
        { type: 'info', text: '[INFO] EDR definition update completed' },
        { type: 'crit', text: '[CRIT] Shadow copies deletion command executed (vssadmin)' },
        { type: 'warn', text: '[WARN] CPU usage spiked to 99% on DB-SRV-01' },
    ];

    // 5. Escenarios del Juego (Bilingüe)
    const scenarios = {
        start: {
            phase: { es: "IDENTIFICACIÓN", en: "IDENTIFICATION" },
            text: {
                es: "🔴 [17:03 PM] ALERTA CRÍTICA: El HelpDesk está saturado. Empleados de finanzas reportan que sus archivos tienen la extensión '.locked'. \n\nEn tu pantalla del EDR ves un pico masivo de encriptación en el servidor de archivos principal. Acaban de pedir 50 BTC de rescate.",
                en: "🔴 [17:03 PM] CRITICAL ALERT: HelpDesk is flooded. Finance employees report files with a '.locked' extension. \n\nOn your EDR screen, you see a massive encryption spike on the main file server. They just demanded a 50 BTC ransom."
            },
            choices: [
                { text: { es: "Aislar inmediatamente la subred afectada desde el EDR/Firewall.", en: "Immediately isolate the affected subnet from the EDR/Firewall." }, cost: 20000, time: 5, next: "triage_good" },
                { text: { es: "Reiniciar los servidores físicos afectados para intentar abortar el malware.", en: "Hard reset the physical servers to try and abort the malware." }, cost: 150000, time: 15, next: "triage_bad" },
                { text: { es: "Esperar a que el equipo de soporte técnico vaya a mirar los ordenadores.", en: "Wait for the IT support team to physically check the computers." }, cost: 300000, time: 25, next: "triage_fatal" }
            ]
        },
        triage_good: {
            phase: { es: "TRIAJE / ANÁLISIS", en: "TRIAGE / ANALYSIS" },
            text: {
                es: "🟢 Has contenido el sangrado inicial. Ahora necesitas saber CÓMO han entrado para cerrar el agujero.\n\nTienes varias fuentes de datos. ¿Qué investigas primero para encontrar al Paciente Cero?",
                en: "🟢 You stopped the initial bleeding. Now you need to know HOW they got in to close the gap.\n\nYou have several data sources. What do you investigate first to find Patient Zero?"
            },
            choices: [
                { text: { es: "Revisar los logs del proxy/email en busca de phishing reciente o descargas anómalas.", en: "Review proxy/email logs for recent phishing or anomalous downloads." }, cost: 5000, time: 10, next: "containment_good" },
                { text: { es: "Hacer un volcado de memoria (Memory Dump) completo de todos los servidores.", en: "Perform a full Memory Dump of all servers." }, cost: 30000, time: 35, next: "containment_slow" }
            ]
        },
        triage_bad: {
            phase: { es: "TRIAJE / ANÁLISIS", en: "TRIAGE / ANALYSIS" },
            text: {
                es: "❌ Al apagar los servidores a lo bruto, has destruido la memoria volátil (RAM). El equipo forense acaba de perder las claves de cifrado en memoria y los procesos inyectados.\n\nHas perdido mucho tiempo y dinero. Debes seguir adelante.",
                en: "❌ By hard resetting the servers, you destroyed volatile memory (RAM). The forensics team just lost the encryption keys in memory and the injected processes.\n\nYou lost a lot of time and money. You must move on."
            },
            choices: [
                { text: { es: "Revisar los logs del proxy/email para buscar el vector de entrada.", en: "Review proxy/email logs to find the entry vector." }, cost: 10000, time: 15, next: "containment_good" }
            ]
        },
        triage_fatal: {
            phase: { es: "TRIAJE / ANÁLISIS", en: "TRIAGE / ANALYSIS" },
            text: {
                es: "💀 Perder 25 minutos esperando a soporte ha permitido que el ransomware salte de la red de finanzas a la red de producción. Toda la fábrica está parada.\n\nEstás al borde del colapso.",
                en: "💀 Wasting 25 minutes waiting for support allowed the ransomware to jump from the finance network to the production network. The entire factory is halted.\n\nYou are on the brink of collapse."
            },
            choices: [
                { text: { es: "Desconectar literalmente el cable de internet principal de la compañía.", en: "Literally unplug the company's main internet cable." }, cost: 100000, time: 5, next: "containment_good" }
            ]
        },
        containment_good: {
            phase: { es: "CONTENCIÓN Y ERRADICACIÓN", en: "CONTAINMENT & ERADICATION" },
            text: {
                es: "🔍 Descubriste que un empleado abrió un ZIP falso (Phishing). El atacante estableció persistencia con una tarea programada y robó credenciales con Mimikatz.\n\nEs hora de erradicar la amenaza. ¿Qué haces?",
                en: "🔍 You discovered an employee opened a fake ZIP (Phishing). The attacker established persistence with a scheduled task and stole credentials using Mimikatz.\n\nIt's time to eradicate the threat. What do you do?"
            },
            choices: [
                { text: { es: "Forzar reseteo masivo de contraseñas, borrar la tarea programada y bloquear la IP del atacante (C2).", en: "Force a massive password reset, delete the scheduled task, and block the attacker's IP (C2)." }, cost: 15000, time: 15, next: "pr_event" },
                { text: { es: "Pasar el Antivirus por los servidores, borrar el malware y volver a encenderlos rápido.", en: "Run Antivirus on the servers, delete the malware, and turn them back on quickly." }, cost: 250000, time: 20, next: "eradication_fail" }
            ]
        },
        containment_slow: {
            phase: { es: "CONTENCIÓN Y ERRADICACIÓN", en: "CONTAINMENT & ERADICATION" },
            text: {
                es: "⚠️ El volcado de memoria fue útil, pero analizar 64GB de RAM de cada servidor te hizo perder 35 minutos de oro.\n\nEncuentras el malware, pero la empresa está perdiendo miles de euros por inactividad.",
                en: "⚠️ The memory dump was useful, but analyzing 64GB of RAM per server wasted 35 golden minutes.\n\nYou found the malware, but the company is losing thousands of euros in downtime."
            },
            choices: [
                { text: { es: "Resetear contraseñas, borrar persistencias y aislar dominios maliciosos.", en: "Reset passwords, delete persistence, and isolate malicious domains." }, cost: 20000, time: 10, next: "pr_event" }
            ]
        },
        eradication_fail: {
            phase: { es: "ERRADICACIÓN FALLIDA", en: "ERADICATION FAILED" },
            text: {
                es: "❌ NUNCA confíes en un sistema comprometido. El Antivirus borró el ejecutable, pero el atacante tenía un Rootkit oculto y credenciales válidas. A la media hora, vuelven a cifrar todo.\n\nEl CEO está gritando por teléfono.",
                en: "❌ NEVER trust a compromised system. The Antivirus deleted the executable, but the attacker had a hidden Rootkit and valid credentials. Half an hour later, they encrypt everything again.\n\nThe CEO is screaming on the phone."
            },
            choices: [
                { text: { es: "Formatear a bajo nivel (Re-image) todos los equipos afectados.", en: "Low-level format (Re-image) all affected machines." }, cost: 150000, time: 25, next: "pr_event" }
            ]
        },
        pr_event: {
            phase: { es: "COMUNICACIONES / LEGAL", en: "COMMUNICATIONS / LEGAL" },
            text: {
                es: "🚨 EVENTO INESPERADO: Un periódico local acaba de publicar que la empresa ha sido hackeada. Los clientes están llamando asustados preguntando si sus datos han sido robados.\n\nEl departamento de Legal y PR te pide instrucciones técnicas inmediatas.",
                en: "🚨 UNEXPECTED EVENT: A local newspaper just published that the company was hacked. Scared customers are calling to ask if their data was stolen.\n\nThe Legal and PR department asks you for immediate technical instructions."
            },
            choices: [
                { text: { es: "Emitir un comunicado honesto confirmando el ataque, pero asegurando que los sistemas están contenidos y bajo investigación forense.", en: "Issue an honest statement confirming the attack, but assuring that systems are contained and under forensic investigation." }, cost: 10000, time: 10, next: "victory" },
                { text: { es: "Negar el ataque publicamente y decir que es un 'mantenimiento técnico programado'.", en: "Publicly deny the attack and say it's a 'scheduled technical maintenance'." }, cost: 400000, time: 15, next: "pr_disaster" }
            ]
        },
        pr_disaster: {
            phase: { es: "RECUPERACIÓN", en: "RECOVERY" },
            text: {
                es: "💀 Mentir fue la peor idea. El grupo de Ransomware, al ver que lo negabas, acaba de publicar 10GB de correos del CEO en la Dark Web como prueba.\n\nLas multas de la GDPR/RGPD van a ser históricas, pero debes terminar de levantar los sistemas.",
                en: "💀 Lying was the worst idea. The Ransomware group, seeing you deny it, just published 10GB of the CEO's emails on the Dark Web as proof.\n\nGDPR fines will be historic, but you must finish bringing the systems up."
            },
            choices: [
                { text: { es: "Levantar servicios desde backups inmutables offline.", en: "Bring up services from offline immutable backups." }, cost: 50000, time: 10, next: "victory" }
            ]
        },
        victory: { phase: { es: "", en: "" }, text: { es: "", en: "" }, choices: [] },
        game_over: { phase: { es: "", en: "" }, text: { es: "", en: "" }, choices: [] }
    };

    // 6. Funciones de Ayuda
    function formatMoney(amount) {
        return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + (currentLang === 'es' ? " €" : " €");
    }
    
    function formatTime(seconds) {
        const m = Math.floor(seconds / 60).toString().padStart(2, '0');
        const s = (seconds % 60).toString().padStart(2, '0');
        return `${m}:${s}`;
    }

    function updateHUD() {
        elBudget.innerText = formatMoney(budget);
        elTimer.innerText = formatTime(timeLeft);
        
        elBudget.style.color = budget < 300000 ? "#ff2a2a" : "#00d45a";
        elTimer.style.color = timeLeft < 900 ? "#ff2a2a" : "#fff";
    }

    function generateLiveLog() {
        if(!elLogStream) return;
        const randomLog = logMessages[Math.floor(Math.random() * logMessages.length)];
        const time = new Date().toLocaleTimeString();
        
        const logDiv = document.createElement('div');
        logDiv.className = `log-entry log-${randomLog.type}`;
        logDiv.innerText = `${time} ${randomLog.text}`;
        
        elLogStream.appendChild(logDiv);
        
        if(elLogStream.children.length > 12) {
            elLogStream.removeChild(elLogStream.firstChild);
        }
    }

    function showAAR(isWin) {
        clearInterval(timerInterval);
        clearInterval(logGeneratorInterval);
        
        elSimInterface.style.display = 'none';
        elAarScreen.style.display = 'block';

        let rank = 'F';
        let color = '#ff2a2a';
        let desc = '';

        if (!isWin || budget <= 0 || timeLeft <= 0) {
            desc = currentLang === 'es' ? 'Incidente catastrófico. Negocio paralizado y SOC externalizado.' : 'Catastrophic incident. Business paralyzed and SOC outsourced.';
        } else {
            if (budget >= 800000 && timeLeft >= 1800) {
                rank = 'S'; color = '#b400ff'; desc = currentLang === 'es' ? 'Respuesta perfecta. Daños minimizados con precisión de cirujano.' : 'Perfect response. Damage minimized with surgical precision.';
            } else if (budget >= 500000) {
                rank = 'A'; color = '#00d45a'; desc = currentLang === 'es' ? 'Gran respuesta. La empresa sufrió pero está a salvo.' : 'Great response. The company suffered but is safe.';
            } else if (budget >= 200000) {
                rank = 'B'; color = '#00ffff'; desc = currentLang === 'es' ? 'Sistemas recuperados, pero con daños económicos altos.' : 'Systems recovered, but with high economic damages.';
            } else {
                rank = 'C'; color = '#f0c000'; desc = currentLang === 'es' ? 'Empresa al borde de la quiebra. Has salvado los muebles por poco.' : 'Company on the verge of bankruptcy. You barely saved the furniture.';
            }
        }

        const elRank = document.getElementById('aar-rank');
        elRank.innerText = rank;
        elRank.style.color = color;
        
        document.getElementById('aar-desc').innerText = desc;
        document.getElementById('aar-budget').innerText = formatMoney(budget < 0 ? 0 : budget);
        document.getElementById('aar-time').innerText = formatTime(timeLeft < 0 ? 0 : timeLeft);
    }

    function typeWriterEffect(text, element, callback) {
        elChoices.style.display = 'none'; 
        element.innerHTML = "";
        let i = 0;
        
        clearInterval(typeWriterInterval);
        typeWriterInterval = setInterval(() => {
            if (i < text.length) {
                element.innerHTML += text.charAt(i) === '\n' ? '<br>' : text.charAt(i);
                i++;
            } else {
                clearInterval(typeWriterInterval);
                elChoices.style.display = 'flex';
                if(callback) callback();
            }
        }, 12); 
    }

    function loadScenario(scenarioKey) {
        if (scenarioKey === 'victory') return showAAR(true);
        if (scenarioKey === 'game_over') return showAAR(false);

        const data = scenarios[scenarioKey];
        elPhase.innerText = data.phase[currentLang];
        
        typeWriterEffect(data.text[currentLang], elStoryText, () => {
            elChoices.innerHTML = "";
            data.choices.forEach(choice => {
                const btn = document.createElement('button');
                btn.className = 'ir-choice-btn';
                
                const textSpan = document.createElement('span');
                textSpan.innerText = "> " + choice.text[currentLang];
                
                const metaSpan = document.createElement('span');
                metaSpan.className = 'ir-choice-meta';
                if(choice.cost > 0 || choice.time > 0) {
                    metaSpan.innerHTML = `⏳ -${choice.time} ${textMin}<br>💸 -${formatMoney(choice.cost)}`;
                }

                btn.appendChild(textSpan);
                btn.appendChild(metaSpan);

                btn.onclick = () => handleChoice(choice.next, choice.cost, choice.time);
                elChoices.appendChild(btn);
            });
        });
    }

    function handleChoice(nextStep, cost, timePenaltyMinutes) {
        budget -= cost;
        timeLeft -= (timePenaltyMinutes * 60);
        updateHUD();

        if (budget <= 0 || timeLeft <= 0) {
            showAAR(false);
        } else {
            loadScenario(nextStep);
        }
    }

    // 7. Event Listeners (Botones)
    if(btnStart) {
        btnStart.addEventListener('click', () => {
            document.getElementById('start-screen').style.display = 'none';
            document.getElementById('sim-interface').style.display = 'block';
            
            // Iniciar Reloj Principal
            timerInterval = setInterval(() => {
                if(timeLeft > 0) {
                    timeLeft--;
                    updateHUD();
                    if(timeLeft === 0) showAAR(false);
                }
            }, 1000); 

            // Iniciar Logs Falsos
            logGeneratorInterval = setInterval(generateLiveLog, Math.random() * 2000 + 1500);

            updateHUD();
            loadScenario('start');
        });
    }

    // El botón que fallaba ahora recargará limpiamente la página usando JS
    if(btnRestart) {
        btnRestart.addEventListener('click', () => {
            location.reload();
        });
    }
});
/* ═══════════════════════════════════════════════════════
   MITRE ATT&CK VISUAL MAPPER (LIVE SYNC VERSION)
═══════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    const mitreContainer = document.getElementById('mitre-mapper');
    if (!mitreContainer) return;

    const currentLang = mitreContainer.getAttribute('data-lang') || 'es';
    const baseUrl = mitreContainer.getAttribute('data-url') || '';

    const uiTexts = {
        descLabel: currentLang === 'es' ? 'Descripción Táctica (Original)' : 'Tactical Description',
        platLabel: currentLang === 'es' ? 'Sistemas Afectados' : 'Affected Platforms',
        kqlLabel: currentLang === 'es' ? 'Arsenal SOC (KQL)' : 'SOC Arsenal (KQL)',
        kqlBtn: currentLang === 'es' ? 'BUSCAR REGLA DE DETECCIÓN' : 'SEARCH DETECTION RULE',
        loading: currentLang === 'es' ? 'Descargando inteligencia de MITRE...' : 'Downloading MITRE intelligence...',
        syncText: currentLang === 'es' ? 'Última Sync: ' : 'Last Sync: '
    };

    const board = document.getElementById('matrix-board');
    const searchInput = document.getElementById('mitre-search');
    const detailPanel = document.getElementById('detail-panel');
    const syncStatus = document.getElementById('sync-status');

    let globalTechniques = [];

    board.innerHTML = `<div style="padding: 2rem; color: var(--cyan); font-family: var(--mono);"><span class="cyber-spinner"></span> ${uiTexts.loading}</div>`;

    // Truco antibalas: Añadimos ?v=timestamp para que el navegador NUNCA use caché viejo
    fetch(`${baseUrl}/assets/data/mitre-cache.json?v=${new Date().getTime()}`)
        .then(response => {
            if(!response.ok) throw new Error("Caché no encontrada.");
            return response.json();
        })
        .then(data => {
            globalTechniques = data.techniques || [];
            
            const syncDate = new Date(data.last_sync).toLocaleString();
            if(syncStatus) syncStatus.innerText = `${uiTexts.syncText} ${syncDate}`;

            board.innerHTML = ''; 

            if (globalTechniques.length === 0) {
                board.innerHTML = `<div style="color: #ff5050; padding: 2rem; font-family: var(--mono);">⚠️ ERROR: La base de datos está vacía. Ejecuta el script update-mitre.php en tu servidor.</div>`;
                return;
            }

            (data.tactics || []).forEach(tactic => {
                const col = document.createElement('div');
                col.className = 'tactic-col';
                
                const header = document.createElement('div');
                header.className = 'tactic-header';
                header.innerText = tactic.name;
                col.appendChild(header);

                const techContainer = document.createElement('div');
                techContainer.className = 'tactic-techniques';

                const tTechs = globalTechniques.filter(t => t.tactic_shortname === tactic.shortname);
                
                // Muestra hasta 25 técnicas por columna (MITRE tiene cientos, si ponemos todas el PC explota)
                tTechs.slice(0, 25).forEach(tech => {
                    const card = document.createElement('div');
                    card.className = 'tech-card';
                    card.setAttribute('data-id', tech.id);
                    card.innerHTML = `<div class="tech-id">${tech.id}</div><div class="tech-name">${tech.name}</div>`;
                    card.addEventListener('click', () => showDetails(tech));
                    techContainer.appendChild(card);
                });

                if(tTechs.length > 25) {
                    const more = document.createElement('div');
                    more.style = "text-align:center; font-family:var(--mono); color:var(--gray-dark); font-size: 0.7rem; padding: 0.5rem;";
                    more.innerText = `+${tTechs.length - 25} ocultas... Usa el buscador`;
                    techContainer.appendChild(more);
                }

                col.appendChild(techContainer);
                board.appendChild(col);
            });
        })
        .catch(error => {
            board.innerHTML = `<div style="color: #ff5050; padding: 2rem; font-family: var(--mono);">⚠️ ERROR: ${error.message}</div>`;
            if(syncStatus) {
                syncStatus.innerText = "Sincronización Fallida";
                syncStatus.parentElement.style.borderColor = "#ff5050";
                syncStatus.parentElement.style.color = "#ff5050";
            }
        });

    if(searchInput) {
        searchInput.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase().trim();
            const allCards = document.querySelectorAll('.tech-card');

            if (term === '') {
                allCards.forEach(c => { c.classList.remove('dimmed'); c.classList.remove('highlight'); });
                return;
            }

            allCards.forEach(card => {
                const techId = card.getAttribute('data-id');
                const tech = globalTechniques.find(t => t.id === techId);
                if(!tech) return;
                
                const matchName = (tech.name || '').toLowerCase().includes(term);
                const matchId = (tech.id || '').toLowerCase().includes(term);
                
                // Protegemos el buscador por si no tiene plataformas asignadas
                const platforms = Array.isArray(tech.platforms) ? tech.platforms : [];
                const matchPlat = platforms.some(p => (p || '').toLowerCase().includes(term));
                
                if (matchName || matchId || matchPlat) {
                    card.classList.remove('dimmed');
                    card.classList.add('highlight');
                } else {
                    card.classList.add('dimmed');
                    card.classList.remove('highlight');
                }
            });
        });
    }

    function showDetails(tech) {
        const platforms = Array.isArray(tech.platforms) ? tech.platforms : ['General'];
        const platHtml = platforms.map(p => `<span class="apt-badge" style="color:#fff; border-color:#555; background:rgba(255,255,255,0.1);">${p}</span>`).join('');
        
        detailPanel.innerHTML = `
            <div class="dt-id">${tech.id}</div>
            <div class="dt-name">${tech.name}</div>
            
            <div class="dt-section">
                <div class="dt-label">${uiTexts.descLabel}</div>
                <div class="dt-text" style="color:#a0d0c0;">${tech.desc || 'Sin descripción'}</div>
            </div>

            <div class="dt-section">
                <div class="dt-label">${uiTexts.platLabel}</div>
                <div>${platHtml}</div>
            </div>

            <div class="dt-section">
                <div class="dt-label">${uiTexts.kqlLabel}</div>
                <a href="${baseUrl}/soc-arsenal.php" class="kql-btn">
                    <span style="font-size: 1.2rem;">🛡️</span> ${uiTexts.kqlBtn}
                </a>
            </div>
        `;
        
        document.querySelectorAll('.tech-card').forEach(c => c.style.borderLeftColor = 'transparent');
        document.querySelector(`.tech-card[data-id="${tech.id}"]`).style.borderLeftColor = 'var(--cyan)';
    }
    // =========================================================
    // MEJORA UX: SCROLL HORIZONTAL CON CLICK & DRAG
    // =========================================================
    let isDown = false;
    let startX;
    let scrollLeft;
    let isDragging = false;

    // Cambiar el cursor por defecto al pasar por encima del panel
    board.style.cursor = 'grab';

    board.addEventListener('mousedown', (e) => {
        isDown = true;
        isDragging = false; // Reiniciamos el estado de arrastre
        board.style.cursor = 'grabbing'; // Cursor de "agarrar"
        startX = e.pageX - board.offsetLeft;
        scrollLeft = board.scrollLeft;
    });

    board.addEventListener('mouseleave', () => {
        isDown = false;
        board.style.cursor = 'grab';
    });

    board.addEventListener('mouseup', () => {
        isDown = false;
        board.style.cursor = 'grab';
    });

    board.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault(); // Evita que se seleccione el texto mientras arrastras
        const x = e.pageX - board.offsetLeft;
        const walk = (x - startX) * 1.5; // Multiplicador de velocidad de scroll
        
        // Si el usuario mueve el ratón más de 5 píxeles, lo consideramos un "arrastre" y no un clic normal
        if (Math.abs(walk) > 5) {
            isDragging = true; 
        }
        
        board.scrollLeft = scrollLeft - walk;
    });

    // Escudo protector: Evita que se abra una tarjeta de técnica si el usuario la usó para arrastrar
    board.addEventListener('click', (e) => {
        if (isDragging) {
            e.preventDefault();
            e.stopPropagation(); // Detiene el clic para que no llegue a la tarjeta
        }
    }, true); // El "true" captura el evento antes de que baje a los elementos hijos
});
/* ==========================================================================
   MÓDULO: ENTERPRISE REPORT GENERATOR V4.0 (Compliance, KPIs + AutoSave)
   ========================================================================== */
document.addEventListener('DOMContentLoaded', () => {
    const reportContainer = document.getElementById('modern-report-tool');
    if (!reportContainer) return;

    const lang = reportContainer.getAttribute('data-lang') || 'es';
    const baseUrl = reportContainer.getAttribute('data-url') || '';
    const STORAGE_KEY = 'cyberescudo_report_draft_v4';

    const i = {
        title: document.getElementById('in-title'),
        risk: document.getElementById('in-risk'),
        asset: document.getElementById('in-asset'),
        exec: document.getElementById('in-exec'),
        impact: document.getElementById('in-impact'),
        remed: document.getElementById('in-remed'),
        tech: document.getElementById('in-tech'),
        tlp: document.getElementById('in-tlp'),
        status: document.getElementById('in-status'),
        author: document.getElementById('in-author'),
        mttd: document.getElementById('in-mttd'),
        mttr: document.getElementById('in-mttr')
    };
    
    const o = {
        title: document.getElementById('out-title'),
        risk: document.getElementById('out-risk'),
        asset: document.getElementById('out-asset'),
        exec: document.getElementById('out-exec'),
        impact: document.getElementById('out-impact'),
        remed: document.getElementById('out-remed'),
        tech: document.getElementById('out-tech'),
        tlp: document.getElementById('out-tlp-badge'),
        status: document.getElementById('out-status'),
        author: document.getElementById('out-author'),
        mttd: document.getElementById('out-mttd'),
        mttr: document.getElementById('out-mttr'),
        compliance: document.getElementById('out-compliance')
    };

    const checkGdpr = document.getElementById('chk-gdpr');
    const checkPci = document.getElementById('chk-pci');
    const checkNis2 = document.getElementById('chk-nis2');

    // 1. SISTEMA DE AUTOGUARDADO (LocalStorage)
    const saveDraft = () => {
        const draftData = {
            title: i.title.value, risk: i.risk.value, asset: i.asset.value,
            exec: i.exec.value, impact: i.impact.value, remed: i.remed.value,
            tech: i.tech.value, tlp: i.tlp.value, status: i.status.value,
            author: i.author.value, mttd: i.mttd.value, mttr: i.mttr.value,
            gdpr: checkGdpr.checked, pci: checkPci.checked, nis2: checkNis2.checked
        };
        localStorage.setItem(STORAGE_KEY, JSON.stringify(draftData));
    };

    const loadDraft = () => {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved) {
            try {
                const draft = JSON.parse(saved);
                i.title.value = draft.title || ''; i.risk.value = draft.risk || 'MEDIUM';
                i.asset.value = draft.asset || ''; i.exec.value = draft.exec || '';
                i.impact.value = draft.impact || ''; i.remed.value = draft.remed || '';
                i.tech.value = draft.tech || ''; i.tlp.value = draft.tlp || 'CLEAR';
                i.status.value = draft.status || 'OPEN'; i.author.value = draft.author || '';
                i.mttd.value = draft.mttd || ''; i.mttr.value = draft.mttr || '';
                checkGdpr.checked = !!draft.gdpr; checkPci.checked = !!draft.pci; checkNis2.checked = !!draft.nis2;
                return true;
            } catch (e) { console.error("Error cargando borrador:", e); }
        }
        return false;
    };

    // 2. ACTUALIZACIÓN EN VIVO
    const updatePreview = () => {
        o.title.innerText = i.title.value || (lang === 'es' ? '[ INSERTE TÍTULO ]' : '[ INSERT TITLE ]');
        o.asset.innerText = i.asset.value || 'N/A';
        o.exec.innerText = i.exec.value || '...';
        o.impact.innerText = i.impact.value || (lang === 'es' ? 'No se ha registrado impacto crítico directo.' : 'No direct critical impact registered.');
        o.remed.innerText = i.remed.value || '...';
        o.tech.innerText = i.tech.value || '...';
        o.author.innerText = i.author.value || 'System Generated';
        o.mttd.innerText = i.mttd.value || 'N/A';
        o.mttr.innerText = i.mttr.value || 'N/A';
        
        o.risk.className = 'badge-pill pill-' + i.risk.value;
        o.risk.innerText = i.risk.value;

        o.tlp.className = 'tlp-badge tlp-' + i.tlp.value;
        o.tlp.innerText = 'TLP:' + i.tlp.value;
        // Lógica del Aviso Legal Dinámico según TLP
        const legalTexts = {
            'RED': lang === 'es' 
                ? 'ESTRICTAMENTE CONFIDENCIAL: Este documento contiene información clasificada TLP:RED. Prohibida su distribución fuera de las partes explícitamente autorizadas en esta investigación. La filtración de este documento puede acarrear acciones legales severas.' 
                : 'STRICTLY CONFIDENTIAL: This document contains TLP:RED classified intelligence. Not for disclosure or distribution outside explicitly authorized parties. Unauthorized leakage may result in severe legal action.',
            'AMBER': lang === 'es'
                ? 'USO RESTRINGIDO: Información clasificada TLP:AMBER. Puede compartirse únicamente dentro de la propia organización y con los clientes afectados directamente para tomar medidas defensivas. No distribuir públicamente.'
                : 'RESTRICTED USE: TLP:AMBER intelligence. May be shared only within the organization and with directly affected clients to take defensive actions. Do not distribute publicly.',
            'GREEN': lang === 'es'
                ? 'USO INTERNO: Información TLP:GREEN. Puede compartirse libremente con todos los socios y comunidad de la organización, pero no a través de canales de acceso público generales.'
                : 'INTERNAL USE: TLP:GREEN intelligence. May be freely shared within the organization’s partners and community, but not via general public-accessible channels.',
            'CLEAR': lang === 'es'
                ? 'PÚBLICO: TLP:CLEAR. Esta información no está clasificada. Se permite su distribución sin restricciones sujeta a derechos de autor estándar.'
                : 'PUBLIC: TLP:CLEAR. Unclassified information. Unrestricted distribution is permitted subject to standard copyright.'
        };
        
        const legalFooter = document.getElementById('out-legal');
        if(legalFooter) legalFooter.innerText = legalTexts[i.tlp.value];

        o.status.className = 'meta-val status-' + i.status.value;
        o.status.innerText = i.status.options[i.status.selectedIndex].text;

        // Render de Compliance Tags
        let compHTML = [];
        if (checkGdpr.checked) compHTML.push('<span class="comp-chip comp-active">GDPR</span>');
        if (checkPci.checked) compHTML.push('<span class="comp-chip comp-active">PCI-DSS</span>');
        if (checkNis2.checked) compHTML.push('<span class="comp-chip comp-active">NIS 2</span>');
        
        o.compliance.innerHTML = compHTML.length > 0 ? compHTML.join('') : (lang === 'es' ? 'Ninguna' : 'None');

        saveDraft();
    };

    [i.title, i.risk, i.asset, i.exec, i.impact, i.remed, i.tech, i.tlp, i.status, i.author, i.mttd, i.mttr].forEach(el => {
        el.addEventListener('input', updatePreview);
        el.addEventListener('change', updatePreview);
    });
    [checkGdpr, checkPci, checkNis2].forEach(el => el.addEventListener('change', updatePreview));

    // Logo Reader
    const logoUpload = document.getElementById('inp-logo-upload');
    const previewLogo = document.getElementById('preview-user-logo');
    const logoContainer = document.getElementById('user-logo-container');

    logoUpload?.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = (event) => {
                previewLogo.src = event.target.result;
                logoContainer.style.display = 'block';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // 3. SMART LOOKUP (MITRE / CVE)
    const smartSearch = document.getElementById('inp-smart-search');
    const btnLookup = document.getElementById('btn-smart-lookup');

    btnLookup?.addEventListener('click', async () => {
        const query = smartSearch.value.trim().toUpperCase();
        if (!query) return;
        btnLookup.innerText = "...";

        try {
            if (query.startsWith('CVE-')) {
                const response = await fetch(`${baseUrl}/scripts/get-cve.php?id=${query}`);
                if (!response.ok) throw new Error("CVE_NOT_FOUND");
                const data = await response.json();

                if (data.cveMetadata && data.cveMetadata.cveId) {
                    const cna = data.containers?.cna;
                    const descObj = cna?.descriptions?.find(d => d.lang === 'en') || cna?.descriptions?.[0];
                    const desc = descObj ? descObj.value : 'No official description.';

                    let risk = "HIGH"; let score = "N/A";
                    if (cna?.metrics) {
                        const metric = cna.metrics.find(m => m.cvssV3_1 || m.cvssV3_0 || m.cvssV2_0);
                        if (metric) {
                            const cvss = metric.cvssV3_1 || metric.cvssV3_0 || metric.cvssV2_0;
                            if (cvss && cvss.baseScore) {
                                score = cvss.baseScore;
                                if (score >= 9.0) risk = "CRITICAL";
                                else if (score >= 7.0) risk = "HIGH";
                                else if (score >= 4.0) risk = "MEDIUM";
                                else risk = "LOW";
                            }
                        }
                    }

                    i.title.value = lang === 'es' ? `Vulnerabilidad: ${query}` : `Vulnerability: ${query}`;
                    i.risk.value = risk; i.tlp.value = "AMBER"; i.status.value = "OPEN";
                    i.asset.value = "Pending Identification"; i.mttd.value = "0 mins"; i.mttr.value = "Pending";
                    checkGdpr.checked = true; checkNis2.checked = true; checkPci.checked = false;

                    i.exec.value = `[CVE Alert - ${query}]\n\nOfficial Description:\n${desc}`;
                    i.impact.value = lang === 'es' ? "Riesgo extremo de ejecución remota de comandos. Alto peligro regulatorio." : "Extreme remote code execution risk. High regulatory exposure.";
                    i.remed.value = "Apply official vendor security patch immediately.";
                    i.tech.value = `ID: ${query}\nCVSS Score: ${score}\nDatabase: MITRE CVE API`;

                    updatePreview();
                    smartSearch.style.borderColor = "#00d45a";
                } else { throw new Error("CVE_NOT_FOUND"); }
            } 
            else {
                const response = await fetch(`${baseUrl}/assets/data/mitre-cache.json?v=${new Date().getTime()}`);
                if(!response.ok) throw new Error("LOCAL_DB_ERROR");
                
                const data = await response.json();
                const technique = data.techniques.find(t => t.id.toUpperCase() === query || t.name.toUpperCase().includes(query));

                if (technique) {
                    i.title.value = technique.name; i.risk.value = "HIGH"; i.tlp.value = "GREEN"; i.status.value = "CONTAINED";
                    const platforms = Array.isArray(technique.platforms) ? technique.platforms.join(', ') : 'General';
                    i.asset.value = platforms; i.mttd.value = "12 mins"; i.mttr.value = "1h 05m";
                    checkGdpr.checked = false; checkNis2.checked = true; checkPci.checked = false;
                    
                    i.exec.value = `[MITRE Technique ${technique.id}]\n\n${technique.desc}`;
                    i.impact.value = "Technique allows threat actors to move laterally or compromise defense stability.";
                    i.remed.value = "Enforce endpoint containment policies.";
                    i.tech.value = `MITRE ID: ${technique.id}\nTactic: ${technique.tactic_shortname}`;
                    
                    updatePreview();
                    smartSearch.style.borderColor = "#00d45a";
                } else { throw new Error("MITRE_NOT_FOUND"); }
            }
        } catch (error) {
            console.error(error); smartSearch.style.borderColor = "#ff2a2a";
            alert("Search failed or threat not found.");
        } finally { btnLookup.innerText = lang === 'es' ? "BUSCAR" : "SEARCH"; }
    });

    // 4. PLANTILLAS CORPORATIVAS AVANZADAS
    const templates = {
        phish: {
            title: "Phishing Campaign: Executive Credential Theft",
            risk: "HIGH", tlp: "AMBER", status: "CONTAINED", asset: "Office 365 / Azure AD Tenant", mttd: "8 mins", mttr: "35 mins",
            gdpr: true, pci: false, nis2: true,
            exec: "A spear-phishing attack successfully harvested corporate credentials from members of the finance department using a look-alike domain.",
            impact: "Compromise of 3 email accounts. Fraudulent wire attempt detected and blocked. Potential PII exposure under GDPR regulation laws.",
            remed: "1. Trigger immediate user session revocation.\n2. Enable mandatory FIDO2 hardware MFA tokens.\n3. Implement brand protection anti-spoofing controls.",
            tech: "Phishing Link: https://login-microsoft-auth-verify.com/login.php\nHarvested Accounts: finance-manager@company.com"
        },
        ddos: {
            title: "Volumetric DDoS Attack: Public Services Outage",
            risk: "CRITICAL", tlp: "GREEN", status: "MITIGATED", asset: "Public Web App Gateway Cluster", mttd: "2 mins", mttr: "18 mins",
            gdpr: false, pci: true, nis2: true,
            exec: "A massive Layer 7 HTTP flood attack saturated server connection pools, making the checkout and transaction portals entirely unavailable to the public.",
            impact: "Operational Downtime: 18 minutes.\nEstimated revenue loss: €24,500 based on average transaction volumes per minute during peak hours.",
            remed: "1. Upgrade global CDN rate-limiting profiles.\n2. Deploy Cloudflare Advanced Under-Attack scrubbing challenges.\n3. Restructure API gateway connection drop timeouts.",
            tech: "Attack Type: HTTP GET Flood / Cloudflare Bypass Attempt\nMax Velocity: 140,000 requests per second"
        },
        insider: {
            title: "Insider Threat: Intellectual Property Exfiltration",
            risk: "CRITICAL", tlp: "RED", status: "OPEN", asset: "Engineering Core File Server (WIN-FS01)", mttd: "2 hours", mttr: "Active",
            gdpr: true, pci: false, nis2: false,
            exec: "An employee with valid corporate admin credentials executed an unauthorized backup and bulk download of core industrial blueprints outside shift hours.",
            impact: "Exfiltration of 18GB of proprietary product blueprints and active client NDAs. Legal, compliance, and corporate risk vectors triggered.",
            remed: "1. Revoke active directory domain credentials.\n2. Terminate active enterprise VPN connections.\n3. Enforce endpoint DLP block rules for physical storage devices.",
            tech: "SIEM Alert: Access anomalies detected on network share 'D:\\Industrial_Core_Vault\\'\nActive Directory User Account: s.miller"
        },
        breach: {
            title: "Data Breach: Misconfigured NoSQL Cluster",
            risk: "CRITICAL", tlp: "RED", status: "CONTAINED", asset: "Production ElasticSearch Backend Cluster", mttd: "0 mins", mttr: "4 mins",
            gdpr: true, pci: true, nis2: true,
            exec: "A DevOps deployment error left an active production indexing server exposed to the public internet without firewall rules or active authentication settings.",
            impact: "Exposure of 1.4 million corporate accounts. Regulatory exposure: Major severity under GDPR and PCI compliance laws (fines up to 4% of revenue).",
            remed: "1. Enforce strict security group firewall drops on port 9200.\n2. Enable unified x-pack cluster authentication.\n3. Initiate public data breach incident disclosure protocol.",
            tech: "Exposed IP: 54.210.x.x:9200\nExposed Document Index: /customer_vault_live/"
        }
    };

    const loadTemplate = (k) => {
        const t = templates[k];
        i.title.value = t.title; i.risk.value = t.risk; i.tlp.value = t.tlp; i.status.value = t.status;
        i.asset.value = t.asset; i.exec.value = t.exec; i.impact.value = t.impact; i.remed.value = t.remed; i.tech.value = t.tech;
        i.mttd.value = t.mttd; i.mttr.value = t.mttr;
        checkGdpr.checked = t.gdpr; checkPci.checked = t.pci; checkNis2.checked = t.nis2;
        updatePreview();
    };

    document.getElementById('tpl-phish')?.addEventListener('click', () => loadTemplate('phish'));
    document.getElementById('tpl-ddos')?.addEventListener('click', () => loadTemplate('ddos'));
    document.getElementById('tpl-insider')?.addEventListener('click', () => loadTemplate('insider'));
    document.getElementById('tpl-breach')?.addEventListener('click', () => loadTemplate('breach'));
    document.getElementById('tpl-clear')?.addEventListener('click', () => {
        Object.values(i).forEach(el => { if(el.tagName==='INPUT' || el.tagName==='TEXTAREA') el.value = ''; });
        i.tlp.value = "CLEAR"; i.status.value = "OPEN"; i.risk.value = "MEDIUM";
        checkGdpr.checked = false; checkPci.checked = false; checkNis2.checked = false;
        previewLogo.src = ''; logoContainer.style.display = 'none'; logoUpload.value = '';
        localStorage.removeItem(STORAGE_KEY);
        updatePreview();
    });
// 5. EXPORTAR A PDF (Cumpliendo la estricta política CSP)
    const btnExportPdf = document.getElementById('btn-export-pdf');
    btnExportPdf?.addEventListener('click', () => {
        window.print();
    });
    loadDraft();
    updatePreview();
});
})();