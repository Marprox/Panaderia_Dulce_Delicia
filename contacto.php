<?php
session_start();
require "php/conexion.php";

$usuario = $_SESSION['usuario_nombre'] ?? null;
$foto = $_SESSION['usuario_foto'] ?? 'img/default.png';

$mensaje_contacto = "";

// Procesar formulario de contacto
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nombre'])) {
    $nombre = trim($_POST["nombre"]);
    $email = trim($_POST["email"]);
    $telefono = trim($_POST["telefono"] ?? '');
    $mensaje = trim($_POST["mensaje"]);
    
    if (empty($nombre) || empty($email) || empty($mensaje)) {
        $mensaje_contacto = "❌ Por favor completa todos los campos obligatorios";
    } else {
        $stmt = $conn->prepare("INSERT INTO contactos (nombre, email, telefono, mensaje) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $email, $telefono, $mensaje);
        
        if ($stmt->execute()) {
            $mensaje_contacto = "✅ ¡Mensaje enviado con éxito! Te contactaremos pronto.";
            // Limpiar formulario
            $_POST = [];
        } else {
            $mensaje_contacto = "❌ Error al enviar el mensaje: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto | Panadería Dulce Delicia</title>
    <link rel="stylesheet" href="css/contacto.css">
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
            <a href="index.php">Inicio</a>
            <a href="productos.php">Productos</a>
            <a href="nosotros.php">Nosotros</a>
            <a href="contacto.php" class="activo">Contacto</a>

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
    <section class="hero-contacto reveal">
        <h2>Contáctanos</h2>
        <p>Queremos escuchar tus ideas, pedidos o comentarios 🍞💬</p>
    </section>

    <!-- FORMULARIO -->
    <section class="contacto-form reveal">
        <div class="formulario">
            <h3>Envíanos un mensaje</h3>
            
            <?php if (!empty($mensaje_contacto)): ?>
                <div class="mensaje-contacto <?php echo strpos($mensaje_contacto, '✅') !== false ? 'exito' : 'error'; ?>">
                    <?php echo $mensaje_contacto; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="campo">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Tu nombre" required 
                           value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
                </div>
                <div class="campo">
                    <label for="email">Correo:</label>
                    <input type="email" id="email" name="email" placeholder="tu@correo.com" required
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                <div class="campo">
                    <label for="telefono">Teléfono (opcional):</label>
                    <input type="tel" id="telefono" name="telefono" placeholder="+52 951 123 4567"
                           value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>">
                </div>
                <div class="campo">
                    <label for="mensaje">Mensaje:</label>
                    <textarea id="mensaje" name="mensaje" rows="5" placeholder="Escribe tu mensaje aquí..." required><?php echo isset($_POST['mensaje']) ? htmlspecialchars($_POST['mensaje']) : ''; ?></textarea>
                </div>
                <button type="submit" class="btn-enviar">Enviar Mensaje</button>
            </form>
        </div>

        <div class="mapa">
            <h3>Visítanos</h3>
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3763.260654975794!2d-96.72779562578257!3d17.075701188293734!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85c72238e6e9464f%3A0x8f67e3f06f3767e3!2sOaxaca%20de%20Ju%C3%A1rez%2C%20Oax.!5e0!3m2!1ses!2smx!4v1696878978979!5m2!1ses!2smx"
                width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer reveal">
        <div class="footer-contenido">
            <div class="footer-logo">
                <img src="img/logo.png" alt="Logo Dulce Delicia">
                <h2>Dulce Delicia</h2>
                <p>Pan artesanal con sabor a hogar.</p>
            </div>

            <div class="footer-info">
                <h3>Contacto</h3>
                <p>📍 Calle Pan Dulce #12, Oaxaca</p>
                <p>📞 +52 951 123 4567</p>
                <p>📧 contacto@dulcedelicia.com</p>
            </div>

            <div class="footer-redes">
                <h3>Síguenos</h3>
                <div class="iconos-redes">
                    <a href="#"><ion-icon name="logo-facebook"></ion-icon></a>
                    <a href="#"><ion-icon name="logo-instagram"></ion-icon></a>
                    <a href="#"><ion-icon name="logo-tiktok"></ion-icon></a>
                </div>
            </div>
        </div>

        <div class="footer-creditos">
            <p>&copy; 2025 Panadería Dulce Delicia | Desarrollado con ❤️ por MARPROX</p>
        </div>
    </footer>

    <!-- ICONOS Y SCRIPT -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="js/contacto.js"></script>

    <!-- SCRIPT PARA SESIÓN ENTRE PESTAÑAS -->
    <script>
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

    <style>
        .mensaje-contacto {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }

        .mensaje-contacto.exito {
            background: rgba(40, 167, 69, 0.9);
            color: white;
        }

        .mensaje-contacto.error {
            background: rgba(220, 53, 69, 0.9);
            color: white;
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
</body>
</html>