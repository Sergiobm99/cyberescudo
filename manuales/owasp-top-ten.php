<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'OWASP Top 10 — CyberEscudo' : 'OWASP Top 10 — CyberEscudo';
$contentTitle = $lang==='es' ? 'OWASP Top 10: Riesgos en Aplicaciones Web' : 'OWASP Top 10: Web Application Risks';
$contentDate  = '2022-05-01';
$contentTags  = ['OWASP','Top 10','Vulnerabilidades Web','Auditoría'];
ob_start();
if ($lang==='es'): ?>
<div class="prose">
  <p>El <strong>OWASP Top 10</strong> es la lista de referencia mundial de los riesgos de seguridad más críticos en aplicaciones web, mantenida por la Open Web Application Security Project.</p>

  <h2>Factores de datos de cada categoría</h2>
  <ul>
    <li><strong>CWE asignados:</strong> número de debilidades (Common Weakness Enumeration) mapeadas a la categoría.</li>
    <li><strong>Tasa de incidencia:</strong> porcentaje de aplicaciones vulnerables encontradas en pruebas reales.</li>
    <li><strong>Cobertura de pruebas:</strong> porcentaje de apps analizadas para ese CWE.</li>
    <li><strong>Explotación ponderada:</strong> subpuntuación CVSSv2/v3 normalizada (escala 0-10).</li>
    <li><strong>Impacto ponderado:</strong> subpuntuación de impacto CVSSv2/v3 normalizada.</li>
    <li><strong>CVE totales:</strong> total de CVEs en la base NVD asignados a los CWE de la categoría.</li>
  </ul>

  <h2>OWASP Top 10 — 2021</h2>
  <table>
    <thead><tr><th>Posición</th><th>Categoría</th><th>Descripción breve</th></tr></thead>
    <tbody>
      <tr><td>A01</td><td>Broken Access Control</td><td>Sube desde #5. El 94% de las apps fueron probadas. Controles de acceso defectuosos.</td></tr>
      <tr><td>A02</td><td>Fallos Criptográficos</td><td>Antes: Exposición de datos sensibles. Ahora enfocado en fallos de cifrado en tránsito y reposo.</td></tr>
      <tr><td>A03</td><td>Inyección</td><td>Baja al #3. SQL, NoSQL, OS, LDAP. El 94% de las apps fueron testadas.</td></tr>
      <tr><td>A04</td><td>Diseño Inseguro</td><td>Nueva categoría 2021. Fallos de diseño y arquitectura de seguridad.</td></tr>
      <tr><td>A05</td><td>Error de Configuración</td><td>Sube desde #6. El 90% de apps testadas tenían algún error de configuración.</td></tr>
      <tr><td>A06</td><td>Componentes Vulnerables</td><td>Antes #2 en la encuesta de industria. Uso de librerías y componentes desactualizados.</td></tr>
      <tr><td>A07</td><td>Fallos de Autenticación</td><td>Baja desde #2. Incluye CWE de fallos de identificación.</td></tr>
      <tr><td>A08</td><td>Integridad de Software y Datos</td><td>Nueva categoría. Supuestos incorrectos en actualizaciones y pipelines CI/CD.</td></tr>
      <tr><td>A09</td><td>Fallos de Registro y Monitoreo</td><td>Sube. Registro insuficiente que retrasa la detección de brechas.</td></tr>
      <tr><td>A10</td><td>SSRF</td><td>Nueva desde encuesta de industria. Tasa de incidencia baja pero impacto alto.</td></tr>
    </tbody>
  </table>

  <h2>Descripción y contramedidas de las principales vulnerabilidades</h2>

  <h3>SQL Injection (A03)</h3>
  <p>Inyección de código SQL en variables de la aplicación para manipular la base de datos.</p>
  <pre><code>-- Ataque: mipass=' OR ''='
-- Defensa:
$stmt = $pdo->prepare("SELECT * FROM users WHERE pass = ?");
$stmt->execute([$_POST['password']]);</code></pre>

  <h3>XSS — Cross-Site Scripting (A03)</h3>
  <p>Inyección de scripts en páginas vistas por otros usuarios.</p>
  <pre><code>// Defensa: escapar salida
echo htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
// + Content-Security-Policy en cabeceras HTTP</code></pre>

  <h3>XXE — XML External Entities (A03)</h3>
  <p>Las aplicaciones que procesan XML pueden ser engañadas para incluir entidades externas.</p>
  <pre><code># Defensa: deshabilitar DTDs en el parser XML
libxml_disable_entity_loader(true);</code></pre>

  <h3>Broken Access Control (A01)</h3>
  <p>Usuarios acceden a recursos o funciones para los que no tienen permiso.</p>
  <pre><code>// Defensa: verificar autorización en cada endpoint
if (!$user->hasPermission('admin')) { http_response_code(403); exit; }</code></pre>

  <h3>Security Misconfiguration (A05)</h3>
  <ul>
    <li>Limitar acceso a interfaces de administración.</li>
    <li>Deshabilitar depuración en producción.</li>
    <li>Eliminar cuentas y contraseñas por defecto.</li>
    <li>Deshabilitar listado de directorios.</li>
  </ul>

  <h3>Insecure Deserialization (A08)</h3>
  <p>Permite ejecución remota de código mediante objetos serializados manipulados.</p>
  <pre><code>// Defensa: verificar integridad con firma digital antes de deserializar</code></pre>

  <h3>SSRF (A10)</h3>
  <p>La app realiza peticiones HTTP a dominios arbitrarios elegidos por el atacante.</p>
  <pre><code>// Defensa: lista blanca de dominios, no enviar respuestas raw al cliente
$allowed = ['api.example.com', 'cdn.example.com'];
if (!in_array(parse_url($url, PHP_URL_HOST), $allowed)) die('Forbidden');</code></pre>

  <h2>Tipos de auditoría basada en OWASP</h2>
  <ul>
    <li><strong>Auditoría OWASP Top 10:</strong> revisión de las 10 categorías principales. Recomendada para una primera evaluación o cuando el presupuesto es limitado.</li>
    <li><strong>Auditoría OWASP completa (ASVS):</strong> validación de los 90+ controles del estándar ASVS. Para aplicaciones críticas o con requisitos de compliance.</li>
  </ul>
</div>
<?php else: ?>
<div class="prose">
  <p>The <strong>OWASP Top 10</strong> is the global reference for the most critical web application security risks.</p>
  <table>
    <thead><tr><th>Position</th><th>Category</th></tr></thead>
    <tbody>
      <tr><td>A01</td><td>Broken Access Control</td></tr>
      <tr><td>A02</td><td>Cryptographic Failures</td></tr>
      <tr><td>A03</td><td>Injection</td></tr>
      <tr><td>A04</td><td>Insecure Design</td></tr>
      <tr><td>A05</td><td>Security Misconfiguration</td></tr>
      <tr><td>A06</td><td>Vulnerable & Outdated Components</td></tr>
      <tr><td>A07</td><td>Identification & Authentication Failures</td></tr>
      <tr><td>A08</td><td>Software & Data Integrity Failures</td></tr>
      <tr><td>A09</td><td>Security Logging & Monitoring Failures</td></tr>
      <tr><td>A10</td><td>Server-Side Request Forgery (SSRF)</td></tr>
    </tbody>
  </table>
</div>
<?php endif;
$contentBody=ob_get_clean();
require __DIR__.'/../templates/content-page.php';
