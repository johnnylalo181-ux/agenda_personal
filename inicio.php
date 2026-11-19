

<?php
session_start();

require_once "config/conexion.php";

// Verificar si el usuario inició sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// Obtener los datos del usuario
$id_usuario = $_SESSION['id_usuario'];
$nombre = $_SESSION['nombre'];
$correo = $_SESSION['correo'];

// Consultar los contactos del usuario
$sql = "SELECT * FROM contactos WHERE id_usuario = ? ORDER BY id_contacto DESC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();

$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Agenda Personal</title>

    <!-- Conectar archivo CSS -->
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>

<div class="contenedor">

    <header class="encabezado">
        <h1>Agenda Personal</h1>
        <p>Sistema de gestión de contactos</p>
    </header>

    <main class="contenido">

        <section class="datos">
            <h2>Bienvenido, <?php echo htmlspecialchars($nombre); ?></h2>

            <p>
                <strong>Correo electrónico:</strong>
                <?php echo htmlspecialchars($correo); ?>
            </p>
        </section>

        <section class="administracion">

            <div class="titulo-seccion">
                <h2>Administración de contactos</h2>
                <p>Gestiona tu información personal desde un solo lugar.</p>
            </div>

            <div class="acciones-principales">

                <a href="agregar_contacto.php" class="btn btn-agregar">
                     + Registrar nuevo contacto
                </a>

                <a href="contactos.php" class="btn btn-agregar">
                      Consultar contactos
                </a>

             </div>
        </section>

        <section class="mis-contactos">

            <h2>Mis contactos</h2>

            <div class="tabla-contenedor">

                <table>

                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Correo electrónico</th>
                            <th>Ciudad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if ($resultado->num_rows > 0): ?>

                            <?php while ($contacto = $resultado->fetch_assoc()): ?>

                                <tr>

                                    <td>
                                        <?php echo htmlspecialchars($contacto['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($contacto['telefono'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($contacto['correo'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($contacto['ciudad'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td class="acciones">

                                        <a href="editar_contacto.php?id_contacto=<?php echo (int)$contacto['id_contacto']; ?>"
                                           class="btn-editar">
                                            Editar
                                        </a>

                                        <a href="eliminar_contacto.php?id_contacto=<?php echo (int)$contacto['id_contacto']; ?>"
                                           class="btn-eliminar"
                                           onclick="return confirm('¿Estás seguro de eliminar este contacto?');">
                                            Eliminar
                                        </a>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="5" class="sin-contactos">
                                    Todavía no tienes contactos registrados.
                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>

        <footer class="pie">

            <a href="cerrar_sesion.php" class="btn-salir">
                Cerrar sesión
            </a>

            <p>Agenda Personal | Sistema de gestión de contactos</p>

        </footer>

    </main>

</div>

</body>
</html>

<?php
$stmt->close();
$conexion->close();
?>
