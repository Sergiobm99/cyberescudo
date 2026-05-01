<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $lang==='es' ? 'Referencia de Puertos y Servicios — CyberEscudo' : 'Port & Service Reference — CyberEscudo';
$current_page = basename($_SERVER['PHP_SELF']);
require __DIR__ . '/templates/header.php';

// Definición de puertos estructurada en PHP
$portsData = [
  ['port'=>21, 'proto'=>'TCP', 'svc'=>'FTP', 'cat'=>'file', 'risk'=>'high', 'color'=>'#ff8040',
   'desc'=>$lang==='es'?'Transferencia de archivos. Login anónimo frecuente en CTFs.':'File transfer. Anonymous login common in CTFs.',
   'enum'=>['nmap -sV -p 21 --script ftp-anon,ftp-bounce IP','ftp IP\n# user: anonymous\n# pass: anonymous','hydra -l admin -P rockyou.txt ftp://IP'],
   'attacks'=>$lang==='es'?['Login anónimo','Bruteforce de credenciales','FTP Bounce','Directory traversal']:['Anonymous login','Credential brute-force','FTP Bounce','Directory traversal']],
  ['port'=>22, 'proto'=>'TCP', 'svc'=>'SSH', 'cat'=>'auth', 'risk'=>'medium', 'color'=>'#f0c000',
   'desc'=>$lang==='es'?'Shell remota segura. Bruteforce si contraseña débil.':'Secure remote shell. Brute-force if weak password.',
   'enum'=>['nmap -p 22 --script ssh2-enum-algos,ssh-auth-methods IP','ssh-audit IP','hydra -l root -P rockyou.txt ssh://IP'],
   'attacks'=>$lang==='es'?['Bruteforce de credenciales','Claves SSH débiles (RSA-1024)','Enumeración de usuarios (timing)']:['Credential brute-force','Weak SSH keys (RSA-1024)','User enumeration (timing)']],
  ['port'=>23, 'proto'=>'TCP', 'svc'=>'Telnet', 'cat'=>'auth', 'risk'=>'critical', 'color'=>'#ff5050',
   'desc'=>$lang==='es'?'Comunicación en texto claro. NUNCA usar en producción.':'Cleartext communication. NEVER use in production.',
   'enum'=>['nmap -p 23 --script telnet-ntlm-info,telnet-encryption IP','nc -nv IP 23'],
   'attacks'=>$lang==='es'?['Sniffing de credenciales','MITM','Bruteforce']:['Credential sniffing','MITM','Brute-force']],
  ['port'=>25, 'proto'=>'TCP', 'svc'=>'SMTP', 'cat'=>'net', 'risk'=>'medium', 'color'=>'#f0c000',
   'desc'=>$lang==='es'?'Envío de correo. Enumeración de usuarios con VRFY/EXPN.':'Mail sending. User enumeration via VRFY/EXPN.',
   'enum'=>['nmap -p 25 --script smtp-commands,smtp-enum-users IP','smtp-user-enum -M VRFY -U users.txt -t IP','nc -nv IP 25\nVRFY root'],
   'attacks'=>$lang==='es'?['Enumeración de usuarios','Open relay','Spoofing de email','Header injection']:['User enumeration','Open relay','Email spoofing','Header injection']],
  ['port'=>53, 'proto'=>'TCP/UDP', 'svc'=>'DNS', 'cat'=>'net', 'risk'=>'medium', 'color'=>'#f0c000',
   'desc'=>$lang==='es'?'Resolución de nombres. Transferencia de zona si mal configurado.':'Name resolution. Zone transfer if misconfigured.',
   'enum'=>['dig axfr @IP domain.com','nmap -p 53 --script dns-zone-transfer IP','fierce --domain domain.com'],
   'attacks'=>$lang==='es'?['Transferencia de zona (AXFR)','DNS cache poisoning','DNS amplification DDoS']:['Zone transfer (AXFR)','DNS cache poisoning','DNS amplification DDoS']],
  ['port'=>80, 'proto'=>'TCP', 'svc'=>'HTTP', 'cat'=>'web', 'risk'=>'high', 'color'=>'#ff8040',
   'desc'=>$lang==='es'?'Tráfico web no cifrado. Enumeración y ataques web.':'Unencrypted web traffic. Web enumeration and attacks.',
   'enum'=>['gobuster dir -u http://IP -w /usr/share/seclists/Discovery/Web-Content/common.txt -x php,html,txt','nikto -h http://IP','whatweb http://IP'],
   'attacks'=>$lang==='es'?['SQLi, XSS, LFI/RFI','SSRF','Directorios no protegidos','Archivos sensibles expuestos']:['SQLi, XSS, LFI/RFI','SSRF','Unprotected directories','Exposed sensitive files']],
  ['port'=>110, 'proto'=>'TCP', 'svc'=>'POP3', 'cat'=>'net', 'risk'=>'medium', 'color'=>'#f0c000',
   'desc'=>$lang==='es'?'Descarga de correo. Credenciales en texto claro.':'Mail retrieval. Cleartext credentials.',
   'enum'=>['nmap -p 110 --script pop3-capabilities IP','nc -nv IP 110\nUSER admin\nPASS password'],
   'attacks'=>$lang==='es'?['Bruteforce','Sniffing de credenciales']:['Brute-force','Credential sniffing']],
  ['port'=>135, 'proto'=>'TCP', 'svc'=>'RPC/DCOM', 'cat'=>'net', 'risk'=>'high', 'color'=>'#ff8040',
   'desc'=>$lang==='es'?'RPC de Windows. Punto de entrada para ataques en entornos Windows.':'Windows RPC. Entry point for attacks in Windows environments.',
   'enum'=>['nmap -p 135 --script msrpc-enum IP','rpcclient -U "" IP -N'],
   'attacks'=>$lang==='es'?['MS03-026 (Blaster)','Enumeración de usuarios RPC']:['MS03-026 (Blaster)','RPC user enumeration']],
  ['port'=>139, 'proto'=>'TCP', 'svc'=>'NetBIOS', 'cat'=>'net', 'risk'=>'high', 'color'=>'#ff8040',
   'desc'=>$lang==='es'?'Sesiones NetBIOS. Enumeración de usuarios y shares en Windows.':'NetBIOS sessions. User and share enumeration on Windows.',
   'enum'=>['enum4linux -a IP','nmblookup -A IP','smbclient -L //IP -N'],
   'attacks'=>$lang==='es'?['Enumeración de usuarios/shares','Pass-the-hash','NTLM relay']:['User/share enumeration','Pass-the-hash','NTLM relay']],
  ['port'=>143, 'proto'=>'TCP', 'svc'=>'IMAP', 'cat'=>'net', 'risk'=>'medium', 'color'=>'#f0c000',
   'desc'=>$lang==='es'?'Acceso a correo en servidor. Credenciales en texto claro si no TLS.':'Server-side mail access. Cleartext credentials without TLS.',
   'enum'=>['nmap -p 143 --script imap-capabilities IP','curl -v imap://IP --user admin:password'],
   'attacks'=>$lang==='es'?['Bruteforce','Sniffing IMAP']:['Brute-force','IMAP sniffing']],
  ['port'=>389, 'proto'=>'TCP', 'svc'=>'LDAP', 'cat'=>'auth', 'risk'=>'high', 'color'=>'#ff8040',
   'desc'=>$lang==='es'?'Directorio LDAP. Enumeración de usuarios, grupos y atributos AD.':'LDAP directory. Enumerate AD users, groups and attributes.',
   'enum'=>['ldapsearch -x -H ldap://IP -b "dc=domain,dc=com"','nmap -p 389 --script ldap-search IP','python3 bloodhound-python -u user -p pass -d domain.com -ns IP --zip'],
   'attacks'=>$lang==='es'?['LDAP injection','Null bind (acceso anónimo)','Enumeración de AD']:['LDAP injection','Null bind (anonymous access)','AD enumeration']],
  ['port'=>443, 'proto'=>'TCP', 'svc'=>'HTTPS', 'cat'=>'web', 'risk'=>'medium', 'color'=>'#f0c000',
   'desc'=>$lang==='es'?'Tráfico web cifrado. Analizar certificado y configuración TLS.':'Encrypted web traffic. Analyse certificate and TLS configuration.',
   'enum'=>['sslyze --certinfo --robot --heartbleed IP:443','testssl.sh IP:443','nmap -p 443 --script ssl-enum-ciphers IP'],
   'attacks'=>$lang==='es'?['SQLi, XSS en app web','Heartbleed (antiguo)','TLS downgrade','Certificado inválido']:['SQLi, XSS on web app','Heartbleed (legacy)','TLS downgrade','Invalid certificate']],
  ['port'=>445, 'proto'=>'TCP', 'svc'=>'SMB', 'cat'=>'file', 'risk'=>'critical', 'color'=>'#ff5050',
   'desc'=>$lang==='es'?'Compartición de archivos Windows. EternalBlue, NTLM relay, Pass-the-hash.':'Windows file sharing. EternalBlue, NTLM relay, Pass-the-hash.',
   'enum'=>['smbclient -L //IP -N','enum4linux-ng -A IP','crackmapexec smb IP','nmap -p 445 --script smb-vuln* IP'],
   'attacks'=>$lang==='es'?['EternalBlue (MS17-010)','NTLM relay','Pass-the-hash','Enumeración de shares','PrintNightmare']:['EternalBlue (MS17-010)','NTLM relay','Pass-the-hash','Share enumeration','PrintNightmare']],
  ['port'=>1433, 'proto'=>'TCP', 'svc'=>'MSSQL', 'cat'=>'db', 'risk'=>'critical', 'color'=>'#ff5050',
   'desc'=>$lang==='es'?'Microsoft SQL Server. RCE con xp_cmdshell si SA comprometida.':'Microsoft SQL Server. RCE via xp_cmdshell if SA compromised.',
   'enum'=>['nmap -p 1433 --script ms-sql-info,ms-sql-empty-password IP','crackmapexec mssql IP -u sa -p password','impacket-mssqlclient sa:password@IP'],
   'attacks'=>$lang==='es'?['xp_cmdshell RCE','SQLi','Login SA sin contraseña','Linked servers']:['xp_cmdshell RCE','SQLi','SA without password','Linked servers']],
  ['port'=>1521, 'proto'=>'TCP', 'svc'=>'Oracle DB', 'cat'=>'db', 'risk'=>'critical', 'color'=>'#ff5050',
   'desc'=>$lang==='es'?'Base de datos Oracle. SID enumeration para conectarse.':'Oracle database. SID enumeration to connect.',
   'enum'=>['nmap -p 1521 --script oracle-sid-brute IP','oscanner -s IP -P 1521'],
   'attacks'=>$lang==='es'?['SID enumeration','Bruteforce (sys, system)','Java stored procedures RCE']:['SID enumeration','Brute-force (sys, system)','Java stored procedures RCE']],
  ['port'=>2049, 'proto'=>'TCP', 'svc'=>'NFS', 'cat'=>'file', 'risk'=>'high', 'color'=>'#ff8040',
   'desc'=>$lang==='es'?'Network File System. Montar shares remotas sin autenticación.':'Network File System. Mount remote shares without authentication.',
   'enum'=>['showmount -e IP','nmap -p 2049 --script nfs-ls,nfs-showmount IP'],
   'attacks'=>$lang==='es'?['Montar shares con UID 0','Lectura de archivos sensibles','SSH key injection']:['Mount shares with UID 0','Sensitive file reading','SSH key injection']],
  ['port'=>3306, 'proto'=>'TCP', 'svc'=>'MySQL', 'cat'=>'db', 'risk'=>'critical', 'color'=>'#ff5050',
   'desc'=>$lang==='es'?'Base de datos MySQL/MariaDB. Credenciales por defecto frecuentes.':'MySQL/MariaDB database. Default credentials often present.',
   'enum'=>['nmap -p 3306 --script mysql-info,mysql-empty-password IP','mysql -h IP -u root -p','hydra -l root -P rockyou.txt mysql://IP'],
   'attacks'=>$lang==='es'?['Root sin contraseña','SQLi','UDF RCE','INTO OUTFILE webshell']:['Root without password','SQLi','UDF RCE','INTO OUTFILE webshell']],
  ['port'=>3389, 'proto'=>'TCP', 'svc'=>'RDP', 'cat'=>'auth', 'risk'=>'high', 'color'=>'#ff8040',
   'desc'=>$lang==='es'?'Remote Desktop Protocol. Acceso gráfico a Windows.':'Remote Desktop Protocol. Graphical access to Windows.',
   'enum'=>['nmap -p 3389 --script rdp-enum-encryption,rdp-vuln-ms12-020 IP','xfreerdp /v:IP /u:admin /p:password'],
   'attacks'=>$lang==='es'?['Bruteforce','BlueKeep (CVE-2019-0708)','DejaBlue','Pass-the-hash']:['Brute-force','BlueKeep (CVE-2019-0708)','DejaBlue','Pass-the-hash']],
  ['port'=>5432, 'proto'=>'TCP', 'svc'=>'PostgreSQL', 'cat'=>'db', 'risk'=>'high', 'color'=>'#ff8040',
   'desc'=>$lang==='es'?'Base de datos PostgreSQL. COPY TO/FROM para LFI/RFI.':'PostgreSQL database. COPY TO/FROM for LFI/RFI.',
   'enum'=>['nmap -p 5432 --script pgsql-brute IP','psql -h IP -U postgres'],
   'attacks'=>$lang==='es'?['Credenciales por defecto','COPY FROM PROGRAM RCE','SQLi']:['Default credentials','COPY FROM PROGRAM RCE','SQLi']],
  ['port'=>5900, 'proto'=>'TCP', 'svc'=>'VNC', 'cat'=>'auth', 'risk'=>'high', 'color'=>'#ff8040',
   'desc'=>$lang==='es'?'Virtual Network Computing. Acceso gráfico remoto sin cifrado.':'Virtual Network Computing. Unencrypted graphical remote access.',
   'enum'=>['nmap -p 5900 --script vnc-info,vnc-brute IP','hydra -P rockyou.txt vnc://IP'],
   'attacks'=>$lang==='es'?['Sin autenticación','Bruteforce','Sniffing de sesión']:['No authentication','Brute-force','Session sniffing']],
  ['port'=>6379, 'proto'=>'TCP', 'svc'=>'Redis', 'cat'=>'db', 'risk'=>'critical', 'color'=>'#ff5050',
   'desc'=>$lang==='es'?'Base de datos Redis. Sin auth por defecto → RCE frecuente.':'Redis database. No auth by default → frequent RCE.',
   'enum'=>['nmap -p 6379 --script redis-info IP','redis-cli -h IP INFO','redis-cli -h IP config get *'],
   'attacks'=>$lang==='es'?['Sin autenticación','Escritura SSH authorized_keys','Escritura cron RCE']:['No authentication','Write SSH authorized_keys','Write cron RCE']],
  ['port'=>8080, 'proto'=>'TCP', 'svc'=>'HTTP Alt', 'cat'=>'web', 'risk'=>'medium', 'color'=>'#f0c000',
   'desc'=>$lang==='es'?'HTTP alternativo. Tomcat Manager, Jenkins, Jira, proxies.':'Alternative HTTP. Tomcat Manager, Jenkins, Jira, proxies.',
   'enum'=>['curl -I http://IP:8080','gobuster dir -u http://IP:8080 -w common.txt','whatweb http://IP:8080'],
   'attacks'=>$lang==='es'?['Tomcat Manager (credenciales por defecto)','WAR malicioso','Jenkins RCE']:['Tomcat Manager (default credentials)','Malicious WAR','Jenkins RCE']],
  ['port'=>9200, 'proto'=>'TCP', 'svc'=>'Elasticsearch', 'cat'=>'db', 'risk'=>'critical', 'color'=>'#ff5050',
   'desc'=>$lang==='es'?'Elasticsearch. Sin auth por defecto → datos expuestos.':'Elasticsearch. No auth by default → exposed data.',
   'enum'=>['curl http://IP:9200/','curl http://IP:9200/_cat/indices','curl http://IP:9200/_all/_search?size=1'],
   'attacks'=>$lang==='es'?['Sin autenticación','Lectura de índices','Script injection']:['No authentication','Read indices','Script injection']],
  ['port'=>27017,'proto'=>'TCP', 'svc'=>'MongoDB', 'cat'=>'db', 'risk'=>'critical', 'color'=>'#ff5050',
   'desc'=>$lang==='es'?'MongoDB. Sin auth por defecto → acceso total a datos.':'MongoDB. No auth by default → full data access.',
   'enum'=>['nmap -p 27017 --script mongodb-info IP','mongo --host IP','mongosh IP'],
   'attacks'=>$lang==='es'?['Sin autenticación','NoSQL injection','Acceso a colecciones']:['No authentication','NoSQL injection','Access collections']],
];

