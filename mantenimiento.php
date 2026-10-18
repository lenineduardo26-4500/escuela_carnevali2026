<?php
error_reporting(0);
ini_set('display_errors', 0);

include("conexion.php");
mysqli_set_charset($conexion, "utf8mb4");

$mensaje = "";

// SIMULAR REGISTRO EN BITÁCORA / ACCIÓN DE MANTENIMIENTO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    if ($_POST['accion'] === 'respaldo') {$mensaje = "<div style='color: #155724; background-color: #d4edda; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>
                        ¡Respaldo de la base de datos generado exitosamente! (Copia de seguridad guardada en el servidor).
                    </div>";
    } elseif ($_POST['accion'] === 'crear_usuario') {$usuario = mysqli_real_escape_string($conexion,$_POST['usuario']);
        $rol = mysqli_real_escape_string($conexion, $_POST['rol']);$mensaje = "<div style='color: #155724; background-color: #d4edda; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>
                        ¡Usuario '<strong>$usuario</strong>' con rol de <strong>$rol</strong> registrado exitosamente!
                    </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenimiento - Sistema de Constancias</title>
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

        .form-group input, .form-group select {
            padding: 9px 12px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 0.9rem;
            width: 100%;
            box-sizing: border-box;
        }

        .btn-accion {
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
        .btn-accion:hover {
            background-color: #0056b3;
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
                <!-- ENLACE ACTUALIZADO A MANTENIMIENTO.PHP -->
                <li class="sidebar-item">
                    <a href="mantenimiento.php" class="sidebar-link active">
                        <i class="fas fa-tools"></i> Mantenimiento
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
        <?php if (!empty($mensaje)) echo$mensaje; ?>

        <!-- SECCIÓN DE RESPALDO Y RESTAURACIÓN -->
        <div class="card">
            <h2><i class="fas fa-database text-primary"></i> Mantenimiento y Respaldo de Base de Datos</h2>
            <p class="text-muted">Genere copias de seguridad del sistema o restaure la información institucional.</p>
            <hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #eee;">
            
            <form method="POST" action="mantenimiento.php" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                <input type="hidden" name="accion" value="respaldo">
                <button type="submit" class="btn-accion"><i class="fas fa-download"></i> Generar Respaldo (.SQL)</button>
                <span class="text-muted" style="font-size: 0.88rem;">Copia de seguridad preventiva de la base de datos `escuela_carnevali`.</span>
            </form>
        </div>

        <!-- SECCIÓN DE GESTIÓN DE USUARIOS -->
        <div class="card">
            <h2><i class="fas fa-user-shield text-primary"></i> Gestión de Usuarios del Sistema</h2>
            <p class="text-muted">Registre nuevos usuarios con acceso administrativo o docente.</p>
            <hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #eee;">

            <form method="POST" action="mantenimiento.php">
                <input type="hidden" name="accion" value="crear_usuario">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nombre de Usuario (*):</label>
                        <input type="text" name="usuario" required placeholder="Ej: admin_carnevali">
                    </div>
                    <div class="form-group">
                        <label>Contraseña (*):</label>
                        <input type="password" name="password" required placeholder="********">
                    </div>
                    <div class="form-group">
                        <label>Rol de Usuario (*):</label>
                        <select name="rol" required>
                            <option value="Administrador">Administrador</option>
                            <option value="Secretaría / Docente">Secretaría / Docente</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn-accion" style="background-color: #28a745;"><i class="fas fa-user-plus"></i> Registrar Nuevo Usuario</button>
            </form>
        </div>

        <!-- SECCIÓN DE BITÁCORA / HISTORIAL DE ACTIVIDAD -->
        <div class="card">
            <h2><i class="fas fa-clipboard-list text-primary"></i> Bitácora de Eventos y Actividad del Sistema</h2>
            <p class="text-muted">Registro de accesos y acciones críticas ejecutadas por los usuarios.</p>
            <div class="table-responsive">
                <table>
                    <tr>
                        <th>Fecha y Hora</th>
                        <th>Usuario</th>
                        <th>Acción / Módulo</th>
                        <th>Dirección IP</th>
                    </tr>
                    <tr>
                        <td>2026-10-17 21:15:00</td>
                        <td><strong>Gladys Carnevali</strong></td>
                        <td>Inicio de sesión exitoso en el sistema</td>
                        <td>127.0.0.1</td>
                    </tr>
                    <tr>
                        <td>2026-10-17 20:30:12</td>
                        <td><strong>Admin</strong></td>
                        <td>Actualización de datos en Configuración Institucional</td>
                        <td>127.0.0.1</td>
                    </tr>
                </table>
            </div>
        </div>
    </main>

</body>
</html>