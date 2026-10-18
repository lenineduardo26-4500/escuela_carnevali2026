<?php
include("conexion.php");

$mensaje = "";
$tipo_alerta = "";
$paso = 1; // Paso 1: Buscar usuario. Paso 2: Cambiar contraseña.
$usuario_encontrado = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si se busca el usuario
    if (isset($_POST['buscar_usuario'])) {
        $usuario_ingresado = trim($_POST['usuario']);
        $usuario_clean = mysqli_real_escape_string($conexion, $usuario_ingresado);

        $sql = "SELECT * FROM usuarios WHERE LOWER(TRIM(usuario)) = LOWER('$usuario_clean') LIMIT 1";
        $resultado = mysqli_query($conexion, $sql);

        if ($resultado && mysqli_num_rows($resultado) > 0) {
            $paso = 2;
            $usuario_encontrado = $usuario_ingresado;
        } else {
            $mensaje = "El usuario ingresado no se encuentra registrado en el sistema.";
            $tipo_alerta = "error";
        }
    }

    // Si se actualiza la contraseña
    if (isset($_POST['actualizar_clave'])) {
        $usuario_ingresado = trim($_POST['usuario_oculto']);
        $nueva_clave = trim($_POST['nueva_clave']);
        $conf_clave = trim($_POST['conf_clave']);

        if (!empty($nueva_clave) && $nueva_clave === $conf_clave) {
            $usuario_clean = mysqli_real_escape_string($conexion, $usuario_ingresado);
            $clave_clean = mysqli_real_escape_string($conexion, $nueva_clave);

            $sql_update = "UPDATE usuarios SET clave = '$clave_clean' WHERE LOWER(TRIM(usuario)) = LOWER('$usuario_clean')";
            
            if (mysqli_query($conexion, $sql_update)) {
                $mensaje = "¡Contraseña actualizada con éxito! Ya puedes iniciar sesión.";
                $tipo_alerta = "success";
                $paso = 1;
            } else {
                $mensaje = "Error al actualizar la contraseña en la base de datos.";
                $tipo_alerta = "error";
                $paso = 2;
                $usuario_encontrado = $usuario_ingresado;
            }
        } else {
            $mensaje = "Las contraseñas no coinciden o están vacías.";
            $tipo_alerta = "error";
            $paso = 2;
            $usuario_encontrado = $usuario_ingresado;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña - Gladys Carnevali</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #87CEEB; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .container-box { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); width: 100%; max-width: 360px; text-align: center; }
        input { width: 100%; padding: 10px; margin-bottom: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 0.95rem; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #00BFFF; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        button:hover { background-color: #009acd; }
        .alert { padding: 8px; border-radius: 5px; font-size: 0.85rem; margin-bottom: 15px; }
        .alert.error { color: #d9534f; background-color: #f2dede; border: 1px solid #ebccd1; }
        .alert.success { color: #3c763d; background-color: #dff0d8; border: 1px solid #d6e9c6; }
        .back-link { display: inline-block; margin-top: 15px; color: #00BFFF; text-decoration: none; font-size: 0.85rem; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container-box">
        <h2>Recuperar Contraseña</h2>
        <p style="color: #666; font-size: 0.9rem;">Sistema de Constancias</p>

        <?php if (!empty($mensaje)): ?>
            <div class="alert <?php echo $tipo_alerta; ?>"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>

        <?php if ($paso === 1): ?>
            <!-- PASO 1: Consultar si el usuario existe -->
            <form action="recuperar.php" method="POST">
                <p style="font-size: 0.85rem; text-align: left; color: #444;">Ingresa tu nombre de usuario </p>
                <input type="text" name="usuario" placeholder="Nombre de usuario" required autocomplete="username">
                <button type="submit" name="buscar_usuario">Buscar Usuario</button>
            </form>
        <?php else: ?>
            <!-- PASO 2: Asignar nueva contraseña -->
            <form action="recuperar.php" method="POST">
                <input type="hidden" name="usuario_oculto" value="<?php echo htmlspecialchars($usuario_encontrado); ?>">
                <p style="font-size: 0.85rem; text-align: left; color: #444;">Usuario verificado: <strong><?php echo htmlspecialchars($usuario_encontrado); ?></strong></p>
                <input type="password" name="nueva_clave" placeholder="Nueva contraseña" required autocomplete="new-password">
                <input type="password" name="conf_clave" placeholder="Confirmar nueva contraseña" required autocomplete="new-password">
                <button type="submit" name="actualizar_clave">Asignar Nueva Contraseña</button>
            </form>
        <?php endif; ?>

        <a href="procesar.php" class="back-link">&larr; Volver al Login</a>
    </div>
</body>
</html>