<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Auditoría DIVA: Vulnerabilidades Android — CyberEscudo' : 'DIVA Audit: Android Vulnerabilities — CyberEscudo';
$contentTitle = $lang==='es' ? 'Auditoría DIVA: Vulnerabilidades Android' : 'DIVA Audit: Android Vulnerabilities';
$contentDate  = '2022-04-01';
$contentTags  = ['DIVA','Android','ADB','SQLite','Seguridad Móvil'];
ob_start();
if ($lang==='es'): ?>
<div class="prose">
  <p>Auditoría práctica sobre <strong>DIVA (Damn Insecure and Vulnerable App)</strong>: demostración de vulnerabilidades reales en aplicaciones Android usando ADB, logcat y sqlite3.</p>

  <h2>Apartado 1 — Insecure Logging</h2>
  <p>DIVA guarda información confidencial (número de tarjeta de crédito) en los logs del sistema con la etiqueta <code>diva-log</code>.</p>
  <pre><code># Acceder al shell del dispositivo
adb shell

# Ver todos los logs filtrados por diva
logcat | grep "diva-log"

# En Windows: exportar logs desde Genymotion y buscar
Find "diva-log" *</code></pre>
  <p><strong>Riesgo:</strong> cualquier app con permiso de lectura de logs puede obtener los datos.</p>

  <h2>Apartado 2 — Hardcoding Issues (Part 1)</h2>
  <p>La clave de acceso está en texto plano dentro del código fuente sin encriptar:</p>
  <pre><code># Abrir HardcodeActivity en el código descompilado
# La clave aparece visible en el código Java sin ofuscación</code></pre>

  <h2>Apartado 3 — Insecure Data Storage (SharedPreferences)</h2>
  <p>Las credenciales se guardan en SharedPreferences sin cifrar:</p>
  <pre><code>adb shell
cd /data/data/jakhar.aseem.diva/shared_prefs/
cat jakhar.aseem.diva_preferences.xml
# Muestra usuario y contraseña en texto plano</code></pre>

  <h2>Apartado 4 — Insecure Data Storage (SQLite)</h2>
  <p>Las credenciales se almacenan en una base de datos SQLite sin cifrar:</p>
  <pre><code>adb shell
cd /data/data/jakhar.aseem.diva/databases/
sqlite3 ids2
.tables
SELECT * FROM myuser;
.headers on       # Mostrar nombres de columnas
sqlite3 ids2 .dump > ids2_backup.txt   # Backup</code></pre>

  <h2>Apartado 5 — Insecure Data Storage (Archivos temporales)</h2>
  <p>Las credenciales se guardan en archivos temporales con nombre predecible (<code>uinfo+identificador</code>) en el directorio del paquete:</p>
  <pre><code>ls /data/data/jakhar.aseem.diva/
cat uinfo*   # Credenciales en texto plano</code></pre>

  <h2>Apartado 6 — Insecure Data Storage (Almacenamiento externo)</h2>
  <p>Las credenciales se escriben en la tarjeta SD/almacenamiento externo, accesible por cualquier app con permiso <code>READ_EXTERNAL_STORAGE</code>. Para reproducirlo, activar permiso en <code>Settings → Apps → Permissions</code>.</p>

  <h2>Apartado 7 — Input Validation Issues (SQL Injection en DIVA)</h2>
  <p>La aplicación acepta nombres de usuario sin sanitizar y los usa directamente en una consulta SQL. Inyectar <code>' OR '1'='1</code> devuelve todos los registros.</p>

  <h2>Resumen de vulnerabilidades</h2>
  <table>
    <thead><tr><th>Apartado</th><th>Vulnerabilidad</th><th>Almacenamiento</th></tr></thead>
    <tbody>
      <tr><td>1</td><td>Insecure Logging</td><td>Logs del sistema</td></tr>
      <tr><td>2</td><td>Hardcoded Credentials</td><td>Código fuente</td></tr>
      <tr><td>3</td><td>Insecure Data Storage</td><td>SharedPreferences (XML)</td></tr>
      <tr><td>4</td><td>Insecure Data Storage</td><td>SQLite sin cifrar</td></tr>
      <tr><td>5</td><td>Insecure Data Storage</td><td>Archivos temporales</td></tr>
      <tr><td>6</td><td>Insecure Data Storage</td><td>Almacenamiento externo</td></tr>
      <tr><td>7</td><td>SQL Injection</td><td>Base de datos interna</td></tr>
    </tbody>
  </table>
</div>
<?php else: ?>
<div class="prose">
  <p>Practical audit of <strong>DIVA</strong>: demonstrating 7 Android vulnerabilities using ADB, logcat and sqlite3.</p>

  <h2>Section 1 — Insecure Logging</h2>
  <pre><code">adb shell
logcat | grep "diva-log"   # Exposes credit card numbers in logs</code></pre>

  <h2>Section 2 — Hardcoded Credentials</h2>
  <p>The access key is visible in plain text in the decompiled Java source code.</p>

  <h2>Section 3 — SharedPreferences (XML, unencrypted)</h2>
  <pre><code">cat /data/data/jakhar.aseem.diva/shared_prefs/jakhar.aseem.diva_preferences.xml</code></pre>

  <h2>Section 4 — SQLite Database (unencrypted)</h2>
  <pre><code">sqlite3 /data/data/jakhar.aseem.diva/databases/ids2
.tables
SELECT * FROM myuser;</code></pre>

  <h2>Section 5 — Temporary Files (unencrypted)</h2>
  <pre><code">cat /data/data/jakhar.aseem.diva/uinfo*</code></pre>

  <h2>Section 6 — External Storage</h2>
  <p>Credentials written to SD card, accessible by any app with READ_EXTERNAL_STORAGE permission.</p>

  <h2>Section 7 — SQL Injection</h2>
  <p>Inject <code>' OR '1'='1</code> to return all user records.</p>

  <h2>Summary</h2>
  <table>
    <thead><tr><th>Section</th><th>Vulnerability</th><th>Storage</th></tr></thead>
    <tbody>
      <tr><td>1</td><td>Insecure Logging</td><td>System logcat</td></tr>
      <tr><td>2</td><td>Hardcoded Credentials</td><td>Source code</td></tr>
      <tr><td>3</td><td>Insecure Storage</td><td>SharedPreferences</td></tr>
      <tr><td>4</td><td>Insecure Storage</td><td>SQLite unencrypted</td></tr>
      <tr><td>5</td><td>Insecure Storage</td><td>Temp files</td></tr>
      <tr><td>6</td><td>Insecure Storage</td><td>External storage</td></tr>
      <tr><td>7</td><td>SQL Injection</td><td>Internal database</td></tr>
    </tbody>
  </table>
</div>
<?php endif;
$contentBody=ob_get_clean();
require __DIR__.'/../templates/content-page.php';
