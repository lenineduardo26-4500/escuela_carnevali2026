<?php
$conexion = mysqli_connect("localhost", "root", "", "escuela_carnevali");
if ($conexion) {
    mysqli_set_charset($conexion, "utf8mb4");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - Sistema de Constancias</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #F4F7F6;
            color: #212529;
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 240px;
            background-color: #FFFFFF;
            padding: 15px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
        }
        .sidebar-logo {
            font-size: 1.1rem;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 25px;
            text-align: center;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-item {
            margin-bottom: 10px;
        }
        .sidebar-link {
            text-decoration: none;
            color: #495057;
            display: flex;
            align-items: center;
            padding: 10px 12px;
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
        .has-submenu {
            position: relative;
        }
        .submenu {
            list-style: none;
            padding-left: 25px;
            margin: 5px 0 10px 0;
            display: none;
            flex-direction: column;
            gap: 5px;
        }
        .submenu.show {
            display: flex;
        }
        .submenu-link {
            text-decoration: none;
            color: #6c757d;
            font-size: 0.88rem;
            padding: 6px 10px;
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
            background-color: #f1f3f5;
            color: #0056b3;
            padding-left: 14px;
        }
        .main-content {
            flex: 1;
            padding: 20px 30px;
            background-color: #F4F7F6;
            overflow-y: hidden;
        }
        .header-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #343a40;
        }
        .search-container {
            display: flex;
            margin-bottom: 15px;
            background-color: #FFFFFF;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
            overflow: hidden;
            width: 350px;
        }
        .search-input {
            flex: 1;
            padding: 10px 15px;
            border: none;
            outline: none;
            background: transparent;
            font-size: 0.9rem;
        }
        .search-btn {
            background: transparent;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            color: #6c757d;
        }
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        .module-card {
            background-color: #E6F7FF;
            padding: 12px 15px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 123, 255, 0.06);
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 55px;
        }
        .module-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.12);
        }
        .module-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .module-icon {
            font-size: 1.3rem;
            color: #0056b3;
        }
        .module-title {
            font-weight: 600;
            color: #212529;
            font-size: 0.95rem;
            text-align: left;
        }
        .module-btn {
            text-decoration: none;
            display: inline-block;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s;
            background-color: #FFFFFF;
            color: #0056b3;
            border: 1px solid #B3E0FF;
        }
        .module-btn:hover {
            background-color: #CCE5FF;
        }
        .quick-actions {
            margin-top: 15px;
            background: #FFFFFF;
            padding: 12px 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
        }
        .quick-actions h3 {
            margin: 0 0 10px 0;
            font-size: 1rem;
            color: #343a40;
        }
        .quick-actions button {
            padding: 8px 14px;
            font-size: 0.85rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; height: auto; }
            .modules-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <nav class="sidebar">
        <div class="sidebar-logo">Sistema de Constancias</div>
        <ul class="sidebar-menu">
            <li class="sidebar-item"><a href="modulo.php" class="sidebar-link active" onclick="showAllModules(event)"><i class="fas fa-home"></i> Inicio</a></li>
            <li class="sidebar-item"><a href="retirados.php" class="sidebar-link"><i class="fas fa-user-slash"></i> Retirados</a></li>
           
            <li class="sidebar-item has-submenu">
                <a href="#" class="sidebar-link" onclick="toggleSubmenu(event)"><i class="fas fa-users-cog"></i> Gestión Alumnos</a>
                <ul class="submenu" id="alumnosSubmenu">
                    <li><a href="agregar_alumno.php" class="submenu-link"><i class="fas fa-plus-circle"></i> Registrar</a></li>
                    <li><a href="consultar_alumno.php" class="submenu-link"><i class="fas fa-search"></i> Consultar</a></li>
                    <li><a href="modificar_alumno.php" class="submenu-link"><i class="fas fa-edit"></i> Modificar</a></li>
                    <li><a href="retirar_alumno.php" class="submenu-link"><i class="fas fa-trash-alt"></i> Retirar</a></li>
                </ul>
            </li>

            <li class="sidebar-item"><a href="calendario.php" class="sidebar-link"><i class="fas fa-calendar-alt"></i> Calendario</a></li>
            <li class="sidebar-item"><a href="configuracion.php" class="sidebar-link"><i class="fas fa-cogs"></i> Configuración</a></li>
            <li class="sidebar-item"><a href="procesar.php?action=logout" class="sidebar-link" style="color: #dc3545;" onclick="return confirm('¿Desea cerrar sesión?')"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <h1 class="header-title">Panel de Control - Escuela "Gladys Carnevali"</h1>

        <div class="search-container">
            <input type="text" class="search-input" placeholder="Buscar módulos..." id="searchInput" oninput="searchModules()">
            <button class="search-btn" onclick="searchModules()"><i class="fas fa-search"></i></button>
        </div>

        <div class="modules-grid" id="modulesGrid">
            <div class="module-card">
                <div class="module-left">
                    <i class="fas fa-user-graduate module-icon"></i>
                    <div class="module-title">Alumnos</div>
                </div>
                <a href="alumnos.php" class="module-btn">Acceder</a>
            </div>
           
            <div class="module-card">
                <div class="module-left">
                    <i class="fas fa-file-lines module-icon"></i>
                    <div class="module-title">Constancias</div>
                </div>
                <a href="constancias.php" class="module-btn">Acceder</a>
            </div>

            <div class="module-card">
                <div class="module-left">
                    <i class="fas fa-users-cog module-icon"></i>
                    <div class="module-title">Autoridades</div>
                </div>
                <a href="autoridades.php" class="module-btn">Acceder</a>
            </div>

            <div class="module-card">
                <div class="module-left">
                    <i class="fas fa-history module-icon"></i>
                    <div class="module-title">Historial</div>
                </div>
                <a href="historial.php" class="module-btn">Acceder</a>
            </div>

            <div class="module-card">
                <div class="module-left">
                    <i class="fas fa-file-invoice module-icon"></i>
                    <div class="module-title">Formatos</div>
                </div>
                <a href="formatos.php" class="module-btn">Acceder</a>
            </div>

            <div class="module-card">
                <div class="module-left">
                    <i class="fas fa-chart-bar module-icon"></i>
                    <div class="module-title">Reportes</div>
                </div>
                <a href="reportes_modulo.php" class="module-btn">Acceder</a>
            </div>
        </div>

        <div class="quick-actions">
            <h3>Acciones Rápidas</h3>
            <button onclick="sendEmail()" style="background-color: #007bff; color: white; margin-right: 8px;">
                <i class="fas fa-envelope"></i> Enviar Constancia por Correo
            </button>
            <button onclick="printConstancia()" style="background-color: #6c757d; color: white;">
                <i class="fas fa-print"></i> Imprimir Constancia
            </button>
        </div>
    </main>

    <script>
        function toggleSubmenu(e) {
            e.preventDefault();
            const submenu = document.getElementById('alumnosSubmenu');
            submenu.classList.toggle('show');
        }

        function searchModules() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const cards = document.querySelectorAll('.module-card');
            cards.forEach(card => {
                const title = card.querySelector('.module-title').innerText.toLowerCase();
                card.style.display = title.includes(input) ? "flex" : "none";
            });
        }

        function showAllModules(e) {
            e.preventDefault();
            document.getElementById('searchInput').value = '';
            const cards = document.querySelectorAll('.module-card');
            cards.forEach(card => {
                card.style.display = "flex";
            });
            document.querySelectorAll('.sidebar-link').forEach(link => link.classList.remove('active'));
            e.currentTarget.classList.add('active');
        }

        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if(e.key === 'Enter') searchModules();
        });

        function sendEmail() {
            alert("Simulando: Enviando constancia por correo electrónico...");
        }

        function printConstancia() {
            window.print();
        }
    </script>
</body>
</html>