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
})();