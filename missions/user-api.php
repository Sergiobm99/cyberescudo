<?php
// Endpoint intencionadamente vulnerable para la misión OP: IDOR-ACCESS
header('Content-Type: application/json; charset=utf-8');

$userId = (int) ($_GET['user_id'] ?? 0);

$users = [
    1  => ['id' => 1,  'username' => 'alice',   'role' => 'user'],
    2  => ['id' => 2,  'username' => 'bob',     'role' => 'user'],
    7  => [
        'id'       => 7,
        'username' => 'administrator',
        'role'     => 'admin',
        'flag'     => 'FLAG{1d0r_byp4ss_l1k3_4_b0ss}',
        'note'     => 'TOP SECRET'
    ],
    42 => ['id' => 42, 'username' => 'you',     'role' => 'user', 'note' => 'Your profile.']
];

if ($userId === 0) {
    echo json_encode(['error' => 'Missing user_id parameter. Ex: ?user_id=42']);
    exit;
}

if (!isset($users[$userId])) {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
    exit;
}

// Retorna los datos sin comprobar permisos (Vulnerabilidad IDOR)
echo json_encode($users[$userId], JSON_PRETTY_PRINT);