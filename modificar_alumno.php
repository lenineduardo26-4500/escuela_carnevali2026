<?php
include("conexion.php");
mysqli_set_charset($conexion, "utf8mb4");

$estudiante = null;
$mensaje = "";

// 1. PROCESAR LA ACTUALIZACIÓN DE LOS DATOS DEL ALUMNO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'actualizar') {
    $cedula_original = mysqli_real_escape_string($conexion, $_POST['Cedula_original']);
    $nombres = mysqli_real_escape_string($conexion, $_POST['Nombres']);
    $apellidos = mysqli_real_escape_string($conexion, $_POST['Apellidos']);
    $fecha_nacimiento = mysqli_real_escape_string($conexion, $_POST['Fecha_nacimiento']);
    $cedula_alumno = mysqli_real_escape_string($conexion,$_POST['Cedula_alumno']);
    $lugar_nacimiento = mysqli_real_escape_string($conexion, $_POST['Lugar_Nacimiento']);$estado = mysqli_real_escape_string($conexion,$_POST['Estado']);
    $grado = mysqli_real_escape_string($conexion, $_POST['Grado']);$seccion = mysqli_real_escape_string($conexion,$_POST['Sección']);
    $periodo_escolar = mysqli_real_escape_string($conexion,$_POST['Periodo_escolar']);
    $nombre_representante = mysqli_real_escape_string($conexion, $_POST['Nombre_representante']);$cedula_identidad = mysqli_real_escape_string($conexion,$_POST['Cédula_Identidad']);
    $correo_representante = mysqli_real_escape_string($conexion,$_POST['correo_electronico_representante']);

    $sql_update = "UPDATE alumnos SET 
                    Nombres = '$nombres',
                    Apellidos = '$apellidos',
                    Fecha_nacimiento = '$fecha_nacimiento',
                    Cedula_alumno = '$cedula_alumno',
                    Lugar_Nacimiento = '$lugar_nacimiento',
                    Estado = '$estado',
                    Grado = '$grado',
                    `Sección` = '$seccion',
                    `Año escolar` = '$periodo_escolar',
                    Nombre_representante = '$nombre_representante',
                    Cédula_Identidad = '$cedula_identidad',
                    email_representante = '$correo_representante'
                   WHERE Cedula_alumno = '$cedula_original'";
    
    if (mysqli_query($conexion, $sql_update)) {$mensaje = "<div style='color: #155724; background-color: #d4edda; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>
                        ¡Datos del estudiante actualizados exitosamente!
                    </div>";
        $sql_refresh = "SELECT * FROM alumnos WHERE Cedula_alumno = '$cedula_alumno' LIMIT 1";
        $res_ref = mysqli_query($conexion,$sql_refresh);
        if ($res_ref && mysqli_num_rows($res_ref) > 0) {
            $estudiante = mysqli_fetch_assoc($res_ref);
        }
    } else {
        $mensaje = "<div style='color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>
                        Error al actualizar los datos: " . mysqli_error($conexion) . "
                    </div>";
    }
}

