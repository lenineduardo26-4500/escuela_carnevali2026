<?php
include("conexion.php");
mysqli_set_charset($conexion, "utf8mb4");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Alumnos - Sistema de Constancias</title>
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
            margin-bottom: 30px;
        }

        /* BARRA DE DESPLAZAMIENTO SUPERIOR REAL */
        .scroll-superior-wrapper {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            margin-bottom: 8px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            background-color: #f8f9fa;
        }

        .scroll-superior-content {
            height: 1px;
        }

        /* CONTENEDOR DE LA TABLA */
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
            min-width: 1200px;
            background-color: #FFFFFF;
        }

        th, td {
            border: 1px solid #dee2e6;
            padding: 11px;
            text-align: left;
            white-space: nowrap;
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
                
                <!-- GESTIÓN ALUMNOS CON SUBMENÚ DESPLEGADO -->
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link active" style="background-color: #f8f9fa; font-weight: 600;">
                        <i class="fas fa-users"></i> Gestión Alumnos
                    </a>
                    <ul class="submenu">
                        <li><a href="agregar_alumno.php" class="sidebar-link"><i class="fas fa-plus-circle"></i> Registrar</a></li>
                        <li><a href="alumnos.php" class="sidebar-link active"><i class="fas fa-search"></i> Consultar</a></li>
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
            <h2><i class="fas fa-users text-primary"></i> Consultar Listado de Alumnos</h2>
            <p class="text-muted">Registro completo de los estudiantes activos en la institución.</p>
            <hr style="margin-bottom: 15px; border: 0; border-top: 1px solid #eee;">

            <!-- BARRA DE DESPLAZAMIENTO SUPERIOR -->
            <div class="scroll-superior-wrapper" id="scrollSuperior">
                <div class="scroll-superior-content" id="scrollSuperiorContent"></div>
            </div>

            <!-- CONTENEDOR DE LA TABLA -->
            <div class="table-responsive" id="tableResponsive">
                <table>
                    <tr>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Fecha Nacimiento</th>
                        <th>Cédula Alumno</th>
                        <th>Lugar Nacimiento</th>
                        <th>Estado</th>
                        <th>Grado</th>
                        <th>Sección</th>
                        <th>Año escolar</th>
                        <th>Representante</th>
                        <th>Cédula Identidad</th>
                        <th>Email Representante</th>
                    </tr>
                    <?php
                    $resultado = mysqli_query($conexion, "SELECT * FROM alumnos");
                    if ($resultado && mysqli_num_rows($resultado) > 0) {
                        while($fila = mysqli_fetch_assoc($resultado)) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($fila['Nombres'] ?? '') . "</td>
                                    <td>" . htmlspecialchars($fila['Apellidos'] ?? '') . "</td>
                                    <td>" . htmlspecialchars($fila['Fecha_nacimiento'] ?? '') . "</td>
                                    <td>" . htmlspecialchars($fila['Cédula_alumno'] ?? '') . "</td>
                                    <td>" . htmlspecialchars($fila['Lugar_Nacimiento'] ?? '') . "</td>
                                    <td>" . htmlspecialchars($fila['Estado'] ?? '') . "</td>
                                    <td>" . htmlspecialchars($fila['Grado'] ?? '') . "</td>
                                    <td>" . htmlspecialchars($fila['Sección'] ?? '') . "</td>
                                    <td>" . htmlspecialchars($fila['Año escolar'] ?? $fila['Periodo_escolar'] ?? '') . "</td>
                                    <td>" . htmlspecialchars($fila['Nombre_representante'] ?? '') . "</td>
                                    <td>" . htmlspecialchars($fila['Cédula_Identidad'] ?? '') . "</td>
                                    <td>" . htmlspecialchars($fila['email_representante'] ?? $fila['correo_electronico_representante'] ?? '') . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='12' style='text-align: center; color: #6c757d; padding: 20px;'>No hay alumnos registrados actualmente.</td></tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </main>

    <!-- SCRIPT DE SINCRONIZACIÓN PERFECTA -->
    <script>
        const scrollSup = document.getElementById('scrollSuperior');
        const scrollSupContent = document.getElementById('scrollSuperiorContent');
        const tableResp = document.getElementById('tableResponsive');
        const tabla = document.querySelector('table');

        // Igualar el ancho del contenido fantasma al ancho real de la tabla
        function sincronizarAnchos() {
            scrollSupContent.style.width = tabla.scrollWidth + 'px';
        }

        window.addEventListener('load', sincronizarAnchos);
        window.addEventListener('resize', sincronizarAnchos);

        // Sincronizar el scroll superior con la tabla inferior
        scrollSup.addEventListener('scroll', () => {
            if (tableResp.scrollLeft !== scrollSup.scrollLeft) {
                tableResp.scrollLeft = scrollSup.scrollLeft;
            }
        });

        // Sincronizar la tabla inferior con el scroll superior
        tableResp.addEventListener('scroll', () => {
            if (scrollSup.scrollLeft !== tableResp.scrollLeft) {
                scrollSup.scrollLeft = tableResp.scrollLeft;
            }
        });
    </script>

</body>
</html>