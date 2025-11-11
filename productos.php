<?php
session_start();
require "php/conexion.php";

$usuario = $_SESSION['usuario_nombre'] ?? null;
$foto = $_SESSION['usuario_foto'] ?? 'img/default.png';
$usuario_id = $_SESSION['usuario_id'] ?? null;

// Inicializar carrito en sesión
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Agregar producto al carrito
if (isset($_POST['agregar_carrito'])) {
    if (!$usuario_id) {
        header("Location: login.php");
        exit();
    }
    
    $producto_id = $_POST['producto_id'];
    
    // Buscar producto en la base de datos
    $stmt = $conn->prepare("SELECT id, nombre, precio, imagen FROM productos WHERE id = ? AND activo = 1");
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        $producto = $resultado->fetch_assoc();
        
        // Verificar si el producto ya está en el carrito
        $encontrado = false;
        foreach ($_SESSION['carrito'] as &$item) {
            if ($item['id'] == $producto_id) {
                $item['cantidad']++;
                $encontrado = true;
                break;
            }
        }
        
        // Si no está, agregarlo
        if (!$encontrado) {
            $_SESSION['carrito'][] = [
                'id' => $producto['id'],
                'nombre' => $producto['nombre'],
                'precio' => $producto['precio'],
                'imagen' => $producto['imagen'],
                'cantidad' => 1
            ];
        }
    }
    $stmt->close();
    
    header("Location: productos.php");
    exit();
}

// Obtener productos de la base de datos
$productos = [];
$categorias = [];

// Obtener categorías
$sql_categorias = "SELECT id, nombre FROM categorias WHERE activo = 1";
$result_categorias = $conn->query($sql_categorias);
while ($categoria = $result_categorias->fetch_assoc()) {
    $categorias[] = $categoria;
}

// Obtener productos
$sql_productos = "SELECT p.*, c.nombre as categoria_nombre 
                  FROM productos p 
                  JOIN categorias c ON p.categoria_id = c.id 
                  WHERE p.activo = 1 
                  ORDER BY p.destacado DESC, p.nombre ASC";
$result_productos = $conn->query($sql_productos);
while ($producto = $result_productos->fetch_assoc()) {
    $productos[] = $producto;
}

