

<?php
session_start();

require_once "config/conexion.php";

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}

// Obtener el identificador del usuario autenticado
$id_usuario = $_SESSION['id_usuario'];

// Consultar únicamente los contactos del usuario actual
$sql = "SELECT id_contacto, nombre, telefono, correo, ciudad
        FROM contactos
        WHERE id_usuario = ?
        ORDER BY id_contacto DESC";

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
    <title>Mis contactos - Agenda Personal</title>

    <link rel="stylesheet" href="css/estilos.css">

    <style>
        /* Enlaces superiores */
        .enlaces-superiores a {
            color: #ffffff;
            font-weight: bold;
            text-decoration: none;
            font-size: 16px;
            margin-right: 20px;
            display: inline-block;
            padding: 10px 16px;
            border-radius: 8px;
            background-color: #12376a;
            transition: 0.3s;
        }

        .enlaces-superiores a:hover {
            background-color: #0b2448;
            color: #ffffff;
        }

        /* Enlaces de acciones en negrita y color visible sobre fondo blanco */
        .acciones a {
            font-weight: bold !important;
            text-decoration: none;
            color: #000000 !important;
            background-color: transparent;
        }

        .acciones a:hover {
            text-decoration: underline;
            color: #12376a !important;
        }
    </style>
</head>

<body>

    <div class="contenedor">

        <h1>Mis contactos</h1>

        <div class="enlaces-superiores">
            <a href="inicio.php">Volver al inicio</a>

            <a href="agregar_contacto.php">Agregar contacto</a>
        </div>

        <br>

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

                                    <a href="editar_contacto.php?id_contacto=<?php echo (int)$contacto['id_contacto']; ?>">
                                        Editar
                                    </a>

                                    |

                                    <a href="eliminar_contacto.php?id_contacto=<?php echo (int)$contacto['id_contacto']; ?>"
                                       onclick="return confirm('¿Estás seguro de eliminar este contacto?');">
                                        Eliminar
                                    </a>

                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="5">
                                No tienes contactos registrados.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>
            </table>

        </div>

    </div>

</body>
</html>

<?php
$stmt->close();
$conexion->close();
?>