<?php
error_reporting(0);
ini_set('display_errors', 0);

$mensaje = "";

// Conexión y procesamiento (si se envió el formulario)
if (isset($_POST['registrar'])) {
    $servidor = "localhost";
    $usuario = "root";
    $password = "";
    $base_datos = "escuela_carnevali";

    try {
        $conexion = new PDO("mysql:host=$servidor;dbname=$base_datos;charset=utf8", $usuario, $password);
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "INSERT INTO alumnos (Nombres, Apellidos, Fecha_nacimiento, Cedula_alumno, Lugar_Nacimiento, Estado, Grado, Sección, Periodo_escolar, Nombre_representante, Cedula_Identidad, correo_electronico_representante) 
                VALUES (:nombres, :apellidos, :fecha_nacimiento, :cedula_alumno, :lugar_nacimiento, :estado, :grado, :seccion, :periodo_escolar, :nombre_representante, :cedula_identidad, :correo_representante)";
        
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ':nombres' => $_POST['nombres'],
            ':apellidos' => $_POST['apellidos'],
            ':fecha_nacimiento' => $_POST['fecha_nacimiento'],
            ':cedula_alumno' => $_POST['cedula_alumno'],
            ':lugar_nacimiento' => $_POST['lugar_nacimiento'],
            ':estado' => $_POST['estado'],
            ':grado' => $_POST['grado'],
            ':seccion' => $_POST['seccion'],
            ':periodo_escolar' => $_POST['periodo_escolar'],
            ':nombre_representante' => $_POST['nombre_representante'],
            ':cedula_identidad' => $_POST['cedula_identidad'],
            ':correo_representante' => $_POST['correo_electronico_representante']
        ]);

        // --- INSERCIÓN AUTOMÁTICA EN EL HISTORIAL ---
        $desc_hist = "Se registró al alumno: " . $_POST['nombres'] . " " . $_POST['apellidos'] . " (Cédula: " . $_POST['cedula_alumno'] . ")";
        $user_act = "Gladys Carnevali";
        $stmt_h = $conexion->prepare("INSERT INTO historial (descripcion, usuario) VALUES (:desc, :usr)");
        $stmt_h->execute([':desc' => $desc_hist, ':usr' => $user_act]);
        // --------------------------------------------

        $mensaje = "<div style='color: #155724; background-color: #d4edda; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: 600;'>¡Alumno registrado exitosamente y guardado en el historial!</div>";
    } catch (PDOException $e) {
        $mensaje = "<div style='color: #721c24; background-color: #f8d7da; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: 600;'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Alumno - Sistema de Constancias</title>
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
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-size: 0.88rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: #495057;
        }

        .form-group input {
            padding: 9px 12px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 0.9rem;
            width: 100%;
            box-sizing: border-box;
        }

        .btn-submit {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95rem;
            transition: background-color 0.2s;
        }
        .btn-submit:hover {
            background-color: #218838;
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
                
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link active">
                        <i class="fas fa-users"></i> Gestión Alumnos
                    </a>
                    <ul class="submenu">
                        <li><a href="agregar_alumno.php" class="sidebar-link active"><i class="fas fa-plus-circle"></i> Registrar</a></li>
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
                    <a href="autoridades.php" class="sidebar-link">
                        <i class="fas fa-user-tie"></i> Autoridades
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="constancias.php" class="sidebar-link">
                        <i class="fas fa-file-alt"></i> Constancias
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="configuracion.php" class="sidebar-link">
                        <i class="fas fa-cogs"></i> Configuración
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="seguridad.php" class="sidebar-link">
                        <i class="fas fa-shield-alt"></i> Seguridad y Bitácora
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
            <h2><i class="fas fa-user-plus text-primary"></i> Formulario de Registro de Alumnos</h2>
            <hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #eee;">

            <?php if (!empty($mensaje)) echo $mensaje; ?>

            <form action="" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nombres:</label>
                        <input type="text" name="nombres" required>
                    </div>

                    <div class="form-group">
                        <label>Apellidos:</label>
                        <input type="text" name="apellidos" required>
                    </div>

                    <div class="form-group">
                        <label>Fecha de Nacimiento:</label>
                        <input type="date" name="fecha_nacimiento" required>
                    </div>

                    <div class="form-group">
                        <label>Cédula del Alumno:</label>
                        <input type="text" name="cedula_alumno">
                    </div>

                    <div class="form-group">
                        <label>Lugar de Nacimiento:</label>
                        <input type="text" name="lugar_nacimiento">
                    </div>

                    <div class="form-group">
                        <label>Estado:</label>
                        <input type="text" name="estado">
                    </div>

                    <div class="form-group">
                        <label>Grado:</label>
                        <input type="number" name="grado" min="1" max="6">
                    </div>

                    <div class="form-group">
                        <label>Sección:</label>
                        <input type="text" name="seccion" maxlength="2">
                    </div>

                    <div class="form-group">
                        <label>Periodo Escolar:</label>
                        <input type="text" name="periodo_escolar" placeholder="Ej. 2025-2026">
                    </div>

                    <div class="form-group">
                        <label>Nombre del Representante:</label>
                        <input type="text" name="nombre_representante">
                    </div>

                    <div class="form-group">
                        <label>Cédula de Identidad (Representante):</label>
                        <input type="text" name="cedula_identidad">
                    </div>

                    <div class="form-group">
                        <label>Correo Electrónico (Representante):</label>
                        <input type="email" name="correo_electronico_representante">
                    </div>
                </div>

                <button type="submit" name="registrar" class="btn-submit"><i class="fas fa-save"></i> Registrar Alumno</button>
            </form>
        </div>
    </main>

</body>
</html>