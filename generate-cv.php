<?php
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$dompdf = new Dompdf($options);

// Detectar el idioma pasado por la URL (por defecto español)
$lang = isset($_GET['lang']) && $_GET['lang'] === 'en' ? 'en' : 'es';

// Textos dinámicos basados en el idioma
$t = [
    'title' => $lang === 'es' ? 'Perfil Profesional' : 'Professional Profile',
    'profile' => $lang === 'es' 
        ? 'Analista de Ciberseguridad con sólida experiencia en la gestión de operaciones de seguridad (SOC) y hardening de infraestructuras. Especializado en el ecosistema de Microsoft 365 y Azure Sentinel, con un enfoque ofensivo respaldado por la certificación eCPPT. Experto en detección de amenazas mediante KQL y respuesta ante incidentes.' 
        : 'Cybersecurity Analyst with solid experience in Security Operations Center (SOC) management and infrastructure hardening. Specialized in the Microsoft 365 and Azure Sentinel ecosystem, with an offensive security mindset backed by the eCPPT certification. Expert in threat detection using KQL and incident response.',
    'exp_title' => $lang === 'es' ? 'Experiencia Laboral' : 'Work Experience',
    'job1_title' => $lang === 'es' ? 'Analista de Ciberseguridad' : 'Cybersecurity Analyst',
    'job1_desc' => $lang === 'es' 
        ? '<strong>Detección y Respuesta (SOC):</strong> Administración de Azure Sentinel, creando alertas personalizadas y Playbooks mediante KQL. Análisis de correo con Abnormal Security.<br><strong>Gestión de Endpoints:</strong> Despliegue de Microsoft Defender for Endpoint e Intune. Políticas de Endpoint Hardening.<br><strong>Seguridad Ofensiva:</strong> Cyber Phishing (Gophish) y gestión de vulnerabilidades (OpenVAS, Nessus).<br><strong>Infraestructura:</strong> Active Directory, Azure AD, SCCM y Firewalls (Palo Alto, Cisco).'
        : '<strong>Detection & Response (SOC):</strong> Administration of Azure Sentinel, creating custom analytic rules and Playbooks using KQL. Malicious email analysis with Abnormal Security.<br><strong>Endpoint Management:</strong> Deployment of Microsoft Defender for Endpoint and Intune. Endpoint Hardening policies.<br><strong>Offensive Security:</strong> Cyber Phishing (Gophish) and vulnerability management (OpenVAS, Nessus).<br><strong>Infrastructure:</strong> Active Directory, Azure AD, SCCM, and Firewalls (Palo Alto, Cisco).',
    'cert_title' => $lang === 'es' ? 'Certificaciones' : 'Certifications',
    'edu_title' => $lang === 'es' ? 'Educación y Formación' : 'Education & Training',
    'edu1' => $lang === 'es' ? 'Curso de Especialización en Ciberseguridad en Entornos TI' : 'Cybersecurity Specialization Course in IT Environments',
    'edu2' => $lang === 'es' ? 'Grado Superior en Administración de Sistemas Informáticos en Red' : 'Associate Degree in Network Computer Systems Administration (ASIR)',
    'skills_title' => $lang === 'es' ? 'Capacidades Técnicas' : 'Technical Skills',
];

$html = '
<!DOCTYPE html>
<html lang="' . $lang . '">
<head>
    <meta charset="UTF-8">
    <title>CV Sergio Belmonte</title>
    <style>
        body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; color: #333; line-height: 1.4; margin: 30px; font-size: 13px; }
        .header { text-align: center; border-bottom: 3px solid #00ff41; padding-bottom: 15px; margin-bottom: 20px; }
        h1 { color: #111; margin: 0; font-size: 28px; text-transform: uppercase; letter-spacing: 1px; }
        .subtitle { color: #666; font-size: 16px; margin-top: 5px; }
        .contact { font-size: 12px; color: #555; margin-top: 10px; }
        h2 { color: #00ff41; border-bottom: 1px solid #ddd; padding-bottom: 3px; margin-top: 20px; text-transform: uppercase; font-size: 16px; }
        .item-title { font-weight: bold; color: #222; font-size: 14px;}
        .item-subtitle { color: #555; font-style: italic; font-size: 13px; }
        .item-date { color: #888; font-size: 12px; margin-bottom: 5px; }
        .item-desc { margin-bottom: 15px; }
        .skills { list-style: none; padding: 0; }
        .skills li { display: inline-block; background: #eee; padding: 4px 8px; margin: 0 4px 4px 0; border-radius: 3px; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        td { padding: 4px 0; vertical-align: top; }
        .td-date { width: 80px; color: #888; font-size: 12px;}
    </style>
</head>
<body>

    <div class="header">
        <h1>Sergio Belmonte Morales</h1>
        <div class="subtitle">Cybersecurity Analyst | SOC Operator | Pentester</div>
        <div class="contact">
            +34 611691046 | sergiobelmor99@gmail.com | linkedin.com/in/sergio-belmonte-morales99<br>
            Villa del Río (Córdoba), España
        </div>
    </div>

    <h2>' . $t['title'] . '</h2>
    <p class="item-desc">' . $t['profile'] . '</p>

    <h2>' . $t['exp_title'] . '</h2>
    <div class="item-title">' . $t['job1_title'] . '</div>
    <div class="item-subtitle">Midway Technologies | Sevilla</div>
    <div class="item-date">01/06/2022 - 05/04/2024</div>
    <p class="item-desc">' . $t['job1_desc'] . '</p>

    <h2>' . $t['cert_title'] . '</h2>
    <table>
        <tr><td class="td-date">03/2026</td><td><strong>eCPPT</strong> - Certified Professional Penetration Tester (INE)</td></tr>
        <tr><td class="td-date">10/2025</td><td><strong>eJPT</strong> - Junior Penetration Tester (INE)</td></tr>
        <tr><td class="td-date">10/2023</td><td><strong>SC-200</strong> - Microsoft Certified: Security Operations Analyst Associate</td></tr>
        <tr><td class="td-date">12/2022</td><td><strong>SC-900</strong> - Microsoft Security, Compliance, and Identity Fundamentals</td></tr>
        <tr><td class="td-date">04/2025</td><td><strong>Aptis ESOL B2</strong> - Upper Intermediate English (British Council)</td></tr>
    </table>

    <h2>' . $t['edu_title'] . '</h2>
    <table>
        <tr><td class="td-date">2021 - 2022</td><td><strong>' . $t['edu1'] . '</strong><br>IES Punta del verde | Sevilla</td></tr>
        <tr><td class="td-date">2019 - 2021</td><td><strong>' . $t['edu2'] . '</strong><br>IES Triana | Sevilla</td></tr>
    </table>

    <h2>' . $t['skills_title'] . '</h2>
    <ul class="skills">
        <li>Azure Sentinel</li>
        <li>KQL</li>
        <li>Microsoft 365 Defender</li>
        <li>Microsoft Intune</li>
        <li>Active Directory</li>
        <li>OpenVAS / Nessus</li>
        <li>Gophish</li>
        <li>PowerShell Scripting</li>
        <li>Jira / OTRS</li>
        <li>Palo Alto / Cisco Firewalls</li>
    </ul>

</body>
</html>
';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$filename = $lang === 'es' ? "CV_Sergio_Belmonte_ES.pdf" : "CV_Sergio_Belmonte_EN.pdf";
$dompdf->stream($filename, array("Attachment" => true));
?>