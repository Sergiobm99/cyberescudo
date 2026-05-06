<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Análisis APK InsecureBankv2 — CyberEscudo' : 'InsecureBankv2 APK Analysis — CyberEscudo';
$contentTitle = $lang==='es' ? 'Ingeniería Inversa: Análisis de InsecureBankv2' : 'Reverse Engineering: InsecureBankv2 Analysis';
$contentDate  = '2022-04-10';
$contentDiff  = 'intermediate';
$contentTags  = ['Android','APK','ADB','Reverse Engineering','Drozer','JADX'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p><strong>InsecureBankv2</strong> es una aplicación Android vulnerable por diseño, creada para que los analistas de seguridad practiquen ingeniería inversa, análisis estático (SAST) y análisis dinámico (DAST). En este manual abordaremos las vulnerabilidades más críticas de la arquitectura Android.</p>

  <h2>1. Análisis Estático (SAST): Desempaquetado e Ingeniería Inversa</h2>
  <p>Una APK es simplemente un archivo comprimido (ZIP). Sin embargo, su código fuente está compilado en bytecode (DEX). Necesitamos herramientas para revertirlo a formatos legibles.</p>
  
  <h3>Obtención del AndroidManifest y recursos (Apktool)</h3>
  <pre><code># Descompilar recursos y Manifest usando apktool en Kali Linux:
apktool d InsecureBankv2.apk -o InsecureBank_Source

# Leer el Manifest para identificar Actividades, Permisos y Broadcast Receivers:
cat InsecureBank_Source/AndroidManifest.xml</code></pre>

  <h3>Obtención del Código Fuente Java (JADX)</h3>
  <p>Leer código <em>Smali</em> es muy tedioso. <strong>JADX</strong> decompila los archivos <code>classes.dex</code> directamente a código Java legible, permitiéndonos buscar contraseñas hardcodeadas o lógica criptográfica débil.</p>
  <pre><code># Abrir la APK directamente en la interfaz gráfica de JADX:
jadx-gui InsecureBankv2.apk

# 1. Busca en "Resources/res/values/strings.xml" (Suele haber claves API aquí).
# 2. Revisa la clase "CryptoClass" (InsecureBank usa AES con una clave cifrada hardcodeada).</code></pre>

  <h2>2. Análisis de Permisos (AndroidManifest.xml)</h2>
  <p>Un buen analista primero revisa qué pide la app para funcionar.</p>
  <table>
    <thead><tr><th>Tipo de Permiso</th><th>Descripción</th><th>Riesgo</th></tr></thead>
    <tbody>
      <tr><td><strong>Normales</strong></td><td>Internet, Bluetooth. Se conceden automáticamente.</td><td>Bajo</td></tr>
      <tr><td><strong>Dangerous (Runtime)</strong></td><td>Cámara, Micrófono, Contactos, SMS, Ubicación. Requieren popup de confirmación al usuario.</td><td>Alto</td></tr>
      <tr><td><strong>Signature</strong></td><td>Permisos customizados. Solo apps firmadas por el mismo desarrollador pueden usarlos.</td><td>Bajo</td></tr>
      <tr><td><strong>Device Admin / Root</strong></td><td>Bloquear el teléfono, borrar datos o acceso completo a nivel Kernel.</td><td>Crítico</td></tr>
    </tbody>
  </table>

  <!-- ─── SECCIÓN DEL RETO CTF 24 (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Simulador ADB: Activity Bypass
      </h3>
      <p style="margin-bottom: 1.5rem;">Analizando el <code>AndroidManifest.xml</code>, descubres que la actividad de transferencias (<code>.DoTransfer</code>) tiene el atributo <code>android:exported="true"</code>. Utiliza el <strong>Android Debug Bridge (ADB)</strong> y el <strong>Activity Manager (am)</strong> para invocar directamente esta pantalla en el móvil conectado y saltarte el login.</p>
      <a href="/ctf/ctf-24.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 24
      </a>
  </div>

  <h2>3. Invocación de Actividades Exportadas (Bypass de Login)</h2>
  <p>Si una actividad (pantalla) en Android tiene <code>android:exported="true"</code>, cualquier otra aplicación o usuario del dispositivo puede abrirla directamente, saltándose el flujo lógico de la app (ej. saltándose la pantalla de Login).</p>
  <pre><code># 1. Conectarse al dispositivo Android virtual o físico:
adb connect 127.0.0.1:5555
adb shell

# 2. Iniciar una actividad específica forzadamente con el Activity Manager (am):
# Bypass hacia el panel post-login:
am start -n com.android.insecurebankv2/.PostLogin

# Bypass directo hacia la pantalla de cambio de contraseñas:
am start -n com.android.insecurebankv2/.ChangePassword</code></pre>

  <h2>4. Análisis Dinámico (DAST) y Fuga de Datos Locales</h2>
  <p>Almacenar datos sin cifrar en el propio teléfono es el error #1 en aplicaciones móviles.</p>

  <h3>Fuga de Logs (Logcat)</h3>
  <p>Los desarrolladores a menudo dejan funciones <code>Log.d()</code> en el código de producción que imprimen contraseñas o tokens en los registros globales del sistema.</p>
  <pre><code># Monitorizar los logs del dispositivo en tiempo real filtrando por la app:
adb logcat | grep "insecurebankv2"</code></pre>

  <h3>Shared Preferences y SQLite (Root Requerido)</h3>
  <p>Si el atacante roba el teléfono y logra hacerle Root, puede extraer los archivos locales de la app ubicados en <code>/data/data/com.android.insecurebankv2/</code>.</p>
  <pre><code>adb shell
su
cd /data/data/com.android.insecurebankv2/

# 1. Shared Preferences (Contraseñas guardadas en texto claro XML):
cat shared_prefs/mySharedPreferences.xml

# 2. Base de datos local SQLite (Historial de transacciones sin cifrar):
sqlite3 databases/mydb
sqlite> SELECT * FROM transfers;</code></pre>

  <h2>5. Drozer — El Framework de Pentesting Android</h2>
  <p><strong>Drozer</strong> es el equivalente a Metasploit para aplicaciones Android. Interactúa con las apps mediante IPC (Inter-Process Communication).</p>
  <pre><code># Conectarse al agente Drozer en el teléfono:
drozer console connect

# Identificar la superficie de ataque (Muestra qué está exportado):
dz> run app.package.attacksurface com.android.insecurebankv2

# Explotar Content Providers (Robo de datos a través de URIs vulnerables):
dz> run app.provider.query content://com.android.insecurebankv2.TrackUserContentProvider/trackerusers</code></pre>

  <h2>6. Herramientas de Análisis Online (VT vs Metadefender)</h2>
  <p>Para análisis automatizado de Malware en APKs, plataformas como VirusTotal son indispensables.</p>
  <ul>
      <li><strong>VirusTotal:</strong> Posee más de 70 motores. La pestaña <em>"Details"</em> muestra información de certificados y firmas. La pestaña <em>"Behavior"</em> ejecuta la APK en un Sandbox y te muestra a qué IPs se conecta, qué archivos crea y qué SMS intenta enviar.</li>
      <li><strong>Metadefender:</strong> Menos motores de detección, pero muy enfocado en metadatos y análisis estático puro de binarios. Útil como segunda opinión.</li>
  </ul>
</div>

<?php else: ?>
<div class="prose">
  <p><strong>InsecureBankv2</strong> is a vulnerable-by-design Android application created for security analysts to practice reverse engineering, static analysis (SAST), and dynamic analysis (DAST).</p>

  <h2>1. Static Analysis (SAST): Unpacking & Reverse Engineering</h2>
  <pre><code># Decompile resources and AndroidManifest.xml using apktool:
apktool d InsecureBankv2.apk -o InsecureBank_Source

# Decompile directly to readable Java code using JADX:
jadx-gui InsecureBankv2.apk</code></pre>

  <h2>2. Permission Analysis</h2>
  <p>A good analyst first reviews what the app requests in the <code>AndroidManifest.xml</code>.</p>
  <ul>
      <li><strong>Normal:</strong> Internet, Bluetooth. Granted automatically.</li>
      <li><strong>Dangerous (Runtime):</strong> Camera, Mic, Contacts, Location. Require user prompt.</li>
      <li><strong>Device Admin:</strong> Critical. Can lock the phone or wipe data.</li>
  </ul>

  <!-- ─── SECCIÓN DEL RETO CTF 24 (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> ADB Simulator: Activity Bypass
      </h3>
      <p style="margin-bottom: 1.5rem;">Analyzing the <code>AndroidManifest.xml</code>, you discover that the transfer activity (<code>.DoTransfer</code>) has the <code>android:exported="true"</code> attribute. Use the <strong>Android Debug Bridge (ADB)</strong> and the <strong>Activity Manager (am)</strong> to directly invoke this screen on the connected mobile device and bypass the login.</p>
      <a href="/ctf/ctf-24.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 24 CHALLENGE
      </a>
  </div>

  <h2>3. Invoking Exported Activities (Login Bypass)</h2>
  <p>If an activity is exported, any other app can open it directly, skipping the login screen.</p>
  <pre><code>adb shell
am start -n com.android.insecurebankv2/.PostLogin
am start -n com.android.insecurebankv2/.ChangePassword</code></pre>

  <h2>4. Dynamic Analysis (DAST) & Local Data Leaks</h2>
  <p>Storing unencrypted data on the phone is the #1 mistake in mobile apps.</p>
  <pre><code># Monitor logs for hardcoded credentials being printed:
adb logcat | grep "insecurebankv2"

# Check Shared Preferences for plaintext passwords (Requires Root):
adb shell
su
cat /data/data/com.android.insecurebankv2/shared_prefs/mySharedPreferences.xml</code></pre>

  <h2>5. Drozer — The Android Pentesting Framework</h2>
  <p>Drozer interacts with apps via IPC (Inter-Process Communication).</p>
  <pre><code>dz> run app.package.attacksurface com.android.insecurebankv2
dz> run app.provider.query content://com.android.insecurebankv2.TrackUserContentProvider/trackerusers</code></pre>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';