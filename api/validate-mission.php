<?php
// api/validate-mission.php
header('Content-Type: application/json');

// Las claves deben coincidir EXACTAMENTE con el ID que el jugador pone en la terminal
$flags = [
    // Tus misiones anteriores
    'OP-GHOST-TRAFFIC' => 'FLAG{DNS_tunneling_detected_2026}',
    'OP-SECURE-DEV'    => 'FLAG{python_eval_is_evil_99x}',
    'OP-DEEP-STATE'    => 'FLAG{steg_hidden_data_recovered}',
    'OP-FOOTPRINT'     => 'FLAG{bash_history_leaks_secrets_88}',
    'OP-ROBOTS'        => 'FLAG{r0b0ts_gu4rd_s3cr3ts}',
    'OP-SOURCE'        => 'FLAG{h1dd3n_1n_pl41n_s1ght}',
    'OP-HEADERS'       => 'FLAG{h34d3rs_4r3_t4lk4t1v3}',
    'OP-B64-DECODE'    => 'FLAG{b4s364_1s_n0t_encryp710n}',
    'OP-COOKIE-MONSTER' => 'FLAG{c00k13s_kn0w_3v3ryth1ng}',
    'OP-DOUBLE-CIPHER' => 'FLAG{d0ubl3_c1ph3r}',
    'OP-JWT-TOKEN'     => 'FLAG{jwt_p4yl04d_3xp0s3d}',
    'OP-BROKEN-HASH'   => 'FLAG{md5_1s_d34d_us3_bcrpyt}'
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