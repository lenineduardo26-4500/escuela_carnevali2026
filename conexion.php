<?php
$database_url = getenv('DATABASE_URL');

if ($database_url) {
    // Conexión para Neon (PostgreSQL) cuando esté en Vercel
    $db = parse_url($database_url);
    $dsn = "pgsql:host=" . $db['host'] . ";port=" . ($db['port'] ?? 5432) . ";dbname=" . ltrim($db['path'], '/');
    try {
        $conexion = new PDO($dsn, $db['user'], $db['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    } catch (PDOException $e) {
        die("Error de conexión a la nube: " . $e->getMessage());
    }
} else {
    // Tu conexión local original de XAMPP (MySQL) para cuando trabajes en tu PC
    $servidor = "localhost";
    $usuario_db = "root";
    $clave_db = "";
    $nombre_bd = "escuela_carnevali";

    $conexion = mysqli_connect($servidor, $usuario_db, $clave_db, $nombre_bd); 

    if (!$conexion) { 
        die("Error al conectar a la base de datos: " . mysqli_connect_error());
    }

    mysqli_set_charset($conexion, "utf8mb4");
}
?>