<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Android Reversing: InsecureBankv2 y KGB Messenger — CyberEscudo' : 'Android Reversing: InsecureBankv2 & KGB Messenger — CyberEscudo';
$contentTitle = $lang==='es' ? 'Android Reversing: Modificación de Binarios (Smali)' : 'Android Reversing: Binary Patching (Smali)';
$contentDate  = '2022-03-15';
$contentDiff  = 'advanced';
$contentTags  = ['Android','APK','Reversing','Smali','dex2jar','Apktool'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p>La <strong>Ingeniería Inversa (Reversing)</strong> en Android consiste en desensamblar una aplicación (APK) para estudiar su lógica, extraer secretos o modificar su comportamiento (Patching). En esta guía abordaremos el parcheo de vulnerabilidades en <em>InsecureBankv2</em> y cómo saltarse los controles del famoso CTF <em>KGB Messenger</em>.</p>

  <h2>1. El Ciclo de Vida del Reversing</h2>
  <p>El código Java/Kotlin que escriben los desarrolladores se compila a un formato bytecode llamado <strong>DEX</strong> (Dalvik Executable). Para analizarlo, tenemos dos caminos:</p>
  <ul>
      <li><strong>Decompilar a Java (Para leer):</strong> Herramientas como <code>JADX</code> o la combinación <code>dex2jar + JD-GUI</code> intentan reconstruir el código Java original. Es ideal para leer y entender la lógica, pero <strong>no se puede recompilar</strong> de vuelta a APK.</li>
      <li><strong>Desensamblar a Smali (Para parchear):</strong> Usando <code>Apktool</code>, convertimos el DEX a <em>Smali</em>, una representación en texto del lenguaje de ensamblador de la Máquina Virtual de Android. El código Smali es feo y difícil de leer, pero <strong>sí se puede alterar y volver a compilar</strong>.</li>
  </ul>

  <h2>2. InsecureBankv2: Parcheando el AndroidManifest</h2>
  <p>En el laboratorio anterior vimos que la actividad <code>.PostLogin</code> estaba exportada, permitiendo un Bypass del Login. Vamos a parchear la APK para arreglar la vulnerabilidad.</p>
  
  <pre><code># 1. Desensamblar la APK original:
apktool d InsecureBankv2.apk -o InsecureBank_Source

# 2. Abrir InsecureBank_Source/AndroidManifest.xml con tu editor.
# Cambiar: android:exported="true" -> android:exported="false"

# 3. Recompilar la APK a partir de la carpeta modificada:
apktool b InsecureBank_Source -o InsecureBank_Patched.apk

# 4. Firmar la nueva APK (Android no instala apps sin firma):
# Generar una clave falsa:
keytool -genkey -v -keystore my-release-key.keystore -alias alias_name -keyalg RSA -keysize 2048 -validity 10000
# Firmar la APK:
apksigner sign --ks my-release-key.keystore InsecureBank_Patched.apk

# 5. Instalar la versión parcheada en el dispositivo:
adb install InsecureBank_Patched.apk</code></pre>

  <h2>3. KGB Messenger CTF: Bypass de Lógica en Smali</h2>
  <p>El CTF <em>KGB Messenger</em> es una app que, al abrirla, comprueba si el idioma de tu dispositivo está en ruso ("Russia"). Si no lo está, la app se cierra. Nuestro objetivo es parchear el binario para saltarnos este control.</p>

  <h3>Paso 1: Identificar el Control</h3>
  <p>Abriendo la APK en JADX, vemos algo como esto en <code>MainActivity.java</code>:</p>
  <pre><code>String locale = Locale.getDefault().getCountry();
if (!locale.equals("RU")) {
    System.exit(0);
}</code></pre>

  <h3>Paso 2: Parchear en Smali</h3>
  <p>Desensamblamos con Apktool y abrimos <code>smali/com/kgb/messenger/MainActivity.smali</code>. Buscamos la instrucción de salto condicional (Branching).</p>
  <pre><code># Código Smali original:
invoke-virtual {v0, v1}, Ljava/lang/String;->equals(Ljava/lang/Object;)Z
move-result v0
if-eqz v0, :cond_0   # "if-eqz" significa "If Equal to Zero" (Si NO son iguales, salta a cond_0 que cierra la app)
...
:cond_0
invoke-static {v0}, Ljava/lang/System;->exit(I)V</code></pre>
  
  <p><strong>El Parcheo:</strong> Cambiamos la instrucción <code>if-eqz</code> por <code>if-nez</code> (If Not Equal to Zero). Ahora la lógica se invierte: la app se cerrará <em>solo</em> si el dispositivo ESTÁ en ruso. Recompilamos, firmamos y la app se abrirá en nuestro emulador en inglés.</p>

  <!-- ─── SECCIÓN DEL RETO CTF 27 (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Simulador de Parcheo Smali
      </h3>
      <p style="margin-bottom: 1.5rem;">Te has infiltrado en el código ensamblador (Smali) de un Malware bancario. La app realiza una comprobación de seguridad: verifica si el dispositivo está "Rooteado". Demuestra tus habilidades de Reversing indicando cómo manipular las instrucciones para burlar esta defensa.</p>
      <a href="/ctf/ctf-27.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 27
      </a>
  </div>

  <h2>4. Obtención de Credenciales y Criptografía</h2>
  <p>Una vez dentro del KGB Messenger, nos pide un usuario y contraseña. En el código Java descompilado (JADX), vemos que la app carga un string llamado <code>user_name</code> y comprueba la contraseña contra un MD5.</p>
  <pre><code># En InsecureBank_Source/res/values/strings.xml encontramos:
&lt;string name="user_name"&gt;Stearling Archer&lt;/string&gt;
&lt;string name="flag_part1"&gt;RkxBR3tLNEc4X...&lt;/string&gt;</code></pre>
  <p>La contraseña no está en el código, pero aplicando OSINT/Ingeniería Social sobre el personaje "Sterling Archer", deducimos que la contraseña es <code>Guest</code>. Al introducirla, la app nos da acceso y podemos descifrar los mensajes en AES que están hardcodeados en <code>MessengerActivity.java</code>.</p>

  <h2>5. Glosario Rápido de Instrucciones Smali</h2>
  <table style="width:100%; border-collapse: collapse; margin-top: 1rem;">
      <tr style="border-bottom: 1px solid var(--cyan);"><th>Instrucción</th><th>Significado Lógico</th></tr>
      <tr><td><code>if-eqz v0, :cond_0</code></td><td>If Equal Zero (Si v0 == 0 / false, salta a cond_0)</td></tr>
      <tr><td><code>if-nez v0, :cond_0</code></td><td>If Not Equal Zero (Si v0 != 0 / true, salta a cond_0)</td></tr>
      <tr><td><code>move-result v0</code></td><td>Guarda el resultado de la última función en el registro v0</td></tr>
      <tr><td><code>const/4 v0, 0x1</code></td><td>Asigna el valor 1 (true) al registro v0</td></tr>
      <tr><td><code>goto :goto_0</code></td><td>Salto incondicional hacia la etiqueta :goto_0</td></tr>
  </table>
</div>

<?php else: ?>
<div class="prose">
  <p><strong>Reverse Engineering</strong> on Android consists of disassembling an application (APK) to study its logic, extract secrets, or modify its behavior (Patching). In this guide, we will cover patching vulnerabilities in <em>InsecureBankv2</em> and bypassing checks in the famous <em>KGB Messenger CTF</em>.</p>

  <h2>1. The Reversing Lifecycle</h2>
  <p>Java/Kotlin code is compiled into a bytecode format called <strong>DEX</strong>. We have two paths:</p>
  <ul>
      <li><strong>Decompile to Java (To Read):</strong> Tools like <code>JADX</code> or <code>dex2jar</code> rebuild the Java code. It's great for reading, but <strong>cannot be recompiled</strong> back into an APK.</li>
      <li><strong>Disassemble to Smali (To Patch):</strong> Using <code>Apktool</code>, we convert DEX to <em>Smali</em> (Android VM assembly language). Smali is hard to read, but it <strong>can be altered and recompiled</strong>.</li>
  </ul>

  <h2>2. Patching the AndroidManifest</h2>
  <pre><code># 1. Disassemble the APK:
apktool d InsecureBankv2.apk -o Source

# 2. Patch AndroidManifest.xml (Change android:exported to "false").

# 3. Rebuild the APK:
apktool b Source -o Patched.apk

# 4. Sign the new APK (Android requires signatures):
apksigner sign --ks my-release-key.keystore Patched.apk</code></pre>

  <h2>3. KGB Messenger CTF: Smali Logic Bypass</h2>
  <p>The app checks if the device's locale is "Russia". If not, it exits. We need to patch the binary.</p>

  <h3>Step 1: Identify the Check in JADX</h3>
  <pre><code>if (!locale.equals("RU")) { System.exit(0); }</code></pre>

  <h3>Step 2: Patch the Smali Code</h3>
  <p>We find the conditional branch in <code>MainActivity.smali</code>:</p>
  <pre><code>invoke-virtual {v0, v1}, Ljava/lang/String;->equals(Ljava/lang/Object;)Z
move-result v0
if-eqz v0, :cond_0   # "If Equal to Zero" (If not equal, jump to exit)</code></pre>
  
  <p><strong>The Patch:</strong> Change <code>if-eqz</code> to <code>if-nez</code> (If Not Equal to Zero). The logic inverts: the app will only exit if the device IS in Russian. Rebuild, sign, and install.</p>

  <!-- ─── SECCIÓN DEL RETO CTF 27 (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Smali Patching Simulator
      </h3>
      <p style="margin-bottom: 1.5rem;">You have infiltrated the assembly code (Smali) of a banking Malware. The app performs a security check: it verifies if the device is "Rooted". Show your Reversing skills by indicating how to manipulate the instructions to bypass this defense.</p>
      <a href="/ctf/ctf-27.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 27 CHALLENGE
      </a>
  </div>

  <h2>4. Smali Instruction Glossary</h2>
  <ul>
      <li><code>if-eqz v0, :cond</code> - If Equal Zero (If v0 is false, jump)</li>
      <li><code>if-nez v0, :cond</code> - If Not Equal Zero (If v0 is true, jump)</li>
      <li><code>move-result v0</code> - Save result of last method to v0</li>
  </ul>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';