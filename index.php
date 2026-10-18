<?php
error_reporting(0);
ini_set('display_errors', 0);

include("conexion.php");
mysqli_set_charset($conexion, "utf8mb4");

// Obtener contadores para el panel
$total_alumnos = 0;
$res_al = mysqli_query($conexion, "SELECT COUNT(*) as total FROM alumnos");
if ($res_al) {
    $row = mysqli_fetch_assoc($res_al);
    $total_alumnos = $row['total'];
}

$total_eventos = 0;
$res_ev = mysqli_query($conexion, "SELECT COUNT(*) as total FROM calendario");
if ($res_ev) {
    $row = mysqli_fetch_assoc($res_ev);
    $total_eventos = $row['total'];
}

$total_autoridades = 0;
$res_aut = mysqli_query($conexion, "SELECT COUNT(*) as total FROM autoridades");
if ($res_aut) {
    $row = mysqli_fetch_assoc($res_aut);
    $total_autoridades = $row['total'];
}

// Obtener datos de la escuela para el saludo
$res_conf = mysqli_query($conexion, "SELECT * FROM configuracion LIMIT 1");
$conf = ($res_conf && mysqli_num_rows($res_conf) > 0) ? mysqli_fetch_assoc($res_conf) : [
    'nombre_escuela' => 'Escuela Básica "Gladys C. Lobo de Carnevali"',
    'periodo_escolar' => '2026-2027'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Sistema de Constancias</title>
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
            cursor: pointer;
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

        /* --- SUBMENÚ DESPLEGABLE OCULTO ESTRICTAMENTE POR DEFECTO --- */
        .has-submenu {
            position: relative;
        }

        .submenu {
            list-style: none;
            padding-left: 25px;
            margin: 4px 0 8px 0;
            display: none !important; /* Forzado a ocultarse siempre al cargar */
            flex-direction: column;
            gap: 5px;
        }

        .submenu.show {
            display: flex !important; /* Solo se muestra cuando la función JS activa la clase show */
        }

        .submenu-link {
            text-decoration: none;
            color: #6c757d;
            font-size: 0.88rem;
            padding: 7px 10px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            transition: all 0.2s;
        }

        .submenu-link i {
            font-size: 0.75rem;
            margin-right: 8px;
        }

        .submenu-link:hover {
            background-color: #eef6ff;
            color: #0056b3;
            padding-left: 14px;
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

        .welcome-box {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-sizing: border-box;
        }

        .welcome-box h1 {
            margin: 0 0 10px 0;
            font-size: 1.8rem;
        }

        .welcome-box p {
            margin: 0;
            font-size: 1.05rem;
            opacity: 0.9;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
            width: 100%;
            box-sizing: border-box;
        }

        .stat-card {
            background: #FFFFFF;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-sizing: border-box;
        }

        .stat-info h3 {
            margin: 0 0 5px 0;
            font-size: 1.8rem;
            color: #212529;
        }

        .stat-info p {
            margin: 0;
            font-size: 0.88rem;
            color: #6c757d;
            font-weight: 600;
        }

        .stat-icon {
            font-size: 2.5rem;
            color: #007bff;
            background: #e6f7ff;
            padding: 15px;
            border-radius: 8px;
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
                    <a href="index.php" class="sidebar-link active">
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
                
                <!-- Gestión de Alumnos con Submenú Desplegable Oculto por Defecto -->
                <li class="sidebar-item has-submenu">
                    <a href="#" class="sidebar-link" onclick="toggleSubmenu(event)">
                        <i class="fas fa-users"></i> Gestión Alumnos
                    </a>
                    <ul class="submenu" id="alumnosSubmenu">
                        <li><a href="agregar_alumno.php" class="submenu-link"><i class="fas fa-plus-circle"></i> Registrar</a></li>
                        <li><a href="alumnos.php" class="submenu-link"><i class="fas fa-search"></i> Consultar</a></li>
                        <li><a href="modificar_alumno.php" class="submenu-link"><i class="fas fa-edit"></i> Modificar</a></li>
                        <li><a href="retirar_alumno.php" class="submenu-link"><i class="fas fa-user-minus"></i> Retirar</a></li>
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
                    <a href="mantenimiento.php" class="sidebar-link">
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
        <!-- BANNER DE BIENVENIDA -->
        <div class="welcome-box">
            <h1><i class="fas fa-school"></i> ¡Bienvenido al Sistema!</h1>
            <p><?php echo htmlspecialchars($conf['nombre_escuela']); ?> — Periodo Escolar: <strong><?php echo htmlspecialchars($conf['periodo_escolar']); ?></strong></p>
        </div>

        <!-- TARJETAS DE ESTADÍSTICAS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $total_alumnos; ?></h3>
                    <p>Alumnos Registrados</p>
                </div>
                <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $total_eventos; ?></h3>
                    <p>Eventos en Calendario</p>
                </div>
                <div class="stat-icon" style="color: #28a745; background: #e8f5e9;"><i class="fas fa-calendar-check"></i></div>
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $total_autoridades; ?></h3>
                    <p>Autoridades Firmantes</p>
                </div>
                <div class="stat-icon" style="color: #ffc107; background: #fff9e6;"><i class="fas fa-user-tie"></i></div>
            </div>
        </div>

        <!-- TARJETA INFORMATIVA -->
        <div class="card">
            <h2><i class="fas fa-info-circle text-primary"></i> Panel de Control Institucional</h2>
            <p class="text-muted">Utilice el menú lateral izquierdo para navegar entre los diferentes módulos de gestión de alumnos, configuración del plantel, cronograma escolar y emisión de constancias de estudio.</p>
        </div>
    </main>

    <script>
        /* Función estricta para que el submenú solo se abra al hacer clic explícito */
        function toggleSubmenu(e) {
            e.preventDefault();
            const submenu = document.getElementById('alumnosSubmenu');
            submenu.classList.toggle('show');
        }
    </script>
</body>
</html>