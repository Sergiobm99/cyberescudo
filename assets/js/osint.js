/* ─── SCRIPT EXCLUSIVO OSINT (V7: 12 MÓDULOS - EDICIÓN PRO) ─── */
document.addEventListener('DOMContentLoaded', () => {
    console.log("🚀 SCRIPT OSINT: Iniciado versión PRO (12 Módulos)");

    const btnRunOsint = document.getElementById('btn-run-osint');
    const targetInput = document.getElementById('osint-target');
    const resultsDiv = document.getElementById('osint-results');
    const exportContainer = document.getElementById('export-container');
    const reportDomain = document.getElementById('report-domain');
    
    // Las 12 cajas
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
                if(typeof window.showToast === 'function') window.showToast('Introduce un dominio válido', 'error');
                return;
            }

            if(resultsDiv) resultsDiv.style.display = 'block';
            if(exportContainer) exportContainer.style.display = 'block';
            if(reportDomain) reportDomain.textContent = 'Objetivo: ' + domain;
            
            // Textos de carga
            const loadingBoxes = [geoList, whoisList, mxList, txtList, nsList, archList, portsList, vulnsList, dmarcList, soaList, caaList, ipv6List];
            loadingBoxes.forEach(box => { if(box) box.innerHTML = '<li>Analizando...</li>'; });

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
                        geoList.innerHTML = `<li><strong>IP:</strong> ${ipAddress}</li><li><strong>País:</strong> ${geoData.country_name}</li><li><strong>ISP:</strong> ${geoData.org}</li>`;
                    } else if (geoList) { geoList.innerHTML = `<li><strong>IP:</strong> ${ipAddress}</li>`; }

                    // EXTRA: SHODAN InternetDB (Puertos y Vulns) basado en la IP
                    try {
                        const shodanRes = await fetch(`https://internetdb.shodan.io/${ipAddress}`);
                        if(shodanRes.ok) {
                            const shodanData = await shodanRes.json();
                            // Puertos
                            if(shodanData.ports && shodanData.ports.length > 0 && portsList) {
                                let portsHtml = `<li>Detectados ${shodanData.ports.length} puertos:</li>`;
                                portsHtml += `<li style="color:#00ffff; font-size:0.85rem;">[ ${shodanData.ports.join(', ')} ]</li>`;
                                portsList.innerHTML = portsHtml;
                            } else if (portsList) { portsList.innerHTML = '<li>No constan puertos abiertos.</li>'; }
                            
                            // Vulnerabilidades
                            if(shodanData.vulns && shodanData.vulns.length > 0 && vulnsList) {
                                let vulnsHtml = `<li><strong style="color:#ff4444;">¡Peligro!</strong> ${shodanData.vulns.length} CVEs públicos:</li>`;
                                let showVulns = shodanData.vulns.slice(0, 3).join(', '); // Mostramos 3 max
                                vulnsHtml += `<li style="color:#ff4444; font-size:0.85rem;">${showVulns}${shodanData.vulns.length > 3 ? '...' : ''}</li>`;
                                vulnsList.innerHTML = vulnsHtml;
                            } else if (vulnsList) { vulnsList.innerHTML = '<li style="color:#00ff00;">Ninguna vulnerabilidad pública en BD.</li>'; }
                        } else {
                            if(portsList) portsList.innerHTML = '<li>Sin registros en Shodan.</li>';
                            if(vulnsList) vulnsList.innerHTML = '<li>Sin registros en Shodan.</li>';
                        }
                   } catch(e) { 
                        if(portsList) portsList.innerHTML = '<li style="color:#ff4444;">Bloqueado por Firewall (CSP)</li>'; 
                        if(vulnsList) vulnsList.innerHTML = '<li style="color:#ff4444;">Bloqueado por Firewall (CSP)</li>'; 
                    }

                } else { 
                    if(geoList) geoList.innerHTML = '<li>No se encontraron registros A públicos.</li>'; 
                    if(portsList) portsList.innerHTML = '<li>Requiere IP para análisis.</li>';
                    if(vulnsList) vulnsList.innerHTML = '<li>Requiere IP para análisis.</li>';
                }

                // Las demás consultas DNS en paralelo para que sea súper rápido
                const reqs = [
                    fetch(`https://networkcalc.com/api/dns/whois/${domain}`), // WHOIS
                    fetch(`https://cloudflare-dns.com/dns-query?name=${domain}&type=MX`, { headers: { 'Accept': 'application/dns-json' } }), // MX
                    fetch(`https://cloudflare-dns.com/dns-query?name=${domain}&type=TXT`, { headers: { 'Accept': 'application/dns-json' } }), // TXT
                    fetch(`https://cloudflare-dns.com/dns-query?name=${domain}&type=NS`, { headers: { 'Accept': 'application/dns-json' } }), // NS
                    fetch(`https://archive.org/wayback/available?url=${domain}`), // ARCHIVE
                    fetch(`https://cloudflare-dns.com/dns-query?name=_dmarc.${domain}&type=TXT`, { headers: { 'Accept': 'application/dns-json' } }), // DMARC
                    fetch(`https://cloudflare-dns.com/dns-query?name=${domain}&type=SOA`, { headers: { 'Accept': 'application/dns-json' } }), // SOA
                    fetch(`https://cloudflare-dns.com/dns-query?name=${domain}&type=CAA`, { headers: { 'Accept': 'application/dns-json' } }), // CAA
                    fetch(`https://cloudflare-dns.com/dns-query?name=${domain}&type=AAAA`, { headers: { 'Accept': 'application/dns-json' } }) // AAAA
                ];

                const results = await Promise.allSettled(reqs);

                // 2. WHOIS
                try {
                    if(results[0].status === 'fulfilled') {
                        const wData = await results[0].value.json();
                        if(wData.status === 'OK' && wData.whois && wData.whois.registrar && whoisList) {
                            whoisList.innerHTML = `<li><strong>Registrador:</strong> ${wData.whois.registrar}</li><li><strong>Estado:</strong> Registrado</li>`;
                        } else if (whoisList) { whoisList.innerHTML = '<li>Privado o no disponible.</li>'; }
                    } else if (whoisList) {
                        whoisList.innerHTML = '<li style="color:#ff4444;">Bloqueado por Firewall (CSP)</li>';
                    }
                } catch(e) {}

                // 3. MX
                try {
                    if(results[1].status === 'fulfilled') {
                        const mxData = await results[1].value.json();
                        if(mxData.Answer && mxList) {
                            mxList.innerHTML = mxData.Answer.slice(0,2).map(r => `<li>🎯 ${r.data}</li>`).join('');
                        } else if (mxList) { mxList.innerHTML = '<li>No hay servicios de correo.</li>'; }
                    }
                } catch(e) {}

                // 4. TXT (SPF)
                try {
                    if(results[2].status === 'fulfilled') {
                        const txtData = await results[2].value.json();
                        let html = '';
                        if(txtData.Answer) {
                            txtData.Answer.forEach(r => {
                                if(r.data.includes('v=spf1')) html += `<li style="color:#00ff00;">✅ <strong>SPF:</strong> Encontrado</li>`;
                            });
                        }
                        if(txtList) txtList.innerHTML = html || '<li style="color:#ff4444;">❌ <strong>SPF:</strong> Riesgo de Phishing</li>';
                    }
                } catch(e) {}

                // 5. NS
                try {
                    if(results[3].status === 'fulfilled') {
                        const nsData = await results[3].value.json();
                        if(nsData.Answer && nsList) {
                            nsList.innerHTML = nsData.Answer.slice(0,2).map(r => `<li>🔗 ${r.data}</li>`).join('');
                        } else if (nsList) { nsList.innerHTML = '<li>Sin registros NS.</li>'; }
                    }
                } catch(e) {}

                // 6. ARCHIVE
                try {
                    if(results[4].status === 'fulfilled') {
                        const arData = await results[4].value.json();
                        if(arData.archived_snapshots?.closest && archList) {
                            let ts = arData.archived_snapshots.closest.timestamp;
                            archList.innerHTML = `<li><strong>Archivado:</strong> Sí</li><li><strong>Captura:</strong> ${ts.substring(6,8)}/${ts.substring(4,6)}/${ts.substring(0,4)}</li>`;
                        } else if (archList) { archList.innerHTML = '<li>Sin registros en Wayback Machine.</li>'; }
                    } else if (archList) {
                        archList.innerHTML = '<li style="color:#ff4444;">Bloqueado por Firewall (CSP)</li>';
                    }
                } catch(e) {}

                // 7. DMARC
                try {
                    if(results[5].status === 'fulfilled') {
                        const dmData = await results[5].value.json();
                        if(dmData.Answer && dmarcList) {
                            dmarcList.innerHTML = `<li style="color:#00ff00;">✅ Configurado:</li><li style="font-size:0.8rem; color:#aaaaaa;">${dmData.Answer[0].data}</li>`;
                        } else if (dmarcList) { dmarcList.innerHTML = '<li style="color:#ff4444;">❌ No se detecta política DMARC</li>'; }
                    }
                } catch(e) {}

                // 8. SOA
                try {
                    if(results[6].status === 'fulfilled') {
                        const soaData = await results[6].value.json();
                        if(soaData.Answer && soaList) {
                            let parts = soaData.Answer[0].data.split(' ');
                            soaList.innerHTML = `<li><strong>Admin:</strong> ${parts[1].replace('.', '@')}</li><li><strong>Primario:</strong> ${parts[0]}</li>`;
                        } else if (soaList) { soaList.innerHTML = '<li>Sin registro SOA.</li>'; }
                    }
                } catch(e) {}

                // 9. CAA
                try {
                    if(results[7].status === 'fulfilled') {
                        const caaData = await results[7].value.json();
                        if(caaData.Answer && caaList) {
                            caaList.innerHTML = `<li style="color:#00ff00;">✅ Restringido</li><li style="font-size:0.8rem;">Emisores autorizados encontrados.</li>`;
                        } else if (caaList) { caaList.innerHTML = '<li style="color:#ff4444;">⚠️ Libre (Cualquiera puede emitir SSL)</li>'; }
                    }
                } catch(e) {}

                // 10. AAAA (IPv6)
                try {
                    if(results[8].status === 'fulfilled') {
                        const v6Data = await results[8].value.json();
                        if(v6Data.Answer && ipv6List) {
                            ipv6List.innerHTML = `<li>✅ Soportado</li><li style="font-size:0.8rem; font-family:monospace;">${v6Data.Answer[0].data}</li>`;
                        } else if (ipv6List) { ipv6List.innerHTML = '<li>⚠️ Solo IPv4 soportado</li>'; }
                    }
                } catch(e) {}

                if(typeof window.showToast === 'function') window.showToast('Análisis OSINT Finalizado', 'success');

            } catch (error) {
                console.error("Error global:", error);
                if(typeof window.showToast === 'function') window.showToast('Error en el escaneo', 'error');
            }
        });
    }

    /* --- LÓGICA DEL PDF (Soporte Multipágina Automático) --- */
    if (btnExport && modal && btnFree && resultsDiv) {
        btnExport.addEventListener('click', (e) => { e.preventDefault(); modal.classList.remove('hidden'); });

        btnFree.addEventListener('click', () => {
            modal.classList.add('hidden');
            if(typeof window.showToast === 'function') window.showToast('Generando reporte PDF...', 'success');

            window.scrollTo(0, 0);

            // Se añade el parámetro pagebreak para que no corte cajas
            const opt = {
                margin:       [0, 0, 0, 0], 
                filename:     'Reporte-Inteligencia-CyberEscudo.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true, backgroundColor: '#0a0f14' }, 
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' },
                pagebreak:    { mode: ['avoid-all', 'css', 'legacy'] }
            };

            if (typeof html2pdf !== 'undefined') {
                setTimeout(() => {
                    html2pdf().set(opt).from(resultsDiv).save().then(() => {
                        if(typeof window.showToast === 'function') window.showToast('PDF descargado con éxito', 'success');
                    });
                }, 400);
            }
        });
        
        modal.addEventListener('click', (e) => { if(e.target === modal) modal.classList.add('hidden'); });
    }
});