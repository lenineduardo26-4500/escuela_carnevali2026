<?php
// Activar reporte de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir conexión
include 'conexion.php';

// Asegurar utf8mb4 en la conexión activa
if (isset($conexion) && $conexion instanceof mysqli) {
    mysqli_set_charset($conexion, "utf8mb4");
}

// Manejo de compatibilidad con la variable de conexión
if (!isset($pdo)) {
    if (isset($conexion)) {
        $pdo = $conexion;
    } elseif (isset($conn)) {
        $pdo = $conn;
    }
}

// Función para calcular la edad basada en la fecha de nacimiento
function calcularEdad($fecha_nacimiento) {
    if (empty($fecha_nacimiento)) return "___";
    try {
        $nacimiento = new DateTime($fecha_nacimiento);
        $hoy = new DateTime();
        $edad = $hoy->diff($nacimiento);
        return $edad->y;
    } catch (Exception $e) {
        return "___";
    }
}

// Función infalible para extraer cualquier campo de la BD ignorando mayúsculas o variaciones
function obtenerDato($fila, $opciones) {
    if (!$fila || !is_array($fila)) return '';
    foreach ((array)$opciones as $opcion) {
        foreach ($fila as $columna => $valor) {
            if (strcasecmp($columna, $opcion) === 0 && !empty(trim($valor))) {
                return trim($valor);
            }
        }
    }
    return '';
}

$alumno = null;
$error = "";

// Lógica de Búsqueda
if (isset($_POST['buscar'])) {
    $tipo_busqueda = $_POST['tipo_busqueda'];
    $valor = trim($_POST['valor']);

    if (!empty($valor)) {
        try {
            if ($pdo instanceof PDO) {
                if ($tipo_busqueda == 'cedula') {
                    $sql = "SELECT * FROM alumnos WHERE Cedula_alumno = :valor OR Cedula_alumno LIKE :valor_like";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        'valor' => $valor,
                        'valor_like' => "%$valor%"
                    ]);
                } else {
                    $sql = "SELECT * FROM alumnos WHERE CONCAT(Nombres, ' ', Apellidos) LIKE :valor OR CONCAT(Apellidos, ' ', Nombres) LIKE :valor";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(['valor' => "%$valor%"]);
                }
                $alumno = $stmt->fetch(PDO::FETCH_ASSOC);

            } elseif ($pdo instanceof mysqli) {
                // Forzar codificación utf8mb4 antes de consultar para soportar acentos y eñes
                mysqli_query($pdo, "SET NAMES 'utf8mb4'");

                if ($tipo_busqueda == 'cedula') {
                    $stmt = $pdo->prepare("SELECT * FROM alumnos WHERE Cedula_alumno = ? OR Cedula_alumno LIKE ?");
                    if ($stmt) {
                        $like_val = "%$valor%";
                        $stmt->bind_param("ss", $valor, $like_val);
                        $stmt->execute();
                        $resultado = $stmt->get_result();
                        $alumno = $resultado ? $resultado->fetch_assoc() : null;
                    }
                } else {
                    $stmt = $pdo->prepare("SELECT * FROM alumnos WHERE CONCAT(Nombres, ' ', Apellidos) LIKE ? OR CONCAT(Apellidos, ' ', Nombres) LIKE ?");
                    if ($stmt) {
                        $busqueda = "%$valor%";
                        $stmt->bind_param("ss", $busqueda, $busqueda);
                        $stmt->execute();
                        $resultado = $stmt->get_result();
                        $alumno = $resultado ? $resultado->fetch_assoc() : null;
                    }
                }
            }
        } catch (Exception $e) {
            $error = "Error en la consulta de la base de datos: " . $e->getMessage();
        }

        if (!$alumno && empty($error)) {
            $error = "No se encontró ningún alumno registrado con el valor ingresado.";
        }
    } else {
        $error = "Por favor ingrese un número de cédula o un nombre para buscar.";
    }
}

// Función segura para extraer la sección
function obtenerSeccion($alumno) {
    return obtenerDato($alumno, ['seccion', 'sección', 'Seccion', 'Sección']);
}
?>

<!-- REGLAS DE IMPRESIÓN CSS -->
<style>
@media print {
    .no-print, header, nav, footer, button, form {
        display: none !important;
    }
    body {
        background: #fff !important;
        margin: 0;
        padding: 0;
    }
    .printable-area {
        border: none !important;
        box-shadow: none !important;
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
    }
}
</style>

