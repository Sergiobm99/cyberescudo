<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'DIVA Avanzado: IPC, NDK y Buffer Overflow — CyberEscudo' : 'DIVA Advanced: IPC, NDK & Buffer Overflow — CyberEscudo';
$contentTitle = $lang==='es' ? 'DIVA Avanzado: IPC, NDK y Buffer Overflow' : 'DIVA Advanced: IPC, NDK & Buffer Overflow';
$contentDate  = '2022-04-20';
$contentDiff  = 'advanced';
$contentTags  = ['Android','ADB','Buffer Overflow','Content Provider','JNI', 'NDK'];
ob_start();
if ($lang==='es'): ?>
<div class="prose">
  <p>Profundización en la auditoría de <strong>DIVA (Damn Insecure and Vulnerable App)</strong>. En esta segunda parte (Ejercicios 9 al 13), abandonamos los errores de almacenamiento básico y entramos en el núcleo de la arquitectura Android: vulnerabilidades de Comunicación Inter-Procesos (IPC) y la explotación de código nativo (C/C++) mediante el NDK.</p>

  <h2>Ejercicio 9: Acceso a Credenciales (Broken Access Control)</h2>
  <p>En Android, las "Activities" (pantallas) deben ser privadas a menos que estén diseñadas explícitamente para ser llamadas desde fuera (ej. la pantalla de compartir un archivo). Si el desarrollador olvida protegerlas, cualquier app puede invocarlas.</p>
  <pre><code># 1. Identificamos el nombre del componente exportado analizando el Manifest:
# &lt;activity android:name=".APICreds1Activity" android:exported="true" /&gt;

# 2. Invocamos la actividad directamente desde ADB, saltándonos el flujo lógico:
am start -n jakhar.aseem.diva/.APICreds1Activity</code></pre>
  <p><strong>Resultado y Mitigación:</strong> La pantalla se abre y muestra las claves API en texto claro. Para mitigarlo, simplemente hay que definir <code>android:exported="false"</code> en el Manifest.</p>

  <h2>Ejercicio 10: Bypass de Lógica mediante Intent Extras</h2>
  <p>A veces, los desarrolladores intentan proteger una actividad pasando parámetros (Extras) en el <em>Intent</em> (el mensaje de invocación). <code>APICreds2Activity</code> requiere un PIN, pero decide si verificarlo o no basándose en un parámetro booleano llamado <code>check_pin</code>.</p>
  <pre><code># Al invocar la actividad, inyectamos nuestro propio parámetro para engañar a la app:
am start -n jakhar.aseem.diva/.APICreds2Activity --ez check_pin false

# Glosario de Inyección de Intents en ADB:
# --ez (Extra Boolean): false / true
# --es (Extra String): "texto"
# --ei (Extra Integer): 1234</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 26 (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Simulador IPC: Content Providers
      </h3>
      <p style="margin-bottom: 1.5rem;">Analizando otra aplicación bancaria, descubres que el repositorio de claves criptográficas se está compartiendo con el sistema a través de una URI mal configurada. Usa la herramienta de consultas de Android para volcar los datos.</p>
      <a href="/ctf/ctf-26.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 26
      </a>
  </div>

  <h2>Ejercicio 11: Fuga Masiva en Content Provider</h2>
  <p>Los <strong>Content Providers</strong> son la forma oficial en que las apps comparten bases de datos con otras apps (ej. WhatsApp leyendo tus Contactos). Si un Content Provider de datos sensibles se exporta sin permisos de firma, es un desastre.</p>
  <pre><code># Consultar el Content Provider exportado como si fuéramos una app de terceros:
content query --uri content://jakhar.aseem.diva.provider.notesprovider/notes

# Resultado: Vuelca toda la base de datos de notas confidenciales en la consola.</code></pre>

  <h2>Ejercicio 12: Secretos en Código Nativo JNI (C/C++)</h2>
  <p>Muchos desarrolladores creen que si compilan su lógica de negocio o claves criptográficas en una librería compartida de C/C++ (archivos <code>.so</code> a través de JNI), los atacantes no podrán leerlo porque ya no es Java. <strong>Falso.</strong></p>
  <pre><code># Extraer la librería nativa de la APK:
