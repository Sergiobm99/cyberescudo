<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle = $lang === 'es' ? 'Base de Datos de Inteligencia — CyberEscudo' : 'Intel Database — CyberEscudo';
require __DIR__ . '/../templates/header.php';
?>

<style>
    .writeup-container { max-width: 900px; margin: 4rem auto; padding: 2rem; }
    .writeup-card { background: rgba(10, 10, 10, 0.8); border: 1px solid #222; padding: 2rem; margin-bottom: 2rem; border-left: 4px solid var(--cyan); }
    .writeup-card h2 { font-family: var(--mono); color: var(--cyan); font-size: 1.2rem; margin-bottom: 0.5rem; text-transform: uppercase; }
    .writeup-card h3 { font-family: var(--mono); color: #fff; font-size: 1rem; margin: 1.5rem 0 0.5rem 0; }
    .writeup-card p, .writeup-card li { color: #aaa; font-size: 0.95rem; line-height: 1.6; margin-bottom: 0.5rem; }
    .flag-spoiler { background: #111; padding: 0.5rem 1rem; border: 1px dashed #444; color: #444; cursor: pointer; display: inline-block; margin-top: 1rem; font-family: var(--mono); transition: 0.3s; user-select: none; }
    .flag-spoiler:hover { color: var(--cyan); border-color: var(--cyan); }
    .back-btn { display: inline-block; margin-bottom: 2rem; color: var(--gray); text-decoration: none; font-family: var(--mono); font-size: 0.8rem; }
    .back-btn:hover { color: #fff; }
</style>

<main class="content-page">
    <div class="writeup-container">
        <a href="index.php" class="back-btn">← <?= $lang === 'es' ? 'VOLVER AL CENTRO DE OPERACIONES' : 'BACK TO OPS CENTER' ?></a>
        
        <h1 style="font-family: var(--mono); color: #fff; margin-bottom: 3rem;">
            <?= $lang === 'es' ? 'ARCHIVO DE SOLUCIONARIOS (WRITE-UPS)' : 'INTELLIGENCE ARCHIVE (WRITE-UPS)' ?>
        </h1>

        <div class="writeup-card">
            <h2>01. OP: GHOST_TRAFFIC</h2>
            <p><strong><?= $lang === 'es' ? 'Vulnerabilidad:' : 'Vulnerability:' ?></strong> DNS Tunneling.</p>
            <h3><?= $lang === 'es' ? 'Solución:' : 'Solution:' ?></h3>
            <ol>
                <li><?= $lang === 'es' ? 'Abre el archivo .pcap en Wireshark.' : 'Open the .pcap file in Wireshark.' ?></li>
                <li><?= $lang === 'es' ? 'Aplica el filtro "dns" para ver solo ese tráfico.' : 'Apply the "dns" filter to isolate traffic.' ?></li>
                <li><?= $lang === 'es' ? 'Busca respuestas DNS con texto inusualmente largo; ahí está la flag.' : 'Look for DNS responses with unusually long text; the flag is there.' ?></li>
            </ol>
            <div class="flag-spoiler" data-flag="FLAG{DNS_tunneling_detected_2026}">[ <?= $lang === 'es' ? 'REVELAR FLAG' : 'REVEAL FLAG' ?> ]</div>
        </div>

        <div class="writeup-card">
            <h2>02. OP: SECURE_DEV</h2>
            <p><strong><?= $lang === 'es' ? 'Vulnerabilidad:' : 'Vulnerability:' ?></strong> Logic Bomb / Insecure eval().</p>
            <h3><?= $lang === 'es' ? 'Solución:' : 'Solution:' ?></h3>
            <ol>
                <li><?= $lang === 'es' ? 'Lee el código Python proporcionado.' : 'Read the provided Python code.' ?></li>
                <li><?= $lang === 'es' ? 'Localiza la función eval(), que ejecuta código de forma insegura.' : 'Locate the eval() function, which executes code insecurely.' ?></li>
                <li><?= $lang === 'es' ? 'Lee el comentario adyacente a la vulnerabilidad para encontrar la flag.' : 'Read the comment adjacent to the vulnerability to find the flag.' ?></li>
            </ol>
            <div class="flag-spoiler" data-flag="FLAG{python_eval_is_evil_99x}">[ <?= $lang === 'es' ? 'REVELAR FLAG' : 'REVEAL FLAG' ?> ]</div>
        </div>

        <div class="writeup-card">
            <h2>03. OP: DEEP_STATE</h2>
            <p><strong><?= $lang === 'es' ? 'Vulnerabilidad:' : 'Vulnerability:' ?></strong> Steganography.</p>
            <h3><?= $lang === 'es' ? 'Solución:' : 'Solution:' ?></h3>
            <ol>
                <li><?= $lang === 'es' ? 'Descarga la imagen.' : 'Download the image.' ?></li>
                <li><?= $lang === 'es' ? 'Usa la terminal: ejecuta "strings imagen.png | grep FLAG".' : 'Use the terminal: run "strings image.png | grep FLAG".' ?></li>
                <li><?= $lang === 'es' ? 'El texto oculto al final del archivo binario se mostrará en pantalla.' : 'The hidden text at the end of the binary file will be displayed.' ?></li>
            </ol>
            <div class="flag-spoiler" data-flag="FLAG{steg_hidden_data_recovered}">[ <?= $lang === 'es' ? 'REVELAR FLAG' : 'REVEAL FLAG' ?> ]</div>
        </div>

        <div class="writeup-card">
            <h2>04. OP: FOOTPRINT</h2>
            <p><strong><?= $lang === 'es' ? 'Vulnerabilidad:' : 'Vulnerability:' ?></strong> Command History Leak.</p>
            <h3><?= $lang === 'es' ? 'Solución:' : 'Solution:' ?></h3>
            <ol>
                <li><?= $lang === 'es' ? 'Abre el archivo attacker_history.txt.' : 'Open the attacker_history.txt file.' ?></li>
                <li><?= $lang === 'es' ? 'Lee los comandos que intentó usar el atacante antes de borrarlos.' : 'Read the commands the attacker tried to use before wiping them.' ?></li>
                <li><?= $lang === 'es' ? 'Uno de los comandos "echo" revela la flag.' : 'One of the "echo" commands reveals the flag.' ?></li>
            </ol>
            <div class="flag-spoiler" data-flag="FLAG{bash_history_leaks_secrets_88}">[ <?= $lang === 'es' ? 'REVELAR FLAG' : 'REVEAL FLAG' ?> ]</div>
        </div>

        <div class="writeup-card">
            <h2>05. OP: ROBOTS</h2>
            <p><strong><?= $lang === 'es' ? 'Vulnerabilidad:' : 'Vulnerability:' ?></strong> Information Disclosure.</p>
            <h3><?= $lang === 'es' ? 'Solución:' : 'Solution:' ?></h3>
            <ol>
                <li><?= $lang === 'es' ? 'Ve a la barra de direcciones de tu navegador.' : 'Go to your browser\'s address bar.' ?></li>
                <li><?= $lang === 'es' ? 'Añade "/robots.txt" al final de la URL del dominio.' : 'Append "/robots.txt" to the end of the domain URL.' ?></li>
                <li><?= $lang === 'es' ? 'Lee el archivo de texto plano para encontrar el secreto.' : 'Read the plain text file to find the secret.' ?></li>
            </ol>
            <div class="flag-spoiler" data-flag="FLAG{r0b0ts_gu4rd_s3cr3ts}">[ <?= $lang === 'es' ? 'REVELAR FLAG' : 'REVEAL FLAG' ?> ]</div>
        </div>

        <div class="writeup-card">
            <h2>06. OP: SOURCE</h2>
            <p><strong><?= $lang === 'es' ? 'Vulnerabilidad:' : 'Vulnerability:' ?></strong> Source Code Comments.</p>
            <h3><?= $lang === 'es' ? 'Solución:' : 'Solution:' ?></h3>
            <ol>
                <li><?= $lang === 'es' ? 'Abre la página principal de CyberEscudo.' : 'Open the CyberEscudo homepage.' ?></li>
                <li><?= $lang === 'es' ? 'Pulsa Ctrl+U para ver el código fuente.' : 'Press Ctrl+U to view the source code.' ?></li>
                <li><?= $lang === 'es' ? 'Haz scroll hasta abajo del todo para encontrar un comentario HTML oculto.' : 'Scroll to the very bottom to find a hidden HTML comment.' ?></li>
            </ol>
            <div class="flag-spoiler" data-flag="FLAG{h1dd3n_1n_pl41n_s1ght}">[ <?= $lang === 'es' ? 'REVELAR FLAG' : 'REVEAL FLAG' ?> ]</div>
        </div>

        <div class="writeup-card">
            <h2>07. OP: HEADERS</h2>
            <p><strong><?= $lang === 'es' ? 'Vulnerabilidad:' : 'Vulnerability:' ?></strong> HTTP Response Headers.</p>
            <h3><?= $lang === 'es' ? 'Solución:' : 'Solution:' ?></h3>
            <ol>
                <li><?= $lang === 'es' ? 'Abre las DevTools (F12) y ve a la pestaña "Network" / "Red".' : 'Open DevTools (F12) and go to the "Network" tab.' ?></li>
                <li><?= $lang === 'es' ? 'Recarga la página y haz clic en el archivo principal.' : 'Reload the page and click on the main file.' ?></li>
                <li><?= $lang === 'es' ? 'Busca la cabecera personalizada "X-Cyber-Access".' : 'Look for the custom "X-Cyber-Access" header.' ?></li>
            </ol>
            <div class="flag-spoiler" data-flag="FLAG{h34d3rs_4r3_t4lk4t1v3}">[ <?= $lang === 'es' ? 'REVELAR FLAG' : 'REVEAL FLAG' ?> ]</div>
        </div>

        <div class="writeup-card">
            <h2>08. OP: B64-DECODE</h2>
            <p><strong><?= $lang === 'es' ? 'Vulnerabilidad:' : 'Vulnerability:' ?></strong> Base64 Encoding.</p>
            <h3><?= $lang === 'es' ? 'Solución:' : 'Solution:' ?></h3>
            <ol>
                <li><?= $lang === 'es' ? 'Copia la cadena codificada del briefing.' : 'Copy the encoded string from the briefing.' ?></li>
                <li><?= $lang === 'es' ? 'Usa la herramienta Base64 de la plataforma para decodificarla.' : 'Use the platform\'s Base64 tool to decode it.' ?></li>
            </ol>
            <div class="flag-spoiler" data-flag="FLAG{b4s364_1s_n0t_encryp710n}">[ <?= $lang === 'es' ? 'REVELAR FLAG' : 'REVEAL FLAG' ?> ]</div>
        </div>

        <div class="writeup-card">
            <h2>09. OP: COOKIE_MONSTER</h2>
            <p><strong><?= $lang === 'es' ? 'Vulnerabilidad:' : 'Vulnerability:' ?></strong> Unsecured Session Cookies.</p>
            <h3><?= $lang === 'es' ? 'Solución:' : 'Solution:' ?></h3>
            <ol>
                <li><?= $lang === 'es' ? 'Abre las DevTools (F12) y ve a "Application" / "Almacenamiento".' : 'Open DevTools (F12) and go to "Application" / "Storage".' ?></li>
                <li><?= $lang === 'es' ? 'Busca la cookie "ctf_session_data".' : 'Look for the "ctf_session_data" cookie.' ?></li>
                <li><?= $lang === 'es' ? 'Decodifica su valor de Base64 a texto plano.' : 'Decode its value from Base64 to plain text.' ?></li>
            </ol>
            <div class="flag-spoiler" data-flag="FLAG{c00k13s_kn0w_3v3ryth1ng}">[ <?= $lang === 'es' ? 'REVELAR FLAG' : 'REVEAL FLAG' ?> ]</div>
        </div>

        <div class="writeup-card">
            <h2>10. OP: DOUBLE-CIPHER</h2>
            <p><strong><?= $lang === 'es' ? 'Vulnerabilidad:' : 'Vulnerability:' ?></strong> Chained Encoding (Base64 + ROT13).</p>
            <h3><?= $lang === 'es' ? 'Solución:' : 'Solution:' ?></h3>
            <ol>
                <li><?= $lang === 'es' ? 'Decodifica la cadena inicial usando Base64.' : 'Decode the initial string using Base64.' ?></li>
                <li><?= $lang === 'es' ? 'El resultado será texto inteligible pero cifrado (César).' : 'The result will be intelligible but encrypted text (Caesar).' ?></li>
                <li><?= $lang === 'es' ? 'Aplica ROT13 al resultado para obtener la flag final.' : 'Apply ROT13 to the result to get the final flag.' ?></li>
            </ol>
            <div class="flag-spoiler" data-flag="FLAG{d0ubl3_c1ph3r}">[ <?= $lang === 'es' ? 'REVELAR FLAG' : 'REVEAL FLAG' ?> ]</div>
        </div>

        <div class="writeup-card" style="border-left-color: #aa00ff;">
            <h2 style="color: #aa00ff;">11. OP: JWT-TOKEN</h2>
            <p><strong><?= $lang === 'es' ? 'Vulnerabilidad:' : 'Vulnerability:' ?></strong> JWT Payload Exposure.</p>
            <h3><?= $lang === 'es' ? 'Solución:' : 'Solution:' ?></h3>
            <ol>
                <li><?= $lang === 'es' ? 'Copia solo la parte central del token (entre los dos puntos).' : 'Copy only the middle part of the token (between the two dots).' ?></li>
                <li><?= $lang === 'es' ? 'Decodifícalo de Base64 para leer el JSON interno.' : 'Decode it from Base64 to read the internal JSON.' ?></li>
            </ol>
            <div class="flag-spoiler" style="border-color: #aa00ff;" data-flag="FLAG{jwt_p4yl04d_3xp0s3d}">[ <?= $lang === 'es' ? 'REVELAR FLAG' : 'REVEAL FLAG' ?> ]</div>
        </div>

        <div class="writeup-card">
            <h2>12. OP: BROKEN-HASH</h2>
            <p><strong><?= $lang === 'es' ? 'Vulnerabilidad:' : 'Vulnerability:' ?></strong> MD5 Hash Cracking.</p>
            <h3><?= $lang === 'es' ? 'Solución:' : 'Solution:' ?></h3>
            <ol>
                <li><?= $lang === 'es' ? 'Copia el hash MD5.' : 'Copy the MD5 hash.' ?></li>
                <li><?= $lang === 'es' ? 'Pásalo por un crackeador (herramienta nativa o CrackStation) usando Rainbow Tables.' : 'Pass it through a cracker (native tool or CrackStation) using Rainbow Tables.' ?></li>
            </ol>
            <div class="flag-spoiler" data-flag="FLAG{md5_1s_d34d_us3_bcrpyt}">[ <?= $lang === 'es' ? 'REVELAR FLAG' : 'REVEAL FLAG' ?> ]</div>
        </div>

        <div class="writeup-card" style="border-left-color: #00ff41;">
            <h2 style="color: #00ff41;">13. OP: DIGITAL-TRAIL</h2>
            <p><strong><?= $lang === 'es' ? 'Vulnerabilidad:' : 'Vulnerability:' ?></strong> OSINT / SSL Logs.</p>
            <h3><?= $lang === 'es' ? 'Solución:' : 'Solution:' ?></h3>
            <ol>
                <li><?= $lang === 'es' ? 'Descarga el archivo JSON simulado de crt.sh.' : 'Download the simulated JSON file from crt.sh.' ?></li>
                <li><?= $lang === 'es' ? 'Inspecciona la lista de subdominios.' : 'Inspect the list of subdomains.' ?></li>
                <li><?= $lang === 'es' ? 'Uno de los subdominios filtrados es la flag en sí misma.' : 'One of the leaked subdomains is the flag itself.' ?></li>
            </ol>
            <div class="flag-spoiler" style="border-color: #00ff41;" data-flag="FLAG{crt_sh_subd0m41n_l34k}">[ <?= $lang === 'es' ? 'REVELAR FLAG' : 'REVEAL FLAG' ?> ]</div>
        </div>

        <div class="writeup-card" style="border-left-color: #ffcc00;">
            <h2 style="color: #ffcc00;">14. OP: IDOR-ACCESS</h2>
            <p><strong><?= $lang === 'es' ? 'Vulnerabilidad:' : 'Vulnerability:' ?></strong> Insecure Direct Object Reference (IDOR).</p>
            <h3><?= $lang === 'es' ? 'Solución:' : 'Solution:' ?></h3>
            <ol>
                <li><?= $lang === 'es' ? 'Observa la URL: user-api.php?user_id=42.' : 'Observe the URL: user-api.php?user_id=42.' ?></li>
                <li><?= $lang === 'es' ? 'Cambia el número 42 por el número 7 (perfil del administrador).' : 'Change the number 42 to the number 7 (administrator profile).' ?></li>
                <li><?= $lang === 'es' ? 'La API escupirá la flag en formato JSON.' : 'The API will output the flag in JSON format.' ?></li>
            </ol>
            <div class="flag-spoiler" style="border-color: #ffcc00;" data-flag="FLAG{1d0r_byp4ss_l1k3_4_b0ss}">[ <?= $lang === 'es' ? 'REVELAR FLAG' : 'REVEAL FLAG' ?> ]</div>
        </div>

        <div class="writeup-card" style="border-left-color: #ffcc00;">
            <h2 style="color: #ffcc00;">15. OP: EXIF-DATA</h2>
            <p><strong><?= $lang === 'es' ? 'Vulnerabilidad:' : 'Vulnerability:' ?></strong> Metadata Leak.</p>
            <h3><?= $lang === 'es' ? 'Solución:' : 'Solution:' ?></h3>
            <ol>
                <li><?= $lang === 'es' ? 'Descarga la imagen.' : 'Download the image.' ?></li>
                <li><?= $lang === 'es' ? 'Utiliza exiftool o revisa las Propiedades > Detalles en Windows.' : 'Use exiftool or check Properties > Details in Windows.' ?></li>
                <li><?= $lang === 'es' ? 'La flag está inyectada en el campo Comentario o Descripción.' : 'The flag is injected into the Comment or Description field.' ?></li>
            </ol>
            <div class="flag-spoiler" style="border-color: #ffcc00;" data-flag="FLAG{3x1f_m3t4d4t4_h1dd3n}">[ <?= $lang === 'es' ? 'REVELAR FLAG' : 'REVEAL FLAG' ?> ]</div>
        </div>

        <div class="writeup-card" style="border-left-color: #ff2a2a;">
            <h2 style="color: #ff2a2a;">16. OP: XOR-CRYPTO</h2>
            <p><strong><?= $lang === 'es' ? 'Vulnerabilidad:' : 'Vulnerability:' ?></strong> Weak XOR Obfuscation.</p>
            <h3><?= $lang === 'es' ? 'Solución:' : 'Solution:' ?></h3>
            <ol>
                <li><?= $lang === 'es' ? 'Toma el array de bytes hexadecimales.' : 'Take the array of hexadecimal bytes.' ?></li>
                <li><?= $lang === 'es' ? 'La clave es la longitud de "flag" (el número 4).' : 'The key is the length of "flag" (the number 4).' ?></li>
                <li><?= $lang === 'es' ? 'Haz un script en Python (chr(byte ^ 4)) o usa CyberChef (XOR con clave 4) para revertir los bytes.' : 'Write a Python script (chr(byte ^ 4)) or use CyberChef (XOR with key 4) to reverse the bytes.' ?></li>
            </ol>
            <div class="flag-spoiler" style="border-color: #ff2a2a;" data-flag="FLAG{x0r_pwnd}">[ <?= $lang === 'es' ? 'REVELAR FLAG' : 'REVEAL FLAG' ?> ]</div>
        </div>
<div class="writeup-card" style="border-left-color: #aa00ff;">
            <h2 style="color: #aa00ff;">17. OP: SHADOW_PATH</h2>
            <p><strong><?= $lang === 'es' ? 'Vulnerabilidad:' : 'Vulnerability:' ?></strong> Local File Inclusion (LFI) / WAF Bypass.</p>
            <h3><?= $lang === 'es' ? 'Solución:' : 'Solution:' ?></h3>
            <ol>
                <li><?= $lang === 'es' ? 'El servidor carga archivos usando el parámetro "?file=". Si intentas usar "../" el firewall (WAF) te bloqueará con un Error 403.' : 'The server loads files using the "?file=" parameter. If you try to use "../" the firewall (WAF) will block you with a 403 Error.' ?></li>
                <li><?= $lang === 'es' ? 'Para evadir el WAF, debemos usar URL Encoding. El equivalente de "../" es "%2E%2E%2F".' : 'To bypass the WAF, we must use URL Encoding. The equivalent of "../" is "%2E%2E%2F".' ?></li>
                <li><?= $lang === 'es' ? 'La inteligencia indicó que la bóveda está 3 niveles arriba. La carga útil final es: ?file=%2E%2E%2F%2E%2E%2F%2E%2E%2Fhidden_vault/credentials.txt' : 'Intelligence indicated the vault is 3 levels up. The final payload is: ?file=%2E%2E%2F%2E%2E%2F%2E%2E%2Fhidden_vault/credentials.txt' ?></li>
            </ol>
            <div class="flag-spoiler" style="border-color: #aa00ff;" data-flag="FLAG{lfi_byp4ss_url_3nc0d1ng}">[ <?= $lang === 'es' ? 'REVELAR FLAG' : 'REVEAL FLAG' ?> ]</div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>