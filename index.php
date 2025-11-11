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

    <style>
        /* ===== CONFIGURACIÓN GENERAL ===== */
body {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  color: #fff;
  background: url("../img/fondo.jpg") no-repeat center center fixed;
  background-size: cover;
  overflow-x: hidden;
}

/* ===== EFECTOS DE LUZ ===== */
.brillo {
  position: fixed;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle at center, rgba(255, 215, 120, 0.2), transparent 70%);
  animation: luzBrillante 8s infinite alternate ease-in-out;
  z-index: 0;
}

@keyframes luzBrillante {
  from { transform: translate(0, 0) scale(1); opacity: 0.4; }
  to { transform: translate(50px, 50px) scale(1.2); opacity: 0.8; }
}

/* ===== EFECTO DE PARTÍCULAS ===== */
.particulas {
  position: fixed;
  width: 100%;
  height: 100%;
  background: url("https://i.imgur.com/zlQPNjD.png") repeat;
  opacity: 0.1;
  animation: flotar 40s linear infinite;
  z-index: 0;
}

@keyframes flotar {
  from { background-position: 0 0; }
  to { background-position: 1000px 1000px; }
}

/* ===== ENCABEZADO ===== */
.encabezado {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 60px;
  background: rgba(0, 0, 0, 0.6);
  position: sticky;
  top: 0;
  z-index: 10;
  backdrop-filter: blur(5px);
  box-shadow: 0 2px 10px rgba(0,0,0,0.3);
}

.logo {
  display: flex;
  align-items: center;
  gap: 10px;
}

.logo img {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  animation: girarLogo 8s linear infinite;
}

