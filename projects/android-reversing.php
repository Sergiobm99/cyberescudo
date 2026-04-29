<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Android Reversing: InsecureBankv2 y KGB Messenger — CyberEscudo' : 'Android Reversing: InsecureBankv2 & KGB Messenger — CyberEscudo';
$contentTitle = $lang==='es' ? 'Android Reversing: InsecureBankv2 y KGB Messenger' : 'Android Reversing: InsecureBankv2 & KGB Messenger';
$contentDate  = '2022-03-15';
$contentTags  = ['Android','APK','Reversing','dex2jar','Smali','CTF'];
ob_start();
if ($lang==='es'): ?>
<div class="prose">
  <p>Práctica de <strong>ingeniería inversa de APKs Android</strong> sobre InsecureBankv2 y el CTF KGB Messenger: obtención de código Java, modificación de smali y bypass de controles de acceso.</p>

  <h2>1. InsecureBankv2 — Reversing y corrección de vulnerabilidad</h2>

  <h3>Paso 1: Descompilar el APK con APK Easy Tool</h3>
  <p>Abrimos APK Easy Tool, cargamos la APK y pulsamos <em>Decompile</em>. Se genera el directorio con todos los archivos del proyecto.</p>

  <h3>Paso 2: Obtener el código Java con dex2jar + JD-GUI</h3>
  <p>En el directorio descompilado encontramos el archivo <code>classes.dex</code>. Lo convertimos a JAR:</p>
  <pre><code># Arrastrar classes.dex sobre d2j-dex2jar.bat o ejecutar:
d2j-dex2jar.bat classes.dex</code></pre>
  <p>Abrimos el <code>.jar</code> generado con <strong>JD-GUI</strong> → <em>File → Save All Sources</em> para obtener los archivos <code>.java</code>.</p>
  <p>Los fuentes se encuentran en la ruta: <code>com.android.insecurebankv2</code></p>

  <h3>Paso 3: Corregir la vulnerabilidad exported=true</h3>
  <p>Las actividades con <code>android:exported="true"</code> en el Manifest pueden ser invocadas desde cualquier otra aplicación. Para corregirlo:</p>
  <pre><code>&lt;!-- AndroidManifest.xml: cambiar exported a false --&gt;
&lt;activity android:name=".PostLogin"
          android:exported="false" /&gt;</code></pre>
  <p>Recompilamos el APK (APK Easy Tool firma automáticamente), lo desinstalamos del emulador y lo reinstalamos para verificar el cambio.</p>

  <h2>2. CTF KGB Messenger — Bypass completo</h2>

  <h3>Análisis inicial</h3>
  <pre><code># Analizar con VirusTotal antes de instalar
# Descompilar con APK Easy Tool
# Revisar AndroidManifest.xml → MainActivity como actividad principal</code></pre>

  <h3>Bypass del control de idioma (Rusia)</h3>
  <p>El código en <code>MainActivity.java</code> verifica que el locale sea "Russia". En el archivo <code>MainActivity.smali</code> cambiamos los saltos condicionales:</p>
  <pre><code># Cambiar if-nez que salta a :cond_0 → que salte a :cond_1
# Cambiar saltos de :cond_2 → :cond_3
# Esto hace que se salte la verificación de idioma</code></pre>
  <p>Recompilamos e instalamos: ahora muestra directamente la pantalla de login.</p>

  <h3>Obtener usuario y contraseña</h3>
  <p>En <code>MainActivty.smali</code> encontramos la referencia <code>7f0d0000</code>. En <code>res/values/strings.xml</code>:</p>
  <pre><code>&lt;!-- strings.xml contiene:
  - Una cadena codificada en Base64 (flag)
  - El usuario: Stearling Archer
  - La contraseña se obtiene por ingeniería social: "Guest" --&gt;</code></pre>

  <h3>Descifrar mensajes del chat</h3>
  <p>En <code>MessengerActivity.java</code> los strings <code>p</code> y <code>r</code> contienen cadenas cifradas que son los mensajes a descifrar para obtener las flags finales.</p>
</div>
<?php else: ?>
<div class="prose">
  <p>Android APK reverse engineering on <strong>InsecureBankv2</strong> and the <strong>KGB Messenger CTF</strong>: Java extraction, smali modification and access control bypass.</p>

  <h2>1. InsecureBankv2</h2>
  <pre><code">d2j-dex2jar.bat classes.dex   # Generate .jar from .dex
# Open .jar in JD-GUI → Save All Sources</code></pre>
  <p>Fix: set <code>android:exported="false"</code> for sensitive activities in the Manifest, recompile and reinstall.</p>

  <h2>2. KGB Messenger CTF</h2>
  <pre><code"># Bypass locale check in MainActivity.smali:
# Change if-nez jumps: cond_0 → cond_1, cond_2 → cond_3
# Find credentials in strings.xml (Base64 encoded)
# User: Stearling Archer | Password: Guest (social engineering)
# Decrypt chat messages in MessengerActivity.java</code></pre>
</div>
<?php endif;
$contentBody=ob_get_clean();
require __DIR__.'/../templates/content-page.php';
