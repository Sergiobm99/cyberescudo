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
    .hero-text .badge { background: rgba(0, 255, 255, 0.1); color: var(--cyan); padding: 0.3rem 0.8rem; border-radius: 4px; font-family: var(--mono); font-size: 0.9rem; border: 1px solid var(--cyan); }

    /* Tech Stack */
    .section-title { font-family: var(--mono); color: var(--cyan); border-bottom: 1px solid #222; padding-bottom: 0.5rem; margin-bottom: 2rem; text-transform: uppercase; }
    .skill-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-bottom: 4rem; }
    .skill-item { margin-bottom: 1rem; }
    .skill-info { display: flex; justify-content: space-between; font-family: var(--mono); color: #888; font-size: 0.85rem; margin-bottom: 0.5rem; }
    .skill-bar-bg { background: #1a1a1a; height: 6px; border-radius: 3px; overflow: hidden; }
    .skill-bar-fill { background: var(--cyan); height: 100%; box-shadow: 0 0 10px var(--cyan); }

    /* Timeline */
    .timeline { border-left: 2px solid #222; margin-left: 1rem; padding-left: 2rem; position: relative; }
    .timeline-item { position: relative; margin-bottom: 3rem; }
    .timeline-item::before { content: ''; position: absolute; left: -2.4rem; top: 0.2rem; width: 12px; height: 12px; background: var(--cyan); border-radius: 50%; box-shadow: 0 0 10px var(--cyan); }
    .timeline-date { font-family: var(--mono); color: var(--cyan); font-size: 0.85rem; margin-bottom: 0.5rem; }
    .timeline-title { color: #fff; font-weight: bold; font-size: 1.1rem; }
    .timeline-desc { color: #888; font-size: 0.9rem; margin-top: 0.5rem; }

    /* Action Buttons */
    .action-group { display: flex; gap: 1rem; margin-top: 3rem; }
</style>

<main class="content-page">
    <div class="about-container">
        
        <section class="hero-profile">
            <img src="assets/img/tu-foto.jpg" alt="Agente" class="profile-photo">
            <div class="hero-text">
                <h1>Sergio Belmonte</h1>
                <div style="margin-bottom: 1.5rem;">
                    <span class="badge">Pentester Jr</span>
                    <span class="badge">Bug Hunter</span>
                    <span class="badge">Blue Team</span>
                </div>
                <p style="color: #aaa; line-height: 1.6; max-width: 600px;">
                    <?= $lang === 'es' ? 'Especialista en seguridad ofensiva con pasión por el análisis de vulnerabilidades y la resolución de retos complejos. Siempre en busca del siguiente 0-day.' : 'Offensive security specialist with a passion for vulnerability analysis and solving complex challenges. Always hunting for the next 0-day.' ?>
                </p>
                
                <div class="action-group">
                    <a href="generate-cv.php" class="btn-deploy" style="background: var(--cyan); color: #000; font-weight: bold;">
                        📄 <?= $lang === 'es' ? 'DESCARGAR CV' : 'DOWNLOAD CV' ?>
                    </a>
                    <a href="https://linkedin.com/in/tu-perfil" target="_blank" class="btn-deploy" style="border-color: #0077b5; color: #0077b5;">
                        LINKEDIN
                    </a>
                </div>
            </div>
        </section>

        <h2 class="section-title"><?= $lang === 'es' ? 'Arsenal Técnico' : 'Technical Arsenal' ?></h2>
        <div class="skill-grid">
            <div class="skill-item">
                <div class="skill-info"><span>NMAP & RECON</span><span>85%</span></div>
                <div class="skill-bar-bg"><div class="skill-bar-fill" style="width: 85%;"></div></div>
            </div>
            <div class="skill-item">
                <div class="skill-info"><span>BURP SUITE</span><span>70%</span></div>
                <div class="skill-bar-bg"><div class="skill-bar-fill" style="width: 70%;"></div></div>
            </div>
            <div class="skill-item">
                <div class="skill-info"><span>PYTHON / BASH</span><span>75%</span></div>
                <div class="skill-bar-bg"><div class="skill-bar-fill" style="width: 75%;"></div></div>
            </div>
            <div class="skill-item">
                <div class="skill-info"><span>KALI / PARROT OS</span><span>90%</span></div>
                <div class="skill-bar-bg"><div class="skill-bar-fill" style="width: 90%;"></div></div>
            </div>
        </div>

        <h2 class="section-title"><?= $lang === 'es' ? 'Trayectoria' : 'Experience' ?></h2>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-date">2023 - <?= $lang === 'es' ? 'PRESENTE' : 'PRESENT' ?></div>
                <div class="timeline-title">CyberEscudo Platform</div>
                <div class="timeline-desc"><?= $lang === 'es' ? 'Desarrollo integral de plataforma CTF educativa con 17 misiones operativas.' : 'Full development of an educational CTF platform with 17 operational missions.' ?></div>
            </div>
            <div class="timeline-item">
                <div class="timeline-date">2022</div>
                <div class="timeline-title">Certificación eJPT</div>
                <div class="timeline-desc">Junior Penetration Tester Certification - INE Security.</div>
            </div>
        </div>

    </div>
</main>

<?php require __DIR__ . '/templates/footer.php'; ?>