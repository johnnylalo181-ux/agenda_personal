
<?php
session_start();

require_once "config/conexion.php";

$mensaje = "";
$tipo_mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = trim($_POST["nombre"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $password = $_POST["password"] ?? "";

    // Validar campos vacíos
    if (empty($nombre) || empty($correo) || empty($password)) {

        $mensaje = "Todos los campos son obligatorios.";
        $tipo_mensaje = "error";

    // Validar correo electrónico
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $mensaje = "Ingrese un correo electrónico válido.";
        $tipo_mensaje = "error";

    // Validar contraseña sin mostrar mensaje
    } elseif (
        strlen($password) < 8 ||
        !preg_match('/[a-zA-Z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        !preg_match('/[^a-zA-Z0-9]/', $password)
    ) {

        // No mostrar mensaje de contraseña incorrecta.
        $mensaje = "";
        $tipo_mensaje = "";

    } else {

        // Comprobar si el correo ya existe
        $sql = "SELECT id_usuario FROM usuarios WHERE correo = ?";
        $consulta = $conexion->prepare($sql);
        $consulta->bind_param("s", $correo);
        $consulta->execute();

        $resultado = $consulta->get_result();

        if ($resultado->num_rows > 0) {

            $mensaje = "Este correo electrónico ya está registrado.";
            $tipo_mensaje = "error";

        } else {

            // Proteger contraseña
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insertar usuario
            $sql = "INSERT INTO usuarios (nombre, correo, password)
                    VALUES (?, ?, ?)";

            $consulta_insertar = $conexion->prepare($sql);

            $consulta_insertar->bind_param(
                "sss",
                $nombre,
                $correo,
                $password_hash
            );

            if ($consulta_insertar->execute()) {

                // Guardar mensaje de éxito
                $_SESSION["mensaje_registro"] =
                    "¡Usuario registrado correctamente!";

                // Evitar que el navegador recupere datos anteriores
                header("Cache-Control: no-store, no-cache, must-revalidate");
                header("Pragma: no-cache");

                // Recargar página y limpiar formulario
                header("Location: registro.php");
                exit();

            } else {

                $mensaje = "No se pudo completar el registro.";
                $tipo_mensaje = "error";
            }

            $consulta_insertar->close();
        }

        $consulta->close();
    }
}

// Recuperar mensaje de éxito
if (isset($_SESSION["mensaje_registro"])) {

    $mensaje = $_SESSION["mensaje_registro"];
    $tipo_mensaje = "exito";

    unset($_SESSION["mensaje_registro"]);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Registro de usuarios</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .contenedor {
            width: 350px;
            margin: 60px auto;
            padding: 25px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .campo-password {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .ayuda-password {
            font-size: 11px;
            font-weight: bold;
            color: #555;
        }

        button {
            width: 100%;
            padding: 11px;
            margin-top: 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .mensaje {
            text-align: center;
            padding: 10px;
            margin-top: 15px;
            border-radius: 5px;
        }

        .exito {
            color: green;
            background-color: #d4edda;
        }

        .error {
            color: red;
            background-color: #f8d7da;
        }

    </style>
</head>

<body>

    <div class="contenedor">

        <h2>Registro de usuarios</h2>

        <form method="POST" action="registro.php" autocomplete="off">

            <label for="nombre">Nombre completo:</label>

            <input
                type="text"
                id="nombre"
                name="nombre"
                autocomplete="off"
                required
            >

            <label for="correo">Correo electrónico:</label>

            <input
                type="email"
                id="correo"
                name="correo"
                autocomplete="off"
                required
            >

            <label for="password">Contraseña:</label>

            <div class="campo-password">

                <input
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="new-password"
                    required
                >

                <span class="ayuda-password">
                    Mínimo 8 caracteres, incluyendo letras, números y caracteres especiales.
                </span>

            </div>

            <button type="submit">Registrarse</button>

        </form>

        <?php if (!empty($mensaje)) { ?>

            <div class="mensaje <?php echo $tipo_mensaje; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>

        <?php } ?>

    </div>

</body>
</html>
