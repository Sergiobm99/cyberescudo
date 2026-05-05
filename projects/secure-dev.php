<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Desarrollo Seguro (OWASP Top 10) — CyberEscudo' : 'Secure Development (OWASP) — CyberEscudo';
$contentTitle = $lang==='es' ? 'Desarrollo Seguro y OWASP Top 10' : 'Secure Development & OWASP Top 10';
$contentDate  = '2024-09-01';
$contentDiff  = 'intermediate';
$contentTags  = ['DevSecOps','OWASP','SAST','DAST','Code Review','PHP'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p>La seguridad no debe ser una fase final en el desarrollo de software, sino estar integrada desde el diseño (<em>Security by Design</em>). Esta guía repasa las prácticas de desarrollo seguro estructuradas en base al <strong>OWASP Top 10</strong>, el estándar global de los riesgos de seguridad más críticos en aplicaciones web.</p>

  <h2>A01: Broken Access Control (Control de Acceso Roto)</h2>
  <p>Ocurre cuando un usuario puede acceder a datos o funciones que no le corresponden. El caso más típico es el <strong>IDOR</strong> (Insecure Direct Object Reference).</p>
  <pre><code>// ❌ VULNERABLE: Confiar ciegamente en el ID proporcionado por el usuario
$id = $_GET['id']; // Ej: ?id=5
$q = $pdo->query("SELECT * FROM invoices WHERE id = $id");

// ✅ SEGURO: Validar la propiedad del objeto
$id = $_GET['id'];
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = ? AND owner_id = ?");
$stmt->execute([$id, $user_id]);</code></pre>

  <h2>A02: Cryptographic Failures (Fallos Criptográficos)</h2>
  <p>El almacenamiento inseguro de datos sensibles (como contraseñas o tarjetas de crédito) es fatal. Nunca uses algoritmos obsoletos como MD5 o SHA1 para contraseñas, ya que son vulnerables a ataques de diccionario y colisiones.</p>
  <pre><code>// ❌ VULNERABLE: Hashes rápidos sin "Salt"
$hash = md5($_POST['password']); 

// ✅ SEGURO: Usar algoritmos modernos con Salt automático (Bcrypt/Argon2)
$hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Para verificar el login:
if (password_verify($_POST['password'], $hash_en_db)) { /* Login OK */ }</code></pre>

  <h2>A03: Injection (Inyección)</h2>
  <p>Ocurre cuando datos no confiables se envían a un intérprete (SQL, OS, LDAP) como parte de un comando. Para prevenir la Inyección SQL, la única defensa 100% efectiva son las <strong>Consultas Preparadas (Prepared Statements)</strong>.</p>
  <pre><code>// ❌ VULNERABLE: Concatenación directa
$email = $_POST['email'];
$pdo->query("SELECT * FROM users WHERE email = '" . $email . "'");

// ✅ SEGURO: Consultas preparadas (Separar código de datos)
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 09 (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Simulador SAST (Auditoría de Código)
      </h3>
      <p style="margin-bottom: 1.5rem;">Como Ingeniero DevSecOps, te han asignado revisar el código heredado de un proyecto. Identifica las funciones nativas inseguras en PHP y aplica los parches recomendados por la industria para asegurar la compilación.</p>
      <a href="/ctf/ctf-09.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 09
      </a>
  </div>

  <h2>A04: Insecure Design (Diseño Inseguro)</h2>
  <p>Errores arquitectónicos. Se soluciona aplicando <strong>Threat Modeling (Modelado de Amenazas)</strong> en la fase de diseño. Si una app permite infinitos intentos de login sin CAPTCHA o bloqueo, es un defecto de diseño, no un bug de código.</p>

  <h2>A05: Security Misconfiguration (Configuración Insegura)</h2>
  <p>Dejar credenciales por defecto, servicios innecesarios activos, o mostrar trazas de error (Stack Traces) detalladas en producción.</p>
  <pre><code># ❌ VULNERABLE: Mostrar errores en Producción (php.ini)
display_errors = On

# ✅ SEGURO: Loggear en archivo, no mostrar al usuario
display_errors = Off
log_errors = On
error_log = /var/log/php_errors.log</code></pre>

  <h2>A06: Vulnerable and Outdated Components</h2>
  <p>Usar librerías (npm, pip, composer) con vulnerabilidades conocidas (CVEs). La mitigación consiste en implementar herramientas SCA (Software Composition Analysis) como <em>Dependabot</em> o <em>Snyk</em> en tu pipeline CI/CD.</p>

  <h2>A07: Identification and Authentication Failures</h2>
  <p>Mala gestión de sesiones. Permite ataques de fuerza bruta, robo de sesiones o relleno de credenciales (Credential Stuffing).</p>
  <ul>
      <li>Implementar MFA (Multi-Factor Authentication).</li>
      <li>Exigir contraseñas robustas (integración con bases de datos como Pwned Passwords).</li>
      <li>Regenerar el ID de sesión tras el login: <code>session_regenerate_id(true);</code></li>
  </ul>

  <h2>Integración DevSecOps (El Pipeline)</h2>
  <p>El código seguro moderno se audita de forma automática antes de llegar a producción mediante un Pipeline CI/CD:</p>
  <ul>
      <li><strong>Pre-Commit Hooks:</strong> Evitan que los desarrolladores suban contraseñas o tokens a Git (ej: Talisman, TruffleHog).</li>
      <li><strong>SAST (Static Application Security Testing):</strong> Analiza el código fuente en busca de vulnerabilidades sin ejecutarlo (ej: SonarQube, Semgrep).</li>
      <li><strong>DAST (Dynamic Application Security Testing):</strong> Ataca la aplicación web en tiempo de ejecución de forma automatizada (ej: OWASP ZAP).</li>
  </ul>
</div>

<?php else: ?>
<div class="prose">
  <p>Security should not be an afterthought, but integrated from the design phase (<em>Security by Design</em>). This guide covers secure coding practices based on the <strong>OWASP Top 10</strong>, the global standard for the most critical security risks in web applications.</p>

  <h2>A01: Broken Access Control</h2>
  <p>Occurs when users can access data or functions they shouldn't. The classic example is <strong>IDOR</strong> (Insecure Direct Object Reference).</p>
  <pre><code>// ❌ VULNERABLE: Blindly trusting user-supplied ID
$id = $_GET['id'];
$q = $pdo->query("SELECT * FROM invoices WHERE id = $id");

// ✅ SECURE: Validate ownership
$id = $_GET['id'];
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = ? AND owner_id = ?");
$stmt->execute([$id, $user_id]);</code></pre>

  <h2>A02: Cryptographic Failures</h2>
  <p>Insecure storage of sensitive data. Never use obsolete algorithms like MD5 or SHA1 for passwords.</p>
  <pre><code>// ❌ VULNERABLE: Fast hashing without Salt
$hash = md5($_POST['password']); 

// ✅ SECURE: Modern algorithms with automatic Salt (Bcrypt)
$hash = password_hash($_POST['password'], PASSWORD_DEFAULT);</code></pre>

  <h2>A03: Injection</h2>
  <p>To prevent SQL Injection, the only 100% effective defense is <strong>Prepared Statements</strong>.</p>
  <pre><code>// ❌ VULNERABLE: Direct concatenation
$pdo->query("SELECT * FROM users WHERE email = '" . $_POST['email'] . "'");

// ✅ SECURE: Prepared statements
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $_POST['email']]);</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 09 (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> SAST Simulator (Code Audit)
      </h3>
      <p style="margin-bottom: 1.5rem;">As a DevSecOps Engineer, you've been assigned to review legacy code. Identify insecure native PHP functions and apply industry-recommended patches to secure the build pipeline.</p>
      <a href="/ctf/ctf-09.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 09 CHALLENGE
      </a>
  </div>

  <h2>A05: Security Misconfiguration</h2>
  <p>Default credentials, unnecessary services, or verbose error stack traces exposed in production.</p>
  <pre><code># ❌ VULNERABLE (php.ini)
display_errors = On

# ✅ SECURE
display_errors = Off
log_errors = On</code></pre>

  <h2>DevSecOps Integration (The Pipeline)</h2>
  <ul>
      <li><strong>Pre-Commit Hooks:</strong> Prevent secret leaks to Git (e.g., TruffleHog).</li>
      <li><strong>SAST (Static Application Security Testing):</strong> Source code analysis (e.g., SonarQube, Semgrep).</li>
      <li><strong>DAST (Dynamic Application Security Testing):</strong> Automated runtime attacks (e.g., OWASP ZAP).</li>
  </ul>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';