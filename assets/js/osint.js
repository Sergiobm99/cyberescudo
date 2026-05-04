/* ─── SCRIPT EXCLUSIVO OSINT (BILINGÜE) ─── */
document.addEventListener('DOMContentLoaded', () => {
    
    // Detectamos el idioma leyendo la clase del body (es / en)
    const isEn = document.body.classList.contains('en');
    
    // Diccionario de traducciones
    const t = {
        target: isEn ? 'Target: ' : 'Objetivo: ',
        analyzing: isEn ? '<span class="cyber-spinner"></span>Analyzing...' : '<span class="cyber-spinner"></span>Analizando...',
        country: isEn ? 'Country' : 'País',
        noA: isEn ? 'No public A records found.' : 'No se encontraron registros A públicos.',
        blocked: isEn ? 'Blocked by Firewall (CSP)' : 'Bloqueado por Firewall (CSP)',
        reg: isEn ? 'Registrar' : 'Registrador',
        status: isEn ? 'Status' : 'Estado',
        registered: isEn ? 'Registered' : 'Registrado',
        priv: isEn ? 'Private or unavailable.' : 'Privado o no disponible.',
        nomx: isEn ? 'No mail services found.' : 'No hay servicios de correo.',
        spfFound: isEn ? 'Found' : 'Encontrado',
        spfRisk: isEn ? 'Phishing Risk' : 'Riesgo de Phishing',
        nons: isEn ? 'No NS records.' : 'Sin registros NS.',
        archived: isEn ? 'Archived' : 'Archivado',
        capture: isEn ? 'Capture' : 'Captura',
        noArchive: isEn ? 'No records in Wayback Machine.' : 'Sin registros en Wayback Machine.',
        yes: isEn ? 'Yes' : 'Sí',
        dmarcOk: isEn ? 'Configured' : 'Configurado',
        dmarcFail: isEn ? 'No DMARC policy detected' : 'No se detecta política DMARC',
        admin: isEn ? 'Admin' : 'Admin',
        primary: isEn ? 'Primary' : 'Primario',
        nosoa: isEn ? 'No SOA record.' : 'Sin registro SOA.',
        caaOk: isEn ? 'Restricted' : 'Restringido',
        caaOkSub: isEn ? 'Authorized issuers found.' : 'Emisores autorizados encontrados.',
        caaFail: isEn ? 'Open (Anyone can issue SSL)' : 'Libre (Cualquiera puede emitir SSL)',
        v6Ok: isEn ? 'Supported' : 'Soportado',
        v6Fail: isEn ? 'Only IPv4 supported' : 'Solo IPv4 soportado',
        portsFound: isEn ? 'Detected {n} ports:' : 'Detectados {n} puertos:',
        noPorts: isEn ? 'No open ports recorded.' : 'No constan puertos abiertos.',
        reqIp: isEn ? 'Requires IP for analysis.' : 'Requiere IP para análisis.',
        vulnDanger: isEn ? 'Danger!' : '¡Peligro!',
        vulnFound: isEn ? '{n} public CVEs:' : '{n} CVEs públicos:',
        noVulns: isEn ? 'No public vulnerabilities in DB.' : 'Ninguna vulnerabilidad pública en BD.',
        shodanErr: isEn ? 'Error querying Shodan.' : 'Error al consultar Shodan.',
        toastSuccess: isEn ? 'OSINT Analysis Completed' : 'Análisis OSINT Finalizado',
        toastError: isEn ? 'Scan Error' : 'Error en el escaneo',
        toastPdf: isEn ? 'Generating PDF report...' : 'Generando reporte PDF...',
        toastPdfOk: isEn ? 'PDF downloaded successfully' : 'PDF descargado con éxito'
    };

    const btnRunOsint = document.getElementById('btn-run-osint');
    const targetInput = document.getElementById('osint-target');
    const resultsDiv = document.getElementById('osint-results');
    const exportContainer = document.getElementById('export-container');
    const reportDomain = document.getElementById('report-domain');
    
    const geoList = document.getElementById('osint-geo');
    const whoisList = document.getElementById('osint-whois');
    const mxList = document.getElementById('osint-mx');
    const txtList = document.getElementById('osint-txt');
    const nsList = document.getElementById('osint-ns');
    const archList = document.getElementById('osint-archive');
    const portsList = document.getElementById('osint-ports');
    const vulnsList = document.getElementById('osint-vulns');
    const dmarcList = document.getElementById('osint-dmarc');
    const soaList = document.getElementById('osint-soa');
    const caaList = document.getElementById('osint-caa');
    const ipv6List = document.getElementById('osint-ipv6');

    const btnExport = document.getElementById('btn-export-pdf');
    const modal = document.getElementById('donation-modal');
    const btnFree = document.getElementById('btn-download-free');

    if(btnRunOsint && targetInput) {
        btnRunOsint.addEventListener('click', async () => {
            
            let domain = targetInput.value.trim();
            domain = domain.replace(/^https?:\/\//,'').replace(/\/$/,'');

            if(!domain) {
                if(typeof window.showToast === 'function') window.showToast(t.toastError, 'error');
                return;
            }

            if(resultsDiv) resultsDiv.style.display = 'block';
            if(exportContainer) exportContainer.style.display = 'block';
            if(reportDomain) reportDomain.textContent = t.target + domain;
            
            const loadingBoxes = [geoList, whoisList, mxList, txtList, nsList, archList, portsList, vulnsList, dmarcList, soaList, caaList, ipv6List];
            loadingBoxes.forEach(box => { if(box) box.innerHTML = `<li>${t.analyzing}</li>`; });

            try {
                // 1. IP y GEO
                const dnsRes = await fetch(`https://cloudflare-dns.com/dns-query?name=${domain}&type=A`, { headers: { 'Accept': 'application/dns-json' } });
                const dnsData = await dnsRes.json();
                let ipAddress = '';
                
                if(dnsData.Answer && dnsData.Answer.length > 0) {
                    ipAddress = dnsData.Answer[0].data;
                    const geoRes = await fetch(`https://ipapi.co/${ipAddress}/json/`);
                    const geoData = await geoRes.json();
                    if(!geoData.error && geoList) {
                        geoList.innerHTML = `<li><strong>IP:</strong> ${ipAddress}</li><li><strong>${t.country}:</strong> ${geoData.country_name}</li><li><strong>ISP:</strong> ${geoData.org}</li>`;
                    } else if (geoList) { geoList.innerHTML = `<li><strong>IP:</strong> ${ipAddress}</li>`; }

                    // EXTRA: SHODAN InternetDB
                    try {
                        const shodanRes = await fetch(`https://internetdb.shodan.io/${ipAddress}`);
                        if(shodanRes.ok) {
                            const shodanData = await shodanRes.json();
                            if(shodanData.ports && shodanData.ports.length > 0 && portsList) {
                                let portsHtml = `<li>${t.portsFound.replace('{n}', shodanData.ports.length)}</li>`;
                                portsHtml += `<li style="color:#00ffff; font-size:0.85rem;">[ ${shodanData.ports.join(', ')} ]</li>`;
                                portsList.innerHTML = portsHtml;
                            } else if (portsList) { portsList.innerHTML = `<li>${t.noPorts}</li>`; }
                            
                            if(shodanData.vulns && shodanData.vulns.length > 0 && vulnsList) {
                                let vulnsHtml = `<li><strong style="color:#ff4444;">${t.vulnDanger}</strong> ${t.vulnFound.replace('{n}', shodanData.vulns.length)}</li>`;
                                let showVulns = shodanData.vulns.slice(0, 3).join(', ');
                                vulnsHtml += `<li style="color:#ff4444; font-size:0.85rem;">${showVulns}${shodanData.vulns.length > 3 ? '...' : ''}</li>`;
                                vulnsList.innerHTML = vulnsHtml;
                            } else if (vulnsList) { vulnsList.innerHTML = `<li style="color:#00ff00;">${t.noVulns}</li>`; }
                        } else {
                            if(portsList) portsList.innerHTML = `<li>${t.shodanErr}</li>`;
                            if(vulnsList) vulnsList.innerHTML = `<li>${t.shodanErr}</li>`;
                        }
                    } catch(e) { 
                        if(portsList) portsList.innerHTML = `<li style="color:#ff4444;">${t.blocked}</li>`; 
                        if(vulnsList) vulnsList.innerHTML = `<li style="color:#ff4444;">${t.blocked}</li>`; 
                    }

                } else { 
                    if(geoList) geoList.innerHTML = `<li>${t.noA}</li>`; 
                    if(portsList) portsList.innerHTML = `<li>${t.reqIp}</li>`;
                    if(vulnsList) vulnsList.innerHTML = `<li>${t.reqIp}</li>`;
                }

                // Resto de consultas DNS
                const reqs = [
                    fetch(`https://networkcalc.com/api/dns/whois/${domain}`), 
                    fetch(`https://cloudflare-dns.com/dns-query?name=${domain}&type=MX`, { headers: { 'Accept': 'application/dns-json' } }), 
                    fetch(`https://cloudflare-dns.com/dns-query?name=${domain}&type=TXT`, { headers: { 'Accept': 'application/dns-json' } }), 
                    fetch(`https://cloudflare-dns.com/dns-query?name=${domain}&type=NS`, { headers: { 'Accept': 'application/dns-json' } }), 
                    fetch(`https://archive.org/wayback/available?url=${domain}`), 
                    fetch(`https://cloudflare-dns.com/dns-query?name=_dmarc.${domain}&type=TXT`, { headers: { 'Accept': 'application/dns-json' } }), 
                    fetch(`https://cloudflare-dns.com/dns-query?name=${domain}&type=SOA`, { headers: { 'Accept': 'application/dns-json' } }), 
                    fetch(`https://cloudflare-dns.com/dns-query?name=${domain}&type=CAA`, { headers: { 'Accept': 'application/dns-json' } }), 
                    fetch(`https://cloudflare-dns.com/dns-query?name=${domain}&type=AAAA`, { headers: { 'Accept': 'application/dns-json' } }) 
                ];

                const results = await Promise.allSettled(reqs);

                // 2. WHOIS
                try {
                    if(results[0].status === 'fulfilled') {
                        const wData = await results[0].value.json();
                        if(wData.status === 'OK' && wData.whois && wData.whois.registrar && whoisList) {
                            whoisList.innerHTML = `<li><strong>${t.reg}:</strong> ${wData.whois.registrar}</li><li><strong>${t.status}:</strong> ${t.registered}</li>`;
                        } else if (whoisList) { whoisList.innerHTML = `<li>${t.priv}</li>`; }
                    } else if (whoisList) { whoisList.innerHTML = `<li style="color:#ff4444;">${t.blocked}</li>`; }
                } catch(e) {}

                // 3. MX
                try {
                    if(results[1].status === 'fulfilled') {
                        const mxData = await results[1].value.json();
                        if(mxData.Answer && mxList) {
                            mxList.innerHTML = mxData.Answer.slice(0,2).map(r => `<li>🎯 ${r.data}</li>`).join('');
                        } else if (mxList) { mxList.innerHTML = `<li>${t.nomx}</li>`; }
                    }
                } catch(e) {}

                // 4. TXT (SPF)
                try {
                    if(results[2].status === 'fulfilled') {
                        const txtData = await results[2].value.json();
                        let html = '';
                        if(txtData.Answer) {
                            txtData.Answer.forEach(r => {
                                if(r.data.includes('v=spf1')) html += `<li style="color:#00ff00;">✅ <strong>SPF:</strong> ${t.spfFound}</li>`;
                            });
                        }
                        if(txtList) txtList.innerHTML = html || `<li style="color:#ff4444;">❌ <strong>SPF:</strong> ${t.spfRisk}</li>`;
                    }
                } catch(e) {}

                // 5. NS
                try {
                    if(results[3].status === 'fulfilled') {
                        const nsData = await results[3].value.json();
                        if(nsData.Answer && nsList) {
                            nsList.innerHTML = nsData.Answer.slice(0,2).map(r => `<li>🔗 ${r.data}</li>`).join('');
                        } else if (nsList) { nsList.innerHTML = `<li>${t.nons}</li>`; }
                    }
                } catch(e) {}

                // 6. ARCHIVE
                try {
                    if(results[4].status === 'fulfilled') {
                        const arData = await results[4].value.json();
                        if(arData.archived_snapshots?.closest && archList) {
                            let ts = arData.archived_snapshots.closest.timestamp;
                            archList.innerHTML = `<li><strong>${t.archived}:</strong> ${t.yes}</li><li><strong>${t.capture}:</strong> ${ts.substring(6,8)}/${ts.substring(4,6)}/${ts.substring(0,4)}</li>`;
                        } else if (archList) { archList.innerHTML = `<li>${t.noArchive}</li>`; }
                    } else if (archList) { archList.innerHTML = `<li style="color:#ff4444;">${t.blocked}</li>`; }
                } catch(e) {}

                // 7. DMARC
                try {
                    if(results[5].status === 'fulfilled') {
                        const dmData = await results[5].value.json();
                        if(dmData.Answer && dmarcList) {
                            dmarcList.innerHTML = `<li style="color:#00ff00;">✅ ${t.dmarcOk}:</li><li style="font-size:0.8rem; color:#aaaaaa;">${dmData.Answer[0].data}</li>`;
                        } else if (dmarcList) { dmarcList.innerHTML = `<li style="color:#ff4444;">❌ ${t.dmarcFail}</li>`; }
                    }
                } catch(e) {}

                // 8. SOA
                try {
                    if(results[6].status === 'fulfilled') {
                        const soaData = await results[6].value.json();
                        if(soaData.Answer && soaList) {
                            let parts = soaData.Answer[0].data.split(' ');
                            soaList.innerHTML = `<li><strong>${t.admin}:</strong> ${parts[1].replace('.', '@')}</li><li><strong>${t.primary}:</strong> ${parts[0]}</li>`;
                        } else if (soaList) { soaList.innerHTML = `<li>${t.nosoa}</li>`; }
                    }
                } catch(e) {}

                // 9. CAA
                try {
                    if(results[7].status === 'fulfilled') {
                        const caaData = await results[7].value.json();
                        if(caaData.Answer && caaList) {
                            caaList.innerHTML = `<li style="color:#00ff00;">✅ ${t.caaOk}</li><li style="font-size:0.8rem;">${t.caaOkSub}</li>`;
                        } else if (caaList) { caaList.innerHTML = `<li style="color:#ff4444;">⚠️ ${t.caaFail}</li>`; }
                    }
                } catch(e) {}

                // 10. AAAA (IPv6)
                try {
                    if(results[8].status === 'fulfilled') {
                        const v6Data = await results[8].value.json();
                        if(v6Data.Answer && ipv6List) {
                            ipv6List.innerHTML = `<li>✅ ${t.v6Ok}</li><li style="font-size:0.8rem; font-family:monospace;">${v6Data.Answer[0].data}</li>`;
                        } else if (ipv6List) { ipv6List.innerHTML = `<li>⚠️ ${t.v6Fail}</li>`; }
                    }
                } catch(e) {}

                if(typeof window.showToast === 'function') window.showToast(t.toastSuccess, 'success');

            } catch (error) {
                console.error("Error global:", error);
                if(typeof window.showToast === 'function') window.showToast(t.toastError, 'error');
            }
        });
    }

    /* --- LÓGICA DEL PDF --- */
    if (btnExport && modal && btnFree && resultsDiv) {
        btnExport.addEventListener('click', (e) => { e.preventDefault(); modal.classList.remove('hidden'); });

        btnFree.addEventListener('click', () => {
            modal.classList.add('hidden');
            if(typeof window.showToast === 'function') window.showToast(t.toastPdf, 'success');

            window.scrollTo(0, 0);

            const opt = {
                margin:       [0, 0, 0, 0], 
                filename:     isEn ? 'CyberEscudo-Intelligence-Report.pdf' : 'Reporte-Inteligencia-CyberEscudo.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true, backgroundColor: '#0a0f14' }, 
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' },
                pagebreak:    { mode: ['avoid-all', 'css', 'legacy'] }
            };

            if (typeof html2pdf !== 'undefined') {
                setTimeout(() => {
                    html2pdf().set(opt).from(resultsDiv).save().then(() => {
                        if(typeof window.showToast === 'function') window.showToast(t.toastPdfOk, 'success');
                    });
                }, 400);
            }
        });
        
        modal.addEventListener('click', (e) => { if(e.target === modal) modal.classList.add('hidden'); });
    }
});