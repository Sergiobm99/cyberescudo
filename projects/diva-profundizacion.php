<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'DIVA Avanzado: Control de Acceso y Buffer Overflow — CyberEscudo' : 'DIVA Advanced: Access Control & Buffer Overflow — CyberEscudo';
$contentTitle = $lang==='es' ? 'DIVA Avanzado: Control de Acceso y Buffer Overflow' : 'DIVA Advanced: Access Control & Buffer Overflow';
$contentDate  = '2022-04-20';
$contentTags  = ['DIVA','Android','ADB','Buffer Overflow','Content Provider','Seguridad Móvil'];
ob_start();
if ($lang==='es'): ?>
<div class="prose">
  <p>Profundización en la auditoría de <strong>DIVA</strong>: ejercicios 9 al 13, cubriendo bypass de control de acceso, credenciales de API, Content Providers y desbordamiento de buffer en código nativo.</p>

  <h2>Ejercicio 9 — Acceso a credenciales de API sin autenticación</h2>
  <p>La actividad que muestra las credenciales de API puede invocarse directamente desde fuera de la app:</p>
  <pre><code># Identificar el administrador de actividades con logcat:
adb shell logcat | grep "APICreds"

# Cerrar DIVA completamente, luego invocar la actividad:
am start -n jakhar.aseem.diva/.APICreds1Activity</code></pre>
  <p>Resultado: las credenciales de API se muestran sin ninguna autenticación desde cualquier otra app.</p>

  <h2>Ejercicio 10 — Bypass de PIN con parámetro booleano</h2>
  <p>La actividad <code>APICreds2Activity</code> usa un booleano <code>check_pin</code> para decidir si muestra el PIN. Pasamos <code>false</code> para saltarnos la verificación:</p>
  <pre><code># Deshabilitar la verificación de PIN:
am start -n jakhar.aseem.diva/.APICreds2Activity --ez check_pin false

# --ez  → introduce un extra booleano (Extra Boolean)
# false → deshabilita la verificación del PIN</code></pre>

  <h2>Ejercicio 11 — Content Provider sin protección</h2>
  <p>Las notas privadas se almacenan en un Content Provider exportado sin autenticación (<code>android:exported="true"</code>). Se puede consultar directamente:</p>
  <pre><code># Consultar el Content Provider desde adb:
content query --uri content://jakhar.aseem.diva.provider.notesprovider/notes

# Devuelve todas las notas privadas sin necesitar el PIN</code></pre>

  <h2>Ejercicio 12 — Clave hardcodeada en librería nativa</h2>
  <p>La app usa una clase JNI (<code>DivaJni</code>) que llama a código nativo en C. La clave está en texto plano en la librería:</p>
  <pre><code># Ruta de la librería nativa:
app/src/main/jni/divajni.c

# La clave aparece sin cifrar en el código C
# Probarla directamente en la app da acceso</code></pre>

  <h2>Ejercicio 13 — Buffer Overflow en código nativo</h2>
  <p>La función nativa <code>initialLaunchSequence</code> define <code>CODESIZEMAX 20</code>. Al introducir más de 20 caracteres se produce un desbordamiento de buffer:</p>
  <pre><code># La constante en divajni.c:
#define CODESIZEMAX 20

# Al introducir más de 20 caracteres:
# → Desbordamiento de buffer
# → La app se bloquea (crash/DoS)

# Esto simula cómo un atacante puede crashear la app
# o en casos reales, ejecutar código arbitrario</code></pre>

  <h2>Resumen de ejercicios</h2>
  <table>
    <thead><tr><th>Ejercicio</th><th>Vulnerabilidad</th><th>Técnica</th></tr></thead>
    <tbody>
      <tr><td>9</td><td>Broken Access Control</td><td>am start directo</td></tr>
      <tr><td>10</td><td>Broken Access Control + PIN bypass</td><td>am start --ez false</td></tr>
      <tr><td>11</td><td>Content Provider inseguro</td><td>content query URI</td></tr>
      <tr><td>12</td><td>Hardcoded key en JNI</td><td>Análisis de .c nativo</td></tr>
      <tr><td>13</td><td>Buffer Overflow</td><td>Input > CODESIZEMAX</td></tr>
    </tbody>
  </table>
</div>
<?php else: ?>
<div class="prose">
  <p>Advanced DIVA audit — exercises 9–13: access control bypass, Content Providers, hardcoded JNI key and native buffer overflow.</p>

  <h2>Exercise 9 — API Credentials Without Auth</h2>
  <pre><code">adb shell logcat | grep "APICreds"
am start -n jakhar.aseem.diva/.APICreds1Activity</code></pre>
  <p>API credentials are exposed with no authentication required.</p>

  <h2>Exercise 10 — PIN Bypass</h2>
  <pre><code">am start -n jakhar.aseem.diva/.APICreds2Activity --ez check_pin false
# --ez inserts a boolean extra; false disables the PIN check</code></pre>

  <h2>Exercise 11 — Unprotected Content Provider</h2>
  <pre><code">content query --uri content://jakhar.aseem.diva.provider.notesprovider/notes
# Returns all private notes without any PIN</code></pre>

  <h2>Exercise 12 — Hardcoded Key in JNI</h2>
  <pre><code"># Inspect: app/src/main/jni/divajni.c
# The key is stored in plain text in the C source</code></pre>

  <h2>Exercise 13 — Buffer Overflow</h2>
  <pre><code"># divajni.c defines: #define CODESIZEMAX 20
# Entering 21+ characters crashes the app (DoS)
# In real scenarios: potential arbitrary code execution</code></pre>

  <h2>Summary</h2>
  <table>
    <thead><tr><th>Exercise</th><th>Vulnerability</th><th>Technique</th></tr></thead>
    <tbody>
      <tr><td>9</td><td>Broken Access Control</td><td>Direct am start</td></tr>
      <tr><td>10</td><td>PIN bypass</td><td>--ez check_pin false</td></tr>
      <tr><td>11</td><td>Insecure Content Provider</td><td>content query URI</td></tr>
      <tr><td>12</td><td>Hardcoded JNI key</td><td>Analyse .c native file</td></tr>
      <tr><td>13</td><td>Buffer Overflow</td><td>Input > CODESIZEMAX</td></tr>
    </tbody>
  </table>
</div>
<?php endif;
$contentBody=ob_get_clean();
require __DIR__.'/../templates/content-page.php';
