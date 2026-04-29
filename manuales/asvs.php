<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle    = $lang==='es' ? 'OWASP ASVS — Estándar de Verificación de Seguridad — CyberEscudo' : 'OWASP ASVS — Application Security Verification Standard — CyberEscudo';
$contentTitle = $lang==='es' ? 'OWASP ASVS: Estándar de Verificación de Seguridad en Aplicaciones' : 'OWASP ASVS: Application Security Verification Standard';
$contentDate  = '2022-05-10';
$contentTags  = ['ASVS','OWASP','Seguridad','Estándar','Verificación'];
ob_start();
if ($lang==='es'): ?>
<div class="prose">
  <p>El <strong>OWASP ASVS</strong> (Application Security Verification Standard) es una lista completa de requisitos de seguridad para aplicaciones web, usada por arquitectos, desarrolladores, testers y auditores.</p>

  <h2>Objetivos principales</h2>
  <ul>
    <li>Ayudar a las organizaciones a desarrollar y mantener aplicaciones seguras.</li>
    <li>Alinear los requerimientos entre proveedores de servicios de seguridad, herramientas y clientes.</li>
  </ul>

  <h2>Niveles de verificación</h2>
  <table>
    <thead><tr><th>Nivel</th><th>Nombre</th><th>Dirigido a</th></tr></thead>
    <tbody>
      <tr><td>Nivel 1</td><td>Oportunista</td><td>Todo el software. Defiende contra vulnerabilidades fáciles de explotar (OWASP Top 10). Análisis rápido, poco esfuerzo.</td></tr>
      <tr><td>Nivel 2</td><td>Estándar</td><td>Aplicaciones con datos sensibles. Controles en lugar adecuado, efectivos y en uso. Defensas contra atacantes con herramientas y motivación específica.</td></tr>
      <tr><td>Nivel 3</td><td>Avanzado</td><td>Aplicaciones críticas: transacciones de alto valor, datos médicos, infraestructuras críticas. Análisis profundo de arquitectura, código y testing.</td></tr>
    </tbody>
  </table>

  <h2>Cómo aplicarlo en la práctica</h2>
  <p>La mejor manera de usar ASVS es como <strong>lista de verificación de seguridad en el ciclo de desarrollo</strong>:</p>
  <ol>
    <li>Seleccionar el nivel apropiado (1, 2 o 3) según el tipo de aplicación.</li>
    <li>Asignar cada requisito a un responsable del equipo (arquitecto, desarrollador, QA).</li>
    <li>Usar ASVS para organizar el informe de pentest: estado de cada requisito + detalles.</li>
    <li>Re-verificar tras cada ciclo de desarrollo o corrección de vulnerabilidades.</li>
  </ol>

  <h2>Caso práctico — Universidad de Utah</h2>
  <p>El equipo rojo del campus utiliza ASVS como guía en sus tests de penetración a aplicaciones internas:</p>
  <ul>
    <li>Organiza las actividades del test y divide la carga de trabajo por requisito ASVS.</li>
    <li>Realiza seguimiento del estado de verificación durante el test.</li>
    <li>Estructura el informe final alrededor de ASVS: estado de cada control + evidencias.</li>
    <li>Permite al cliente priorizar remediaciones según el impacto de cada control fallido.</li>
  </ul>

  <h2>Áreas de verificación (objetivos de control)</h2>
  <table>
    <thead><tr><th>Sección</th><th>Área</th><th>Objetivo</th></tr></thead>
    <tbody>
      <tr><td>V1</td><td>Arquitectura y Diseño</td><td>Componentes identificados, arquitectura definida y código coherente con ella.</td></tr>
      <tr><td>V2</td><td>Autenticación</td><td>Identidad digital verificada, credenciales transportadas de forma segura.</td></tr>
      <tr><td>V3</td><td>Gestión de Sesiones</td><td>Sesiones únicas por usuario, invalidadas cuando no se necesitan, con tiempo de inactividad limitado.</td></tr>
      <tr><td>V4</td><td>Control de Acceso</td><td>Usuarios con credenciales válidas, roles y privilegios bien definidos, metadatos protegidos.</td></tr>
    </tbody>
  </table>
</div>
<?php else: ?>
<div class="prose">
  <p><strong>OWASP ASVS</strong> is a complete list of security requirements for web applications used by architects, developers, testers and auditors.</p>
  <table>
    <thead><tr><th>Level</th><th>Name</th><th>Target</th></tr></thead>
    <tbody>
      <tr><td>1</td><td>Opportunistic</td><td>All software. Defends against easy-to-find vulnerabilities.</td></tr>
      <tr><td>2</td><td>Standard</td><td>Apps with sensitive data. Most security risks addressed.</td></tr>
      <tr><td>3</td><td>Advanced</td><td>Critical apps requiring the highest level of trust.</td></tr>
    </tbody>
  </table>
</div>
<?php endif;
$contentBody=ob_get_clean();
require __DIR__.'/../templates/content-page.php';
