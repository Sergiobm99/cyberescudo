<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $lang==='es' ? 'Analizador de Claves SSH — CyberEscudo' : 'SSH Key Analyzer — CyberEscudo';
$current_page = basename($_SERVER['PHP_SELF']);
require __DIR__ . '/templates/header.php';
?>
<main class="content-page">
  <div class="m-bottom-2">
    <span class="section-label"><?= $lang==='es' ? '// HERRAMIENTAS' : '// TOOLS' ?></span>
    <h1><?= $lang==='es' ? 'Herramientas de Seguridad' : 'Security Tools' ?></h1>
  </div>
  
  <div class="tool-select-wrapper">
    <select id="tool-switcher" class="tool-selector">
      <option value="" disabled>-- <?= $lang==='es' ? 'Selecciona una herramienta' : 'Select a tool' ?> --</option>
      <option value="<?= BASE_URL ?>/tool-waf.php">🛡️ WAF Bypass</option>
      <option value="<?= BASE_URL ?>/tool-cloud.php">☁️ Cloud Enum</option>
      <option value="<?= BASE_URL ?>/tool-loganalyzer.php">📊 Log Analyzer</option>
      <option value="<?= BASE_URL ?>/tool-recon.php">🔍 OSINT Quick Recon</option>
      <option value="<?= BASE_URL ?>/tool-ssh.php" selected>🔑 SSH Analyzer</option>
      <option value="<?= BASE_URL ?>/tool-dnscheck.php">🌐 DNS Zone Enum</option>
      <option value="<?= BASE_URL ?>/tool-ports.php">📋 Port Reference</option>
      <option value="<?= BASE_URL ?>/tool-cve.php">⚠️ CVE Lookup</option>
    </select>
  </div>

  <div class="card">
    <div class="tool-header md-container ssh-header">
      <h2>🔑 <?= $lang==='es'?'Analizador de Claves SSH':'SSH Key Analyzer' ?></h2>
      <p><?= $lang==='es'
        ? 'Pega una clave pública SSH para analizarla: tipo, longitud de bits, comentario, fingerprint y si es segura. También genera comandos ssh-keygen listos para copiar.'
        : 'Paste an SSH public key to analyse it: type, bit length, comment, fingerprint and whether it is secure. Also generates ready-to-copy ssh-keygen commands.' ?></p>
    </div>

    <!-- Tabs -->
    <div class="ssh-tabs">
      <button data-tab="analyze" class="ssh-tab active"><?= $lang==='es'?'Analizar clave':'Analyze key' ?></button>
      <button data-tab="generate" class="ssh-tab"><?= $lang==='es'?'Generar comandos':'Generate commands' ?></button>
      <button data-tab="audit" class="ssh-tab"><?= $lang==='es'?'Auditar servidor':'Audit server' ?></button>
    </div>

    <!-- Tab: Analyze -->
    <div id="tab-analyze" class="ssh-tab-content active">
      <label class="ssh-label">
        <?= $lang==='es'?'Clave pública SSH (id_rsa.pub, id_ed25519.pub, authorized_keys…)':'SSH public key (id_rsa.pub, id_ed25519.pub, authorized_keys…)' ?>
      </label>
      <textarea id="ssh-key-input" class="cyber-input ssh-textarea" rows="5" placeholder="ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQC..."></textarea>
      
      <div class="ssh-action-bar">
        <button id="btn-analyze" class="ssh-btn-primary">
          🔍 <?= $lang==='es'?'Analizar':'Analyze' ?>
        </button>
        <button id="btn-example-key" class="ssh-btn-secondary">
          <?= $lang==='es'?'Ejemplo RSA 1024 (débil)':'Example RSA 1024 (weak)' ?>
        </button>
      </div>
      <div id="ssh-analysis"></div>
    </div>

    <!-- Tab: Generate -->
    <div id="tab-generate" class="ssh-tab-content">
      <p class="ssh-desc"><?= $lang==='es'
        ? 'Genera el comando ssh-keygen óptimo según el caso de uso. Todos los comandos se pueden copiar directamente en Kali/Ubuntu/macOS.'
        : 'Generate the optimal ssh-keygen command for each use case. All commands can be copied directly on Kali/Ubuntu/macOS.' ?></p>

      <?php
      $keyTypes = [
        ['ED25519 (recomendado)', '⭐', 'ed25519', $lang==='es'?'El más seguro y rápido. Clave de 256 bits equivale a RSA 3072. Soportado desde OpenSSH 6.5.':'Most secure and fastest. 256-bit key equivalent to RSA 3072. Supported since OpenSSH 6.5.',
          "ssh-keygen -t ed25519 -C 'user@host' -f ~/.ssh/id_ed25519"],
        ['RSA 4096', '🔒', 'rsa4096', $lang==='es'?'Máxima compatibilidad. Usar cuando el servidor no soporta ED25519.':'Maximum compatibility. Use when server does not support ED25519.',
          "ssh-keygen -t rsa -b 4096 -C 'user@host' -f ~/.ssh/id_rsa"],
        ['ECDSA P-256', '🔐', 'ecdsa', $lang==='es'?'Buena alternativa a ED25519. Soportado en más plataformas antiguas.':'Good alternative to ED25519. Supported on more legacy platforms.',
          "ssh-keygen -t ecdsa -b 256 -C 'user@host' -f ~/.ssh/id_ecdsa"],
        ['ED25519 con passphrase', '🔒⭐', 'ed25519pp', $lang==='es'?'Igual que ED25519 pero con passphrase. Máxima seguridad para uso personal.':'Same as ED25519 but with passphrase. Maximum security for personal use.',
          "ssh-keygen -t ed25519 -C 'user@host' -f ~/.ssh/id_ed25519\n# Te pedirá passphrase durante la generación"],
        ['Clave para CI/CD (sin passphrase)', '⚙️', 'cicd', $lang==='es'?'Para pipelines automatizados. Guardar la privada en secrets del CI/CD.':'For automated pipelines. Store private key in CI/CD secrets.',
          "ssh-keygen -t ed25519 -C 'ci-deploy@pipeline' -f ~/.ssh/deploy_key -N ''"],
        ['Clave de servidor (host key)', '🖥️', 'hostkey', $lang==='es'?'Para regenerar las host keys de un servidor SSH.':'To regenerate SSH server host keys.',
          "# ED25519 host key:\nssh-keygen -t ed25519 -f /etc/ssh/ssh_host_ed25519_key -N ''\n# RSA host key:\nssh-keygen -t rsa -b 4096 -f /etc/ssh/ssh_host_rsa_key -N ''"],
      ];
      foreach($keyTypes as $kt): ?>
      <div class="ssh-gen-card">
        <div class="ssh-gen-header">
          <span class="ssh-gen-icon"><?= $kt[1] ?></span>
          <strong class="ssh-gen-title"><?= $kt[0] ?></strong>
          <span class="ssh-gen-desc"><?= $kt[3] ?></span>
        </div>
        <div class="ssh-cmd-wrap">
          <pre class="ssh-cmd-pre"><?= htmlspecialchars($kt[4]) ?></pre>
          <button class="ssh-copy-btn" data-cmd="<?= htmlspecialchars($kt[4]) ?>">📋</button>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Tab: Audit server -->
    <div id="tab-audit" class="ssh-tab-content">
      <p class="ssh-desc"><?= $lang==='es'
        ? 'Introduce el servidor SSH a auditar y genera comandos para verificar su configuración y detectar debilidades.'
        : 'Enter the SSH server to audit and generate commands to check its configuration and detect weaknesses.' ?></p>
      <div class="m-bottom-1">
        <label class="ssh-label">Host</label>
        <input type="text" id="ssh-host" class="cyber-input m-bottom-0" placeholder="192.168.1.1">
      </div>
      <div id="ssh-audit-cmds"></div>
    </div>
  </div>
</main>

<script src="<?= BASE_URL ?>/assets/js/tools.js?v=<?= time() ?>"></script>
<?php require __DIR__ . '/templates/footer.php'; ?>