unzip diva.apk -d diva_unzipped/
cd diva_unzipped/lib/x86/

# Extraer todas las cadenas de texto imprimibles del archivo binario compilado:
strings libdivajni.so | grep -i "secret"
# Alternativamente, usar Ghidra o IDA Pro para desensamblar la librería.</code></pre>

  <h2>Ejercicio 13: Buffer Overflow en Android (ARM/x86)</h2>
  <p>Las aplicaciones Java (Dalvik/ART) son inmunes a los desbordamientos de buffer clásicos porque la Máquina Virtual lanza una excepción <code>IndexOutOfBounds</code>. Sin embargo, si la app usa JNI para ejecutar código C/C++ mal programado (usando funciones inseguras como <code>strcpy</code>), la aplicación hereda las vulnerabilidades nativas.</p>
  <pre><code>// El código nativo en divajni.c define un buffer fijo:
#define CODESIZEMAX 20
char input_buffer[CODESIZEMAX];
strcpy(input_buffer, user_input); // <- VULNERABILIDAD CRÍTICA</code></pre>
  <p><strong>Explotación:</strong> Al introducir más de 20 caracteres en el campo de texto de la aplicación, el buffer se desborda, sobrescribiendo el puntero de instrucción (EIP/RIP) en la pila de memoria. La aplicación colapsa inmediatamente (<strong>SEGFAULT / DoS</strong>). Un atacante avanzado podría inyectar una <em>shellcode</em> (ROP Chain) en ese desbordamiento para ejecutar código arbitrario con los permisos de la aplicación.</p>

</div>

<?php else: ?>
<div class="prose">
  <p>Deep dive into the <strong>DIVA</strong> audit (Exercises 9 to 13). We transition from basic storage errors to the core of Android architecture: Inter-Process Communication (IPC) vulnerabilities and native code (C/C++) exploitation via the NDK.</p>

  <h2>Exercise 9: API Credentials (Broken Access Control)</h2>
  <p>If developers forget to protect an Activity, any app can invoke it.</p>
  <pre><code># 1. Identify the exported component in the Manifest.
# 2. Invoke the activity directly via ADB:
am start -n jakhar.aseem.diva/.APICreds1Activity</code></pre>

  <h2>Exercise 10: Logic Bypass via Intent Extras</h2>
  <p>Developers sometimes try to protect activities using Intent parameters. We can inject our own extras to bypass checks.</p>
  <pre><code># Inject a boolean extra to disable PIN verification:
am start -n jakhar.aseem.diva/.APICreds2Activity --ez check_pin false</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 26 (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> IPC Simulator: Content Providers
      </h3>
      <p style="margin-bottom: 1.5rem;">While analyzing another banking app, you discover the cryptographic key repository is shared system-wide through a misconfigured URI. Use the Android query tool to dump the data.</p>
      <a href="/ctf/ctf-26.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 26 CHALLENGE
      </a>
  </div>

  <h2>Exercise 11: Unprotected Content Provider</h2>
  <p><strong>Content Providers</strong> share databases with other apps. If exported without signature permissions, it's catastrophic.</p>
  <pre><code># Query the exported Content Provider:
content query --uri content://jakhar.aseem.diva.provider.notesprovider/notes</code></pre>

  <h2>Exercise 12: Hardcoded JNI Keys</h2>
  <p>Developers mistakenly believe compiling keys into C/C++ shared libraries (<code>.so</code> files) hides them.</p>
  <pre><code># Extract printable strings from the binary:
strings libdivajni.so | grep -i "secret"</code></pre>

  <h2>Exercise 13: Native Buffer Overflow</h2>
  <p>Java apps are immune to buffer overflows, but when using unsafe JNI C/C++ functions (like <code>strcpy</code>), native vulnerabilities emerge.</p>
  <p>Entering more than 20 characters overwrites the instruction pointer on the stack, causing an immediate crash (<strong>SEGFAULT / DoS</strong>). Advanced attackers could use this to achieve Arbitrary Code Execution via ROP chains.</p>

</div>
<?php endif;
$contentBody=ob_get_clean();
require __DIR__.'/../templates/content-page.php';