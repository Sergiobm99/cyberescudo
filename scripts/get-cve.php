<?php
/**
 * CYBERESCUDO - CVE API PROXY
 * Puente hacia la API oficial de MITRE CVE (Rápida y sin bloqueos)
 */
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    die(json_encode(['error' => 'No ID provided']));
}

// Limpiamos el ID
$cveId = preg_replace('/[^a-zA-Z0-9-]/', '', $_GET['id']);

// API Oficial de MITRE CVE
$url = "https://cveawg.mitre.org/api/cve/" . $cveId;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'CyberEscudo-SOC-Platform/3.0');
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode >= 400 || !$response || trim($response) === "null") {
    http_response_code(404);
    echo json_encode(['error' => 'API Error']);
} else {
    echo $response;
}
?>