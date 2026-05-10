<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $lang === 'es' ? 'Sobre Mí — Agente CyberEscudo' : 'About Me — CyberEscudo Agent';
require __DIR__ . '/templates/header.php';
?>

<style>
    .about-container { max-width: 1000px; margin: 4rem auto; padding: 2rem; }
    
    /* Hero Section */
    .hero-profile { display: flex; gap: 3rem; align-items: center; margin-bottom: 4rem; flex-wrap: wrap; }
    .profile-photo { width: 250px; height: 250px; border: 2px solid var(--cyan); border-radius: 0.5rem; object-fit: cover; box-shadow: 0 0 20px rgba(0, 255, 255, 0.2); }
    .hero-text h1 { font-family: var(--mono); color: #fff; font-size: 2.5rem; margin-bottom: 0.5rem; }
    .hero-text .badge { background: rgba(0, 255, 255, 0.1); color: var(--cyan); padding: 0.3rem 0.8rem; border-radius: 4px; font-family: var(--mono); font-size: 0.9rem; border: 1px solid var(--cyan); display: inline-block; margin-bottom: 0.5rem; margin-right: 0.5rem;}

    /* Tech Stack */
    .section-title { font-family: var(--mono); color: var(--cyan); border-bottom: 1px solid #222; padding-bottom: 0.5rem; margin-bottom: 2rem; text-transform: uppercase; margin-top: 3rem;}
    .skill-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-bottom: 2rem; }
    .skill-item { margin-bottom: 1rem; }
    .skill-info { display: flex; justify-content: space-between; font-family: var(--mono); color: #888; font-size: 0.85rem; margin-bottom: 0.5rem; }
    .skill-bar-bg { background: #1a1a1a; height: 6px; border-radius: 3px; overflow: hidden; }
    .skill-bar-fill { background: var(--cyan); height: 100%; box-shadow: 0 0 10px var(--cyan); }

    /* Timeline & Certs */
    .timeline { border-left: 2px solid #222; margin-left: 1rem; padding-left: 2rem; position: relative; }
    .timeline-item { position: relative; margin-bottom: 2.5rem; }
    .timeline-item::before { content: ''; position: absolute; left: -2.4rem; top: 0.2rem; width: 12px; height: 12px; background: var(--cyan); border-radius: 50%; box-shadow: 0 0 10px var(--cyan); }
    .timeline-date { font-family: var(--mono); color: var(--cyan); font-size: 0.85rem; margin-bottom: 0.5rem; }
    .timeline-title { color: #fff; font-weight: bold; font-size: 1.1rem; }
    .timeline-subtitle { color: #aaa; font-size: 0.95rem; font-style: italic; margin-bottom: 0.5rem;}
    .timeline-desc { color: #888; font-size: 0.9rem; line-height: 1.5;}

    .cert-badge { border: 1px solid #444; padding: 1rem; border-radius: 0.5rem; background: #0a0a0a; text-align: center;}
    .cert-badge strong { color: #fff; display: block; margin-bottom: 0.5rem; }
    .cert-badge span { color: var(--cyan); font-family: var(--mono); font-size: 0.85rem; }

    /* Action Buttons */
    .action-group { display: flex; gap: 1rem; margin-top: 2rem; flex-wrap: wrap;}
</style>

<main class="content-page">
    <div class="about-container">
        
        <section class="hero-profile">
            <img src="assets/img/mifoto.jpg" alt="Sergio Belmonte" class="profile-photo">
            <div class="hero-text">
                <h1>Sergio Belmonte Morales</h1>
                <div style="margin-bottom: 1rem;">
                    <span class="badge">Cybersecurity Analyst</span>
                    <span class="badge">SOC Operator</span>
                    <span class="badge">Pentester (eCPPT)</span>
                </div>
                <p style="color: #aaa; line-height: 1.6; max-width: 600px;">
                    <?= $lang === 'es' ? 'Analista de Ciberseguridad con sólida experiencia en SOC y hardening de infraestructuras. Especializado en Microsoft 365 y Azure Sentinel, con un enfoque ofensivo respaldado por la certificación eCPPT. Experto en KQL y respuesta ante incidentes.' : 'Cybersecurity Analyst with solid experience in SOC management and infrastructure hardening. Specialized in Microsoft 365 and Azure Sentinel, with an offensive security mindset backed by the eCPPT certification. Expert in KQL and incident response.' ?>
                </p>
                
                <div class="action-group">
                    <a href="generate-cv.php?lang=<?= $lang ?>" class="btn-deploy" style="background: var(--cyan); color: #000; font-weight: bold;">
                        📄 <?= $lang === 'es' ? 'DESCARGAR CV (PDF)' : 'DOWNLOAD CV (PDF)' ?>
                    </a>
                    <a href="https://www.linkedin.com/in/sergio-belmonte-morales99" target="_blank" class="btn-deploy" style="border-color: #0077b5; color: #0077b5;">
                        LINKEDIN
                    </a>
                </div>
            </div>
        </section>

        <h2 class="section-title"><?= $lang === 'es' ? 'Arsenal Técnico' : 'Technical Arsenal' ?></h2>
        <div class="skill-grid">
            <div class="skill-item">
                <div class="skill-info"><span>Azure Sentinel & KQL</span><span>95%</span></div>
                <div class="skill-bar-bg"><div class="skill-bar-fill" style="width: 95%;"></div></div>
            </div>
            <div class="skill-item">
                <div class="skill-info"><span>Microsoft 365 Defender & Intune</span><span>90%</span></div>
                <div class="skill-bar-bg"><div class="skill-bar-fill" style="width: 90%;"></div></div>
            </div>
            <div class="skill-item">
                <div class="skill-info"><span>Offensive Sec (OpenVAS, Gophish)</span><span>85%</span></div>
                <div class="skill-bar-bg"><div class="skill-bar-fill" style="width: 85%;"></div></div>
            </div>
            <div class="skill-item">
                <div class="skill-info"><span>Active Directory & Networking</span><span>80%</span></div>
                <div class="skill-bar-bg"><div class="skill-bar-fill" style="width: 80%;"></div></div>
            </div>
        </div>

        <h2 class="section-title"><?= $lang === 'es' ? 'Certificaciones Oficiales' : 'Official Certifications' ?></h2>
        <div class="skill-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div class="cert-badge"><strong>eCPPT</strong><span>INE Security</span></div>
            <div class="cert-badge"><strong>eJPT</strong><span>INE Security</span></div>
            <div class="cert-badge"><strong>SC-200</strong><span>Microsoft</span></div>
            <div class="cert-badge"><strong>SC-900</strong><span>Microsoft</span></div>
            <div class="cert-badge"><strong>Aptis ESOL B2</strong><span>British Council</span></div>
        </div>

        <h2 class="section-title"><?= $lang === 'es' ? 'Historial de Operaciones' : 'Operations History' ?></h2>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-date">06/2022 - 04/2024</div>
                <div class="timeline-title"><?= $lang === 'es' ? 'Analista de Ciberseguridad' : 'Cybersecurity Analyst' ?></div>
                <div class="timeline-subtitle">Midway Technologies | Sevilla, <?= $lang === 'es' ? 'España' : 'Spain' ?></div>
                <div class="timeline-desc">
                    <?= $lang === 'es' ? 'Gestión de SOC con Azure Sentinel y KQL. Hardening de endpoints con Microsoft Defender e Intune. Seguridad ofensiva con Gophish y OpenVAS/Nessus.' : 'SOC management with Azure Sentinel and KQL. Endpoint hardening with Microsoft Defender and Intune. Offensive security with Gophish and OpenVAS/Nessus.' ?>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-date"><?= $lang === 'es' ? 'Anteriormente' : 'Previously' ?></div>
                <div class="timeline-title"><?= $lang === 'es' ? 'Técnico de Campo' : 'Field Technician' ?></div>
                <div class="timeline-subtitle">Magtel Operaciones S.L.U. | Córdoba, <?= $lang === 'es' ? 'España' : 'Spain' ?></div>
            </div>
        </div>

        <h2 class="section-title"><?= $lang === 'es' ? 'Instrucción y Formación' : 'Education & Training' ?></h2>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-date">2021 - 2022</div>
                <div class="timeline-title"><?= $lang === 'es' ? 'Especialización en Ciberseguridad' : 'Cybersecurity Specialization' ?></div>
                <div class="timeline-subtitle">IES Punta del verde | Sevilla</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-date">2019 - 2021</div>
                <div class="timeline-title"><?= $lang === 'es' ? 'Grado Superior ASIR' : 'Associate Degree in Network Administration (ASIR)' ?></div>
                <div class="timeline-subtitle">IES Triana | Sevilla</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-date">2016 - 2018</div>
                <div class="timeline-title"><?= $lang === 'es' ? 'Grado Medio SMR' : 'Vocational Degree in Microcomputer Systems and Networks' ?></div>
                <div class="timeline-subtitle">IES Fidiana | Córdoba</div>
            </div>
        </div>

    </div>
</main>

<?php require __DIR__ . '/templates/footer.php'; ?>