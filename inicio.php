<?php
session_start();

require_once "config/conexion.php";

// Verificar si el usuario inició sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}

// Obtener los datos del usuario
$id_usuario = $_SESSION['id_usuario'];
$nombre = $_SESSION['nombre'];
$correo = $_SESSION['correo'];

// Capturar parámetros de búsqueda y ordenamiento (GET)
$buscar = trim($_GET['buscar'] ?? '');
$criterio = $_GET['criterio'] ?? 'todos';
$ordenar_por = $_GET['ordenar_por'] ?? 'fecha';
$direccion = strtoupper($_GET['direccion'] ?? 'DESC');

// Validar criterio de búsqueda permitido
$criterios_validos = ['todos', 'nombre', 'correo', 'ciudad'];
if (!in_array($criterio, $criterios_validos, true)) {
    $criterio = 'todos';
}

// Lista blanca de columnas permitidas para ordenar (evita inyección SQL)
// Nota: si tu tabla 'contactos' tiene una columna llamada 'fecha_creacion', puedes usarla aquí;
// de lo contrario 'id_contacto' refleja el orden cronológico de creación del registro.
$columnas_orden = [
    'nombre' => 'nombre',
    'ciudad' => 'ciudad',
    'fecha'  => 'id_contacto'
];

if (!array_key_exists($ordenar_por, $columnas_orden)) {
    $ordenar_por = 'fecha';
}

if (!in_array($direccion, ['ASC', 'DESC'], true)) {
    $direccion = 'DESC';
}

$columna_sql = $columnas_orden[$ordenar_por];
$order_by_sql = " ORDER BY {$columna_sql} {$direccion}, id_contacto DESC";

// Construir consulta segura según el criterio, el término de búsqueda y el orden
if ($buscar !== '') {
    $termino = '%' . $buscar . '%';

    if ($criterio === 'nombre') {
        $sql = "SELECT * FROM contactos WHERE id_usuario = ? AND nombre LIKE ?" . $order_by_sql;
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("is", $id_usuario, $termino);
    } elseif ($criterio === 'correo') {
        $sql = "SELECT * FROM contactos WHERE id_usuario = ? AND correo LIKE ?" . $order_by_sql;
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("is", $id_usuario, $termino);
    } elseif ($criterio === 'ciudad') {
        $sql = "SELECT * FROM contactos WHERE id_usuario = ? AND ciudad LIKE ?" . $order_by_sql;
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("is", $id_usuario, $termino);
    } else {
        // Buscar en Nombre, Correo o Ciudad
        $sql = "SELECT * FROM contactos
                WHERE id_usuario = ?
                  AND (nombre LIKE ? OR correo LIKE ? OR ciudad LIKE ?)"
                . $order_by_sql;
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("isss", $id_usuario, $termino, $termino, $termino);
    }
} else {
    $sql = "SELECT * FROM contactos WHERE id_usuario = ?" . $order_by_sql;
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
}

$stmt->execute();
$resultado = $stmt->get_result();

