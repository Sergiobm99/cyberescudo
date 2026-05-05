<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'Plan de Respuesta a Incidentes — CyberEscudo' : 'Incident Response Plan — CyberEscudo';
$contentTitle = $lang==='es' ? 'Plan de Respuesta a Incidentes (IRP)' : 'Incident Response Plan (IRP)';
$contentDate  = '2024-08-20';
$contentDiff  = 'advanced';
$contentTags  = ['Blue Team','DFIR','SOC','SANS','NIST','Playbooks'];
ob_start(); if ($lang==='es'): ?>
<div class="prose">
  <p>La ciberseguridad al 100% no existe. Tarde o temprano, las defensas caerán. El <strong>Plan de Respuesta a Incidentes (IRP)</strong> define exactamente qué hacer cuando eso ocurre para minimizar el impacto, expulsar al atacante y recuperar el control. Utilizaremos el marco de trabajo estándar de la industria desarrollado por el <strong>SANS Institute (PICERL)</strong>.</p>

  <h2>Las 6 Fases de Respuesta (PICERL)</h2>
  
  <h3>1. Preparación (Preparation)</h3>
  <p>El 90% del éxito en la respuesta a un incidente se basa en esta fase. No puedes defender lo que no conoces ni actuar si no tienes las herramientas.</p>
  <ul>
      <li><strong>Inventario de activos:</strong> ¿Qué servidores y datos tenemos?</li>
      <li><strong>Logging centralizado (SIEM):</strong> Configurar la retención de logs. Si te atacan y no hay logs, estás ciego.</li>
      <li><strong>Playbooks:</strong> Guías paso a paso para ataques específicos (Ej: "Playbook contra Ransomware").</li>
      <li><strong>Equipo CSIRT:</strong> ¿Quién manda? ¿Quién avisa a la policía/medios? ¿Quién desconecta los cables?</li>
  </ul>

  <h3>2. Identificación (Identification / Detection)</h3>
  <p>El momento en el que saltan las alarmas (Alertas del EDR, del SIEM, o un usuario diciendo "mi pantalla tiene una calavera").</p>
  <pre><code># Triage de alertas comunes
- Inicios de sesión desde IPs geolocalizadas en zonas anómalas (Imposible Travel).
- Ejecución de PowerShell codificado en Base64.
- Picos de tráfico saliente de madrugada (Exfiltración de datos).

# Objetivo: Declarar formalmente si es un "Falso Positivo" o un "Incidente de Seguridad Crítico".</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 08 (ESPAÑOL) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> Simulador SOC (Triage & Contención)
      </h3>
      <p style="margin-bottom: 1.5rem;">Nuestro IDS ha detectado actividad anómala en un servidor web. Ponte el sombrero de Analista SOC Nivel 2: Analiza el log interceptado, encuentra el Indicador de Compromiso (IoC) y bloquea la IP enemiga en el firewall antes de que sea tarde.</p>
      <a href="/ctf/ctf-08.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ INICIAR RETO CTF 08
      </a>
  </div>

  <h3>3. Contención (Containment)</h3>
  <p>Detener la hemorragia. <strong>REGLA DE ORO: ¡No apagues el equipo infectado!</strong> Si lo apagas, destruirás la memoria RAM donde el malware suele ocultar sus claves o procesos activos.</p>
  <ul>
      <li><strong>Contención a corto plazo:</strong> Aislar el servidor de la red (desconectar el cable de red o aplicar reglas restrictivas de Firewall/VLAN) para que no propague el malware lateralmente, pero manteniéndolo encendido para análisis forense (DFIR).</li>
      <li><strong>Contención a largo plazo:</strong> Cambiar contraseñas comprometidas de administradores, revocar tokens de sesión.</li>
  </ul>

  <h3>4. Erradicación (Eradication)</h3>
  <p>Eliminar la causa raíz y las puertas traseras (backdoors) que dejó el atacante.</p>
  <pre><code># Acciones típicas de erradicación:
