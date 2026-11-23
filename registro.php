
<?php
session_start();

require_once "config/conexion.php";

$pagina_principal = "login.php";

$mensaje = "";
$tipo_mensaje = "";
$registro_exitoso = false;

// Errores individuales por campo
$errores = [
    "nombre" => "",
    "correo" => "",
    "password" => ""
];

// Mantener valores ingresados para que el usuario no tenga que reescribir todo
$nombre = "";
$correo = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = trim($_POST["nombre"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $password = $_POST["password"] ?? "";

    // 1. Validar Nombre completo (al menos nombre y apellido: mínimo 2 palabras)
    $partes_nombre = preg_split('/\s+/', $nombre, -1, PREG_SPLIT_NO_EMPTY);
    if (empty($nombre)) {
        $errores["nombre"] = "Corrija el campo Nombre completo: ingrese su nombre y apellido.";
    } elseif (count($partes_nombre) < 2 || mb_strlen($nombre) < 3) {
        $errores["nombre"] = "Corrija el campo Nombre completo: debe ingresar nombre completo (nombre y apellido).";
    }

    // 2. Validar Correo electrónico (vacío, formato, dominios temporales y DNS)
    if (empty($correo)) {
        $errores["correo"] = "Corrija el campo Correo electrónico: ingrese su correo.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores["correo"] = "Corrija el campo Correo electrónico: no es un correo válido, por favor ponga un correo válido.";
    } else {
        $dominio = strtolower(substr(strrchr($correo, "@"), 1));

        $dominios_bloqueados = [
            '10minutemail.com',
            'tempmail.com',
            'guerrillamail.com',
            'mailinator.com',
            'yopmail.com',
            'trashmail.com'
        ];

        $tiene_dns = $dominio !== "" && (
            checkdnsrr($dominio, 'MX') ||
            checkdnsrr($dominio, 'A') ||
            checkdnsrr($dominio, 'AAAA')
        );

        if (in_array($dominio, $dominios_bloqueados, true) || !$tiene_dns) {
            $errores["correo"] = "Corrija el campo Correo electrónico: no es un correo válido, por favor ponga un correo válido.";
        }
    }

    // 3. Validar Contraseña (mínimo 8 caracteres, letras, números y caracteres especiales)
    if (empty($password)) {
        $errores["password"] = "Corrija el campo Contraseña: ingrese una contraseña.";
    } elseif (
        strlen($password) < 8 ||
        !preg_match('/[a-zA-Z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        !preg_match('/[^a-zA-Z0-9]/', $password)
    ) {
        $errores["password"] = "Corrija el campo Contraseña: debe tener mínimo 8 caracteres e incluir letras, números y caracteres especiales.";
    }

    // Si no hay errores en los 3 campos, revisar duplicado en BD e insertar
    if (empty($errores["nombre"]) && empty($errores["correo"]) && empty($errores["password"])) {

        // Comprobar si el correo ya existe
        $sql = "SELECT id_usuario FROM usuarios WHERE correo = ?";
        $consulta = $conexion->prepare($sql);
        $consulta->bind_param("s", $correo);
        $consulta->execute();

        $resultado = $consulta->get_result();

        if ($resultado->num_rows > 0) {

            $errores["correo"] = "Corrija el campo Correo electrónico: este correo ya está registrado.";
            $mensaje = "Revise el campo indicado para corregir los datos.";
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

                // Evitar que el navegador recupere datos anteriores
                header("Cache-Control: no-store, no-cache, must-revalidate");
                header("Pragma: no-cache");

                // Mostrar mensaje de éxito y limpiar campos
                $mensaje = "¡Usuario registrado correctamente!";
                $tipo_mensaje = "exito";
                $registro_exitoso = true;
                $nombre = "";
                $correo = "";

            } else {

                $mensaje = "No se pudo completar el registro.";
                $tipo_mensaje = "error";
            }

            $consulta_insertar->close();
        }

        $consulta->close();

    } else {
        $mensaje = "Hay errores en el formulario. Revise los campos marcados para corregir.";
        $tipo_mensaje = "error";
    }
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
            width: 380px;
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

        input.input-error {
            border-color: #dc3545;
            background-color: #fff5f5;
        }

        .error-campo {
            display: block;
            margin-top: 5px;
            font-size: 12px;
            color: #b02a37;
            font-weight: bold;
        }

        .campo-password {
            display: flex;
            flex-direction: column;
            gap: 6px;
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

        .boton-regresar {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 12px;
            box-sizing: border-box;
            text-align: center;
            text-decoration: none;
            background-color: #6c757d;
            color: white;
            border-radius: 5px;
            font-size: 14px;
        }

        .boton-regresar:hover {
            background-color: #545b62;
        }

        .mensaje {
            text-align: center;
            padding: 10px;
            margin-top: 15px;
            border-radius: 5px;
        }

        .exito {
            color: #155724;
            background-color: #d4edda;
            font-weight: bold;
        }

        .error {
            color: #721c24;
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
                value="<?php echo htmlspecialchars($nombre); ?>"
                class="<?php echo !empty($errores['nombre']) ? 'input-error' : ''; ?>"
                autocomplete="off"
                required
            >

            <?php if (!empty($errores["nombre"])) { ?>
                <span class="error-campo"><?php echo htmlspecialchars($errores["nombre"]); ?></span>
            <?php } ?>

            <label for="correo">Correo electrónico:</label>

            <input
                type="email"
                id="correo"
                name="correo"
                value="<?php echo htmlspecialchars($correo); ?>"
                class="<?php echo !empty($errores['correo']) ? 'input-error' : ''; ?>"
                autocomplete="off"
                required
            >

            <?php if (!empty($errores["correo"])) { ?>
                <span class="error-campo"><?php echo htmlspecialchars($errores["correo"]); ?></span>
            <?php } ?>

            <label for="password">Contraseña:</label>

            <div class="campo-password">

                <input
                    type="password"
                    id="password"
                    name="password"
                    class="<?php echo !empty($errores['password']) ? 'input-error' : ''; ?>"
                    autocomplete="new-password"
                    required
                >

                <span class="ayuda-password">
                    Mínimo 8 caracteres, incluyendo letras, números y caracteres especiales.
                </span>

                <?php if (!empty($errores["password"])) { ?>
                    <span class="error-campo"><?php echo htmlspecialchars($errores["password"]); ?></span>
                <?php } ?>

            </div>

            <button type="submit">Registrarse</button>

            <a href="<?php echo htmlspecialchars($pagina_principal); ?>" class="boton-regresar">Regresar</a>

        </form>

        <?php if (!empty($mensaje)) { ?>

            <div class="mensaje <?php echo htmlspecialchars($tipo_mensaje); ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>

        <?php } ?>

    </div>

</body>
</html>