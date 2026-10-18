<?php
// Forzar a PHP a mostrar errores en pantalla para diagnóstico
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$database_url = getenv("DATABASE_URL");

if ($database_url) {
    // Verificar si el driver de PostgreSQL (pgsql) está disponible
    $drivers = PDO::getAvailableDrivers();
    if (!in_array('pgsql', $drivers)) {
        die("Error crítico: El driver PDO 'pgsql' NO está instalado en este contenedor PHP. Drivers disponibles: " . implode(', ', $drivers));
    }

    $db_parsed = parse_url($database_url);
    
    $host = $db_parsed["host"];
    $user = $db_parsed["user"];
    $pass = isset($db_parsed["pass"]) ? $db_parsed["pass"] : "";
    $dbname = ltrim($db_parsed["path"], "/");
    $port = isset($db_parsed["port"]) ? $db_parsed["port"] : 5432;

    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
        $conexion = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $e) {
        die("Error al conectar con la base de datos de Neon: " . $e->getMessage());
    }
} else {
    // Configuración local (XAMPP / MySQL)
    $servidor = "localhost";
    $usuario_db = "root";
    $clave_db = "";
    $nombre_bd = "escuela_carnevali";

    $conexion = mysqli_connect($servidor, $usuario_db, $clave_db, $nombre_bd);
    if (!$conexion) {
        die("Error al conectar localmente: " . mysqli_connect_error());
    }
    mysqli_set_charset($conexion, "utf8");
}
?>