// Calcular total del carrito
$total_carrito = 0;
$total_items = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total_carrito += $item['precio'] * $item['cantidad'];
    $total_items += $item['cantidad'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo | Panadería Dulce Delicia</title>
    <link rel="stylesheet" href="css/productos.css">
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
            <a href="productos.php" class="activo">Productos</a>
            <a href="nosotros.php">Nosotros</a>
            <a href="contacto.php">Contacto</a>

            <?php if ($usuario): ?>
                <div class="usuario">
                    <img src="<?php echo htmlspecialchars($foto); ?>" alt="Foto" class="foto-usuario">
                    <span class="nombre-usuario"><?php echo htmlspecialchars($usuario); ?></span>
                    
                    <!-- Icono del carrito -->
                    <div class="carrito-icono" onclick="toggleCarrito()">
                        <ion-icon name="cart-outline"></ion-icon>
                        <?php if ($total_items > 0): ?>
                            <span class="carrito-contador"><?php echo $total_items; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <a href="php/logout.php" class="boton cerrar">Cerrar sesión</a>
                </div>
            <?php else: ?>
                <a href="login.php" class="boton">Iniciar Sesión</a>
            <?php endif; ?>
        </nav>
    </header>

    <!-- CARRITO LATERAL -->
    <div class="carrito-lateral" id="carritoLateral">
        <div class="carrito-header">
            <h3>🛒 Tu Carrito</h3>
            <button class="cerrar-carrito" onclick="toggleCarrito()">
                <ion-icon name="close-outline"></ion-icon>
            </button>
        </div>
        
        <div class="carrito-items" id="carritoItems">
            <?php if (empty($_SESSION['carrito'])): ?>
                <div class="carrito-vacio">
                    <p>Tu carrito está vacío</p>
                    <ion-icon name="sad-outline"></ion-icon>
                </div>
            <?php else: ?>
                <?php foreach ($_SESSION['carrito'] as $index => $item): ?>
                    <div class="carrito-item">
                        <img src="<?php echo $item['imagen']; ?>" alt="<?php echo $item['nombre']; ?>">
                        <div class="item-info">
                            <h4><?php echo $item['nombre']; ?></h4>
                            <p>$<?php echo number_format($item['precio'], 2); ?> MXN</p>
                            <div class="item-cantidad">
                                <form method="POST" action="php/actualizar_carrito.php" style="display: inline;">
                                    <input type="hidden" name="item_index" value="<?php echo $index; ?>">
                                    <input type="hidden" name="accion" value="disminuir">
                                    <button type="submit" class="btn-cantidad">-</button>
                                </form>
                                <span><?php echo $item['cantidad']; ?></span>
                                <form method="POST" action="php/actualizar_carrito.php" style="display: inline;">
                                    <input type="hidden" name="item_index" value="<?php echo $index; ?>">
                                    <input type="hidden" name="accion" value="aumentar">
                                    <button type="submit" class="btn-cantidad">+</button>
                                </form>
                            </div>
                        </div>
                        <form method="POST" action="php/actualizar_carrito.php" class="eliminar-item">
                            <input type="hidden" name="item_index" value="<?php echo $index; ?>">
                            <input type="hidden" name="accion" value="eliminar">
                            <button type="submit" class="btn-eliminar">
                                <ion-icon name="trash-outline"></ion-icon>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="carrito-footer">
            <div class="carrito-total">
                <strong>Total: $<?php echo number_format($total_carrito, 2); ?> MXN</strong>
            </div>
            <div class="carrito-acciones">
                <button class="btn-vaciar" onclick="vaciarCarrito()">Vaciar Carrito</button>
                <button class="btn-comprar" onclick="realizarCompra()">Realizar Pedido</button>
            </div>
        </div>
    </div>

    <!-- OVERLAY PARA CARRITO -->
    <div class="carrito-overlay" id="carritoOverlay" onclick="toggleCarrito()"></div>

    <!-- SECCIÓN HERO -->
    <section class="hero-productos reveal">
        <h2>Nuestro Catálogo</h2>
        <p>Elaborado con amor, harina y tradición 💛</p>

        <div class="filtros">
            <button class="filtro activo" data-categoria="todos">Todos</button>
            <?php foreach ($categorias as $categoria): ?>
                <button class="filtro" data-categoria="categoria-<?php echo $categoria['id']; ?>">
                    <?php echo htmlspecialchars($categoria['nombre']); ?>
                </button>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- SECCIÓN CATÁLOGO -->
    <section class="catalogo reveal">
        <div class="grid-productos">
            <?php foreach ($productos as $producto): ?>
                <div class="tarjeta" data-categoria="categoria-<?php echo $producto['categoria_id']; ?>">
                    <img src="<?php echo $producto['imagen']; ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                    <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                    <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                    <span class="precio">$<?php echo number_format($producto['precio'], 2); ?> MXN</span>
                    <?php if ($producto['stock'] > 0): ?>
                        <form method="POST" class="form-agregar">
                            <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                            <button type="submit" name="agregar_carrito" class="btn-agregar">
                                <ion-icon name="cart-outline"></ion-icon> Agregar al carrito
                            </button>
                        </form>
                    <?php else: ?>
                        <button class="btn-agotado" disabled>Agotado</button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
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
                    <a href="#"><ion-icon name="logo-whatsapp"></ion-icon></a>
                </div>
            </div>
        </div>

        <div class="footer-creditos">
            <p>&copy; 2025 Panadería Dulce Delicia | Desarrollado con ❤️ por MARPROX</p>
        </div>
    </footer>

    <!-- ICONOS Y SCRIPTS -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="js/productos.js"></script>

    <!-- SCRIPT PARA CARRITO -->
    <script>
        function toggleCarrito() {
            const carrito = document.getElementById('carritoLateral');
            const overlay = document.getElementById('carritoOverlay');
            carrito.classList.toggle('activo');
            overlay.classList.toggle('activo');
        }

        function vaciarCarrito() {
            if (confirm('¿Estás seguro de que quieres vaciar el carrito?')) {
                window.location.href = 'php/vaciar_carrito.php';
            }
        }

        function realizarCompra() {
            const totalItems = <?php echo $total_items; ?>;
            if (totalItems === 0) {
                alert('Tu carrito está vacío');
                return;
            }
            
            if (!<?php echo $usuario_id ? 'true' : 'false'; ?>) {
                alert('Debes iniciar sesión para realizar un pedido');
                window.location.href = 'login.php';
                return;
            }
            
            alert('¡Pedido realizado con éxito! Total: $<?php echo number_format($total_carrito, 2); ?> MXN');
            window.location.href = 'php/realizar_pedido.php';
        }

        // Cerrar carrito con ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                toggleCarrito();
            }
        });
    </script>

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

        /* ===== ESTILOS DEL CARRITO ===== */
        .carrito-icono {
            position: relative;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s;
        }

        .carrito-icono:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .carrito-icono ion-icon {
            font-size: 24px;
            color: #ffd166;
        }

        .carrito-contador {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4e00;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .carrito-lateral {
            position: fixed;
            top: 0;
            right: -400px;
            width: 380px;
            height: 100vh;
            background: rgba(26, 26, 26, 0.95);
            backdrop-filter: blur(15px);
            border-left: 3px solid #d4af37;
            z-index: 1000;
            transition: right 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .carrito-lateral.activo {
            right: 0;
        }

        .carrito-header {
            padding: 20px;
            border-bottom: 2px solid #d4af37;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(212, 175, 55, 0.1);
        }

        .carrito-header h3 {
            color: #d4af37;
            margin: 0;
        }

        .cerrar-carrito {
            background: none;
            border: none;
            color: #d4af37;
            font-size: 24px;
            cursor: pointer;
            padding: 5px;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .cerrar-carrito:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .carrito-items {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }

        .carrito-vacio {
            text-align: center;
            color: #fff;
            padding: 40px 20px;
        }

        .carrito-vacio ion-icon {
            font-size: 48px;
            color: #d4af37;
            margin-top: 10px;
        }

        .carrito-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            margin-bottom: 10px;
            border: 1px solid rgba(212, 175, 55, 0.3);
        }

        .carrito-item img {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
        }

        .item-info {
            flex: 1;
        }

        .item-info h4 {
            color: #fff;
            margin: 0 0 5px 0;
            font-size: 14px;
        }

        .item-info p {
            color: #d4af37;
            margin: 0 0 8px 0;
            font-weight: bold;
        }

        .item-cantidad {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-cantidad {
            background: #d4af37;
            border: none;
            color: #1a1a1a;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }

        .btn-cantidad:hover {
            background: #f7ef8a;
        }

        .item-cantidad span {
            color: #fff;
            font-weight: bold;
            min-width: 20px;
            text-align: center;
        }

        .btn-eliminar {
            background: #ff4e00;
            border: none;
            color: white;
            padding: 5px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-eliminar:hover {
            background: #ff6b33;
        }

        .carrito-footer {
            padding: 20px;
            border-top: 2px solid #d4af37;
            background: rgba(212, 175, 55, 0.1);
        }

        .carrito-total {
            text-align: center;
            color: #fff;
            font-size: 18px;
            margin-bottom: 15px;
        }

        .carrito-acciones {
            display: flex;
            gap: 10px;
        }

        .btn-vaciar, .btn-comprar {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-vaciar {
            background: #6c757d;
            color: white;
        }

        .btn-vaciar:hover {
            background: #5a6268;
        }

        .btn-comprar {
            background: #28a745;
            color: white;
        }

        .btn-comprar:hover {
            background: #218838;
        }

        .carrito-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }

        .carrito-overlay.activo {
            display: block;
        }

        .form-agregar {
            margin: 0;
        }

        .btn-agregar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            width: 100%;
        }

        .btn-agotado {
            width: 100%;
            padding: 12px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: not-allowed;
        }
    </style>
</body>
</html>