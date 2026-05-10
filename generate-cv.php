<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;

$dompdf = new Dompdf();

// Aquí diseñas tu CV en HTML puro
$html = '
<style>
    body { font-family: sans-serif; color: #333; }
    h1 { color: #006699; }
    .header { text-align: center; border-bottom: 2px solid #eee; padding-bottom: 20px; }
</style>
<div class="header">
    <h1>Sergio Belmonte</h1>
    <p>Pentester Jr | Bug Hunter</p>
</div>
<h3>Experiencia</h3>
<ul>
    <li>Creador de CyberEscudo CTF (2023)</li>
</ul>
';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Envía el PDF al navegador
$dompdf->stream("CV_Sergio_Belmonte.pdf", array("Attachment" => true));
?>