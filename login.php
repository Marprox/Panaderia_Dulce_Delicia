<?php
session_start();
require "php/conexion.php";

// Si ya está logueado, redirige al index
if (isset($_SESSION['usuario_nombre'])) {
    header("Location: index.php");
    exit();
}

$mensaje = "";
$mostrar_registro = false;

// Procesar LOGIN
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['email']) && !isset($_POST['nombre'])) {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    
    if (empty($email) || empty($password)) {
        $mensaje = "❌ Faltan datos por completar";
    } else {
        $stmt = $conn->prepare("SELECT id, nombre, password_hash, foto FROM usuarios WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows === 0) {
            $mensaje = "❌ Usuario no encontrado";
        } else {
            $usuario = $resultado->fetch_assoc();
            if (password_verify($password, $usuario["password_hash"])) {
                $_SESSION["usuario_id"] = $usuario["id"];
                $_SESSION["usuario_nombre"] = $usuario["nombre"];
                $_SESSION["usuario_foto"] = $usuario["foto"];
                
                // Guardar sesión en localStorage para persistencia entre pestañas
                echo "<script>
                    localStorage.setItem('userSession', 'active');
                    localStorage.setItem('userName', '" . $usuario["nombre"] . "');
                    localStorage.setItem('userFoto', '" . $usuario["foto"] . "');
                </script>";
                
                header("Location: index.php");
                exit();
            } else {
                $mensaje = "❌ Contraseña incorrecta";
            }
        }
        $stmt->close();
    }
}

// Procesar REGISTRO
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nombre'])) {
    $mostrar_registro = true;
    $nombre = trim($_POST["nombre"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    
    if (empty($nombre) || empty($email) || empty($password)) {
        $mensaje = "❌ Faltan datos por completar";
    } else {
        // Verificar si el correo ya existe
        $verificar = $conn->prepare("SELECT id FROM usuarios WHERE email=?");
        $verificar->bind_param("s", $email);
        $verificar->execute();
        $verificar->store_result();
        
        if ($verificar->num_rows > 0) {
            $mensaje = "❌ Este correo ya está registrado";
        } else {
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            
            // Manejar foto de perfil opcional
            $foto = 'img/default.png';
            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === 0) {
                $foto_temp = $_FILES['foto_perfil']['tmp_name'];
                $foto_nombre = 'user_' . time() . '_' . uniqid() . '.png';
                $foto_destino = 'img/usuarios/' . $foto_nombre;
                
                // Crear directorio si no existe
                if (!file_exists('img/usuarios')) {
                    mkdir('img/usuarios', 0777, true);
                }
                
                if (move_uploaded_file($foto_temp, $foto_destino)) {
                    $foto = $foto_destino;
                }
            }
            
            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password_hash, foto) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nombre, $email, $password_hash, $foto);
            
            if ($stmt->execute()) {
                $_SESSION["usuario_id"] = $conn->insert_id;
                $_SESSION["usuario_nombre"] = $nombre;
                $_SESSION["usuario_foto"] = $foto;
                
                // Guardar sesión en localStorage para persistencia entre pestañas
                echo "<script>
                    localStorage.setItem('userSession', 'active');
                    localStorage.setItem('userName', '" . $nombre . "');
                    localStorage.setItem('userFoto', '" . $foto . "');
                </script>";
                
                header("Location: index.php");
                exit();
            } else {
                $mensaje = "❌ Error al registrar: " . $stmt->error;
            }
            $stmt->close();
        }
        $verificar->close();
    }
}

