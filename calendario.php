<?php
include("conexion.php");
mysqli_set_charset($conexion, "utf8mb4");

$mensaje = "";

// PROCESAR REGISTRO DE NUEVO EVENTO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'guardar_evento') {
    $tipo = mysqli_real_escape_string($conexion, $_POST['tipo']);
    $fecha_inicio = mysqli_real_escape_string($conexion, $_POST['fecha_inicio']);
    $fecha_fin = !empty($_POST['fecha_fin']) ? "'" . mysqli_real_escape_string($conexion, $_POST['fecha_fin']) . "'" : "NULL";
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);

    $sql_insert = "INSERT INTO calendario (tipo, fecha_inicio, fecha_fin, descripcion) 
                   VALUES ('$tipo', '$fecha_inicio', $fecha_fin, '$descripcion')";

    if (mysqli_query($conexion, $sql_insert)) {
        $mensaje = "<div style='color: #155724; background-color: #d4edda; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>
                        ¡Evento agregado exitosamente al calendario escolar!
                    </div>";
    } else {
        $mensaje = "<div style='color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>
                        Error al guardar el evento: " . mysqli_error($conexion) . "
                    </div>";
    }
}

// PROCESAR ELIMINACIÓN DE EVENTO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar_evento') {
    $id_evento = intval($_POST['id_evento']);
    $sql_delete = "DELETE FROM calendario WHERE id = $id_evento";
    
    if (mysqli_query($conexion, $sql_delete)) {
        $mensaje = "<div style='color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>
                        El evento ha sido eliminado del calendario.
                    </div>";
    } else {
        $mensaje = "<div style='color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>
                        Error al eliminar el evento: " . mysqli_error($conexion) . "
                    </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario Escolar - Sistema de Constancias</title>
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

        /* CORRECCIÓN CLAVE: min-width: 0 y flex: 1 para forzar expansión total */
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
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
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

        .form-group input, .form-group select, .form-group textarea {
            padding: 8px 10px;
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
            padding: 9px 18px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            transition: background-color 0.2s;
        }

        .btn-guardar:hover {
            background-color: #0056b3;
        }

        .btn-eliminar {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .btn-eliminar:hover {
            background-color: #c82333;
        }

        /* TABLA AL 100% DE ANCHO REAL */
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
                    <a href="calendario.php" class="sidebar-link active">
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
        <!-- TARJETA FORMULARIO -->
        <div class="card">
            <h2><i class="fas fa-calendar-plus text-primary"></i> Registrar Fecha o Evento Escolar</h2>
            <p class="text-muted">Seleccione el tipo de evento y establezca las fechas en el cronograma institucional.</p>
            <hr style="margin-bottom: 15px; border: 0; border-top: 1px solid #eee;">

            <?php if (!empty($mensaje)) echo $mensaje; ?>

            <form method="POST" action="calendario.php">
                <input type="hidden" name="accion" value="guardar_evento">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Tipo de Evento (*):</label>
                        <select name="tipo" required>
                            <option value="Académico">Académico</option>
                            <option value="Feriado">Feriado / Día Festivo</option>
                            <option value="Administrativo">Administrativo</option>
                            <option value="Evaluación">Evaluación / Lapso</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Fecha de Inicio (*):</label>
                        <input type="date" name="fecha_inicio" required>
                    </div>
                    <div class="form-group">
                        <label>Fecha de Cierre (Opcional):</label>
                        <input type="date" name="fecha_fin">
                    </div>
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Descripción / Observación:</label>
                    <textarea name="descripcion" rows="2" placeholder="Detalles de la fecha o actividad institucional..."></textarea>
                </div>
                <button type="submit" class="btn-guardar"><i class="fas fa-save"></i> Registrar en el Calendario</button>
            </form>
        </div>

        <!-- TARJETA TABLA DE EVENTOS -->
        <div class="card">
            <h2><i class="fas fa-calendar-alt text-primary"></i> Cronograma de Fechas y Actividades</h2>
            <div class="table-responsive">
                <table>
                    <tr>
                        <th style="text-align: center; width: 90px;">Acción</th>
                        <th>Tipo de Evento</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Descripción</th>
                    </tr>
                    <?php
                    $res_cal = mysqli_query($conexion, "SELECT * FROM calendario ORDER BY fecha_inicio ASC");
                    if ($res_cal && mysqli_num_rows($res_cal) > 0) {
                        while($e = mysqli_fetch_assoc($res_cal)) {
                            echo "<tr>
                                    <td style='text-align: center;'>
                                        <form method='POST' action='calendario.php' style='margin:0;'>
                                            <input type='hidden' name='accion' value='eliminar_evento'>
                                            <input type='hidden' name='id_evento' value='" . $e['id'] . "'>
                                            <button type='submit' class='btn-eliminar' onclick=\"return confirm('¿Está seguro de eliminar este evento del calendario?');\">
                                                <i class='fas fa-trash-alt'></i> Eliminar
                                            </button>
                                        </form>
                                    </td>
                                    <td><span style='background:#e6f7ff; color:#0056b3; padding:3px 8px; border-radius:4px; font-weight:600;'>" . htmlspecialchars($e['tipo'] ?? 'Académico') . "</span></td>
                                    <td>" . htmlspecialchars($e['fecha_inicio']) . "</td>
                                    <td>" . htmlspecialchars($e['fecha_fin'] ?? 'N/A') . "</td>
                                    <td>" . htmlspecialchars($e['descripcion'] ?? '-') . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align: center; color: #6c757d; padding: 20px;'>No hay fechas ni eventos registrados en el calendario escolar.</td></tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </main>

</body>
</html>