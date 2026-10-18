<?php
error_reporting(0);
ini_set('display_errors', 0);

include("conexion.php");
mysqli_set_charset($conexion, "utf8mb4");

$mensaje = "";

// PROCESAR CAMBIO DE ESTATUS O REGISTRO DE EGRESADO SI SE REQUIERE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'registrar_egresado') {
    $cedula = mysqli_real_escape_string($conexion, $_POST['cedula']);
    $nombres = mysqli_real_escape_string($conexion, $_POST['nombres']);
    $apellidos = mysqli_real_escape_string($conexion, $_POST['apellidos']);
    $periodo_egreso = mysqli_real_escape_string($conexion, $_POST['periodo_egreso']);

    $sql = "INSERT INTO egresados (cedula, nombres, apellidos, periodo_egreso) VALUES ('$cedula', '$nombres', '$apellidos', '$periodo_egreso')";
    if (mysqli_query($conexion, $sql)) {
        $mensaje = "<div style='color: #155724; background-color: #d4edda; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>
                        ¡Estudiante registrado en el historial de egresados exitosamente!
                    </div>";
    } else {
        // Si la tabla no existe aún, se crea de forma automática
        mysqli_query($conexion, "CREATE TABLE IF NOT EXISTS egresados (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cedula VARCHAR(20),
            nombres VARCHAR(100),
            apellidos VARCHAR(100),
            periodo_egreso VARCHAR(50)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        
        mysqli_query($conexion, $sql);
        $mensaje = "<div style='color: #155724; background-color: #d4edda; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>
                        ¡Egresado registrado exitosamente!
                    </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Egresados - Sistema de Constancias</title>
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

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            margin-top: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            background-color: #FFFFFF;
        }

        th, td {
            border: 1px solid #dee2e6;
            padding: 11px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f3f5;
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
                    <a href="egresados.php" class="sidebar-link active">
                        <i class="fas fa-user-graduate"></i> Egresados
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="retirados.php" class="sidebar-link">
                        <i class="fas fa-user-slash"></i> Retirados
                    </a>
                </li>
                
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
        <?php if (!empty($mensaje)) echo $mensaje; ?>

        <div class="card">
            <h2><i class="fas fa-user-graduate text-primary"></i> Historial de Alumnos Egresados</h2>
            <p class="text-muted">Consulta el listado general de estudiantes graduados de la institución.</p>
            <hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #eee;">

            <div class="table-responsive">
                <table>
                    <tr>
                        <th>Cédula / Identidad</th>
                        <th>Apellidos y Nombres</th>
                        <th>Periodo de Egreso</th>
                        <th style="text-align: center;">Estatus</th>
                    </tr>
                    <?php
                    $res_egr = mysqli_query($conexion, "SELECT * FROM egresados ORDER BY apellidos ASC");
                    if ($res_egr && mysqli_num_rows($res_egr) > 0) {
                        while($eg = mysqli_fetch_assoc($res_egr)) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($eg['cedula'] ?? 'N/A') . "</td>
                                    <td><strong>" . htmlspecialchars($eg['apellidos'] . ", " . $eg['nombres']) . "</strong></td>
                                    <td>" . htmlspecialchars($eg['periodo_egreso'] ?? '2025-2026') . "</td>
                                    <td style='text-align: center;'><span style='background:#e8f5e9; color:#2e7d32; padding:3px 8px; border-radius:4px; font-weight:600;'>Egresado(a)</span></td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align: center; color: #6c757d; padding: 20px;'>No hay registros de alumnos egresados cargados en el historial.</td></tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </main>

</body>
</html>