// Si viene por GET para mostrar registro
if (isset($_GET['registro'])) {
    $mostrar_registro = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso | Dulce Delicia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="img/logo.png" type="image/png">
    <style>
        /* TODOS TUS ESTILOS ORIGINALES SE MANTIENEN IGUAL */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background: url('img/fondo.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
        }

        /* Overlay para mejor legibilidad */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        /* MENÚ IDÉNTICO AL DE LA IMAGEN */
        .encabezado {
            background: rgba(26, 26, 26, 0.95);
            padding: 15px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #d4af37;
            position: relative;
            z-index: 100;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo img {
            height: 50px;
            width: auto;
        }

        .logo h1 {
            color: #d4af37;
            font-size: 28px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 0;
        }

        .menu {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .menu a {
            color: #ffffff;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }

        .menu a:hover {
            background: #d4af37;
            color: #1a1a1a;
        }

        .menu a.activo {
            background: #d4af37;
            color: #1a1a1a;
        }

        /* CONTENEDOR DEL LOGIN/REGISTRO */
        .contenedor-login {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            padding: 40px 20px;
            position: relative;
            z-index: 10;
        }

        .form-caja {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 20px;
            backdrop-filter: blur(15px);
            border: 2px solid #d4af37;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
        }

        .formulario {
            display: none;
        }

        .formulario.activo {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .formulario h2 {
            color: #d4af37;
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .campo {
            margin-bottom: 20px;
        }

        .formulario label {
            color: #ffffff;
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .formulario input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #d4af37;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            font-size: 16px;
            outline: none;
            transition: all 0.3s ease;
        }

        .formulario input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .formulario input:focus {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.3);
        }

        .btn-principal {
            width: 100%;
            background: #d4af37;
            color: #1a1a1a;
            border: none;
            padding: 15px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-principal:hover {
            background: #f7ef8a;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
        }

        .enlace-cambio {
            text-align: center;
            margin-top: 20px;
            color: #ffffff;
        }

        .enlace-cambio a {
            color: #d4af37;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
        }

        .enlace-cambio a:hover {
            text-decoration: underline;
            color: #f7ef8a;
        }

        .mensaje-error {
            background: rgba(220, 53, 69, 0.9);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* ESTILOS NUEVOS PARA LA FOTO OPCIONAL */
        .campo-foto {
            text-align: center;
            margin-bottom: 20px;
        }

        .avatar-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid #d4af37;
            margin: 0 auto 10px;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            overflow: hidden;
        }

        .avatar-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-texto {
            color: #d4af37;
            font-size: 14px;
            cursor: pointer;
            margin-bottom: 10px;
        }

        .avatar-texto:hover {
            color: #f7ef8a;
        }

        #foto_perfil {
            display: none;
        }
    </style>
</head>
<body>
    <!-- MENÚ IDÉNTICO A LA IMAGEN -->
    <header class="encabezado">
        <div class="logo">
            <img src="img/logo.png" alt="Logo Dulce Delicia">
            <h1>Dulce Delicia</h1>
        </div>

        <nav class="menu">
            <a href="index.php">Inicio</a>
            <a href="productos.html">Productos</a>
            <a href="nosotros.html">Nosotros</a>
            <a href="contacto.html">Contacto</a>
        </nav>
    </header>

    <!-- FORMULARIO LOGIN/REGISTRO -->
    <div class="contenedor-login">
        <div class="form-caja">
            <!-- FORMULARIO LOGIN -->
            <div class="formulario <?php echo !$mostrar_registro ? 'activo' : ''; ?>" id="formLogin">
                <form method="POST">
                    <h2>Iniciar Sesión</h2>
                    
                    <?php if (!empty($mensaje) && !$mostrar_registro): ?>
                        <div class="mensaje-error"><?php echo $mensaje; ?></div>
                    <?php endif; ?>

                    <div class="campo">
                        <label><i class="fas fa-envelope"></i> Correo electrónico</label>
                        <input type="email" name="email" placeholder="ejemplo@correo.com" required 
                               value="<?php echo isset($_POST['email']) && !$mostrar_registro ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>

                    <div class="campo">
                        <label><i class="fas fa-lock"></i> Contraseña</label>
                        <input type="password" name="password" placeholder="Tu contraseña" required>
                    </div>

                    <button type="submit" class="btn-principal">
                        <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                    </button>

                    <p class="enlace-cambio">
                        ¿No tienes cuenta? 
                        <a href="#" onclick="mostrarRegistro()">Regístrate aquí</a>
                    </p>
                </form>
            </div>

            <!-- FORMULARIO REGISTRO -->
            <div class="formulario <?php echo $mostrar_registro ? 'activo' : ''; ?>" id="formRegistro">
                <form method="POST" enctype="multipart/form-data">
                    <h2>Crear Cuenta</h2>
                    
                    <?php if (!empty($mensaje) && $mostrar_registro): ?>
                        <div class="mensaje-error"><?php echo $mensaje; ?></div>
                    <?php endif; ?>

                    <!-- CAMPO NUEVO: FOTO OPCIONAL -->
                    <div class="campo-foto">
                        <div class="avatar-preview" onclick="document.getElementById('foto_perfil').click()">
                            <img id="avatarPreview" src="" alt="Vista previa" style="display: none;">
                            <i class="fas fa-camera" style="font-size: 30px; color: #d4af37;"></i>
                        </div>
                        <div class="avatar-texto" onclick="document.getElementById('foto_perfil').click()">
                            Agregar foto de perfil (opcional)
                        </div>
                        <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*" onchange="previewAvatar(event)">
                    </div>

                    <div class="campo">
                        <label><i class="fas fa-user"></i> Nombre completo</label>
                        <input type="text" name="nombre" placeholder="Tu nombre completo" required 
                               value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
                    </div>

                    <div class="campo">
                        <label><i class="fas fa-envelope"></i> Correo electrónico</label>
                        <input type="email" name="email" placeholder="ejemplo@correo.com" required 
                               value="<?php echo isset($_POST['email']) && $mostrar_registro ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>

                    <div class="campo">
                        <label><i class="fas fa-lock"></i> Contraseña</label>
                        <input type="password" name="password" placeholder="Crea una contraseña" required>
                    </div>

                    <button type="submit" class="btn-principal">
                        <i class="fas fa-user-plus"></i> Crear Cuenta
                    </button>

                    <p class="enlace-cambio">
                        ¿Ya tienes cuenta? 
                        <a href="#" onclick="mostrarLogin()">Inicia Sesión aquí</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script>
        function mostrarRegistro() {
            document.getElementById('formLogin').classList.remove('activo');
            document.getElementById('formRegistro').classList.add('activo');
        }

        function mostrarLogin() {
            document.getElementById('formRegistro').classList.remove('activo');
            document.getElementById('formLogin').classList.add('activo');
        }

        // FUNCIÓN NUEVA: Vista previa de avatar
        function previewAvatar(event) {
            const input = event.target;
            const preview = document.getElementById('avatarPreview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    preview.parentElement.querySelector('.fa-camera').style.display = 'none';
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        // FUNCIÓN NUEVA: Verificar sesión entre pestañas
        function verificarSesion() {
            const sesionActiva = localStorage.getItem('userSession');
            if (sesionActiva === 'active') {
                // Si hay sesión activa en otra pestaña, redirigir al index
                window.location.href = 'index.php';
            }
        }

        // Verificar sesión al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            verificarSesion();
            
            // Escuchar cambios en el localStorage entre pestañas
            window.addEventListener('storage', function(e) {
                if (e.key === 'userSession') {
                    verificarSesion();
                }
            });
        });

        // Limpiar localStorage al cerrar sesión (se ejecuta desde otras páginas)
        window.addEventListener('beforeunload', function() {
            // Esto se maneja desde el logout en otras páginas
        });
    </script>
</body>
</html> 