<?php
session_start();
require "php/conexion.php";

$usuario = $_SESSION['usuario_nombre'] ?? null;
$foto = $_SESSION['usuario_foto'] ?? 'img/default.png';

// Obtener miembros del equipo desde la base de datos
$miembros_equipo = [];
$sql_equipo = "SELECT * FROM equipo WHERE activo = 1 ORDER BY orden ASC";
$result_equipo = $conn->query($sql_equipo);

if ($result_equipo->num_rows > 0) {
    while($miembro = $result_equipo->fetch_assoc()) {
        $miembros_equipo[] = $miembro;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nosotros | Panadería Dulce Delicia</title>
    <link rel="stylesheet" href="css/nosotros.css">
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
            <a href="nosotros.php" class="activo">Nosotros</a>
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
    <section class="hero-nosotros reveal">
        <div class="contenido-hero">
            <h2>Nuestra Historia</h2>
            <p>El pan no solo se hornea, se crea con amor y tradición.</p>
        </div>
    </section>

    <!-- HISTORIA -->
    <section class="historia reveal">
        <div class="texto">
            <h3>Desde 1995, horneando sonrisas</h3>
            <p>
                Dulce Delicia nació como un pequeño sueño familiar. 
                Con un horno viejo y mucha pasión, comenzamos a crear panes que recordaban a la infancia: suaves, dorados y llenos de aroma.
            </p>
            <p>
                Hoy, mantenemos esa esencia artesanal, combinando recetas tradicionales con un toque moderno. 
                Cada pan cuenta una historia: la del esfuerzo, la familia y el amor por compartir.
            </p>
        </div>
        <div class="imagen">
            <img src="img/panaderia1.jpg" alt="Pan artesanal en horno">
        </div>
    </section>

    <!-- EQUIPO -->
    <section class="equipo reveal">
        <h2>Nuestro Equipo Directivo</h2>
        <div class="miembros">
            <?php if (!empty($miembros_equipo)): ?>
                <?php foreach ($miembros_equipo as $miembro): ?>
                    <div class="persona">
                        <img src="<?php echo $miembro['imagen']; ?>" alt="<?php echo htmlspecialchars($miembro['nombre']); ?>">
                        <h3><?php echo htmlspecialchars($miembro['nombre']); ?></h3>
                        <p class="puesto"><?php echo htmlspecialchars($miembro['puesto']); ?></p>
                        <?php if (!empty($miembro['experiencia'])): ?>
                            <p class="experiencia"><?php echo htmlspecialchars($miembro['experiencia']); ?> de experiencia</p>
                        <?php endif; ?>
                        <?php if (!empty($miembro['especialidad'])): ?>
                            <p class="especialidad">Especialidad: <?php echo htmlspecialchars($miembro['especialidad']); ?></p>
                        <?php endif; ?>
                        <p class="descripcion"><?php echo htmlspecialchars($miembro['descripcion']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="sin-equipo">
                    <p>Próximamente más información sobre nuestro equipo.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- FRASE FINAL -->
    <section class="frase reveal">
        <blockquote>
            "Hacer pan es un arte que combina paciencia, pasión y aroma a hogar."
        </blockquote>
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
    <script src="js/nosotros.js"></script>

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

    <style>
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

        /* ===== ESTILOS PARA LA SECCIÓN DE EQUIPO ===== */
        .equipo {
            padding: 80px 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 25px;
            margin: 60px auto;
            max-width: 1200px;
            border: 2px solid rgba(212, 175, 55, 0.3);
        }

        .equipo h2 {
            text-align: center;
            color: #d4af37;
            font-size: 2.5rem;
            margin-bottom: 50px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .miembros {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 40px;
            margin-top: 40px;
        }

        .persona {
            background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
            padding: 40px 30px;
            border-radius: 20px;
            text-align: center;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(212, 175, 55, 0.4);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .persona::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.1), transparent);
            transition: left 0.6s ease;
        }

        .persona:hover::before {
            left: 100%;
        }

        .persona:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 35px rgba(212, 175, 55, 0.3);
            border-color: #d4af37;
        }

        .persona img {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #d4af37;
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }

        .persona:hover img {
            border-color: #ffd166;
            transform: scale(1.05);
        }

        .persona h3 {
            color: #d4af37;
            font-size: 1.8rem;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .persona .puesto {
            color: #ffd166;
            font-weight: bold;
            font-size: 1.2rem;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .persona .experiencia {
            color: #ffffff;
            font-style: italic;
            margin-bottom: 8px;
            font-size: 1rem;
            background: rgba(212, 175, 55, 0.2);
            padding: 8px 15px;
            border-radius: 25px;
            display: inline-block;
        }

        .persona .especialidad {
            color: #ffffff;
            font-size: 0.9rem;
            margin-bottom: 20px;
            background: rgba(255, 209, 102, 0.3);
            padding: 6px 12px;
            border-radius: 15px;
            display: inline-block;
            font-weight: 600;
        }

        .persona .descripcion {
            color: #e0e0e0;
            line-height: 1.7;
            font-size: 1rem;
            text-align: justify;
        }

        .sin-equipo {
            text-align: center;
            color: #ffffff;
            padding: 60px 40px;
            grid-column: 1 / -1;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            border: 2px dashed rgba(212, 175, 55, 0.5);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .miembros {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            .persona {
                padding: 30px 20px;
            }
            
            .persona img {
                width: 150px;
                height: 150px;
            }
            
            .equipo h2 {
                font-size: 2rem;
            }
            
            .persona h3 {
                font-size: 1.5rem;
            }
        }

        .equipo-directivo {
    background-color: rgba(0, 0, 0, 0.7); /* Fondo negro con 70% de opacidad */
    padding: 20px;
    border-radius: 8px; /* Opcional: para esquinas redondeadas como en la imagen */
    margin: 20px 0; /* Opcional: espacio arriba y abajo */
}

        @media (max-width: 480px) {
            .equipo {
                padding: 60px 15px;
                margin: 40px 15px;
            }
            
            .persona {
                padding: 25px 15px;
            }
            
            .persona img {
                width: 120px;
                height: 120px;
            }
        }
        
    </style>
</body>
</html>