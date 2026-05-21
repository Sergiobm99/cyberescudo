<?php
/**
 * CYBERESCUDO - CRON SCRIPT (ETL)
 * Descarga los CVEs en segundo plano y actualiza el caché local.
 */

// Ruta absoluta donde guardaremos los datos
$cacheFile = __DIR__ . '/../assets/data/cve-cache.json';

// API Oficial y Actualizada de Red Hat (Sistema Hydra)
$url = "https://access.redhat.com/hydra/rest/securitydata/cve.json?per_page=20";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'CyberEscudo-SOC-CronJob/1.0');
curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Tiempo extra de cortesía para la conexión

echo "Iniciando descarga de Inteligencia de Amenazas desde Red Hat...\n";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200 && $response) {
    // Verificamos que sea un JSON válido
    json_decode($response);
    if(json_last_error() === JSON_ERROR_NONE) {
        // Guardamos el archivo sobrescribiendo el viejo
        file_put_contents($cacheFile, $response);
        echo "[ÉXITO] Base de datos CVE actualizada correctamente a las " . date('Y-m-d H:i:s') . ".\n";
    } else {
        echo "[ERROR] La API respondió, pero el JSON estaba corrupto.\n";
    }
} else {
    echo "[ERROR] No se pudo contactar con la API. Código HTTP: " . $httpCode . ".\n";
}
?>