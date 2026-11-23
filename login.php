


<?php
session_start();

require_once "config/conexion.php";

$mensaje = "";
$tipo_mensaje = "";

// Comprobar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $correo = trim($_POST["correo"] ?? "");
    $password = $_POST["password"] ?? "";

    // Validar campos vacíos
    if (empty($correo) || empty($password)) {

        $mensaje = "Ingrese su correo y contraseña.";
        $tipo_mensaje = "error";

    } else {

        // Buscar usuario en la base de datos
        $sql = "SELECT id_usuario, nombre, correo, password
                FROM usuarios
                WHERE correo = ?";

        $consulta = $conexion->prepare($sql);
        $consulta->bind_param("s", $correo);
        $consulta->execute();

        $resultado = $consulta->get_result();

        if ($resultado->num_rows == 1) {

            $usuario = $resultado->fetch_assoc();

            // Verificar contraseña
            if (password_verify($password, $usuario["password"])) {

                // Crear sesión del usuario
                session_regenerate_id(true);

                $_SESSION["id_usuario"] = $usuario["id_usuario"];
                $_SESSION["nombre"] = $usuario["nombre"];
                $_SESSION["correo"] = $usuario["correo"];

                // Redireccionar al panel principal
                header("Location: inicio.php");
                exit();

            } else {

                $mensaje = "Correo electrónico o contraseña incorrectos.";
                $tipo_mensaje = "error";
            }

        } else {

            $mensaje = "Correo electrónico o contraseña incorrectos.";
            $tipo_mensaje = "error";
        }

        $consulta->close();
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

        /* Texto de bienvenida */
        .bienvenida {
            text-align: center;
            margin-bottom: 25px;
        }

        .bienvenida h1 {
            color: white;
            font-size: 32px;
            margin-bottom: 8px;
        }

        .bienvenida p {
            color: #dbeafe;
            font-size: 18px;
            margin-top: 0;
        }

        /* Cuadro de inicio de sesión */
        .contenedor {
            width: 350px;
            max-width: calc(100% - 50px);
            margin: 0 auto;
            padding: 25px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.30);
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

        .error {
            color: red;
            background-color: #f8d7da;
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

        /* Adaptación para celulares */
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

    <!-- Mensaje de bienvenida fuera del formulario -->
    <div class="bienvenida">
        <h1>Bienvenido</h1>
        <p>Sistema de gestión de contactos</p>
    </div>

    <div class="contenedor">

        <h2>Inicio de sesión</h2>

        <form method="POST" action="login.php" autocomplete="off">

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

            <div class="mensaje <?php echo $tipo_mensaje; ?>">
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