<!-- VISTA DEL MÓDULO CONSTANCIA -->
<div class="container-fluid" style="padding: 20px; font-family: Arial, sans-serif;">
    
    <!-- Botón Volver corregido para ir directo al index.php -->
    <div class="no-print" style="margin-bottom: 15px;">
        <a href="index.php" style="text-decoration: none;">
            <button style="padding: 8px 15px; background: #6c757d; color: #fff; border: none; border-radius: 4px; cursor: pointer;">
                ⬅ Volver al Menú
            </button>
        </a>
    </div>

    <h2 class="no-print">Módulo de Constancias de Estudio</h2>
    <hr class="no-print">

    <!-- Formulario de Búsqueda -->
    <form method="POST" action="" class="no-print" style="background: #e9ecef; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <label><strong>Buscar por:</strong></label>
            <select name="tipo_busqueda" style="padding: 6px; border-radius: 4px; border: 1px solid #ccc;">
                <option value="cedula">Cédula Escolar / Identidad</option>
                <option value="nombre">Nombre o Apellido</option>
            </select>
            <input type="text" name="valor" placeholder="Ingrese número o nombre..." style="padding: 6px; width: 250px; border-radius: 4px; border: 1px solid #ccc;" required>
            <button type="submit" name="buscar" style="padding: 7px 15px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Buscar Alumno</button>
        </div>
    </form>

    <?php if (!empty($error)): ?>
        <div class="no-print" style="color: red; margin-bottom: 15px; font-weight: bold;"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- BARRA DE ACCIONES -->
    <?php if ($alumno): ?>
        <div class="no-print" style="max-width: 800px; margin: 0 auto 15px auto; display: flex; gap: 10px; justify-content: flex-end;">
            <button onclick="window.print()" style="padding: 8px 16px; background: #28a745; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                🖨️ Imprimir / Guardar PDF
            </button>

            <?php
                $correo_rep = obtenerDato($alumno, ['correo_electronico_representante', 'correo_representante', 'email_representante']);
                $nom_rep_email = obtenerDato($alumno, ['Nombre_representante', 'nombre_representante', 'representante', 'nombres_representante']);
                $nombres_alum = obtenerDato($alumno, ['Nombres', 'nombres']) . ' ' . obtenerDato($alumno, ['Apellidos', 'apellidos']);
                $asunto = rawurlencode("Constancia de Estudio - Escuela Gladys de Carnevali");
                $cuerpo = rawurlencode("Estimado(a) " . $nom_rep_email . ",\n\nSe adjunta la notificación de la Constancia de Estudio de su representado(a) " . $nombres_alum . ".\n\nSaludos cordiales,\nEscuela Gladys de Carnevali.");
            ?>
            <a href="mailto:<?php echo $correo_rep; ?>?subject=<?php echo $asunto; ?>&body=<?php echo $cuerpo; ?>"
               style="padding: 8px 16px; background: #17a2b8; color: #fff; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-weight: bold; display: inline-block;">
               ✉️ Enviar por Correo
            </a>
        </div>
    <?php endif; ?>

    <!-- PLANILLA IMPRESA -->
    <div class="printable-area" style="border: 1px solid #ccc; padding: 40px 40px 0 40px; background: #fff; box-shadow: 0px 0px 5px rgba(0,0,0,0.1); max-width: 800px; margin: auto;">
        
        <!-- ENCABEZADO -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <td style="width: 25%; text-align: left; vertical-align: middle;">
                    <img src="./imagenes/logo_izquierda.png" alt="Logo Izquierdo" style="max-height: 85px; width: 520px; object-fit: contain;">
                </td>
                <td style="width: 50%; text-align: center; font-size: 10px; font-weight: bold; line-height: 1.3; vertical-align: middle;">
                    Denominación Nacional<br>
                    Escuela Gladys de Carnevali.<br>
                    Municipio Libertador. Parroquia Milla.<br>
                    OD02721412
                </td>
                <td style="width: 25%; text-align: right; vertical-align: middle;">
                    <img src="./imagenes/logo_derecha.png" alt="Logo Derecho" style="max-height: 80px; max-width: 100%;">
                </td>
            </tr>
        </table>

        <div style="text-align: center; font-weight: bold; font-size: 16px; margin-bottom: 30px;">
            CONSTANCIA DE ESTUDIO
        </div>

        <div style="line-height: 1.8; text-align: justify; font-size: 14px;">
            Quien suscribe la Profesora <strong>Elsy Alarcón</strong>, titular de la Cédula de Identidad N° <strong>V.10.100.589</strong>,
            Directora (E) de la Escuela Gladys C. Lobo de Carnevali, Código <strong>006731530</strong>, ubicada en la Avenida 1 Hoyada de Milla del Municipio Libertador del Estado Mérida, por medio de la presente,
            <br><br>
            <div style="text-align: center; font-weight: bold;">HACE CONSTAR</div>
            <br>
            Que el niño(a): <strong><?php 
                $nombre_completo = obtenerDato($alumno, ['Nombres', 'nombres']) . ' ' . obtenerDato($alumno, ['Apellidos', 'apellidos']);
                echo $alumno && trim($nombre_completo) !== '' ? htmlspecialchars($nombre_completo) : '________________________________________'; 
            ?></strong>
            de <strong><?php echo $alumno ? calcularEdad(obtenerDato($alumno, ['Fecha_nacimiento', 'fecha_nacimiento'])) : '___'; ?></strong> años de edad,
            Cédula Escolar o Cédula de Identidad: N° V <strong><?php echo $alumno ? htmlspecialchars(obtenerDato($alumno, ['Cedula_alumno', 'cedula_alumno', 'cedula'])) : '____________________'; ?></strong>
            Natural de <strong><?php echo $alumno ? htmlspecialchars(obtenerDato($alumno, ['Lugar_Nacimiento', 'lugar_nacimiento', 'lugar'])) : '____________'; ?></strong>,
            estado <strong><?php echo $alumno ? htmlspecialchars(obtenerDato($alumno, ['Estado', 'estado'])) : '________'; ?></strong>,
            fue inscrito (a) para cursar el <strong><?php echo $alumno ? htmlspecialchars(obtenerDato($alumno, ['Grado', 'grado'])) : '___'; ?></strong> grado,
            Sección: <strong><?php echo htmlspecialchars(obtenerSeccion($alumno)); ?></strong>
            de Educación Primaria año Escolar <strong><?php echo $alumno ? htmlspecialchars(obtenerDato($alumno, ['Periodo_escolar', 'periodo_escolar', 'periodo'])) : '_________'; ?></strong>
            Cumpliendo un Horario de 8:00 a 1:00pm.
            <br><br>
            Constancia que se expide a solicitud de su representante Legal:
            <strong><?php 
                $nom_rep = obtenerDato($alumno, ['Nombre_representante', 'nombre_representante', 'representante', 'nombres_representante']);
                echo $alumno && !empty($nom_rep) ? htmlspecialchars($nom_rep) : '________________________________________'; 
            ?></strong>,
            titular de la Cédula de Identidad N° V-<strong><?php 
                $ced_rep = obtenerDato($alumno, ['Cedula_Identidad', 'cedula_identidad', 'Cedula_representante', 'cedula_representante', 'cedula_rep']);
                echo $alumno && !empty($ced_rep) ? htmlspecialchars($ced_rep) : '____________'; 
            ?></strong>.
            <br><br>
            En la ciudad de Mérida a los <strong><?php echo date('d'); ?></strong> días del Mes de
            <strong><?php
                $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
                echo $meses[date('n') - 1];
            ?></strong> del año <strong><?php echo date('Y'); ?></strong>.
        </div>

        <div style="text-align: center; margin-top: 40px; margin-bottom: 20px;">
            _____________________________________<br>
            <strong>Prof. Elsy Alarcón</strong><br>
            Directora (E)
        </div>

        <!-- DIRECCIÓN EN LA ESQUINA IZQUIERDA SOBRE LA FRANJA AMARILLA -->
        <div style="margin-left: -40px; margin-right: -40px; position: relative; line-height: 0;">
            <div style="position: absolute; top: 100px; left: 40px; width: auto; font-size: 12px; font-family: Arial, sans-serif; color: #000; font-weight: bold; line-height: normal; background: rgba(255, 255, 255, 0.85); padding: 3px 6px; border-radius: 3px;">
                Dirección: Avenida 1 Hoyada de Milla. Tlf. 04149788263 - 02742443999
            </div>
            <img src="./imagenes/bandera.png" alt="Franja Tricolor" style="width: 100%; height: 200px; object-fit: cover; display: block;">
        </div>

    </div>
</div>