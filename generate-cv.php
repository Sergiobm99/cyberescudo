<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$dompdf = new Dompdf($options);

$lang = isset($_GET['lang']) && $_GET['lang'] === 'en' ? 'en' : 'es';

$t = [
    'exp_title' => $lang === 'es' ? 'Experiencia Laboral' : 'Work Experience',
    'job1_title' => $lang === 'es' ? 'Analista de Ciberseguridad' : 'Cybersecurity Analyst',
    'job2_title' => $lang === 'es' ? 'Técnico de Campo' : 'Field Technician',
    'edu_title' => $lang === 'es' ? 'Educación y Formación' : 'Education & Training',
    'edu3' => $lang === 'es' ? 'Grado Medio en Sistemas Microinformáticos y Redes (SMR)' : 'Vocational Degree in Microcomputer Systems and Networks',
];

$html = '
<!DOCTYPE html>
<html lang="' . $lang . '">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; line-height: 1.4; }
        h1 { font-size: 24px; color: #111; margin-bottom: 5px; }
        h2 { color: #00ff41; border-bottom: 1px solid #eee; text-transform: uppercase; font-size: 14px; margin-top: 20px; }
        .date { color: #888; font-size: 11px; }
        .company { font-weight: bold; }
    </style>
</head>
<body>
    <div style="text-align: center; border-bottom: 2px solid #00ff41; padding-bottom: 10px;">
        <h1>Sergio Belmonte Morales</h1>
        <div>sergiobelmor99@gmail.com | +34 611691046</div>
    </div>

    <h2>' . $t['exp_title'] . '</h2>
    <div>
        <div class="company">' . $t['job1_title'] . ' - Midway Technologies</div>
        <div class="date">01/06/2022 - 05/04/2024</div>
    </div>
    <div style="margin-top: 10px;">
        <div class="company">' . $t['job2_title'] . ' - Magtel Operaciones S.L.U.</div>
        <div class="date">Córdoba, España</div>
    </div>

    <h2>' . $t['edu_title'] . '</h2>
    <div style="margin-bottom: 8px;">
        <strong>' . ($lang === "es" ? "Grado Superior ASIR" : "Associate Degree ASIR") . '</strong><br>
        IES Triana | 2019 - 2021
    </div>
    <div style="margin-bottom: 8px;">
        <strong>' . $t['edu3'] . '</strong><br>
        IES Fidiana | 2016 - 2018
    </div>

    <h2>Certificaciones</h2>
    <ul>
        <li>eCPPT - Certified Professional Penetration Tester (INE)</li>
        <li>eJPT - Junior Penetration Tester (INE)</li>
        <li>SC-200 / SC-900 (Microsoft)</li>
    </ul>
</body>
</html>
';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("CV_Sergio_Belmonte.pdf", array("Attachment" => true));
?>