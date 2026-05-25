<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle = $lang === 'es' ? 'CyberEscudo — Enterprise Executive Reporter' : 'CyberEscudo — Enterprise Executive Reporter';
require __DIR__ . '/../templates/header.php';
?>

<div class="report-container" id="modern-report-tool" data-lang="<?= $lang ?>" data-url="<?= BASE_URL ?>">
    <div class="report-header hide-on-print">
        <h1 class="report-title"><?= $lang === 'es' ? 'Enterprise Report Generator' : 'Enterprise Report Generator' ?></h1>
    </div>

    <div class="report-layout">
        
        <div class="input-panel hide-on-print">
            
            <div class="form-group" style="border: 1px solid var(--cyan); padding: 1rem; border-radius: 8px; background: rgba(0,255,255,0.05);">
                <label class="form-label" style="color: #fff;">🔍 SMART LOOKUP (MITRE / CVE)</label>
                <div style="display: flex; gap: 0.5rem;">
                    <input type="text" id="inp-smart-search" class="form-control" placeholder="<?= $lang === 'es' ? 'Ej: T1059 o Credential...' : 'Ex: T1059 or Credential...' ?>">
                    <button id="btn-smart-lookup" class="btn-tpl" style="background: var(--cyan); color: #000; border: none; padding: 0.8rem; font-weight: bold; width: auto;"><?= $lang === 'es' ? 'BUSCAR' : 'SEARCH' ?></button>
                </div>
            </div>

            <div style="font-family: var(--mono); color: var(--gray); font-size: 0.8rem; margin-bottom: 0.5rem; margin-top:1.5rem;">> LOAD_TEMPLATE.sh</div>
            <div class="template-grid">
                <button class="btn-tpl" id="tpl-phish">🎣 Phishing</button>
                <button class="btn-tpl" id="tpl-ddos">🚀 DDoS Attack</button>
                <button class="btn-tpl" id="tpl-insider">🕵️ Insider Threat</button>
                <button class="btn-tpl" id="tpl-breach">🔓 Data Breach</button>
                <button class="btn-tpl" id="tpl-clear" style="grid-column: span 2; text-align:center;">🗑️ <?= $lang === 'es' ? 'Limpiar Todo' : 'Clear All' ?></button>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">TLP Protocol</label>
                    <select id="in-tlp" class="form-control">
                        <option value="RED">TLP:RED</option>
                        <option value="AMBER">TLP:AMBER</option>
                        <option value="GREEN">TLP:GREEN</option>
                        <option value="CLEAR" selected>TLP:CLEAR</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label"><?= $lang === 'es' ? 'Estado Fases NIST' : 'NIST Phase Status' ?></label>
                    <select id="in-status" class="form-control">
                        <option value="OPEN"><?= $lang === 'es' ? 'Abierto / Analizando' : 'Open / Analyzing' ?></option>
                        <option value="CONTAINED"><?= $lang === 'es' ? 'Contenido' : 'Contained' ?></option>
                        <option value="MITIGATED"><?= $lang === 'es' ? 'Mitigado' : 'Mitigated' ?></option>
                        <option value="CLOSED"><?= $lang === 'es' ? 'Cerrado' : 'Closed' ?></option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; border: 1px dashed #333; padding: 0.8rem; border-radius: 6px; margin-bottom: 1.2rem;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">MTTD (Detección)</label>
                    <input type="text" id="in-mttd" class="form-control" placeholder="Ej: 14 mins">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">MTTR (Respuesta)</label>
                    <input type="text" id="in-mttr" class="form-control" placeholder="Ej: 45 mins">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label"><?= $lang === 'es' ? 'Regulaciones / Cumplimiento' : 'Compliance Frameworks' ?></label>
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; background:#000; padding:0.6rem; border:1px solid #333; border-radius:6px;">
                    <label style="color:#fff; font-size:0.8rem; display:flex; align-items:center; gap:4px;"><input type="checkbox" id="chk-gdpr" value="GDPR"> GDPR / RGPD</label>
                    <label style="color:#fff; font-size:0.8rem; display:flex; align-items:center; gap:4px;"><input type="checkbox" id="chk-pci" value="PCI-DSS"> PCI-DSS</label>
                    <label style="color:#fff; font-size:0.8rem; display:flex; align-items:center; gap:4px;"><input type="checkbox" id="chk-nis2" value="NIS2"> NIS 2</label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label"><?= $lang === 'es' ? 'Analista Principal' : 'Lead Analyst' ?></label>
                <input type="text" id="in-author" class="form-control" placeholder="Ej: J. Doe (Tier 2 SOC)">
            </div>

            <div class="form-group" style="border-top: 1px solid #333; padding-top: 1rem;">
                <label class="form-label"><?= $lang === 'es' ? 'Título del Informe' : 'Report Title' ?></label>
                <input type="text" id="in-title" class="form-control" placeholder="SQL Injection in Finance API">
            </div>

            <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label class="form-label"><?= $lang === 'es' ? 'Riesgo' : 'Risk' ?></label>
                    <select id="in-risk" class="form-control">
                        <option value="CRITICAL">CRITICAL</option>
                        <option value="HIGH">HIGH</option>
                        <option value="MEDIUM">MEDIUM</option>
                        <option value="LOW">LOW</option>
                    </select>
                </div>
                <div>
                    <label class="form-label"><?= $lang === 'es' ? 'Activo / Target' : 'Asset / Target' ?></label>
                    <input type="text" id="in-asset" class="form-control" placeholder="srv-db-01">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label"><?= $lang === 'es' ? 'Resumen Ejecutivo' : 'Executive Summary' ?></label>
                <textarea id="in-exec" class="form-control" rows="4"></textarea>
            </div>

            <div class="form-group">
                <label class="form-label" style="color:#ffb86c;"><?= $lang === 'es' ? 'Impacto Financiero / Operativo' : 'Financial / Operational Impact' ?></label>
                <textarea id="in-impact" class="form-control" rows="2"></textarea>
            </div>

            <div class="form-group">
                <label class="form-label"><?= $lang === 'es' ? 'Plan de Acción / Mitigación' : 'Action Plan / Mitigation' ?></label>
                <textarea id="in-remed" class="form-control" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label class="form-label"><?= $lang === 'es' ? 'Evidencia Técnica / IoCs' : 'Technical Evidence / IoCs' ?></label>
                <textarea id="in-tech" class="form-control" rows="3"></textarea>
            </div>

            <div class="form-group" style="border-top: 1px dashed #333; padding-top: 1rem;">
                <label class="form-label"><?= $lang === 'es' ? 'Tu Logo (Opcional)' : 'Your Logo (Optional)' ?></label>
                <input type="file" id="inp-logo-upload" accept="image/*" class="form-control" style="font-size: 0.75rem; padding: 0.4rem;">
            </div>

            <button id="btn-export-pdf" class="cyber-btn-donate" style="width: 100%; padding: 1rem; font-size: 1rem; margin-top: 1rem;">
                📄 <?= $lang === 'es' ? 'EXPORTAR INFORME PDF' : 'EXPORT PDF REPORT' ?>
            </button>
        </div>

       <div class="output-panel">
            <div class="paper-a4" id="paper">
                <div class="p-header">
                    <div class="p-brand-container">
                        <div id="user-logo-container" class="p-user-logo">
                            <img id="preview-user-logo" src="" style="width: 100%; height: auto; display: block; max-height: 50px;">
                        </div>
                        <div class="p-brand">Cyber<span style="color:#3182ce;">Escudo</span></div>
                    </div>
                    <div class="p-meta">
                        <div class="tlp-badge tlp-CLEAR" id="out-tlp-badge">TLP:CLEAR</div><br>
                        <strong><?= $lang==='es'?'INFORME DE INCIDENTE':'INCIDENT REPORT' ?></strong><br>
                        ID: #<?= strtoupper(substr(md5(time()), 0, 8)) ?><br>
                        <?= gmdate('Y-m-d H:i') ?> UTC
                    </div>
                </div>

                <h1 class="p-title" id="out-title">REPORT TITLE</h1>
                
                <div class="p-grid-meta">
                    <div class="meta-box">
                        <span class="meta-lbl"><?= $lang === 'es' ? 'Severidad' : 'Severity' ?></span>
                        <span class="meta-val"><span class="badge-pill pill-MEDIUM" id="out-risk">MEDIUM</span></span>
                    </div>
                    <div class="meta-box">
                        <span class="meta-lbl"><?= $lang === 'es' ? 'Estado' : 'Status' ?></span>
                        <span class="meta-val status-OPEN" id="out-status">OPEN</span>
                    </div>
                    <div class="meta-box">
                        <span class="meta-lbl"><?= $lang === 'es' ? 'Target / Activo' : 'Target / Asset' ?></span>
                        <span class="meta-val" id="out-asset">--</span>
                    </div>
                    
                    <div class="meta-box" style="border-top:1px solid #edf2f7; padding-top:0.5rem; margin-top:0.5rem;">
                        <span class="meta-lbl">MTTD (Detection)</span>
                        <span class="meta-val" id="out-mttd" style="color:#2d3748;">--</span>
                    </div>
                    <div class="meta-box" style="border-top:1px solid #edf2f7; padding-top:0.5rem; margin-top:0.5rem;">
                        <span class="meta-lbl">MTTR (Response)</span>
                        <span class="meta-val" id="out-mttr" style="color:#2d3748;">--</span>
                    </div>
                    <div class="meta-box" style="border-top:1px solid #edf2f7; padding-top:0.5rem; margin-top:0.5rem;">
                        <span class="meta-lbl">Compliance</span>
                        <span class="meta-val" id="out-compliance" style="font-size:0.75rem; font-weight:normal;">--</span>
                    </div>
                </div>

                <div class="p-section">
                    <div class="p-h3">01. <?= $lang==='es'?'Resumen Ejecutivo':'Executive Summary' ?></div>
                    <div class="p-text" id="out-exec">...</div>
                </div>

                <div class="p-section">
                    <div class="p-h3">02. <?= $lang==='es'?'Impacto en Negocio':'Business Impact' ?></div>
                    <div class="p-impact-box">
                        <div class="p-text" id="out-impact" style="font-weight: 600; color: #c53030;">--</div>
                    </div>
                </div>

                <div class="p-section">
                    <div class="p-h3">03. <?= $lang==='es'?'Plan de Acción':'Action Plan' ?></div>
                    <div class="p-text" id="out-remed">...</div>
                </div>

                <div class="p-section">
                    <div class="p-h3">04. <?= $lang==='es'?'Evidencia Técnica / IoCs':'Technical Evidence / IoCs' ?></div>
                    <div class="p-code" id="out-tech">...</div>
                </div>

                <div class="p-signatures">
                    <div class="sig-block">
                        <div class="sig-title"><?= $lang === 'es' ? 'Preparado por (Analista)' : 'Prepared By (Analyst)' ?></div>
                        <div class="sig-name" id="out-author">--</div>
                    </div>
                    <div class="sig-block">
                        <div class="sig-title"><?= $lang === 'es' ? 'Aprobado por (CISO / Manager)' : 'Approved By (CISO / Manager)' ?></div>
                        <div class="sig-name"></div>
                    </div>
                </div>

                <div class="p-footer">
                    <div style="width: 100%;">
                        <div class="p-footer-legal" id="out-legal">
                            </div>
                        <div style="display: flex; justify-content: space-between; border-top: 1px solid #edf2f7; padding-top: 0.5rem;">
                            <div>CyberEscudo Security Intelligence | SOC Division</div>
                            <div>Page 1</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php require __DIR__ . '/../templates/footer.php'; ?>