@keyframes girarLogo {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.logo h1 {
  font-size: 1.8em;
  font-weight: 600;
  color: #ffd37a;
}

/* ===== MENÚ ===== */
.menu a {
  margin: 0 15px;
  text-decoration: none;
  color: #fff;
  font-weight: 500;
  transition: 0.3s;
}

.menu a:hover, .menu a.activo {
  color: #ffd37a;
}

.menu .boton {
  padding: 8px 15px;
  border: 1px solid #ffd37a;
  border-radius: 20px;
  transition: all 0.3s ease;
}

.menu .boton:hover {
  background: #ffd37a;
  color: #000;
}

/* ===== HERO ===== */
.hero {
  text-align: center;
  padding: 180px 20px;
  background: rgba(0, 0, 0, 0.5);
  min-height: 80vh;
  position: relative;
  overflow: hidden;
  z-index: 1;
}

.titulo-hero {
  font-size: 3em;
  color: #ffd37a;
  margin-bottom: 15px;
  animation: aparecer 1.5s ease forwards;
}

.subtitulo-hero {
  font-size: 1.3em;
  margin-bottom: 40px;
  animation: deslizarArriba 2s ease forwards;
}

.boton-principal {
  background: #ffd37a;
  color: #000;
  padding: 15px 30px;
  border-radius: 30px;
  text-decoration: none;
  font-weight: bold;
  box-shadow: 0 0 10px rgba(255, 215, 122, 0.8);
  transition: all 0.3s ease;
}

.boton-principal:hover {
  background: #fff4c1;
  box-shadow: 0 0 30px rgba(255, 215, 122, 1);
  transform: scale(1.1);
}

/* ===== EFECTO GLOW ===== */
.glow {
  animation: resplandor 2s infinite alternate;
}

@keyframes resplandor {
  from { box-shadow: 0 0 10px rgba(255,215,122,0.5); }
  to { box-shadow: 0 0 25px rgba(255,215,122,1); }
}

/* ===== DESTACADOS ===== */
.destacados {
  text-align: center;
  padding: 80px 20px;
  background: rgba(0, 0, 0, 0.75);
}

.titulo-seccion {
  color: #ffd37a;
  margin-bottom: 40px;
  font-size: 2.2em;
  letter-spacing: 2px;
  animation: deslizarAbajo 1.5s ease forwards;
}

.galeria {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 40px;
}

.producto {
  width: 260px;
  background: rgba(255, 255, 255, 0.1);
  padding: 25px;
  border-radius: 20px;
  transition: transform 0.4s ease, box-shadow 0.4s ease;
  opacity: 0;
  transform: translateY(50px);
  animation: fadeUp 1.2s forwards;
}

.producto:hover {
  transform: translateY(-10px) scale(1.05);
  box-shadow: 0 8px 25px rgba(255,215,122,0.4);
}

.producto img {
  width: 100%;
  border-radius: 15px;
  margin-bottom: 15px;
}

/* Delays para animación escalonada */
.delay1 { animation-delay: 0.3s; }
.delay2 { animation-delay: 0.6s; }

/* ===== ANIMACIONES ===== */
@keyframes fadeUp {
  to { opacity: 1; transform: translateY(0); }
}

@keyframes deslizarArriba {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes deslizarAbajo {
  from { opacity: 0; transform: translateY(-40px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes aparecer {
  from { opacity: 0; transform: scale(0.9); }
  to { opacity: 1; transform: scale(1); }
}

/* ===== PIE ===== */
.pie {
  text-align: center;
  padding: 15px;
  background: rgba(0, 0, 0, 0.8);
  font-size: 0.9em;
  color: #ddd;
}
/* ===== EFECTO SCROLL REVEAL ===== */
.reveal {
  opacity: 0;
  transform: translateY(50px);
  transition: all 1s ease;
}

.reveal.activo {
  opacity: 1;
  transform: translateY(0);
}

.reveal.delay1 {
  transition-delay: 0.2s;
}
.reveal.delay2 {
  transition-delay: 0.4s;
}
/* ===== FOOTER ELEGANTE ===== */
.footer {
  background: linear-gradient(180deg, rgba(40, 20, 10, 0.9), rgba(10, 5, 0, 0.95));
  color: #fff;
  padding: 60px 20px 20px 20px;
  border-top: 2px solid rgba(255, 215, 122, 0.4);
  position: relative;
  overflow: hidden;
  box-shadow: 0 -2px 20px rgba(255, 215, 122, 0.15);
}

.footer::before {
  content: "";
  position: absolute;
  top: -30%;
  left: -30%;
  width: 160%;
  height: 160%;
  background: radial-gradient(circle at center, rgba(255, 215, 122, 0.15), transparent 60%);
  animation: footerGlow 8s infinite alternate ease-in-out;
  z-index: 0;
}

@keyframes footerGlow {
  from { transform: scale(1); opacity: 0.4; }
  to { transform: scale(1.2); opacity: 0.8; }
}

.footer-contenido {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-evenly;
  gap: 40px;
  position: relative;
  z-index: 1;
}

.footer-logo {
  text-align: center;
  flex: 1 1 200px;
}

.footer-logo img {
  width: 80px;
  border-radius: 50%;
  animation: girarLogo 10s linear infinite;
}

.footer-logo h2 {
  color: #ffd37a;
  margin: 10px 0 5px 0;
  font-size: 1.6em;
}

.footer-logo p {
  color: #ddd;
  font-style: italic;
  font-size: 0.9em;
}

.footer-info, .footer-horario, .footer-redes {
  flex: 1 1 220px;
}

.footer-info h3,
.footer-horario h3,
.footer-redes h3 {
  color: #ffd37a;
  margin-bottom: 10px;
}

.footer-info p,
.footer-horario p {
  margin: 5px 0;
  font-size: 0.95em;
  color: #ddd;
}

/* ===== REDES SOCIALES ===== */
.iconos-redes {
  display: flex;
  gap: 15px;
  margin-top: 10px;
}

.iconos-redes a {
  font-size: 1.8em;
  color: #ffd37a;
  transition: transform 0.3s ease, color 0.3s ease;
}

.iconos-redes a:hover {
  color: #fff4c1;
  transform: scale(1.2) rotate(10deg);
}

/* ===== CRÉDITOS ===== */
.footer-creditos {
  text-align: center;
  margin-top: 40px;
  border-top: 1px solid rgba(255, 215, 122, 0.2);
  padding-top: 15px;
  font-size: 0.9em;
  color: #ccc;
  position: relative;
  z-index: 1;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 800px) {
  .footer-contenido {
      flex-direction: column;
      align-items: center;
      text-align: center;
  }
}
.usuario {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: rgba(255, 255, 255, 0.1);
  padding: 6px 10px;
  border-radius: 10px;
}

.foto-usuario {
  width: 35px;
  height: 35px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #ffd166;
}

.nombre-usuario {
  color: #ffd166;
  font-weight: 600;
}

.boton.cerrar {
  background: linear-gradient(90deg, #ff4e00, #ff9e00);
  color: #fff;
  padding: 6px 12px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s;
}

.boton.cerrar:hover {
  background: #ff7b00;
}
        </style>

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