- Borrar tareas Cron y claves de registro de persistencia (Run/RunOnce).
- Eliminar cuentas de usuario creadas por el atacante.
- Parchear la vulnerabilidad explotada (Ej: Actualizar un plugin de WordPress).</code></pre>

  <h3>5. Recuperación (Recovery)</h3>
  <p>Devolver los sistemas a producción de forma cuidadosa y monitorizada.</p>
  <ul>
      <li>Restaurar servidores desde copias de seguridad limpias (Backups Offline).</li>
      <li>Monitorizar la red intensivamente durante las próximas 48h buscando señales de que el atacante sigue dentro.</li>
  </ul>

  <h3>6. Lecciones Aprendidas (Lessons Learned)</h3>
  <p>El paso más ignorado pero el más vital. Redactar un informe post-mortem respondiendo a: <em>¿Qué pasó? ¿Por qué pasó? ¿Qué hicimos bien? ¿Qué hicimos mal? ¿Cómo evitamos que nos pase el mes que viene?</em></p>
</div>

<?php else: ?>
<div class="prose">
  <p>100% cybersecurity doesn't exist. Sooner or later, defenses will fall. The <strong>Incident Response Plan (IRP)</strong> defines exactly what to do when that happens to minimize impact, expel the attacker, and recover operations. We use the industry-standard <strong>SANS Institute (PICERL)</strong> framework.</p>

  <h2>The 6 Phases of Incident Response (PICERL)</h2>
  
  <h3>1. Preparation</h3>
  <p>90% of incident response success relies on this phase. You cannot defend what you don't know exists.</p>
  <ul>
      <li><strong>Asset Inventory:</strong> What servers and data do we have?</li>
      <li><strong>Centralized Logging (SIEM):</strong> Configure log retention. If attacked without logs, you are blind.</li>
      <li><strong>Playbooks:</strong> Step-by-step guides for specific threats (e.g., Ransomware Playbook).</li>
  </ul>

  <h3>2. Identification (Detection)</h3>
  <p>The moment alarms trigger (EDR alerts, SIEM rules, or users reporting anomalies).</p>
  <pre><code># Common alert triage:
- Impossible travel logins.
- Base64 encoded PowerShell execution.
- Midnight outbound traffic spikes (Exfiltration).</code></pre>

  <!-- ─── SECCIÓN DEL RETO CTF 08 (INGLÉS) ─── -->
  <div style="margin: 3rem 0; padding: 1.5rem; background: rgba(0, 255, 255, 0.05); border-left: 4px solid var(--cyan); border-radius: 4px;">
      <h3 style="margin-top: 0; color: var(--cyan); display: flex; align-items: center; gap: 10px;">
          <span style="animation: pulse 2s infinite;">🔴</span> SOC Simulator (Triage & Containment)
      </h3>
      <p style="margin-bottom: 1.5rem;">Our IDS detected anomalous activity on a web server. Put on your Tier 2 SOC Analyst hat: Analyze the intercepted log, find the Indicator of Compromise (IoC), and block the enemy IP on the firewall before it's too late.</p>
      <a href="/ctf/ctf-08.php" style="display: inline-block; padding: 8px 20px; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); text-decoration: none; font-family: var(--mono); transition: all 0.3s; font-size: 0.9rem;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
          &gt;_ START CTF 08 CHALLENGE
      </a>
  </div>

  <h3>3. Containment</h3>
  <p>Stop the bleeding. <strong>GOLDEN RULE: Do not power off the infected machine!</strong> Powering it off destroys RAM memory where malware hides keys and active processes.</p>
  <ul>
      <li><strong>Short-term containment:</strong> Isolate the server from the network (unplug cable, apply strict VLAN/Firewall rules) to stop lateral movement, but keep it powered on for forensics (DFIR).</li>
      <li><strong>Long-term containment:</strong> Change compromised admin passwords, revoke session tokens.</li>
  </ul>

  <h3>4. Eradication</h3>
  <p>Remove the root cause and backdoors left by the attacker.</p>
  <pre><code># Typical eradication tasks:
- Delete persistence mechanisms (Cron jobs, Run registry keys).
- Remove unauthorized user accounts.
- Patch the exploited vulnerability.</code></pre>

  <h3>5. Recovery</h3>
  <p>Carefully return systems to production.</p>
  <ul>
      <li>Restore from clean offline backups.</li>
      <li>Intensively monitor the network for 48 hours to ensure the attacker is truly gone.</li>
  </ul>

  <h3>6. Lessons Learned</h3>
  <p>Write a post-mortem report: <em>What happened? Why? What did we do right/wrong? How do we prevent it next month?</em></p>
</div>
<?php endif; $contentBody=ob_get_clean(); require __DIR__.'/../templates/content-page.php';