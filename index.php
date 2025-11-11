<?php
session_start();
require "php/conexion.php";

$usuario = $_SESSION['usuario_nombre'] ?? null;
$foto = $_SESSION['usuario_foto'] ?? 'img/default.png';

// Obtener productos destacados
$productos_destacados = [];
$sql_destacados = "SELECT * FROM productos WHERE destacado = 1 AND activo = 1 LIMIT 3";
$result_destacados = $conn->query($sql_destacados);
while ($producto = $result_destacados->fetch_assoc()) {
    $productos_destacados[] = $producto;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panadería Dulce Delicia</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="icon" href="img/logo.png" type="image/png">
</head>
<body>

    <!-- EFECTO DE LUZ Y PARTÍCULAS -->
    <div class="brillo"></div>
    <div class="particulas"></div>

    <!-- ENCABEZADO -->
    <header class="encabezado fade-in">
        <div class="logo">
            <img src="img/logo.png" alt="Logo Dulce Delicia">
            <h1>Dulce Delicia</h1>
        </div>

        <nav class="menu">
            <a href="index.php" class="activo">Inicio</a>
            <a href="productos.php">Productos</a>
            <a href="nosotros.php">Nosotros</a>
            <a href="contacto.php">Contacto</a>

            <?php if ($usuario): ?>
                <div class="usuario">
                    <img src="<?php echo htmlspecialchars($foto); ?>" alt="Foto" class="foto-usuario">
                    <span class="nombre-usuario"><?php echo htmlspecialchars($usuario); ?></span>
                    <a href="php/logout.php" class="boton cerrar">Cerrar sesión</a>
                </div>
            <?php else: ?>
                <a href="login.php" class="boton">Iniciar Sesión</a>
            <?php endif; ?>
        </nav>
    </header>

    <!-- HERO -->
    <section class="hero reveal">
        <div class="contenido-hero">
            <h2 class="titulo-hero">El arte del pan, horneado con pasión</h2>
            <p class="subtitulo-hero">Disfruta la tradición y el sabor que despiertan tus sentidos.</p>
            <a href="productos.php" class="boton-principal glow">Ver Catálogo</a>
        </div>
    </section>

    <!-- PRODUCTOS DESTACADOS -->
    <section class="destacados reveal">
        <h2 class="titulo-seccion">Nuestros Favoritos</h2>
        <div class="galeria">
            <?php foreach ($productos_destacados as $index => $producto): ?>
                <div class="producto reveal <?php echo $index > 0 ? 'delay' . $index : ''; ?>">
                    <img src="<?php echo $producto['imagen']; ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                    <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                    <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                    <span class="precio">$<?php echo number_format($producto['precio'], 2); ?> MXN</span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- PIE DE PÁGINA ELEGANTE -->
    <footer class="footer reveal">
        <div class="footer-contenido">
            <div class="footer-logo">
                <img src="img/logo.png" alt="Logo Dulce Delicia">
                <h2>Dulce Delicia</h2>
                <p>Pan artesanal con sabor a hogar.</p>
            </div>

            <div class="footer-info">
                <h3>Contacto</h3>
                <p><strong>📍 Dirección:</strong> Calle Pan Dulce #12, Oaxaca, México</p>
                <p><strong>📞 Teléfono:</strong> +52 951 123 4567</p>
                <p><strong>📧 Correo:</strong> contacto@dulcedelicia.com</p>
            </div>

            <div class="footer-horario">
                <h3>Horario</h3>
                <p>Lunes a Viernes: 7:00 AM - 8:00 PM</p>
                <p>Sábado y Domingo: 8:00 AM - 6:00 PM</p>
            </div>

            <div class="footer-redes">
                <h3>Síguenos</h3>
                <div class="iconos-redes">
                    <a href="#" title="Facebook"><ion-icon name="logo-facebook"></ion-icon></a>
                    <a href="#" title="Instagram"><ion-icon name="logo-instagram"></ion-icon></a>
                    <a href="#" title="Tiktok"><ion-icon name="logo-tiktok"></ion-icon></a>
                    <a href="#" title="WhatsApp"><ion-icon name="logo-whatsapp"></ion-icon></a>
                </div>
            </div>
        </div>

        <div class="footer-creditos">
            <p>&copy; 2025 Panadería Dulce Delicia | Desarrollado con ❤️ por MARPROX</p>
        </div>
    </footer>

    <!-- Iconos de Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>  

    <!-- SCRIPT DE ANIMACIÓN -->
    <script src="js/index.js"></script>

    <!-- SCRIPT PARA SESIÓN ENTRE PESTAÑAS -->
    <script>
        // Verificar sesión entre pestañas
        window.addEventListener('storage', function(e) {
            if (e.key === 'userSession' && !e.newValue) {
                location.reload();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const sesionActiva = localStorage.getItem('userSession');
            if (sesionActiva === 'active') {
                <?php if (!$usuario): ?>
                    location.reload();
                <?php endif; ?>
            }
        });
    </script>
</body>
</html>