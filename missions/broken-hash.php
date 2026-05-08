<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle = 'OP: BROKEN-HASH — CyberEscudo';
require __DIR__ . '/../templates/header.php';
?>

<main class="content-page">
    <div class="briefing-container" style="border-top: 4px solid var(--cyan);">
        <div class="classification"><?= $lang === 'es' ? 'NIVEL: PRINCIPIANTE' : 'LEVEL: BEGINNER' ?></div>
        <h1 class="mission-title">OP: BROKEN-HASH</h1>
        
        <div class="intel-block">
            <strong><?= $lang === 'es' ? '[ CRACKEO DE CONTRASEÑAS ]' : '[ PASSWORD CRACKING ]' ?></strong><br><br>
            <?= $lang === 'es' ? 'MD5 es un algoritmo de hashing criptográficamente roto. Al no usar \'salt\', los atacantes usan bases de datos gigantes (Rainbow Tables) para revertirlos instantáneamente.' : 'MD5 is a cryptographically broken hashing algorithm. By not using a \'salt\', attackers use giant databases (Rainbow Tables) to revert them instantly.' ?><br><br>
            <strong><?= $lang === 'es' ? 'Objetivo:' : 'Objective:' ?></strong> <?= $lang === 'es' ? 'En una fuga de datos hemos encontrado el siguiente hash MD5. Úsalo para certificar la flag.' : 'In a data leak we found the following MD5 hash. Use it to certify the flag.' ?>
        </div>

        <div style="text-align: center; margin: 2rem 0; padding: 1.5rem; background: #050505; border: 1px dashed #333;">
            <code style="color: var(--cyan); font-size: 1.2rem; word-break: break-all;">482c811da5d5b4bc6d497ffa98491e38</code>
        </div>

        <div style="margin-top: 2rem; text-align: center; font-family: var(--mono);">
            <p style="color: #666; font-size: 0.8rem; margin-bottom: 1rem;">
                <?= $lang === 'es' ? 'Pista: Utiliza la herramienta de crackeo de hashes de tu panel superior.' : 'Hint: Use the hash cracking tool from your top panel.' ?>
            </p>
            <strong style="color: var(--cyan);">submit OP-BROKEN-HASH FLAG{md5_1s_d34d_us3_bcrpyt}</strong>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>