<?php
/**
 * CyberEscudo — Proyecto: Auditoría DIVA
 * Contenido: Práctica Android Pentesting
 */
require_once __DIR__ . '/../bootstrap.php';

$pageTitle    = $lang === 'es' ? 'Auditoría DIVA: Vulnerabilidades Android — CyberEscudo' : 'DIVA Audit: Android Vulnerabilities — CyberEscudo';
$contentTitle = $lang === 'es' ? 'Auditoría DIVA: Vulnerabilidades Android' : 'DIVA Audit: Android Vulnerabilities';
$contentDate  = '2022-04-01';
$contentDiff  = 'intermediate';
$contentTags  = ['DIVA','Android','ADB','SQLite','Reverse Engineering','SAST', 'DAST'];

ob_start();
if ($lang === 'es'): ?>
<div class="prose">
  <p><strong>DIVA (Damn Insecure and Vulnerable App)</strong> es una aplicación Android diseñada intencionalmente con fallos críticos de seguridad. Esta auditoría práctica aborda el análisis estático (SAST) y dinámico (DAST) demostrando cómo los errores de desarrollo comprometen los datos del usuario, y cómo mitigarlos.</p>

  <h2>Concepto Clave: El Sandboxing en Android</h2>
  <p>En Android, cada aplicación se ejecuta en su propia "caja de arena" (Sandbox) con un identificador de usuario único (UID). Por defecto, una app no puede leer los archivos de otra app. Sin embargo, si el dispositivo está <em>rooteado</em> (o si usamos ADB en un emulador), podemos escalar a <code>root</code> y eludir el Sandbox, accediendo al directorio <code>/data/data/</code> donde las apps guardan su información privada.</p>

  <h2>1. Insecure Logging (Fugas de Información en Registros)</h2>
  <p>Los desarrolladores usan la clase <code>Log</code> de Android para depurar errores. Si estos logs no se eliminan en Producción, cualquier aplicación con el permiso <code>READ_LOGS</code> (o un atacante con acceso físico/ADB) puede leer información crítica.</p>
  <pre><code># Acceder a la terminal del dispositivo Android:
adb shell

# DIVA procesa tarjetas de crédito y las imprime en el logcat. 
# Filtramos la salida en tiempo real buscando la etiqueta "diva-log":
logcat | grep "diva-log"</code></pre>
  <p><strong>Mitigación:</strong> Utilizar herramientas como <em>ProGuard</em> o <em>R8</em> para eliminar automáticamente las llamadas a <code>Log.d()</code> y <code>Log.i()</code> al compilar la versión <em>Release</em> de la APK.</p>

  <h2>2. Hardcoding Issues (Secretos en el Código Fuente)</h2>
  <p>Nunca confíes en el cliente. Una APK puede ser descompilada fácilmente. DIVA esconde una "Vendor Key" directamente en el código Java.</p>
  <pre><code># 1. Extraer la APK del dispositivo o descargarla.
# 2. Abrir la APK con JADX (Decompilador de Dalvik a Java):
jadx-gui diva.apk