// 2. BUSCAR ESTUDIANTE POR CÉDULA, NOMBRE PARCIAL O NOMBRE COMPLETO CONCATENADO
if (isset($_GET['busqueda']) && !empty(trim($_GET['busqueda']))) {$busqueda_raw = trim($_GET['busqueda']);$busqueda = mysqli_real_escape_string($conexion,$busqueda_raw);
    
    // Consulta que busca por Cédula, Nombres, Apellidos o la combinación de Nombres + Apellidos
    $sql_search = "SELECT * FROM alumnos 
                   WHERE (Cedula_alumno LIKE '%$busqueda%' 
                      OR Nombres LIKE '%$busqueda%' 
                      OR Apellidos LIKE '%$busqueda%'
                      OR CONCAT(Nombres, ' ', Apellidos) LIKE '%$busqueda%')
                   AND (condicion IS NULL OR condicion != 'Retirado') 
                   LIMIT 1";
                   
    $resultado = mysqli_query($conexion,$sql_search);
    
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $estudiante = mysqli_fetch_assoc($resultado);
    } else {
        $mensaje = "<div style='color: #856404; background-color: #fff3cd; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>
                        No se encontró ningún estudiante activo con los datos ingresados.
                    </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Alumno - Sistema de Constancias</title>
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
            max-width: 800px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        .form-group input {
            width: 100%;
            padding: 9px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 0.95rem;
            box-sizing: border-box;
            background-color: #fdfdfd;
        }

        .btn {
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 6px;
            display: inline-block;
            cursor: pointer;
            border: none;
            font-weight: 600;
        }

        .btn-buscar {
            background-color: #007bff;
            color: white;
            margin-top: 8px;
        }
        .btn-buscar:hover { background-color: #0056b3; }

        .btn-actualizar {
            background-color: #28a745;
            color: white;
            width: 100%;
            text-align: center;
            font-size: 1rem;
            padding: 12px;
            margin-top: 10px;
        }
        .btn-actualizar:hover { background-color: #218838; }
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
                
                <!-- GESTIÓN ALUMNOS CON SUBMENÚ DESPLEGADO -->
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link active" style="background-color: #f8f9fa; font-weight: 600;">
                        <i class="fas fa-users"></i> Gestión Alumnos
                    </a>
                    <ul class="submenu">
                        <li><a href="agregar_alumno.php" class="sidebar-link"><i class="fas fa-plus-circle"></i> Registrar</a></li>
                        <li><a href="alumnos.php" class="sidebar-link"><i class="fas fa-search"></i> Consultar</a></li>
                        <li><a href="modificar_alumno.php" class="sidebar-link active"><i class="fas fa-edit"></i> Modificar</a></li>
                        <li><a href="retirar_alumno.php" class="sidebar-link"><i class="fas fa-user-minus"></i> Retirar</a></li>
                    </ul>
                </li>

                <li class="sidebar-item">
                    <a href="calendario.php" class="sidebar-link">
                        <i class="fas fa-calendar-alt"></i> Calendario
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="configuracion.php" class="sidebar-link">
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
        <div class="card" style="max-width: 100%;">
            <h2><i class="fas fa-user-edit text-primary"></i> Modificar Datos de Alumno</h2>
            <p class="text-muted">Busque al estudiante por cédula, un solo nombre o por su nombre y apellido completo.</p>
            <hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #dee2e6;">

            <?php if (!empty($mensaje)) echo$mensaje; ?>

            <!-- FORMULARIO DE BÚSQUEDA -->
            <form method="GET" action="modificar_alumno.php">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="busqueda">Buscar Estudiante (Cédula o Nombre Completo):</label>
                    <div style="display: flex; gap: 10px; margin-top: 5px;">
                        <input type="text" name="busqueda" id="busqueda" placeholder="Ej. 29500100 o Samuel Andres" value="<?php echo isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : ''; ?>" required style="margin-top: 0;">
                        <button type="submit" class="btn btn-buscar"><i class="fas fa-search"></i> Buscar</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- FORMULARIO DE EDICIÓN -->
        <?php if ($estudiante): ?>
            <div class="card">
                <h3>Editar Información del Estudiante</h3>
                <form method="POST" action="modificar_alumno.php">
                    <input type="hidden" name="accion" value="actualizar">
                    <input type="hidden" name="Cedula_original" value="<?php echo htmlspecialchars($estudiante['Cedula_alumno']); ?>">

                    <div class="form-group">
                        <label>Nombres:</label>
                        <input type="text" name="Nombres" value="<?php echo htmlspecialchars($estudiante['Nombres'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Apellidos:</label>
                        <input type="text" name="Apellidos" value="<?php echo htmlspecialchars($estudiante['Apellidos'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Fecha de Nacimiento:</label>
                        <input type="date" name="Fecha_nacimiento" value="<?php echo htmlspecialchars($estudiante['Fecha_nacimiento'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Cédula del Alumno:</label>
                        <input type="text" name="Cedula_alumno" value="<?php echo htmlspecialchars($estudiante['Cedula_alumno'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>Lugar de Nacimiento:</label>
                        <input type="text" name="Lugar_Nacimiento" value="<?php echo htmlspecialchars($estudiante['Lugar_Nacimiento'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>Estado:</label>
                        <input type="text" name="Estado" value="<?php echo htmlspecialchars($estudiante['Estado'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>Grado:</label>
                        <input type="number" name="Grado" min="1" max="6" value="<?php echo htmlspecialchars($estudiante['Grado'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>Sección:</label>
                        <input type="text" name="Sección" maxlength="2" value="<?php echo htmlspecialchars($estudiante['Sección'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>Año Escolar / Periodo:</label>
                        <input type="text" name="Periodo_escolar" value="<?php echo htmlspecialchars($estudiante['Año escolar'] ?? $estudiante['Periodo_escolar'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>Nombre del Representante:</label>
                        <input type="text" name="Nombre_representante" value="<?php echo htmlspecialchars($estudiante['Nombre_representante'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>Cédula de Identidad (Representante):</label>
                        <input type="text" name="Cédula_Identidad" value="<?php echo htmlspecialchars($estudiante['Cédula_Identidad'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>Correo Electrónico (Representante):</label>
                        <input type="email" name="correo_electronico_representante" value="<?php echo htmlspecialchars($estudiante['email_representante'] ?? $estudiante['correo_electronico_representante'] ?? ''); ?>">
                    </div>

                    <button type="submit" class="btn btn-actualizar" onclick="return confirm('¿Desea guardar los cambios realizados en este estudiante?');">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </main>

</body>
</html>