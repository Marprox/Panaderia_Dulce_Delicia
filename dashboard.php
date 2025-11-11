<?php
session_start();
if (!isset($_SESSION['usuario_nombre'])) {
    header("Location: login.php");
    exit();
}
$nombre = htmlspecialchars($_SESSION['usuario_nombre']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel | Dulce Delicia</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <div class="fondo"></div>
    
    <div class="contenedor">
        <h1>🍞 Bienvenido, <span><?php echo $nombre; ?></span> 🍩</h1>
        <p>Has iniciado sesión correctamente en <b>Dulce Delicia</b>.</p>

        <div class="botones">
            <a href="index.html" class="btn">🏠 Ir al inicio</a>
            <a href="productos.html" class="btn">🧁 Ver productos</a>
            <a href="php/logout.php" class="btn salir">🚪 Cerrar sesión</a>
        </div>
    </div>
</body>
</html>
