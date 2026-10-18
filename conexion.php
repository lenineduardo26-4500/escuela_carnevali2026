<?php
// Obtener la variable de entorno DATABASE_URL que configuramos en Render
$database_url = getenv("DATABASE_URL");

if ($database_url) {
    // Si estamos en la nube (Render), parseamos la URL de Neon
    $db_parsed = parse_url($database_url);
    
    $servidor = $db_parsed["host"];
    $usuario_db = $db_parsed["user"];
    $clave_db = $db_parsed["pass"];
    $nombre_bd = ltrim($db_parsed["path"], "/");
    $puerto = isset($db_parsed["port"]) ? $db_parsed["port"] : 5432; // O 3390 si aplica, pero Neon por defecto usa PostgreSQL o MySQL según la URL
} else {
    // Configuración local por defecto (para cuando pruebes en XAMPP en tu PC)
    $servidor = "localhost";
    $usuario_db = "root";
    $clave_db = "";
    $nombre_bd = "escuela_carnevali";
}

// Nota: Si tu base de datos en Neon es PostgreSQL, cambiamos la conexión a PDO o pgsql. 
// Como tu código usa mysqli_connect (exclusivo para MySQL), asegurémonos de usar los datos correctos:
$conexion = mysqli_connect($servidor, $usuario_db, $clave_db, $nombre_bd);

if (!$conexion) {
    // Mensaje amigable para identificar si falta el controlador o los datos
    die("Error de conexión a la nube: no se pudo encontrar el controlador o falló el acceso. Detalles: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8");
?>