$filters = [
  ['all',  $lang==='es'?'Todos':'All'],
  ['web',  'Web'],
  ['auth', 'Auth/Admin'],
  ['db',   'DB'],
  ['net',  $lang==='es'?'Red':'Network'],
  ['file', $lang==='es'?'Archivos':'Files'],
];
?>
<main class="content-page">
  <div class="m-bottom-2">
    <span class="section-label"><?= $lang==='es' ? '// HERRAMIENTAS' : '// TOOLS' ?></span>
    <h1><?= $lang==='es' ? 'Herramientas de Seguridad' : 'Security Tools' ?></h1>
  </div>
  
  <div class="tool-select-wrapper">
    <select id="tool-switcher" class="tool-selector">
      <option value="" disabled>-- <?= $lang==='es' ? 'Selecciona una herramienta' : 'Select a tool' ?> --</option>
      <option value="<?= BASE_URL ?>/tool-ip.php" <?= $current_page==='tool-ip.php' ? 'selected' : '' ?>>🌐 <?= $lang==='es' ? '¿Cuál es mi IP?' : 'What is my IP?' ?></option>
      <option value="<?= BASE_URL ?>/tool-passgen.php" <?= $current_page==='tool-passgen.php' ? 'selected' : '' ?>>🔑 <?= $lang==='es' ? 'Generador de Contraseñas' : 'Password Generator' ?></option>
      <option value="<?= BASE_URL ?>/tool-passcheck.php" <?= $current_page==='tool-passcheck.php' ? 'selected' : '' ?>>🛡️ <?= $lang==='es' ? 'Fortaleza de Contraseña' : 'Password Strength' ?></option>
      <option value="<?= BASE_URL ?>/tool-hash.php" <?= $current_page==='tool-hash.php' ? 'selected' : '' ?>>#️⃣ <?= $lang==='es' ? 'Generador de Hashes' : 'Hash Generator' ?></option>
      <option value="<?= BASE_URL ?>/tool-hashcrack.php" <?= $current_page==='tool-hashcrack.php' ? 'selected' : '' ?>>🔓 <?= $lang==='es' ? 'Analizador/Cracker de Hashes' : 'Hash Analyzer/Cracker' ?></option>
      <option value="<?= BASE_URL ?>/tool-base64.php" <?= $current_page==='tool-base64.php' ? 'selected' : '' ?>>🔄 <?= $lang==='es' ? 'Codificador/Decodificador Base64' : 'Base64 Encoder/Decoder' ?></option>
      <option value="<?= BASE_URL ?>/tool-cidr.php" <?= $current_page==='tool-cidr.php' ? 'selected' : '' ?>>🌍 <?= $lang==='es' ? 'Calculadora de Subredes CIDR' : 'CIDR Subnet Calculator' ?></option>
      <option value="<?= BASE_URL ?>/tool-jwt.php" <?= $current_page==='tool-jwt.php' ? 'selected' : '' ?>>🔓 <?= $lang==='es' ? 'Decodificador JWT' : 'JWT Decoder' ?></option>
      <option value="<?= BASE_URL ?>/tool-url.php" <?= $current_page==='tool-url.php' ? 'selected' : '' ?>>🔗 <?= $lang==='es' ? 'Codificador/Decodificador de URL' : 'URL Encoder/Decoder' ?></option>
      <option value="<?= BASE_URL ?>/tool-chmod.php" <?= $current_page==='tool-chmod.php' ? 'selected' : '' ?>>🐧 <?= $lang==='es' ? 'Calculadora Chmod Linux' : 'Linux Chmod Calculator' ?></option>
		<option value="<?= BASE_URL ?>/tool-mac.php" <?= $current_page==='tool-mac.php' ? 'selected' : '' ?>>🏷️ <?= $lang==='es' ? 'Buscador de Fabricante MAC' : 'MAC Vendor Lookup' ?></option>
		<option value="<?= BASE_URL ?>/tool-regex.php" <?= $current_page==='tool-regex.php' ? 'selected' : '' ?>>🛡️ <?= $lang==='es' ? 'Generador Regex Contraseñas' : 'Password Regex Generator' ?></option>
		<option value="<?= BASE_URL ?>/tool-revshell.php" <?= $current_page==='tool-revshell.php' ? 'selected' : '' ?>>🐚 <?= $lang==='es' ? 'Generador Reverse Shells' : 'Reverse Shell Generator' ?></option>
    <option value="<?= BASE_URL ?>/tool-cron.php" <?= $current_page==='tool-cron.php' ? 'selected' : '' ?>>⏱ <?= $lang==='es' ? 'Analizador Cron' : 'Cron Parser' ?></option>
    <option value="<?= BASE_URL ?>/tool-dns.php" <?= $current_page==='tool-dns.php' ? 'selected' : '' ?>>🔍 DNS Lookup</option>
    <option value="<?= BASE_URL ?>/tool-headers.php" <?= $current_page==='tool-headers.php' ? 'selected' : '' ?>>📋 <?= $lang==='es' ? 'Analizador de Headers HTTP' : 'HTTP Header Analyzer' ?></option>
    <option value="<?= BASE_URL ?>/tool-wordlist.php" <?= $current_page==='tool-wordlist.php' ? 'selected' : '' ?>>📝 <?= $lang==='es' ? 'Generador Wordlist' : 'Wordlist Generator' ?></option>
    <option value="<?= BASE_URL ?>/tool-multidecode.php" <?= $current_page==='tool-multidecode.php' ? 'selected' : '' ?>>🔄 <?= $lang==='es' ? 'Multi Decoder (CTF)' : 'Multi Decoder (CTF)' ?></option>
    <option value="<?= BASE_URL ?>/tool-httpbuilder.php" <?= $current_page==='tool-httpbuilder.php' ? 'selected' : '' ?>>📡 <?= $lang==='es' ? 'Generador de HTTP' : '📡 HTTP Builder' ?></option>
    <option value="<?= BASE_URL ?>/tool-waf.php" <?= $current_page==='tool-waf.php' ? 'selected' : '' ?>>🛡️ <?= $lang==='es' ? 'biblioteca de payloads para evadir WAFs' : '🛡️ WAF Bypass Payloads' ?></option>
    <option value="<?= BASE_URL ?>/tool-cloud.php" <?= $current_page==='tool-cloud.php' ? 'selected' : '' ?>>☁️ <?= $lang==='es' ? 'comandos de enumeración cloud interactivos' : '☁️ Cloud Enum' ?></option>
    <option value="<?= BASE_URL ?>/tool-loganalyzer.php" <?= $current_page==='tool-loganalyzer.php' ? 'selected' : '' ?>>📊 <?= $lang==='es' ? 'Analizador de Logs' : 'Log Analyzer' ?></option>
    <option value="<?= BASE_URL ?>/tool-cve.php" <?= $current_page==='tool-cve.php' ? 'selected' : '' ?>>🐛 <?= $lang==='es' ? 'Buscador CVE y Exploits' : 'CVE & Exploit Finder' ?></option>
    <option value="<?= BASE_URL ?>/tool-takeover.php" <?= $current_page==='tool-takeover.php' ? 'selected' : '' ?>>🏴‍☠️ <?= $lang==='es' ? 'Auditoría / Bug Bounty' : 'Subdomain Takeover' ?></option>
    <option value="<?= BASE_URL ?>/tool-recon.php" <?= $current_page==='tool-recon.php' ? 'selected' : '' ?>>🔍 <?= $lang==='es' ? 'Reconocimiento rápido OSINT' : 'OSINT Quick Recon' ?></option>
    <option value="<?= BASE_URL ?>/tool-ssh.php" <?= $current_page==='/tool-ssh.php' ? 'selected' : '' ?>>🔑 <?= $lang==='es' ? 'Analizador SSH' : 'SSH Analyzer' ?></option>
    <option value="<?= BASE_URL ?>/tool-ports.php" <?= $current_page==='/tool-ports.php' ? 'selected' : '' ?>>📋 <?= $lang==='es' ? 'Puertos de referencia' : 'Port Reference' ?></option>
    </select>
  </div>

  <div class="card">
    <div class="tool-header md-container port-header">
      <h2>📋 <?= $lang==='es'?'Referencia de Puertos y Servicios':'Port & Service Reference' ?></h2>
      <p><?= $lang==='es'
        ? 'Cheatsheet interactivo de los puertos más importantes en pentesting. Busca por número, nombre o protocolo. Incluye vectores de ataque y comandos de enumeración.'
        : 'Interactive cheatsheet of the most important pentesting ports. Search by number, name or protocol. Includes attack vectors and enumeration commands.' ?></p>
    </div>

    <!-- Search + filter -->
    <div class="port-controls">
      <div class="port-search-wrap">
        <input type="text" id="port-search" class="cyber-input m-bottom-0" placeholder="<?= $lang==='es'?'Buscar puerto, servicio, protocolo...':'Search port, service, protocol...' ?>">
      </div>
      <div class="port-filter-wrap">
        <?php foreach($filters as $f): ?>
        <button data-filter="<?= $f[0] ?>" class="port-filter-btn <?= $f[0]==='all'?'active':'' ?>"><?= $f[1] ?></button>
        <?php endforeach; ?>
      </div>
    </div>

    <div id="port-count" class="port-count"></div>
    <div id="port-list"></div>
  </div>
</main>

<!-- Inyección segura de datos a JS -->
<script nonce="<?= e($cspNonce) ?>">
  window.PORT_DATA = <?= json_encode($portsData) ?>;
  window.PORT_LANG = '<?= $lang ?>';
</script>

<script src="<?= BASE_URL ?>/assets/js/tools.js?v=<?= time() ?>"></script>
<?php require __DIR__ . '/templates/footer.php'; ?>