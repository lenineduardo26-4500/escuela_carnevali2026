<?php
include("conexion.php");
mysqli_set_charset($conexion, "utf8mb4");

$mensaje = "";

// PROCESAR GUARDADO O ACTUALIZACIÓN DE CONFIGURACIÓN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'guardar_config') {
    $nombre_escuela = mysqli_real_escape_string($conexion, $_POST['nombre_escuela']);
    $codigo_dea = mysqli_real_escape_string($conexion, $_POST['codigo_dea']);
    $periodo_escolar = mysqli_real_escape_string($conexion, $_POST['periodo_escolar']);
    $direccion = mysqli_real_escape_string($conexion, $_POST['direccion']);
    $municipio = mysqli_real_escape_string($conexion, $_POST['municipio']);

    // Verificamos si ya existe un registro de configuración
    $verificar = mysqli_query($conexion, "SELECT id FROM configuracion LIMIT 1");

    if ($verificar && mysqli_num_rows($verificar) > 0) {
        // Si ya existe, actualizamos
        $sql = "UPDATE configuracion SET 
                nombre_escuela = '$nombre_escuela', 
                codigo_dea = '$codigo_dea', 
                periodo_escolar = '$periodo_escolar', 
                direccion = '$direccion', 
                municipio = '$municipio' 
                WHERE id = 1";
    } else {
        // Si no existe, insertamos
        $sql = "INSERT INTO configuracion (id, nombre_escuela, codigo_dea, periodo_escolar, direccion, municipio) 
                VALUES (1, '$nombre_escuela', '$codigo_dea', '$periodo_escolar', '$direccion', '$municipio')";
    }

    if (mysqli_query($conexion, $sql)) {
        $mensaje = "<div style='color: #155724; background-color: #d4edda; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>
                        ¡Configuración institucional guardada exitosamente!
                    </div>";
    } else {
        $mensaje = "<div style='color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>
                        Error al guardar la configuración: " . mysqli_error($conexion) . "
                    </div>";
    }
}

