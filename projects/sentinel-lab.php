<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle = $lang === 'es' ? 'CyberEscudo — Sentinel D&R Lab' : 'CyberEscudo — Sentinel D&R Lab';
require __DIR__ . '/../templates/header.php';
?>

<div class="snt-container">
    <div class="snt-header">
        <h1 class="snt-title">Sentinel Detection & Response</h1>
        <p class="snt-subtitle"><?= $lang === 'es' ? 'Simulador interactivo de creación de reglas KQL y alertas' : 'Interactive KQL rule creation and alerting simulator' ?></p>
    </div>

    <div class="snt-tabs">
        <button class="snt-tab-btn active" data-target="pane-kql">01. KQL FORGE</button>
        <button class="snt-tab-btn" data-target="pane-rule">02. ALERT BUILDER</button>
    </div>

    <div id="pane-kql" class="snt-pane active">
        <div class="kql-layout">
            <div class="kql-editor-box">
                <div class="kql-toolbar">
                    <span style="color: #8b949e; font-family: var(--mono); font-size: 0.8rem;">Logs / New Query 1</span>
                    <button class="kql-run-btn" id="btn-run-kql">▶ <?= $lang === 'es' ? 'Ejecutar Consulta' : 'Run Query' ?></button>
                </div>
                <textarea id="kql-input" class="kql-textarea" spellcheck="false" placeholder="// <?= $lang === 'es' ? 'Escribe tu consulta KQL aquí...' : 'Type your KQL query here...' ?>&#10;&#10;SecurityEvent&#10;| where EventID == 4625"></textarea>
            </div>

            <div class="kql-results-box">
                <div class="kql-results-header" id="kql-results-meta">
                    <?= $lang === 'es' ? 'Resultados: 0 registros' : 'Results: 0 records' ?>
                </div>
                <div class="kql-table-wrapper">
                    <table class="kql-table" id="kql-table">
                        <thead>
                            <tr>
                                <th>TimeGenerated</th>
                                <th>EventID</th>
                                <th>Account</th>
                                <th>IpAddress</th>
                                <th>Activity</th>
                            </tr>
                        </thead>
                        <tbody id="kql-tbody">
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <?= $lang === 'es' ? 'Pulsa "Ejecutar Consulta" para procesar los logs.' : 'Click "Run Query" to process logs.' ?>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="pane-rule" class="snt-pane">
        <div class="ab-layout">
            <div class="ab-card">
                <div class="ab-card-title">01. General & Logic</div>
                
                <div class="ab-form-group">
                    <label class="ab-label">Analytics Rule Name</label>
                    <input type="text" id="rule-name-input" class="ab-input" placeholder="Ej. Detección de Fuerza Bruta (EventID 4625)">
                </div>
                
                <div class="ab-form-group">
                    <label class="ab-label">Rule Query (Importada desde KQL Forge)</label>
                    <textarea id="alert-query-preview" class="ab-textarea kql-readonly" readonly></textarea>
                </div>

                <div class="ab-form-group" style="display: flex; gap: 1rem; align-items: center;">
                    <label class="ab-label" style="margin:0;">Trigger alert when results are > </label>
                    <input type="number" class="ab-input" style="width: 80px;" value="5">
                </div>
            </div>

            <div class="ab-card">
                <div class="ab-card-title">02. Incident Settings</div>
                
                <div class="ab-form-group">
                    <label class="ab-label">Severity</label>
                    <select class="ab-select" id="rule-severity">
                        <option value="high">🔴 High</option>
                        <option value="medium">🟠 Medium</option>
                        <option value="low">🟡 Low</option>
                        <option value="info">🔵 Informational</option>
                    </select>
                </div>

                <div class="ab-form-group">
                    <label class="ab-label">MITRE ATT&CK Tactics</label>
                    <select class="ab-select" id="mitre-selector">
                        <option value="">-- Selecciona táctica --</option>
                        <option value="Credential Access">Credential Access (TA0006)</option>
                        <option value="Execution">Execution (TA0002)</option>
                        <option value="Defense Evasion">Defense Evasion (TA0005)</option>
                        <option value="Persistence">Persistence (TA0003)</option>
                    </select>
                    <div class="mitre-tags" id="mitre-tags-container"></div>
                </div>

                <button class="ab-btn-save" id="btn-save-rule">Crear Regla de Detección</button>
            </div>
        </div>

        <div id="active-rules-container" style="display: none; margin-top: 2rem; background: #0d1117; border: 1px solid #30363d; border-radius: 6px; padding: 1.5rem;">
            <div class="ab-card-title" style="color: #2ea043;">🟢 Active Rules in Workspace</div>
            <div class="kql-table-wrapper">
                <table class="kql-table">
                    <thead>
                        <tr>
                            <th>Rule Name</th>
                            <th>Severity</th>
                            <th>MITRE Tactics</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="active-rules-tbody">
                        </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php require __DIR__ . '/../templates/footer.php'; ?>