<?php
session_start();
include("conexion.php");

$mensaje_error = '';

// 1. Procesar la acción de Cerrar Sesión
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: procesar.php");
    exit();
}

// 2. Procesar el formulario de Login (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (!empty($usuario) && !empty($password)) {
        $usuario_clean = mysqli_real_escape_string($conexion, $usuario);
        $password_clean = mysqli_real_escape_string($conexion, $password);

        // Consulta optimizada buscando el usuario y clave en la tabla 'usuarios'
        $sql = "SELECT * FROM usuarios WHERE LOWER(TRIM(usuario)) = LOWER('$usuario_clean') AND clave = '$password_clean' LIMIT 1";
        $resultado = mysqli_query($conexion, $sql);

        if ($resultado && mysqli_num_rows($resultado) > 0) {
            $datos_usuario = mysqli_fetch_assoc($resultado);
            
            // Guardar variables de sesión requeridas por el panel (index.php)
            $_SESSION['usuario'] = $datos_usuario['usuario'];
            $_SESSION['nombre'] = !empty($datos_usuario['nombre']) ? $datos_usuario['nombre'] : $datos_usuario['usuario'];
            $_SESSION['autenticado'] = true;

            $nombre_user = htmlspecialchars($_SESSION['nombre']);
            
            // Redirección exitosa al panel principal
            echo "<script>
                    alert('¡Bienvenido, $nombre_user!');
                    window.location.href = 'index.php';
                  </script>";
            exit();
        } else {
            $mensaje_error = "Usuario o contraseña incorrectos.";
        }
    } else {
        $mensaje_error = "Por favor, complete todos los campos.";
    }
}

// 3. Comprobar si ya está autenticado
$esta_autenticado = isset($_SESSION['autenticado']) && $_SESSION['autenticado'] === true;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $esta_autenticado ? 'Panel - Gladys Carnevali' : 'Login - Gladys Carnevali'; ?></title>
    <style>
        * { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; padding: 0; }
        body { 
            font-family: Arial, sans-serif; 
            background-color: #87CEEB; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            text-align: center;
        }
        .container-box { 
            background: white; 
            padding: 30px; 
            border-radius: 15px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.2); 
            width: 100%; 
            max-width: 360px; 
            margin: auto; 
        }
        .logo-escuela { width: 70px; height: 70px; object-fit: contain; display: block; margin: 0 auto 10px auto; }
        h2 { font-size: 1.2rem; margin: 10px 0 5px 0; color: #333; }
        p { margin: 0 0 20px 0; color: #666; font-size: 0.9rem; }
        input { width: 100%; padding: 10px; margin-bottom: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 0.95rem; }
        .pass-container { position: relative; width: 100%; }
        .pass-container input { padding-right: 40px; }
        .ver-btn { position: absolute; right: 10px; top: 10px; cursor: pointer; background: none; border: none; font-size: 1rem; padding: 0; line-height: 1; }
        button[type="submit"], .btn-action { display: inline-block; width: 100%; padding: 10px; background-color: #00BFFF; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; margin-top: 5px; text-decoration: none; }
        button[type="submit"]:hover, .btn-action:hover { background-color: #009acd; }
        .btn-logout { background-color: #ff4d4d; }
        .btn-logout:hover { background-color: #cc0000; }
        .forgot-pass { display: inline-block; margin-top: 15px; color: #00BFFF; text-decoration: none; font-size: 0.85rem; }
        .forgot-pass:hover { text-decoration: underline; }
        .error-msg { color: #d9534f; background-color: #f2dede; border: 1px solid #ebccd1; padding: 8px; border-radius: 5px; font-size: 0.85rem; margin-bottom: 15px; }
    </style>
</head>
<body>

    <div class="container-box">
        <img src="logo_derecha.png" alt="Logo Escuela Gladys Carnevali" class="logo-escuela" width="70" height="70">
       
        <h2>Sistema de Constancias de Estudio</h2>
        <p>Gladys Carnevali</p>

        <?php if ($esta_autenticado): ?>
            <div style="margin-top: 20px;">
                <h3>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</h3>
                <p>Ya has iniciado sesión correctamente.</p>
                <a href="index.php" class="btn-action" style="margin-bottom: 10px;">Ir al Panel Principal</a>
                <a href="procesar.php?action=logout" class="btn-action btn-logout">Cerrar Sesión</a>
            </div>
        <?php else: ?>
            <?php if (!empty($mensaje_error)): ?>
                <div class="error-msg"><?php echo htmlspecialchars($mensaje_error); ?></div>
            <?php endif; ?>

            <form action="procesar.php" method="POST">
                <input type="text" name="usuario" placeholder="Usuario" required autocomplete="username">
               
                <div class="pass-container">
                    <input type="password" id="miPassword" name="password" placeholder="Contraseña" required autocomplete="current-password">
                    <button type="button" class="ver-btn" onclick="togglePass()" aria-label="Mostrar u ocultar contraseña">👁</button>
                </div>

                <button type="submit">Ingresar</button>
            </form>

            <!-- ENLACE CORREGIDO HACIA EL NUEVO MÓDULO DE RECUPERACIÓN -->
            <a href="recuperar.php" class="forgot-pass">¿Olvidaste tu contraseña?</a>
        <?php endif; ?>
    </div>

    <script>
        function togglePass() {
            const x = document.getElementById("miPassword");
            x.type = (x.type === "password") ? "text" : "password";
        }
    </script>
</body>
</html>