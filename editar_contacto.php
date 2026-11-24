
<?php
session_start();

require_once "config/conexion.php";

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

$mensaje = "";
$tipo_mensaje = "";

// Obtener el identificador del contacto
$id_contacto = 0;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_contacto = (int)($_POST['id_contacto'] ?? 0);
} else {
    $id_contacto = (int)($_GET['id_contacto'] ?? 0);
}

// Verificar que se haya recibido un identificador válido
if ($id_contacto <= 0) {
    header("Location: contactos.php");
    exit();
}

// Consultar si el contacto pertenece al usuario autenticado
$sql = "SELECT id_contacto, nombre, telefono, correo, ciudad
        FROM contactos
        WHERE id_contacto = ? AND id_usuario = ?";

$consulta = $conexion->prepare($sql);

if (!$consulta) {
    die("Error al preparar la consulta: " . $conexion->error);
}

$consulta->bind_param("ii", $id_contacto, $id_usuario);
$consulta->execute();

$resultado = $consulta->get_result();
$contacto = $resultado->fetch_assoc();

$consulta->close();

// Verificar que el contacto exista y pertenezca al usuario
if (!$contacto) {
    $conexion->close();
    header("Location: contactos.php");
    exit();
}

// Procesar la actualización del contacto
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');

    if ($nombre === '' || $telefono === '') {

        $mensaje = "El nombre y el teléfono son obligatorios.";
        $tipo_mensaje = "error";

    } elseif ($correo !== '' && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $mensaje = "Ingresa un correo electrónico válido.";
        $tipo_mensaje = "error";

    } else {

        // Actualizar únicamente el contacto del usuario autenticado
        $sql = "UPDATE contactos
                SET nombre = ?, telefono = ?, correo = ?, ciudad = ?
                WHERE id_contacto = ? AND id_usuario = ?";

        $actualizar = $conexion->prepare($sql);

        if (!$actualizar) {
            die("Error al preparar la actualización: " . $conexion->error);
        }

        $actualizar->bind_param(
            "ssssii",
            $nombre,
            $telefono,
            $correo,
            $ciudad,
            $id_contacto,
            $id_usuario
        );

        if ($actualizar->execute()) {

            $actualizar->close();
            $conexion->close();

            header("Location: contactos.php");
            exit();

        } else {

            $mensaje = "No se pudieron actualizar los datos del contacto.";
            $tipo_mensaje = "error";

            $actualizar->close();
        }
    }

    // Conservar los datos ingresados en el formulario
    $contacto['nombre'] = $nombre;
    $contacto['telefono'] = $telefono;
    $contacto['correo'] = $correo;
    $contacto['ciudad'] = $ciudad;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar contacto - Agenda Personal</title>

    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/editar.css">

</head>

<body>

    <div class="contenedor">

        <h1>Editar contacto</h1>

        <p>Modifica la información del contacto seleccionado.</p>

        <?php if ($mensaje !== ''): ?>

            <p class="<?php echo htmlspecialchars($tipo_mensaje, ENT_QUOTES, 'UTF-8'); ?>">
                <?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?>
            </p>

        <?php endif; ?>

        <form action="editar_contacto.php?id_contacto=<?php echo $id_contacto; ?>"
              method="POST">

            <input type="hidden"
                   name="id_contacto"
                   value="<?php echo $id_contacto; ?>">

            <label for="nombre">Nombre:</label>

            <input type="text"
                   id="nombre"
                   name="nombre"
                   value="<?php echo htmlspecialchars($contacto['nombre'], ENT_QUOTES, 'UTF-8'); ?>"
                   required>

            <label for="telefono">Teléfono:</label>

            <input type="text"
                   id="telefono"
                   name="telefono"
                   value="<?php echo htmlspecialchars($contacto['telefono'], ENT_QUOTES, 'UTF-8'); ?>"
                   required>

            <label for="correo">Correo electrónico:</label>

            <input type="email"
                   id="correo"
                   name="correo"
                   value="<?php echo htmlspecialchars($contacto['correo'], ENT_QUOTES, 'UTF-8'); ?>">

            <label for="ciudad">Ciudad:</label>

            <input type="text"
                   id="ciudad"
                   name="ciudad"
                   value="<?php echo htmlspecialchars($contacto['ciudad'], ENT_QUOTES, 'UTF-8'); ?>">

            <button type="submit">Guardar cambios</button>

            <a href="contactos.php">Cancelar</a>

        </form>

    </div>

</body>

</html>

<?php
$conexion->close();
?>