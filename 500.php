<?php
// Minimal 500 page — avoid including bootstrap.php since it may be what caused the error
http_response_code(500);
?><!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Error del servidor — CyberEscudo</title>
  <style>
    body{background:#0a0e14;color:#f5f5f5;font-family:system-ui,sans-serif;
         display:flex;align-items:center;justify-content:center;min-height:100vh;text-align:center;}
    .accent{color:#00ffff;}
    a{color:#00ffff;}
  </style>
</head>
<body>
  <div>
    <p style="font-family:monospace;color:#00ffff;margin-bottom:1rem;">500</p>
    <h1>Error interno del servidor</h1>
    <p style="color:rgba(255,255,255,.5);margin:1rem 0 2rem;">
      Algo ha salido mal. El error ha sido registrado.<br>
      <em>Something went wrong. The error has been logged.</em>
    </p>
    <a href="/index.php">← Volver al inicio / Back to home</a>
  </div>
</body>
</html>
