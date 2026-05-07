<?php
// api/validate-mission.php
header('Content-Type: application/json');

$flags = [
    'ghost_traffic' => 'FLAG{DNS_tunneling_detected_2024}',
    'logic_bomb'    => 'FLAG{python_eval_is_evil_99x}',
    'phishing'      => 'FLAG{phishing_triage_expert}'
];

$input = json_decode(file_get_contents('php://input'), true);
$missionId = $input['missionId'] ?? '';
$userFlag = $input['flag'] ?? '';

if (isset($flags[$missionId]) && $flags[$missionId] === $userFlag) {
    echo json_encode([
        'status' => 'success',
        'message' => 'MISIÓN COMPLETADA. Acceso nivel superior concedido.',
        'xp' => 500
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'FLAG INCORRECTA. Intento registrado en los logs del sistema.'
    ]);
}