# 3. Navegar a: jakhar.aseem.diva -> HardcodeActivity
# La clave aparecerá en texto plano: String vendorKey = "vendorsecretkey";</code></pre>
  <p><strong>Mitigación:</strong> Las claves de API críticas no deben estar en la APK. Deben ser solicitadas dinámicamente a un servidor backend seguro tras autenticar al usuario, o usar ofuscación avanzada (NDK / C++ JNI) para dificultar (no impedir) su extracción.</p>

  <!-- ─── SECCIÓN DEL RETO CTF 25 (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Simulador Forense Android
      </h3>
      <p style="margin-bottom: 1.5rem;">Como auditor móvil, necesitas acceder a las entrañas del sistema de archivos de Android y utilizar las herramientas correctas para extraer la información. Responde a las preguntas de análisis para completar la auditoría de la aplicación DIVA.</p>
      <a href="/ctf/ctf-25.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 25
      </a>
  </div>

  <h2>3. Insecure Data Storage: SharedPreferences</h2>
  <p><em>SharedPreferences</em> es un mecanismo de Android para guardar pequeñas configuraciones (como el modo oscuro). DIVA comete el error de usarlo para guardar el usuario y la contraseña.</p>
  <pre><code># Explorar el directorio privado de la aplicación usando ADB (requiere root):
adb shell
su
cd /data/data/jakhar.aseem.diva/shared_prefs/

# Ver el archivo XML en texto plano:
cat jakhar.aseem.diva_preferences.xml</code></pre>
  <p><strong>Mitigación:</strong> Utilizar <code>EncryptedSharedPreferences</code> (de la librería AndroidX Security), que cifra automáticamente las claves y valores utilizando el Android Keystore System.</p>

  <h2>4. Insecure Data Storage: SQLite Databases</h2>
  <p>Para datos más complejos, las apps usan bases de datos locales SQLite. Si el teléfono cae en malas manos o es infectado por un troyano con root, la base de datos entera queda expuesta.</p>
  <pre><code># Navegar a la carpeta de bases de datos de la app:
adb shell
su
cd /data/data/jakhar.aseem.diva/databases/

# Interactuar con la base de datos usando la CLI de sqlite3:
sqlite3 ids2
sqlite> .tables
sqlite> .headers on
sqlite> SELECT * FROM myuser;
# ¡Contraseñas expuestas sin cifrar (ni siquiera hasheadas)!</code></pre>
  <p><strong>Mitigación:</strong> Utilizar <strong>SQLCipher</strong>, una extensión open-source de SQLite que provee cifrado AES-256 transparente para los archivos de la base de datos completa.</p>

  <h2>5. Insecure Data Storage: Almacenamiento Externo (SD Card)</h2>
  <p>Almacenar archivos en <code>/sdcard/</code> o en el almacenamiento externo público significa que <strong>CUALQUIER</strong> aplicación en el teléfono con el permiso <code>READ_EXTERNAL_STORAGE</code> puede leer ese archivo. DIVA guarda un archivo temporal de credenciales allí.</p>
  <pre><code># Buscar archivos en el almacenamiento externo:
adb shell
cd /sdcard/
ls -la | grep uinfo
cat .uinfo.txt</code></pre>
  <p><strong>Mitigación:</strong> Almacenar siempre la información sensible en el Almacenamiento Interno (Internal Storage), que está protegido por el Sandbox de la aplicación.</p>

  <h2>6. Input Validation: SQL Injection en Android</h2>
  <p>Las inyecciones SQL no solo ocurren en servidores web; las bases de datos locales (SQLite) de Android también son vulnerables si se concatenan strings de entrada del usuario de forma insegura.</p>
  <p>En el formulario de DIVA, si el usuario introduce <code>' OR '1'='1</code>, la consulta interna se convierte en: <code>SELECT * FROM users WHERE username='' OR '1'='1'</code>, devolviendo todos los usuarios registrados en el dispositivo.</p>
  <p><strong>Mitigación:</strong> Utilizar siempre <em>Prepared Statements</em> (Consultas Preparadas) mediante la API de Android (<code>db.rawQuery("SELECT * FROM users WHERE user=?", new String[]{userInput});</code>).</p>

</div>

<?php else: ?>
<div class="prose">
  <p><strong>DIVA (Damn Insecure and Vulnerable App)</strong> is an Android application intentionally designed with critical security flaws. This practical audit covers static (SAST) and dynamic (DAST) analysis, demonstrating how development errors compromise user data and how to mitigate them.</p>

  <h2>Key Concept: Android Sandboxing</h2>
  <p>In Android, each app runs in its own "Sandbox" with a unique User ID (UID). By default, an app cannot read another app's files. However, on a <em>rooted</em> device, an attacker can bypass the Sandbox and access the <code>/data/data/</code> directory where apps store private information.</p>

  <h2>1. Insecure Logging</h2>
  <p>Developers use the <code>Log</code> class for debugging. If these logs are not removed in Production, any app with the <code>READ_LOGS</code> permission can read critical info.</p>
  <pre><code>adb shell
# DIVA processes credit cards and prints them in logcat:
logcat | grep "diva-log"</code></pre>
  <p><strong>Mitigation:</strong> Use tools like <em>ProGuard</em> or <em>R8</em> to strip <code>Log.d()</code> calls during the Release build.</p>

  <h2>2. Hardcoding Issues (Secrets in Source Code)</h2>
  <p>An APK can be easily decompiled. DIVA hides a "Vendor Key" directly in the Java code.</p>
  <pre><code># 1. Open the APK with JADX (Dalvik to Java Decompiler):
jadx-gui diva.apk
# 2. Navigate to HardcodeActivity to see the plaintext string.</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 25 (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Android Forensics Simulator
      </h3>
      <p style="margin-bottom: 1.5rem;">As a mobile auditor, you need to access the guts of the Android filesystem and use the correct tools to extract information. Answer the analysis questions to complete the DIVA application audit.</p>
      <a href="/ctf/ctf-25.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 25 CHALLENGE
      </a>
  </div>

  <h2>3. Insecure Data Storage: SharedPreferences</h2>
  <pre><code>adb shell
su
cd /data/data/jakhar.aseem.diva/shared_prefs/
cat jakhar.aseem.diva_preferences.xml</code></pre>
  <p><strong>Mitigation:</strong> Use <code>EncryptedSharedPreferences</code> which automatically encrypts keys and values using the Android Keystore.</p>

  <h2>4. Insecure Data Storage: SQLite Databases</h2>
  <pre><code>adb shell
su
cd /data/data/jakhar.aseem.diva/databases/
sqlite3 ids2
sqlite> SELECT * FROM myuser;</code></pre>
  <p><strong>Mitigation:</strong> Use <strong>SQLCipher</strong>, an open-source SQLite extension that provides transparent 256-bit AES encryption.</p>

  <h2>5. Insecure Data Storage: External Storage</h2>
  <p>Storing files in <code>/sdcard/</code> means ANY app with the <code>READ_EXTERNAL_STORAGE</code> permission can read them.</p>

  <h2>6. Input Validation: SQLite Injection</h2>
  <p>Local SQLite databases are vulnerable if user input strings are concatenated insecurely. Injecting <code>' OR '1'='1</code> returns all users.</p>
  <p><strong>Mitigation:</strong> Always use <em>Prepared Statements</em>.</p>
</div>
<?php endif;
$contentBody = ob_get_clean();
require __DIR__ . '/../templates/content-page.php';