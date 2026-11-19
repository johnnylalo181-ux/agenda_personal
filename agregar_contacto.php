

<?php
session_start();

require_once "config/conexion.php";

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

$mensaje = "";
$tipo_mensaje = "";

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');

    // Validar campos obligatorios
    if (
        $nombre === "" ||
        $telefono === "" ||
        $correo === "" ||
        $ciudad === ""
    ) {
        $mensaje = "Todos los campos son obligatorios.";
        $tipo_mensaje = "error";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $mensaje = "Ingresa un correo electrónico válido.";
        $tipo_mensaje = "error";

    } elseif (
        mb_strlen($nombre) > 100 ||
        mb_strlen($telefono) > 20 ||
        mb_strlen($correo) > 100 ||
        mb_strlen($ciudad) > 100
    ) {

        $mensaje = "Uno o más campos superan la longitud permitida.";
        $tipo_mensaje = "error";

    } else {

        // Registrar contacto asociado al usuario
        $sql = "INSERT INTO contactos
                (nombre, telefono, correo, ciudad, id_usuario)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conexion->prepare($sql);

        if ($stmt) {

            $stmt->bind_param(
                "ssssi",
                $nombre,
                $telefono,
                $correo,
                $ciudad,
                $id_usuario
            );

            if ($stmt->execute()) {

                // Evitar duplicar el registro al actualizar la página
                header("Location: agregar_contacto.php?registro=exitoso");
                exit();

            } else {

                $mensaje = "No se pudo registrar el contacto.";
                $tipo_mensaje = "error";
            }

            $stmt->close();

        } else {

            $mensaje = "Error al preparar la consulta.";
            $tipo_mensaje = "error";
        }
    }
}

// Mensaje después del registro
if (isset($_GET['registro']) && $_GET['registro'] === 'exitoso') {
    $mensaje = "¡Contacto registrado correctamente!";
    $tipo_mensaje = "exito";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Agregar contacto | Agenda Personal</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 25px;
            color: #334155;
        }

        .contenedor {
            width: 100%;
            max-width: 700px;
        }

        .encabezado {
            background: #172b4d;
            color: white;
            padding: 25px 30px;
            border-radius: 12px 12px 0 0;
        }

        .encabezado h1 {
            font-size: 25px;
            margin-bottom: 8px;
        }

        .encabezado p {
            color: #cbd5e1;
            font-size: 14px;
        }

        .formulario {
            background: white;
            padding: 35px;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.07);
        }

        .titulo-formulario {
            margin-bottom: 25px;
        }

        .titulo-formulario h2 {
            color: #172b4d;
            font-size: 22px;
            margin-bottom: 8px;
        }

        .titulo-formulario p {
            color: #64748b;
            font-size: 14px;
            line-height: 1.5;
        }

        .grupo {
            margin-bottom: 20px;
        }

        .fila {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: bold;
            color: #334155;
        }

        input {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            outline: none;
            font-size: 14px;
            transition: 0.2s;
        }

        input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        }

        .seccion {
            font-size: 15px;
            color: #2563eb;
            font-weight: bold;
            margin-bottom: 22px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        .botones {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 30px;
        }

        .btn {
            display: inline-block;
            padding: 12px 20px;
            border-radius: 7px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            border: none;
        }

        .btn-cancelar {
            background: #e2e8f0;
            color: #334155;
        }

        .btn-guardar {
            background: #2563eb;
            color: white;
        }

        .btn-guardar:hover {
            background: #1d4ed8;
        }

        .btn-cancelar:hover {
            background: #cbd5e1;
        }

        .mensaje {
            padding: 13px;
            border-radius: 7px;
            margin-bottom: 22px;
            font-size: 14px;
        }

        .exito {
            background: #dcfce7;
            color: #166534;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
        }

        .pie {
            text-align: center;
            margin-top: 22px;
            color: #94a3b8;
            font-size: 12px;
        }

        @media (max-width: 550px) {

            .fila {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .formulario {
                padding: 22px;
            }

            .encabezado {
                padding: 22px;
            }

            .botones {
                flex-direction: column;
            }

            .btn {
                text-align: center;
            }
        }

    </style>
</head>

<body>

<div class="contenedor">

    <header class="encabezado">

        <h1>Agenda Personal</h1>

        <p>Sistema de gestión de contactos</p>

    </header>

    <main class="formulario">

        <div class="titulo-formulario">

            <h2>Agregar nuevo contacto</h2>

            <p>
                Completa los siguientes campos para
                guardar un nuevo contacto en tu agenda personal.
            </p>

        </div>

        <?php if ($mensaje !== ""): ?>

            <div class="mensaje <?php echo $tipo_mensaje; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>

        <?php endif; ?>

        <div class="seccion">
            Información del contacto
        </div>

        <form method="POST" action="">

            <div class="grupo">

                <label for="nombre">Nombre completo</label>

                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    maxlength="100"
                    placeholder="Ingresa el nombre completo"
                    value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>"
                    required
                >

            </div>

            <div class="fila">

                <div class="grupo">

                    <label for="telefono">Teléfono</label>

                    <input
                        type="tel"
                        id="telefono"
                        name="telefono"
                        maxlength="20"
                        placeholder="Ej. 0991234567"
                        value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>"
                        required
                    >

                </div>

                <div class="grupo">

                    <label for="ciudad">Ciudad</label>

                    <input
                        type="text"
                        id="ciudad"
                        name="ciudad"
                        maxlength="100"
                        placeholder="Ingresa la ciudad"
                        value="<?php echo htmlspecialchars($_POST['ciudad'] ?? ''); ?>"
                        required
                    >

                </div>

            </div>

            <div class="grupo">

                <label for="correo">Correo electrónico</label>

                <input
                    type="email"
                    id="correo"
                    name="correo"
                    maxlength="100"
                    placeholder="ejemplo@correo.com"
                    value="<?php echo htmlspecialchars($_POST['correo'] ?? ''); ?>"
                    required
                >

            </div>

            <div class="botones">

                <a href="inicio.php" class="btn btn-cancelar">
                    Cancelar
                </a>

                <button type="submit" class="btn btn-guardar">
                    Guardar contacto
                </button>

            </div>

        </form>

        <div class="pie">
            Agenda Personal | Sistema de gestión de contactos
        </div>

    </main>

</div>

</body>
</html>
