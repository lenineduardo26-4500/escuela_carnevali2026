<?php
error_reporting(0);
ini_set('display_errors', 0);

include("conexion.php");
mysqli_set_charset($conexion, "utf8mb4");

$mensaje = "";

// PROCESAR ACCIÓN DE LIMPIAR HISTORIAL
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'limpiar_historial') {
    if (mysqli_query($conexion, "TRUNCATE TABLE historial")) {
        $mensaje = "<div style='color: #155724; background-color: #d4edda; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>
                        ¡El historial de actividad ha sido limpiado exitosamente!
                    </div>";
    } else {
        $mensaje = "<div style='color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>
                        Error al limpiar el historial: " . mysqli_error($conexion) . "
                    </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Actividad - Escuela "Gladys Carnevali"</title>
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

        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .btn-imprimir {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-imprimir:hover {
            background-color: #0056b3;
        }

        .btn-limpiar {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-limpiar:hover {
            background-color: #c82333;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border: 1px solid #dee2e6;
            border-radius: 6px;
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
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
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

        @media print {
            .sidebar, .header-actions, .btn-imprimir, .btn-limpiar {
                display: none !important;
            }
            .main-content {
                padding: 0 !important;
                height: auto !important;
            }
            .card {
                box-shadow: none !important;
                border: none !important;
            }
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
            <div class="header-actions">
                <h2 style="margin: 0;"><i class="fas fa-history text-primary"></i> Historial de Actividad del Sistema</h2>
                
                <div style="display: flex; gap: 10px;">
                    <button onclick="window.print()" class="btn-imprimir">
                        <i class="fas fa-print"></i> Imprimir
                    </button>

                    <form method="POST" action="historial.php" style="margin:0;">
                        <input type="hidden" name="accion" value="limpiar_historial">
                        <button type="submit" class="btn-limpiar" onclick="return confirm('¿Está seguro de que desea limpiar todo el historial de movimientos?');">
                            <i class="fas fa-trash-alt"></i> Limpiar
                        </button>
                    </form>
                </div>
            </div>

            <?php if (!empty($mensaje)) echo $mensaje; ?>

            <p class="text-muted" style="margin-top: 0; margin-bottom: 15px; font-weight: 600; color: #495057;">Registro de Movimientos Recientes</p>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Descripción del Movimiento</th>
                            <th>Usuario / Módulo</th>
                            <th style="width: 180px;">Fecha y Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res_h = mysqli_query($conexion, "SELECT * FROM historial ORDER BY id DESC");
                        if ($res_h && mysqli_num_rows($res_h) > 0) {
                            while($row = mysqli_fetch_assoc($res_h)) {
                                echo "<tr>
                                        <td>" . htmlspecialchars($row['descripcion']) . "</td>
                                        <td><strong>" . htmlspecialchars($row['usuario']) . "</strong></td>
                                        <td>" . htmlspecialchars($row['fecha_hora']) . "</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr>
                                    <td colspan='3' style='text-align: center; color: #6c757d; padding: 25px;'>
                                        No hay registros en el historial todavía.
                                    </td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>