// Determinar si el panel debe mostrarse abierto al cargar
$panel_abierto = ($buscar !== '' || isset($_GET['ordenar_por']) || isset($_GET['direccion']));
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Agenda Personal</title>

    <!-- Conectar archivo CSS -->
    <link rel="stylesheet" href="css/estilos.css">

    <style>
        .busqueda-contenedor {
            margin: 18px 0;
            padding: 14px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
        }

        .form-busqueda {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        .form-busqueda select,
        .form-busqueda input[type="text"] {
            padding: 9px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-size: 14px;
            background-color: #ffffff;
        }

        .form-busqueda input[type="text"] {
            flex: 1;
            min-width: 200px;
        }

        .btn-buscar {
            padding: 9px 16px;
            border: none;
            border-radius: 7px;
            background-color: #12376a;
            color: #ffffff;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
        }

        .btn-buscar:hover {
            background-color: #0b2448;
        }

        .btn-limpiar-busqueda {
            padding: 9px 14px;
            border-radius: 7px;
            background-color: #e2e8f0;
            color: #1e293b;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
        }

        .btn-limpiar-busqueda:hover {
            background-color: #cbd5e1;
        }
    </style>
</head>

<body>

<div class="contenedor">

    <header class="encabezado">
        <h1>Agenda Personal</h1>
        <p>Sistema de gestión de contactos</p>
    </header>

    <main class="contenido">

        <section class="datos">
            <h2>Bienvenido, <?php echo htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?></h2>

            <p>
                <strong>Correo electrónico:</strong>
                <?php echo htmlspecialchars($correo, ENT_QUOTES, 'UTF-8'); ?>
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

                <button type="button" class="btn btn-agregar" id="btn-toggle-busqueda" onclick="alternarBuscador()">
                    🔍 Buscar y ordenar
                </button>

             </div>

             <div class="busqueda-contenedor"
                  id="panel-busqueda"
                  style="<?php echo $panel_abierto ? 'display: block;' : 'display: none;'; ?>">

                 <form method="GET" action="inicio.php" class="form-busqueda">

                     <select name="criterio" aria-label="Buscar por">
                         <option value="todos" <?php echo ($criterio === 'todos') ? 'selected' : ''; ?>>Todos (Nombre, Correo, Ciudad)</option>
                         <option value="nombre" <?php echo ($criterio === 'nombre') ? 'selected' : ''; ?>>1. Nombre</option>
                         <option value="correo" <?php echo ($criterio === 'correo') ? 'selected' : ''; ?>>2. Correo</option>
                         <option value="ciudad" <?php echo ($criterio === 'ciudad') ? 'selected' : ''; ?>>3. Ciudad</option>
                     </select>

                     <input
                         type="text"
                         name="buscar"
                         id="campo-buscar"
                         value="<?php echo htmlspecialchars($buscar, ENT_QUOTES, 'UTF-8'); ?>"
                         placeholder="Escribe nombre, correo o ciudad..."
                     >

                     <select name="ordenar_por" aria-label="Ordenar por">
                         <option value="fecha" <?php echo ($ordenar_por === 'fecha') ? 'selected' : ''; ?>>Ordenar: Fecha de creación</option>
                         <option value="nombre" <?php echo ($ordenar_por === 'nombre') ? 'selected' : ''; ?>>Ordenar: 1. Nombre</option>
                         <option value="ciudad" <?php echo ($ordenar_por === 'ciudad') ? 'selected' : ''; ?>>Ordenar: 2. Ciudad</option>
                     </select>

                     <select name="direccion" aria-label="Dirección del orden">
                         <option value="DESC" <?php echo ($direccion === 'DESC') ? 'selected' : ''; ?>>Descendente (Z-A / Recientes)</option>
                         <option value="ASC" <?php echo ($direccion === 'ASC') ? 'selected' : ''; ?>>Ascendente (A-Z / Antiguos)</option>
                     </select>

                     <button type="submit" class="btn-buscar">Aplicar</button>

                     <?php if ($buscar !== '' || $ordenar_por !== 'fecha' || $direccion !== 'DESC'): ?>
                         <a href="inicio.php" class="btn-limpiar-busqueda">Limpiar</a>
                     <?php endif; ?>

                 </form>

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
                                    <?php if ($buscar !== ''): ?>
                                        No se encontraron contactos que coincidan con tu búsqueda.
                                    <?php else: ?>
                                        Todavía no tienes contactos registrados.
                                    <?php endif; ?>
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

<script>
    function alternarBuscador() {
        const panel = document.getElementById("panel-busqueda");
        const input = document.getElementById("campo-buscar");

        if (!panel) return;

        const estaOculto = panel.style.display === "none" || getComputedStyle(panel).display === "none";
        panel.style.display = estaOculto ? "block" : "none";

        if (estaOculto && input) {
            input.focus();
        }
    }
</script>

</body>
</html>

<?php
$stmt->close();
$conexion->close();
?>