<?php
// OP: SHADOW PATH - Vulnerable Download Endpoint
// Asegúrate de inicializar $lang aquí también si es necesario

if (!isset($lang)) { $lang = 'es'; } // Fallback

if (isset($_GET['file'])) {
    $file = $_GET['file'];
    
    $base_dir = "reports/";
    $path = $base_dir . $file;

    if (file_exists($path)) {
        header('Content-Type: text/plain');
        readfile($path);
    } else {
        echo $lang === 'es' 
            ? "[!] ERROR DEL SISTEMA: Archivo no encontrado en el directorio de reportes." 
            : "[!] SYSTEM ERROR: File not found in the reports directory.";
    }
} else {
    echo $lang === 'es' 
        ? "[!] PARÁMETRO FALTANTE: Especifique un archivo." 
        : "[!] MISSING PARAMETER: Specify a file.";
}
?>