<?php
/**
 * CYBERESCUDO - MITRE ATT&CK SYNC ENGINE (MODO PARANOIA / DEBUG)
 */

ini_set('memory_limit', '1024M'); 
set_time_limit(300);              

$cacheFile = __DIR__ . '/../assets/data/mitre-cache.json';

echo "<h3>[CyberEscudo] Motor de Sincronización MITRE ATT&CK</h3>";
echo "1. Conectando con GitHub de MITRE...<br>";

$url = "https://raw.githubusercontent.com/mitre/cti/master/enterprise-attack/enterprise-attack.json";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$stixRaw = curl_exec($ch);

if(curl_errno($ch)) {
    die("[-] <b style='color:red;'>Error cURL:</b> " . curl_error($ch));
}
curl_close($ch);

echo "2. Descarga completada. Analizando estructura STIX 2.1...<br>";
$data = json_decode($stixRaw, true);

if (!isset($data['objects'])) {
    die("[-] <b style='color:red;'>Error FATAL:</b> El archivo JSON no es un STIX válido o faltó memoria RAM para decodificarlo.");
}

$tactics = [];
$techniques = [];

function getMitreId($obj) {
    if (!isset($obj['external_references'])) return '';
    foreach ($obj['external_references'] as $ref) {
        if (isset($ref['source_name']) && $ref['source_name'] === 'mitre-attack') {
            return $ref['external_id'] ?? '';
        }
    }
    return '';
}

foreach ($data['objects'] as $obj) {
    $type = $obj['type'] ?? '';
    
    // Tácticas
    if ($type === 'x-mitre-tactic') {
        $extId = getMitreId($obj);
        if ($extId !== '') {
            $tactics[] = [
                'id' => $extId,
                'name' => $obj['name'] ?? 'Unknown',
                'shortname' => $obj['x_mitre_shortname'] ?? ''
            ];
        }
    }
    
    // Técnicas
    if ($type === 'attack-pattern' && empty($obj['revoked']) && empty($obj['x_mitre_deprecated'])) {
        $extId = getMitreId($obj);
        
        if ($extId !== '') {
            $tacticShortname = '';
            if (!empty($obj['kill_chain_phases'])) {
                $tacticShortname = $obj['kill_chain_phases'][0]['phase_name'] ?? '';
            }
            
            if ($tacticShortname !== '') {
                $techniques[] = [
                    'id' => $extId,
                    'name' => $obj['name'] ?? 'Unknown',
                    'tactic_shortname' => $tacticShortname,
                    'desc' => strip_tags(substr($obj['description'] ?? 'No description.', 0, 300)) . '...',
                    'platforms' => $obj['x_mitre_platforms'] ?? ['General']
                ];
            }
        }
    }
}

echo "3. Empaquetando datos optimizados...<br>";
$finalCache = [
    'last_sync' => date('Y-m-d\TH:i:s\Z'),
    'tactics' => $tactics,
    'techniques' => $techniques
];

// Comprobación ESTRICTA de conversión JSON (Ignora caracteres corruptos)
// Quitamos JSON_PRETTY_PRINT para que el archivo sea más pequeño y no sature la memoria
$jsonContent = json_encode($finalCache, JSON_INVALID_UTF8_SUBSTITUTE);

if ($jsonContent === false) {
    die("[-] <b style='color:red;'>Error CRÍTICO al codificar JSON:</b> " . json_last_error_msg());
}

if (!is_dir(dirname($cacheFile))) {
    if(!mkdir(dirname($cacheFile), 0755, true)) {
         die("[-] <b style='color:red;'>Error de Permisos:</b> No se pudo crear la carpeta /assets/data/.");
    }
}

echo "4. Guardando archivo en disco...<br>";

// Usamos file_put_contents directamente que es más seguro para archivos de una sola escritura
$bytesWritten = file_put_contents($cacheFile, $jsonContent, LOCK_EX);

if ($bytesWritten === false) {
    die("[-] <b style='color:red;'>Error de Permisos CRÍTICO:</b> PHP no tiene permisos para escribir en el archivo: <br><code>$cacheFile</code><br>Solución: Da permisos 755 o 777 a la carpeta /assets/data/ en Plesk.");
}

$megabytes = round($bytesWritten / 1024 / 1024, 2);

echo "<br><h3 style='color:green;'>✅ SINCRONIZACIÓN EXITOSA</h3>";
echo "- Tácticas mapeadas: <b>" . count($tactics) . "</b><br>";
echo "- Técnicas mapeadas: <b>" . count($techniques) . "</b><br>";
echo "- Tamaño del archivo caché: <b style='color:blue;'>" . $megabytes . " MB (" . $bytesWritten . " bytes)</b><br>";
echo "<br><i>Ya puedes volver a la herramienta y recargar la página (Ctrl+F5).</i>";
?>