<?php
/**
 * CyberEscudo — Bootstrap
 * Included at the top of every page. Loads config, i18n, helpers, and security settings.
 */

require_once __DIR__ . '/config.php';

// ── Error handling (never expose errors to visitors in production) ────────────
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

// ── CSP Nonce (per-request random value for inline scripts) ───────────────────
// Allows specific inline <script nonce="..."> blocks while keeping CSP strict.
$cspNonce = base64_encode(random_bytes(16));

// ── Security headers (sent before any output) ─────────────────────────────────
// Prevent clickjacking
header('X-Frame-Options: DENY');
// Prevent MIME-type sniffing
header('X-Content-Type-Options: nosniff');
// Enforce HTTPS for 1 year (only effective once HTTPS is configured)
header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
// Limit referrer information
header('Referrer-Policy: strict-origin-when-cross-origin');
// Disable browser features not needed by the site
header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');
// Content Security Policy with nonce for inline scripts AND Google Analytics support
// Content Security Policy with nonce for inline scripts AND Google Analytics support
header(
    "Content-Security-Policy: " .
    "default-src 'self'; " .
    "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://*.googletagmanager.com; " .
    "font-src 'self' https://fonts.gstatic.com; " .
    "script-src 'self' 'nonce-{$cspNonce}' https://*.googletagmanager.com; " .
    "img-src 'self' data: https://*.googletagmanager.com https://*.google-analytics.com; " .
   "connect-src 'self' https://ipwho.is https://cloudflare-dns.com https://internetdb.shodan.io https://networkcalc.com https://archive.org https://*.archive.org https://*.google-analytics.com https://*.analytics.google.com https://*.googletagmanager.com; " .
    "frame-src 'none'; " .
    "object-src 'none'; " .
    "base-uri 'self'; " .
    "form-action 'self'; " .
    "frame-ancestors 'none'"
);
// Remove PHP version fingerprint from responses
header_remove('X-Powered-By');

// ── Session security ──────────────────────────────────────────────────────────
ini_set('session.use_strict_mode',  '1');   // Reject uninitialized session IDs
ini_set('session.use_only_cookies', '1');   // Never pass session ID in URL
ini_set('session.cookie_httponly',  '1');   // JS cannot read the cookie
ini_set('session.cookie_samesite',  'Lax'); // Mitigate CSRF
ini_set('session.cookie_path',      '/');
// Enable cookie_secure only when running over HTTPS
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', '1');
}

session_start();

// Regenerate session ID on language change to prevent session fixation
$langSwitched = false;

// ── Language detection ────────────────────────────────────────────────────────

// 1. Explicit switch via ?lang=es|en — whitelist, nothing else accepted
if (isset($_GET['lang']) && in_array($_GET['lang'], ['es', 'en'], true)) {
    if (($_SESSION['lang'] ?? '') !== $_GET['lang']) {
        session_regenerate_id(true); // prevent session fixation on lang switch
    }
    $_SESSION['lang'] = $_GET['lang'];
}

// 2. Use session value, or default
$lang = $_SESSION['lang'] ?? DEFAULT_LANG;

// Load translation strings from a fixed, whitelisted path (no user input in path)
$langFile = __DIR__ . '/lang/' . ($lang === 'en' ? 'en' : 'es') . '.php';
$t = require $langFile;

// ── Helpers ───────────────────────────────────────────────────────────────────

/**
 * Safely escape a value for HTML output.
 * Use this on ALL user-influenced or dynamic data.
 */
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Build a language-switch URL that is safe against XSS and path injection.
 * - Only the 'lang' parameter is carried; all other GET params are dropped
 *   to prevent parameter pollution / cache-poisoning.
 * - The path is extracted via parse_url + FILTER_SANITIZE_URL,
 *   then validated to start with '/'.
 */
function langUrl(string $newLang): string {
    // Only accept whitelisted values
    $newLang = in_array($newLang, ['es', 'en'], true) ? $newLang : 'es';

    // Extract just the path component from REQUEST_URI, then sanitize
    $rawPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $path    = filter_var((string) $rawPath, FILTER_SANITIZE_URL);

    // Ensure the path starts with '/' and contains no dangerous characters
    if (!$path || !str_starts_with($path, '/')) {
        $path = '/';
    }

    return $path . '?lang=' . urlencode($newLang);
}

/**
 * Validate that a URL belongs to a known allowed scheme (for link hrefs).
 * Returns '#' if the URL is not safe.
 */
function safeUrl(string $url): string {
    $allowed = ['https', 'http', 'mailto'];
    $scheme  = strtolower((string) parse_url($url, PHP_URL_SCHEME));
    return in_array($scheme, $allowed, true) ? $url : '#';
}

/**
 * Inline SVG icons — all strings are hardcoded, no user input involved.
 */
