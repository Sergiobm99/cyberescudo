<?php
// api/validate-mission.php
header('Content-Type: application/json');

// Las claves deben coincidir EXACTAMENTE con el ID que el jugador pone en la terminal
$flags = [
    'OP-GHOST-TRAFFIC' => 'FLAG{DNS_tunneling_detected_2026}',
    'OP-SECURE-DEV'    => 'FLAG{python_eval_is_evil_99x}', // Ya preparado para la misión 2
    'OP-PHISHING'      => 'FLAG{phishing_triage_expert}'
];

$input = json_decode(file_get_contents('php://input'), true);
$missionId = $input['missionId'] ?? '';
$userFlag = $input['flag'] ?? '';

// Comprobamos si la misión existe en nuestro array y si la flag coincide
if (isset($flags[$missionId]) && $flags[$missionId] === $userFlag) {
    echo json_encode([
        'status' => 'success',
        'message' => 'MISIÓN COMPLETADA. Operación neutralizada con éxito.',
        'xp' => 500
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'FLAG INCORRECTA. Intento registrado en los logs del sistema.'
    ]);
}