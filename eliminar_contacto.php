

<?php

session_start();

require_once "config/conexion.php";

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// Obtener el identificador del usuario autenticado
$id_usuario = $_SESSION['id_usuario'];

// Obtener el identificador del contacto
$id_contacto = (int)($_GET['id_contacto'] ?? 0);

// Verificar que el identificador sea válido
if ($id_contacto <= 0) {
    header("Location: inicio.php");
    exit();
}

// Eliminar únicamente el contacto del usuario autenticado
$sql = "DELETE FROM contactos
        WHERE id_contacto = ? AND id_usuario = ?";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error al preparar la consulta: " . $conexion->error);
}

// Asociar los parámetros
$stmt->bind_param("ii", $id_contacto, $id_usuario);

// Ejecutar la eliminación
if ($stmt->execute()) {

    // Comprobar si se eliminó el contacto
    if ($stmt->affected_rows > 0) {

        $stmt->close();
        $conexion->close();

        // Regresar automáticamente a la página principal
        header("Location: inicio.php?eliminacion=exitosa");
        exit();

    } else {

        $stmt->close();
        $conexion->close();

        header("Location: inicio.php?eliminacion=no_encontrada");
        exit();
    }

} else {

    $stmt->close();
    $conexion->close();

    die("No se pudo eliminar el contacto.");
}

?>
