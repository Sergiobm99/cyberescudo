<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle = 'OP: XOR-CRYPTO — CyberEscudo';
require __DIR__ . '/../templates/header.php';
?>
<main class="content-page">
    <div class="briefing-container" style="border-top: 4px solid #ff2a2a;">
        <div class="classification" style="color:#ff2a2a; border-color:#ff2a2a;"><?= $lang === 'es' ? 'NIVEL: AVANZADO' : 'LEVEL: ADVANCED' ?></div>
        <h1 class="mission-title">OP: XOR-CRYPTO</h1>
        <div class="intel-block" style="border-color:#ff2a2a; background:rgba(255,42,42,0.02);">
            <strong style="color:#ff2a2a;"><?= $lang === 'es' ? '[ OFUSCACIÓN MATEMÁTICA ]' : '[ MATHEMATICAL OBFUSCATION ]' ?></strong><br><br>
            <?= $lang === 'es' ? 'La operación XOR es simétrica. Es muy usada por el malware moderno para evadir antivirus ofuscando sus cadenas de texto.' : 'The XOR operation is symmetric. It is heavily used by modern malware to evade antivirus by obfuscating its text strings.' ?><br><br>
            <strong><?= $lang === 'es' ? 'Objetivo:' : 'Objective:' ?></strong> <?= $lang === 'es' ? 'El siguiente array está cifrado con XOR. La clave (key) es un número entero igual al número de letras de la palabra "flag".' : 'The following array is XOR-encrypted. The key is an integer equal to the number of letters in the word "flag".' ?>
        </div>
        <div style="margin: 2rem 0; padding: 1.5rem; background: #050505; border: 1px dashed #333; overflow-x: auto;">
            <code style="color: #ff2a2a; font-family: var(--mono);">
                bytes = [0x42, 0x48, 0x45, 0x43, 0x7f, 0x7c, 0x34, 0x76, 0x5b, 0x74, 0x73, 0x6a, 0x60, 0x79]
            </code>
        </div>
        <div style="margin-top: 2rem; text-align: center; font-family: var(--mono);">
            <p style="color: #666; font-size: 0.8rem;"><?= $lang === 'es' ? 'Pista: Un simple bucle FOR en Python te dará la respuesta.' : 'Hint: A simple FOR loop in Python will give you the answer.' ?></p>
            <code style="color: #ff2a2a;">submit OP-XOR-CRYPTO FLAG{...}</code>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../templates/footer.php'; ?>