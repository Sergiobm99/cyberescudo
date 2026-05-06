<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Árbol de Habilidades — CyberEscudo' : 'Skill Tree — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Ruta de aprendizaje interactiva y laboratorios de ciberseguridad.' : 'Interactive learning path and cybersecurity labs.';
$current_page = 'projects/skill-tree.php';
require __DIR__ . '/../templates/header.php';
?>

<main class="content-page">
    <div class="md-container" style="padding-top: 4rem; padding-bottom: 6rem;">
        
        <div style="text-align: center; margin-bottom: 3rem;">
            <span class="section-label" style="color: var(--cyan);">// <?= $lang === 'es' ? 'ROADMAP INTERACTIVO' : 'INTERACTIVE ROADMAP' ?></span>
            <h1 style="font-size: 3rem; text-transform: uppercase; letter-spacing: 2px;">
                <?= $lang === 'es' ? 'Árbol de Habilidades' : 'Skill Tree' ?>
            </h1>
            <p style="color: var(--gray); max-width: 600px; margin: 1rem auto;">
                <?= $lang === 'es' ? 'Sigue la ruta de aprendizaje recomendada. Completa los laboratorios de los niveles inferiores para dominar las técnicas de los niveles superiores.' : 'Follow the recommended learning path. Complete lower-tier labs to master techniques in higher tiers.' ?>
            </p>
        </div>

        <div class="tree-container">
            
            <div class="tree-tier">
                <div class="tier-badge"><?= $lang === 'es' ? 'Nivel 1: Reconocimiento' : 'Tier 1: Reconnaissance' ?></div>
                <div class="tier-nodes">
                    <a href="nmap.php" class="node-card">
                        <span class="node-icon">🗺️</span>
                        <div class="node-title">Nmap & Escaneo</div>
                        <div class="node-desc"><?= $lang === 'es' ? 'Descubrimiento de puertos y servicios.' : 'Port and service discovery.' ?></div>
                    </a>
                    <a href="shodan.php" class="node-card">
                        <span class="node-icon">👁️</span>
                        <div class="node-title">Shodan OSINT</div>
                        <div class="node-desc"><?= $lang === 'es' ? 'Inteligencia de fuentes abiertas y banners.' : 'Open-source intelligence and banners.' ?></div>
                    </a>
                </div>
            </div>

            <div class="tree-tier">
                <div class="tier-badge"><?= $lang === 'es' ? 'Nivel 2: Análisis de Vulnerabilidades' : 'Tier 2: Vulnerability Analysis' ?></div>
                <div class="tier-nodes">
                    <a href="burpsuite.php" class="node-card">
                        <span class="node-icon">🕷️</span>
                        <div class="node-title">Burp Suite</div>
                        <div class="node-desc"><?= $lang === 'es' ? 'Intercepción de tráfico web y proxy.' : 'Web traffic interception and proxy.' ?></div>
                    </a>
                    <a href="vuln-scanner.php" class="node-card">
                        <span class="node-icon">🎯</span>
                        <div class="node-title">Vuln Scanners</div>
                        <div class="node-desc"><?= $lang === 'es' ? 'Análisis automatizado de vulnerabilidades.' : 'Automated vulnerability scanning.' ?></div>
                    </a>
                </div>
            </div>

            <div class="tree-tier">
                <div class="tier-badge"><?= $lang === 'es' ? 'Nivel 3: Explotación (Red Team)' : 'Tier 3: Exploitation (Red Team)' ?></div>
                <div class="tier-nodes">
                    <a href="sqlmap.php" class="node-card">
                        <span class="node-icon">💉</span>
                        <div class="node-title">SQL Injection</div>
                        <div class="node-desc"><?= $lang === 'es' ? 'Extracción de bases de datos relacionales.' : 'Relational database extraction.' ?></div>
                    </a>
                    <a href="metasploit.php" class="node-card">
                        <span class="node-icon">💣</span>
                        <div class="node-title">Metasploit</div>
                        <div class="node-desc"><?= $lang === 'es' ? 'Framework de explotación y payloads.' : 'Exploitation framework and payloads.' ?></div>
                    </a>
                    <a href="xss-practica.php" class="node-card">
                        <span class="node-icon">📝</span>
                        <div class="node-title">XSS & Client-Side</div>
                        <div class="node-desc"><?= $lang === 'es' ? 'Inyección de JavaScript malicioso.' : 'Malicious JavaScript injection.' ?></div>
                    </a>
                </div>
            </div>

            <div class="tree-tier">
                <div class="tier-badge"><?= $lang === 'es' ? 'Nivel 4: Defensa (Blue Team)' : 'Tier 4: Defense (Blue Team)' ?></div>
                <div class="tier-nodes">
                    <a href="firewall.php" class="node-card">
                        <span class="node-icon">🧱</span>
                        <div class="node-title">Firewall & Hardening</div>
                        <div class="node-desc"><?= $lang === 'es' ? 'Configuración de reglas iptables y seguridad.' : 'iptables rules and security config.' ?></div>
                    </a>
                    <a href="incident-response.php" class="node-card">
                        <span class="node-icon">🚨</span>
                        <div class="node-title">Incident Response</div>
                        <div class="node-desc"><?= $lang === 'es' ? 'Respuesta y contención de amenazas.' : 'Threat containment and response.' ?></div>
                    </a>
                </div>
            </div>

            <div class="tree-tier">
                <div class="tier-badge" style="border-color: #ff2a2a; color: #ff2a2a; text-shadow: 0 0 10px rgba(255,42,42,0.5);"><?= $lang === 'es' ? 'Nivel 5: Especialización Avanzada' : 'Tier 5: Advanced Specialization' ?></div>
                <div class="tier-nodes">
                    <a href="android-reversing.php" class="node-card" style="border-color: rgba(255,42,42,0.5);">
                        <span class="node-icon">📱</span>
                        <div class="node-title" style="color: #ff2a2a;">Android Reversing</div>
                        <div class="node-desc"><?= $lang === 'es' ? 'Ingeniería inversa de APKs y análisis de código.' : 'APK reverse engineering and code analysis.' ?></div>
                    </a>
                    <a href="privilege-escalation-linux.php" class="node-card" style="border-color: rgba(255,42,42,0.5);">
                        <span class="node-icon">👑</span>
                        <div class="node-title" style="color: #ff2a2a;">Privilege Escalation</div>
                        <div class="node-desc"><?= $lang === 'es' ? 'De usuario raso a root en sistemas Linux.' : 'From standard user to root on Linux systems.' ?></div>
                    </a>
                </div>
            </div>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>