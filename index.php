<?php
session_start();

$mensaje = "";
$tipo_mensaje = "";

$ruta_conexion = __DIR__ . "/config/conexion.php";

if (!file_exists($ruta_conexion)) {
    $mensaje = "No se encontró el archivo de conexión (config/conexion.php).";
    $tipo_mensaje = "error";
} else {
    require_once $ruta_conexion;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && empty($mensaje)) {

    $correo = trim($_POST["correo"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($correo === "" || $password === "") {
        $mensaje = "Ingrese su correo y contraseña.";
        $tipo_mensaje = "error";
    } elseif (!isset($conexion) || !($conexion instanceof mysqli)) {
        $mensaje = "Error de conexión con la base de datos.";
        $tipo_mensaje = "error";
    } else {
        $sql = "SELECT id_usuario, nombre, correo, password
                FROM usuarios
                WHERE correo = ?
                LIMIT 1";

        $consulta = $conexion->prepare($sql);

        if ($consulta) {
            $consulta->bind_param("s", $correo);
            $consulta->execute();
            $resultado = $consulta->get_result();

            if ($resultado && $resultado->num_rows === 1) {
                $usuario = $resultado->fetch_assoc();

                if (password_verify($password, $usuario["password"])) {
                    session_regenerate_id(true);

                    $_SESSION["id_usuario"] = $usuario["id_usuario"];
                    $_SESSION["nombre"] = $usuario["nombre"];
                    $_SESSION["correo"] = $usuario["correo"];

                    $consulta->close();
                    header("Location: inicio.php");
                    exit();
                }
            }

            $mensaje = "Correo electrónico o contraseña incorrectos.";
            $tipo_mensaje = "error";
            $consulta->close();
        } else {
            $mensaje = "No se pudo procesar la consulta de inicio de sesión.";
            $tipo_mensaje = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de sesión</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #071b3d, #0d47a1, #1976d2, #42a5f5);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .bienvenida {
            text-align: center;
            margin-bottom: 25px;
        }

        .bienvenida h1 {
            color: #ffffff;
            font-size: 32px;
            margin-bottom: 8px;
        }

        .bienvenida p {
            color: #dbeafe;
            font-size: 18px;
            margin-top: 0;
        }

        .contenedor {
            width: 350px;
            max-width: calc(100% - 50px);
            margin: 0 auto;
            padding: 25px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.30);
        }

        h2 {
            text-align: center;
            color: #333333;
            margin-top: 0;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            color: #222222;
        }

        input {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #cccccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 11px;
            margin-top: 20px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 15px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .mensaje {
            text-align: center;
            padding: 10px;
            margin-top: 15px;
            border-radius: 5px;
            font-size: 14px;
        }

        .error {
            color: #842029;
            background-color: #f8d7da;
            border: 1px solid #f5c2c7;
        }

        .registro {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .registro a {
            color: #007bff;
            text-decoration: none;
        }

        .registro a:hover {
            text-decoration: underline;
        }

        @media (max-width: 450px) {
            .bienvenida h1 {
                font-size: 26px;
            }

            .bienvenida p {
                font-size: 16px;
            }

            .contenedor {
                width: auto;
            }
        }
    </style>
</head>

<body>

    <div class="bienvenida">
        <h1>Bienvenido</h1>
        <p>Sistema de gestión de contactos</p>
    </div>

    <div class="contenedor">
        <h2>Inicio de sesión</h2>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" autocomplete="off">
            <label for="correo">Correo electrónico:</label>
            <input
                type="email"
                id="correo"
                name="correo"
                autocomplete="off"
                required
            >

            <label for="password">Contraseña:</label>
            <input
                type="password"
                id="password"
                name="password"
                autocomplete="new-password"
                required
            >

            <button type="submit">Iniciar sesión</button>
        </form>

        <?php if (!empty($mensaje)) { ?>
            <div class="mensaje <?php echo htmlspecialchars($tipo_mensaje); ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php } ?>

        <div class="registro">
            ¿No tienes una cuenta?
            <a href="registro.php">Regístrate aquí</a>
        </div>
    </div>

</body>

</html>