function icon(string $name, string $class = ''): string {
    // Whitelist icon names to prevent any unexpected output
    $allowed = ['shield','firewall','scan','network','code','alert',
                'server','globe','sword','email','heart','logo'];
    if (!in_array($name, $allowed, true)) {
        return '';
    }
    $c = $class ? ' class="' . e($class) . '"' : '';
    $icons = [
        'shield'   => '<svg'.$c.' width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 2L4 6v6c0 5.25 4.27 9.17 8 10.39C15.73 21.17 20 17.25 20 12V6L12 2z" stroke="#00ffff" stroke-width="1.5" fill="none" stroke-linejoin="round"/><path d="M9 12l2 2 4-4" stroke="#00ffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        'firewall' => '<svg'.$c.' width="24" height="24" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" stroke="#00ffff" stroke-width="1.5"/><line x1="3" y1="9" x2="21" y2="9" stroke="#00ffff" stroke-width="1.5"/><line x1="3" y1="15" x2="21" y2="15" stroke="#00ffff" stroke-width="1.5"/><line x1="9" y1="3" x2="9" y2="21" stroke="#00ffff" stroke-width="1.5"/></svg>',
        'scan'     => '<svg'.$c.' width="24" height="24" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="#00ffff" stroke-width="1.5"/><path d="m21 21-4.35-4.35" stroke="#00ffff" stroke-width="1.5" stroke-linecap="round"/><circle cx="11" cy="11" r="3" stroke="#00ffff" stroke-width="1.5" stroke-dasharray="2 2"/></svg>',
        'network'  => '<svg'.$c.' width="24" height="24" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="2" fill="#00ffff"/><path d="M8 12a4 4 0 0 1 4-4" stroke="#00ffff" stroke-width="1.5" stroke-linecap="round"/><path d="M16 12a4 4 0 0 1-4 4" stroke="#00ffff" stroke-width="1.5" stroke-linecap="round"/><path d="M5 12a7 7 0 0 1 7-7" stroke="#00ffff" stroke-width="1.5" stroke-linecap="round"/><path d="M19 12a7 7 0 0 1-7 7" stroke="#00ffff" stroke-width="1.5" stroke-linecap="round"/></svg>',
        'code'     => '<svg'.$c.' width="24" height="24" viewBox="0 0 24 24" fill="none"><rect x="2" y="4" width="20" height="14" rx="2" stroke="#00ffff" stroke-width="1.5"/><path d="M8 11l2 2-2 2" stroke="#00ffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><line x1="13" y1="13" x2="16" y2="13" stroke="#00ffff" stroke-width="1.5" stroke-linecap="round"/></svg>',
        'alert'    => '<svg'.$c.' width="24" height="24" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="#00ffff" stroke-width="1.5"/><line x1="12" y1="7" x2="12" y2="13" stroke="#00ffff" stroke-width="1.5" stroke-linecap="round"/><circle cx="12" cy="16" r="1" fill="#00ffff"/></svg>',
        'server'   => '<svg'.$c.' width="18" height="18" viewBox="0 0 18 18" fill="none"><rect x="1" y="2" width="16" height="11" rx="1.5" stroke="#00ffff" stroke-width="1.4"/><line x1="1" y1="16" x2="17" y2="16" stroke="#00ffff" stroke-width="1.4" stroke-linecap="round"/></svg>',
        'globe'    => '<svg'.$c.' width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="7.5" stroke="#00ffff" stroke-width="1.4"/><path d="M9 1.5c0 0-3 3-3 7.5s3 7.5 3 7.5" stroke="#00ffff" stroke-width="1.4"/><path d="M9 1.5c0 0 3 3 3 7.5s-3 7.5-3 7.5" stroke="#00ffff" stroke-width="1.4"/><line x1="1.5" y1="9" x2="16.5" y2="9" stroke="#00ffff" stroke-width="1.4"/></svg>',
        'sword'    => '<svg'.$c.' width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M3 3l12 12M3 3l5 1-1-5M15 15l-5-1 1 5" stroke="#00ffff" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        'email'    => '<svg'.$c.' width="16" height="16" viewBox="0 0 16 16" fill="none"><rect x="1" y="3" width="14" height="10" rx="1.5" stroke="#00ffff" stroke-width="1.4"/><path d="M1 5l7 5 7-5" stroke="#00ffff" stroke-width="1.4" stroke-linecap="round"/></svg>',
        'heart'    => '<svg'.$c.' width="28" height="28" viewBox="0 0 28 28" fill="none"><path d="M14 24s-10-6.27-10-13a6 6 0 0 1 10-4.47A6 6 0 0 1 24 11c0 6.73-10 13-10 13z" stroke="#00ffff" stroke-width="1.5" fill="none" stroke-linejoin="round"/></svg>',
        'logo'     => '<svg'.$c.' width="28" height="28" viewBox="0 0 28 28" fill="none"><path d="M14 2L4 7v8c0 5.25 4.27 10.17 10 11.38C19.73 25.17 24 20.25 24 15V7L14 2z" stroke="#00ffff" stroke-width="2" fill="none" stroke-linejoin="round"/><path d="M10 14l3 3 5-5" stroke="#00ffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    ];
    return $icons[$name];
}