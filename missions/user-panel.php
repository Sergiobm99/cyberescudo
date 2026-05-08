<?php
/**
 * CTF-05 auxiliary page — sets a cookie the player must find
 * The actual flag is in ctf/config.php (server-side); this page
 * only sets a cookie with a hint/obfuscated value to guide discovery.
 */
define('CYBERESCUDO_BOOTSTRAP', true);
require_once __DIR__ . '/../bootstrap.php';

// Set a suspicious-looking cookie — players must inspect it with DevTools or curl
// The cookie value is base64 of the flag so the reto is: decode the cookie value
$cookiePayload = base64_encode('FLAG{c00k13s_kn0w_3v3ryth1ng}');


// Crear las cookies de forma sencilla sin forzar HTTPS estricto
setcookie('ctf_session_data', $cookiePayload, time() + 3600, '/');
setcookie('user_pref', 'theme=dark&lang=es', time() + 3600, '/');

$pageTitle = $lang === 'es'
    ? 'Panel de usuario — CTF-05 — CyberEscudo'
    : 'User panel — CTF-05 — CyberEscudo';
require __DIR__ . '/../templates/header.php';
?>
<main class="content-page">
  <div class="card">
    <h2 style="font-size:1.3rem;color:var(--white);margin-bottom:1rem;">
      👤 <?= $lang === 'es' ? 'Panel de usuario' : 'User panel' ?>
    </h2>
    <p style="color:var(--gray);font-family:var(--mono);font-size:.88rem;margin-bottom:1.5rem;">
      <?= $lang === 'es'
        ? 'Bienvenido al panel de usuario. Tu sesión ha sido inicializada correctamente.'
        : 'Welcome to the user panel. Your session has been initialised successfully.' ?>
    </p>

    <div style="background:rgba(0,0,0,.35);border:1px solid var(--border);border-radius:.5rem;padding:1rem 1.25rem;font-family:var(--mono);font-size:.82rem;color:var(--gray-dark);margin-bottom:1.25rem;">
      <div style="margin-bottom:.4rem;">User-Agent: <span style="color:rgba(255,255,255,.6);"><?= e($_SERVER['HTTP_USER_AGENT'] ?? '') ?></span></div>
      <div style="margin-bottom:.4rem;">IP: <span style="color:rgba(255,255,255,.6);"><?= e($_SERVER['REMOTE_ADDR'] ?? '') ?></span></div>
      <div>Status: <span style="color:#00d45a;">authenticated</span></div>
    </div>

    <p style="color:var(--gray-dark);font-family:var(--mono);font-size:.78rem;">
      <?= $lang === 'es'
        ? '💡 Pista: tu sesión contiene más datos de los que ves en pantalla. Abre las DevTools (F12 → Application → Cookies) o usa curl con <code style="color:var(--cyan);">-v</code> para ver todo.'
        : '💡 Hint: your session contains more data than what you see on screen. Open DevTools (F12 → Application → Cookies) or use curl with <code style="color:var(--cyan);">-v</code> to see everything.' ?>
    </p>
  </div>
</main>
<?php require __DIR__ . '/../templates/footer.php'; ?>
