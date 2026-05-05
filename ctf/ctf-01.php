<?php
// Usamos ../ para subir un nivel y volver a la raíz
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = 'Reto CTF #01: Authentication Bypass — CyberEscudo';
$pageDescription = 'Pon a prueba tus habilidades de inyección SQL en este entorno simulado y seguro.';
$current_page = 'ctf/ctf-01.php';
require __DIR__ . '/../templates/header.php';

$mensaje = "";
$flag = "";
$exito = false;

// Lógica de simulación del CTF (100% Seguro)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    
    $user_lower = strtolower($user);
    
    // Comprobamos si el usuario intenta un Bypass clásico de SQLi
    if (strpos($user_lower, "' or") !== false || strpos($user_lower, "'or") !== false || strpos($user, "' --") !== false || strpos($user, "' #") !== false) {
        $exito = true;
        $mensaje = "¡BYPASS EXITOSO! Has engañado a la base de datos.";
        $flag = "FLAG{sql_bypass_master}";
    } elseif ($user === 'admin' && $pass === 'admin') {
        $exito = true;
        $mensaje = "Credenciales por defecto... Aburrido, pero efectivo.";
        $flag = "FLAG{default_creds_hunter}";
    } elseif (!empty($user)) {
        $mensaje = "Acceso denegado. Credenciales incorrectas.<br><small style='color:var(--gray);'>Pista: Piensa en cómo cerrar una cadena y forzar un TRUE en SQL.</small>";
    }
}
?>

<main class="content-page">
    <div class="md-container m-bottom-2" style="max-width: 600px; margin: 0 auto; padding-top: 4rem;">
        <span class="section-label" style="color: #ff2a2a;">// RETO CTF 01</span>
        <h1 style="text-align: center; margin-bottom: 2rem;">Panel Clasificado</h1>
        
        <div style="background: rgba(10,15,20,0.8); padding: 2rem; border: 1px solid var(--cyan); border-radius: 8px; box-shadow: 0 0 20px rgba(0,255,255,0.1);">
            
            <?php if($exito): ?>
                <!-- PANTALLA DE ÉXITO -->
                <div style="text-align: center; animation: pulse 2s infinite;">
                    <h2 style="color: #00ff00;">🔓 ACCESO CONCEDIDO</h2>
                    <p style="color: var(--white);"><?= $mensaje ?></p>
                    <div style="margin-top: 2rem; padding: 1rem; background: rgba(0,255,0,0.1); border: 1px dashed #00ff00;">
                        <p style="margin:0; font-size: 0.9rem; color: var(--gray);">Tu recompensa:</p>
                        <h3 style="color: #00ff00; margin: 0.5rem 0; font-family: var(--mono); letter-spacing: 2px;"><?= $flag ?></h3>
                        <p style="margin:0; font-size: 0.8rem; color: var(--cyan);">Pista: Abre la terminal de CyberEscudo e introduce el comando: <strong>submit <?= $flag ?></strong></p>
                    </div>
                </div>
            <?php else: ?>
                <!-- FORMULARIO VULNERABLE -->
                <p style="text-align: center; color: var(--gray); margin-bottom: 2rem;">Sistema de autenticación legacy. Acceso restringido a personal autorizado.</p>
                
                <?php if($mensaje): ?>
                    <div style="color: #ff2a2a; border: 1px solid #ff2a2a; padding: 10px; margin-bottom: 1rem; text-align: center; background: rgba(255,42,42,0.1);"><?= $mensaje ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">Usuario:</label>
                        <input type="text" name="username" class="cyber-input" style="width: 100%; box-sizing: border-box;" autocomplete="off">
                    </div>
                    <div style="margin-bottom: 2rem;">
                        <label style="display:block; color: var(--cyan); font-family: var(--mono); margin-bottom: 0.5rem;">Contraseña:</label>
                        <input type="password" name="password" class="cyber-input" style="width: 100%; box-sizing: border-box;">
                    </div>
                    <button type="submit" style="width: 100%; background: transparent; border: 1px solid var(--cyan); color: var(--cyan); padding: 10px; font-family: var(--mono); font-size: 1rem; cursor: pointer; transition: all 0.3s;" onmouseover="this.style.background='var(--cyan)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--cyan)';">
                        INICIAR SESIÓN
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php 
// Usamos ../ para buscar el footer en la raíz
require __DIR__ . '/../templates/footer.php'; 
?>