<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = $lang === 'es' ? 'Reto CTF #20: PCAP Forensics — CyberEscudo' : 'CTF Challenge #20: PCAP Forensics — CyberEscudo';
$pageDescription = $lang === 'es' ? 'Simulador de construcción de filtros de Wireshark.' : 'Wireshark filter construction simulator.';
$current_page = 'ctf/ctf-20.php';
require __DIR__ . '/../templates/header.php';

$feedback = "";
$flag = "";
$esExito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filter'])) {
    $filter = trim($_POST['filter']);
    
    // Validar sintaxis del Display Filter
    // Objetivo: ip.src == 192.168.1.100 and tcp.dstport == 443 and tcp.flags.syn == 1
    
    // 1. Origen IP
    $hasIpSrc = preg_match('/ip\.src\s*(==|eq)\s*192\.168\.1\.100/i', $filter);
    
    // 2. Puerto destino (tcp.dstport o tcp.port)
    $hasPort = preg_match('/tcp\.(dst)?port\s*(==|eq)\s*443/i', $filter);
    
    // 3. Bandera SYN activada
    $hasSyn = preg_match('/tcp\.flags\.syn\s*(==|eq)\s*1/i', $filter);
    
    // 4. Uso de operadores lógicos (and o &&)
    $hasLogicalAnd = preg_match('/\s+(and|&&)\s+/i', $filter);

    if ($hasIpSrc && $hasPort && $hasSyn && $hasLogicalAnd) {
        $esExito = true;
        $feedback = $lang === 'es' 
            ? "¡Filtro perfecto! Has aislado exitosamente el paquete de inicio de conexión (SYN) de entre 2 millones de paquetes de ruido." 
            : "Perfect filter! You successfully isolated the connection initiation (SYN) packet from among 2 million noise packets.";
        $flag = "FLAG{wireshark_pcap_hunter}";
    } else {
        $errores = [];
        if (!$hasIpSrc) $errores[] = $lang === 'es' ? "Filtro de IP origen incorrecto (ip.src == 192.168.1.100)" : "Incorrect source IP filter (ip.src == 192.168.1.100)";
        if (!$hasPort) $errores[] = $lang === 'es' ? "Filtro de puerto TCP incorrecto (tcp.dstport == 443)" : "Incorrect TCP port filter (tcp.dstport == 443)";
        if (!$hasSyn) $errores[] = $lang === 'es' ? "Falta filtrar por el flag SYN activo (tcp.flags.syn == 1)" : "Missing active SYN flag filter (tcp.flags.syn == 1)";
        if (!$hasLogicalAnd) $errores[] = $lang === 'es' ? "Debes concatenar los filtros usando 'and' o '&&'" : "You must concatenate filters using 'and' or '&&'";
        
        $feedback = "[ERROR SINTAXIS BPF] " . implode(" | ", $errores);
    }
}
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 900px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// <?= $lang === 'es' ? 'RETO CTF 20' : 'CTF CHALLENGE 20' ?></span>
        <h1 style="text-align: center; margin-bottom: 2rem;"><?= $lang === 'es' ? 'Network Forensics: The Needle in the Haystack' : 'Network Forensics: The Needle in the Haystack' ?></h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <div style="background: #111; padding: 1.5rem; border-left: 4px solid var(--cyan); margin-bottom: 2rem;">
                <h3 style="color: var(--cyan); margin-top: 0;">🦈 <?= $lang === 'es' ? 'Analizando evidencia PCAP' : 'Analyzing PCAP Evidence' ?></h3>
                <p style="color: var(--white); font-size: 0.95rem; line-height: 1.6; margin-bottom: 0;">
                    <?= $lang === 'es' ? 'Has abierto <code>evidence_01.pcap</code>. El archivo contiene 2.4 millones de paquetes. Necesitas aislar un evento específico: <br><br>Construye un único <strong>Filtro de Visualización (Display Filter)</strong> que te muestre los paquetes cuyo <strong>Origen</strong> sea la IP <code>192.168.1.100</code>, <strong>Y</strong> cuyo puerto de <strong>Destino TCP</strong> sea el <code>443</code>, <strong>Y</strong> que representen el inicio de la conexión (El flag <strong>TCP SYN</strong> activado a <code>1</code>).' : 'You have opened <code>evidence_01.pcap</code>. The file contains 2.4 million packets. You need to isolate a specific event: <br><br>Construct a single <strong>Display Filter</strong> that shows packets where the <strong>Source IP</strong> is <code>192.168.1.100</code>, <strong>AND</strong> the <strong>TCP Destination Port</strong> is <code>443</code>, <strong>AND</strong> they represent the connection start (<strong>TCP SYN</strong> flag set to <code>1</code>).' ?>
                </p>
            </div>
            
            <form method="POST" action="">
                <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">
                    Wireshark Display Filter:
                </label>
                <div style="display: flex; align-items: center; background: #fff; padding: 0.2rem; border-radius: 4px; margin-bottom: 1.5rem;">
                    <span style="color: #666; margin-left: 10px; margin-right: 10px; font-weight: bold; font-family: sans-serif;">Filter:</span>
                    <input type="text" name="filter" class="cyber-input" style="flex: 1; border: none; background: #fff; color: #000; padding: 8px; box-shadow: none; border-radius: 3px;" placeholder="ip.addr == ... and ..." autocomplete="off" value="<?= htmlspecialchars($_POST['filter'] ?? '') ?>">
                </div>
                
                <button type="submit" style="width: 100%; background: var(--cyan); border: none; color: #000; padding: 15px; font-family: var(--mono); font-weight: bold; cursor: pointer; font-size: 1rem; transition: background 0.3s;">
                    <?= $lang === 'es' ? 'APLICAR FILTRO' : 'APPLY FILTER' ?>
                </button>
            </form>

            <?php if($feedback !== ""): ?>
                <div style="margin-top: 2rem; padding: 1rem; background: <?= $esExito ? 'rgba(0,255,0,0.1)' : 'rgba(255,42,42,0.1)' ?>; border: 1px solid <?= $esExito ? '#00ff00' : '#ff2a2a' ?>; font-family: var(--mono); font-size: 0.9rem; color: <?= $esExito ? '#00ff00' : '#ff2a2a' ?>; line-height: 1.5;">
                    <?= $feedback ?>
                </div>
            <?php endif; ?>

            <?php if($esExito): ?>
                <div style="margin-top: 1.5rem; text-align: center; animation: pulse 2s infinite;">
                    <h3 style="color: #00ff00; margin: 1rem 0; font-family: var(--mono); letter-spacing: 2px;"><?= $flag ?></h3>
                    <p style="margin:0; font-size: 0.85rem; color: var(--cyan);">
                        <?= $lang === 'es' ? 'Carga tu medalla forense en la terminal principal:' : 'Load your forensics medal in the main terminal:' ?> <strong>submit <?= $flag ?></strong>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>