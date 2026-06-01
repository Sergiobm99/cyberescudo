const code = `<?php
// Módulo de perfil de usuario (Legacy)
require_once 'db_config.php';

// 1. Obtenemos el ID del usuario por GET
$user_id = $_GET['id'];

// ¡VULNERABILIDAD CRÍTICA! Inyección SQL
// Concatenamos directamente sin usar sentencias preparadas.
$query = "SELECT username, email, role FROM users WHERE id = " . $user_id;
$result = mysqli_query($conn, $query);
$user_data = mysqli_fetch_assoc($result);

// ¡VULNERABILIDAD ALTA! Cross-Site Scripting (XSS)
// Mostramos la biografía directamente sin sanear.
echo "<div class='bio-panel'>Biografía: " . $_POST['bio'] . "</div>";

// ¡VULNERABILIDAD CRÍTICA! Deserialización Insegura
// Leemos una cookie codificada y la instanciamos, lo que puede llevar a RCE (Remote Code Execution)
if (isset($_COOKIE['session_prefs'])) {
    $prefs = unserialize(base64_decode($_COOKIE['session_prefs']));
}

// ¡VULNERABILIDAD CRÍTICA! Secreto Hardcodeado (AWS Key)
// Alguien se dejó las credenciales en claro en el código fuente.
$aws_access_token = "AKIAIOSFODNN7EXAMPLE";

?>`;

const rules = [
    { regex: /\.call\{value:/g, type: 'critical', title: 'Reentrancy Risk', desc: 'Low-level call with value before updating state. Can lead to reentrancy attacks.' },
    { regex: /selfdestruct\(/g, type: 'high', title: 'Unprotected Selfdestruct', desc: 'Ensure selfdestruct is protected by access controls (e.g. onlyOwner).' },
    { regex: /tx\.origin/g, type: 'high', title: 'tx.origin Auth', desc: 'Using tx.origin for authorization is vulnerable to phishing.' },
    { regex: /SELECT.*WHERE.*\$.*/gi, type: 'critical', title: 'SQL Injection', desc: 'Concatenating variables into SQL queries. Use Prepared Statements instead.' },
    { regex: /echo.*\$_(GET|POST|REQUEST).*/gi, type: 'high', title: 'Cross-Site Scripting (XSS)', desc: 'Directly echoing user input. Use htmlspecialchars() to sanitize.' },
    { regex: /unserialize\(/g, type: 'critical', title: 'Insecure Deserialization', desc: 'Deserializing untrusted data can lead to Remote Code Execution (RCE).' },
    { regex: /os\.system\(.*f".*\{/g, type: 'critical', title: 'Command Injection', desc: 'Passing user-controlled format strings to os.system. Use subprocess with arrays.' },
    { regex: /(AKIA[0-9A-Z]{16}|sk_live_[0-9a-zA-Z]{24})/g, type: 'critical', title: 'Hardcoded Secret', desc: 'API Keys or secrets found in code. Use environment variables.' }
];

const lines = code.split('\n');
let count = 0;

lines.forEach((line, i) => {
    const lineNum = i + 1;
    rules.forEach(rule => {
        rule.regex.lastIndex = 0; // Reset regex
        if (rule.regex.test(line)) {
            count++;
            console.log(`Found [${rule.type}] ${rule.title} at line ${lineNum}`);
        }
    });
});
console.log('Total:', count);
