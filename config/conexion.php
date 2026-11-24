
<?php

$es_local = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1'], true);

if ($es_local) {
    // Entorno local (XAMPP)
    $servidor   = "localhost";
    $usuario    = "root";
    $password   = "";
    $basedatos  = "agenda_personal";
} else {
    // Entorno de producción (InfinityFree)
    $servidor   = "sql301.infinityfree.com";
    $usuario    = "if0_43508428";
    $password   = "bJW0f7BnuFe";
    $basedatos  = "if0_43508428_agendapersonal";
}

$conexion = new mysqli($servidor, $usuario, $password, $basedatos);

if ($conexion->connect_error) {
    die("Error de conexión con la base de datos.");
}

$conexion->set_charset("utf8mb4");