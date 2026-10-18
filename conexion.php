<?php
$database_url = getenv("DATABASE_URL");

if ($database_url) {
    // Si estamos en Render, parseamos la URL de Neon (PostgreSQL)
    $db_parsed = parse_url($database_url);
    
    $host = $db_parsed["host"];
    $user = $db_parsed["user"];
    $pass = isset($db_parsed["pass"]) ? $db_parsed["pass"] : "";
    $dbname = ltrim($db_parsed["path"], "/");
    $port = isset($db_parsed["port"]) ? $db_parsed["port"] : 5432;

    // Conexión mediante PDO para PostgreSQL (Neon)
    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
        $conexion = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $e) {
        die("Error de conexión a la nube (Neon): " . $e->getMessage());
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