// OBTENER DATOS ACTUALES DE CONFIGURACIÓN DE FORMA SEGURA
$res_conf = mysqli_query($conexion, "SELECT * FROM configuracion LIMIT 1");
$datos_conf = ($res_conf && mysqli_num_rows($res_conf) > 0) ? mysqli_fetch_assoc($res_conf) : [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración Institucional - Sistema de Constancias</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #F4F7F6;
            color: #212529;
            display: flex;
            width: 100vw;
            overflow: hidden;
        }

        .sidebar {
            width: 250px;
            background-color: #FFFFFF;
            padding: 15px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100vh;
            box-sizing: border-box;
            flex-shrink: 0;
            overflow-y: auto;
        }

        .sidebar-logo {
            font-size: 1.0rem;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 20px;
            text-align: center;
        }

        .sidebar-logo img {
            width: 55px;
            height: 55px;
            object-fit: contain;
            display: block;
            margin: 0 auto 8px auto;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-item {
            margin-bottom: 6px;
        }

        .sidebar-link {
            text-decoration: none;
            color: #495057;
            display: flex;
            align-items: center;
            padding: 9px 12px;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: background-color 0.2s;
        }

        .sidebar-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .sidebar-link:hover, .sidebar-link.active {
            background-color: #E6F7FF;
            color: #0056b3;
            font-weight: 600;
        }

        .submenu {
            list-style: none;
            padding-left: 25px;
            margin: 4px 0 8px 0;
        }

        .submenu .sidebar-link {
            font-size: 0.88rem;
            padding: 7px 10px;
            color: #6c757d;
        }

        .submenu .sidebar-link:hover, .submenu .sidebar-link.active {
            color: #0056b3;
            background-color: #eef6ff;
        }

        .main-content {
            flex: 1;
            min-width: 0;
            height: 100vh;
            padding: 20px 30px;
            background-color: #F4F7F6;
            overflow-y: auto;
            box-sizing: border-box;
        }

        .card {
            background: #FFFFFF;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
            margin-bottom: 25px;
            width: 100%;
            box-sizing: border-box;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
            width: 100%;
            box-sizing: border-box;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            width: 100%;
            box-sizing: border-box;
        }

        .form-group label {
            font-size: 0.88rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: #495057;
        }

        .form-group input, .form-group textarea {
            padding: 9px 12px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 0.9rem;
            width: 100%;
            box-sizing: border-box;
        }

        .btn-guardar {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95rem;
            transition: background-color 0.2s;
        }

        .btn-guardar:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <!-- BARRA LATERAL PERMANENTE -->
    <nav class="sidebar">
        <div>
            <div class="sidebar-logo">
                <img src="logo_derecha.png" alt="Logo Escuela">
                Sistema de Constancias
            </div>
            <ul class="sidebar-menu">
                <li class="sidebar-item">
                    <a href="index.php" class="sidebar-link">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="egresados.php" class="sidebar-link">
                        <i class="fas fa-user-graduate"></i> Egresados
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="retirados.php" class="sidebar-link">
                        <i class="fas fa-user-slash"></i> Retirados
                    </a>
                </li>
                
                <!-- GESTIÓN ALUMNOS CON SUBMENÚ NORMAL -->
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="fas fa-users"></i> Gestión Alumnos
                    </a>
                    <ul class="submenu">
                        <li><a href="agregar_alumno.php" class="sidebar-link"><i class="fas fa-plus-circle"></i> Registrar</a></li>
                        <li><a href="alumnos.php" class="sidebar-link"><i class="fas fa-search"></i> Consultar</a></li>
                        <li><a href="modificar_alumno.php" class="sidebar-link"><i class="fas fa-edit"></i> Modificar</a></li>
                        <li><a href="retirar_alumno.php" class="sidebar-link"><i class="fas fa-user-minus"></i> Retirar</a></li>
                    </ul>
                </li>

                <li class="sidebar-item">
                    <a href="calendario.php" class="sidebar-link">
                        <i class="fas fa-calendar-alt"></i> Calendario
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="configuracion.php" class="sidebar-link active">
                        <i class="fas fa-cogs"></i> Configuración
                    </a>
                </li>
            </ul>
        </div>

        <ul class="sidebar-menu">
            <li class="sidebar-item">
                <a href="procesar.php?action=logout" class="sidebar-link" style="color: #dc3545;" onclick="return confirm('¿Desea cerrar sesión?')">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </li>
        </ul>
    </nav>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="main-content">
        <div class="card">
            <h2><i class="fas fa-school text-primary"></i> Configuración de Datos Institucionales</h2>
            <p class="text-muted">Actualice la información oficial de la escuela que aparecerá reflejada en los encabezados y documentos del sistema.</p>
            <hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #eee;">

            <?php if (!empty($mensaje)) echo $mensaje; ?>

            <form method="POST" action="configuracion.php">
                <input type="hidden" name="accion" value="guardar_config">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nombre Oficial de la Institución (*):</label>
                        <input type="text" name="nombre_escuela" required value="<?php echo htmlspecialchars($datos_conf['nombre_escuela'] ?? 'Escuela Básica "Gladys C. Lobo de Carnevali"'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Código DEA / Estadístico (*):</label>
                        <input type="text" name="codigo_dea" required placeholder="Ej: OD01234567" value="<?php echo htmlspecialchars($datos_conf['codigo_dea'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Periodo Escolar Activo (*):</label>
                        <input type="text" name="periodo_escolar" required placeholder="Ej: 2026-2027" value="<?php echo htmlspecialchars($datos_conf['periodo_escolar'] ?? '2026-2027'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Municipio / Localidad:</label>
                        <input type="text" name="municipio" placeholder="Ej: Municipio Libertador" value="<?php echo htmlspecialchars($datos_conf['municipio'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label>Dirección Completa del Plantel:</label>
                    <textarea name="direccion" rows="3" placeholder="Ubicación exacta de la escuela..."><?php echo htmlspecialchars($datos_conf['direccion'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn-guardar"><i class="fas fa-save"></i> Guardar Configuración</button>
            </form>
        </div>
    </main>

</body>
</html>