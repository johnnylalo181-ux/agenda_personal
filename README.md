Aprendizaje práctico - experimental (APE)
Actividad: Despliegue y Mejoras de una Aplicación Web en un Hosting Gratuito
Opción A. Búsqueda de contactos
En este caso, se ha implementado un tercer botón denominado Buscar y ordenar en la parte de Agenda Personal; Administración de contactos, con el objetivo de facilitar al usuario realizar la consulta de los contactos de su agenda personal; cuando el usuario inicia sesión con un correo electrónico valido y una contraseña creada anteriormente en la pagina principal o de inicio, el usuario puede ingresar y ver sus contactos guardados.
Además, este botón facilita al usuario en la búsqueda de un contacto en particular, este botón le ayuda a buscar un contacto a través de Nombre o Apellido, correo electrónico o ciudad. 
Búsqueda por nombre: el usuario puede buscar un contacto ya sea utilizando un nombre o apellido, otra alternativa es por nombres y apellidos.
Búsqueda por correo electrónico:  esta es una alternativa que le ayudara a encontrar un contacto en específico, y arrojara los datos correspondientes a ese correo. 
Búsqueda por ciudad: es una búsqueda que arrojara varios resultados, por ejemplo, si hay contactos guardados con la ciudad de Loja, aparecerán varios contactos con ese dato. Si quiero encontrar uno especifico, se recomienda buscar por nombres y apellidos o correo electrónico.   
Opción B. Ordenamiento
Ordenar los contactos es muy importante para el usuario, el botón denominado Buscar y ordenar que se encuentra en la parte de Agenda Personal; Administración de contactos es un botón de opción múltiple. Permite al usuario el poder ordenar por la categoría nombre y ciudad. 
Este botón no permite ordenar por Fecha de creación ya que el sistema es automático, si el usuario registra un contacto, ese contacto se guarda en orden Cronológico inverso. Es decir, el último queda arriba y el primero queda abajo de todos los contactos.   
Parte 2. Despliegue
Publicar la aplicación en un hosting gratuito.
En mi caso, utilicé el hosting gratuito InfinityFree para alojar mi sistema web denominado Sistema de gestión de contactos. Este proyecto lo desarrollé localmente utilizando el servidor XAMPP, el lenguaje PHP, HTML, CSS, una base de datos MySQL gestionada con phpMyAdmin y el entorno de desarrollo Visual Studio Code, luego del desarrollo local, procedí a llevar mi sistema al hosting de InfinityFree. 
Para que mi sistema corra correctamente en un hosting tuve que ejecutar los siguientes pasos.
1.	Revisar las carpetas y archivos(.php) de mi proyecto agenda_personal en Visual Studio Code, con el fin de mantener una estructura limpia y ordena. 
2.	Crear el archivo index.php dentro de mi proyecto agenda_personal en Visual Studio Code, para poder cargar al hosting InfinityFree, ya que anteriormente tenía creada login.php. El hosting InfinityFree solo permite controlar o comunicarse con un archivo central que tenga la extensión index.php. Para index.php solo tuve que migrar mi código desde login.php. 
3.	Crear una extensión de código para el archivo config/conexión.php, para que el sistema web puede ejecutarse sin problema al cargar en el hosting, se tuvo que implementar la sentencia comunicación con el servidor local y entorno de producción InfinityFree. Si no se implementa esta acción, el hosting no presenta mi sistema en el navegador a través del dominio y me genera un error de pantalla blanca.  
4.	Crear la base de datos dentro en InfinityFree usando el entorno MySQL Databases, dentro de ella procedí a crear un usuario y luego ingresé con ese usuario a phpMyAdmin en la cual procedí a crear las tablas usuarios y contactos, utilizando el script generado en phpMyAdmin del servidor XAMPP.
5.	Correr el Sistema de gestión de contactos en el hosting InfinityFree con el dominio https://agendapersonal.infinityfreeapp.com/ y la aplicación queda cien por ciento funcional. 
 
Parte 3. Repositorio
El repositorio esta actualizado en GitHub. 
Entregables
1.	URL del sistema funcionando.
https://agendapersonal.infinityfreeapp.com/
2.	Repositorio GitHub.

3.	Script SQL actualizado.
-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: sql301.infinityfree.com
-- Tiempo de generación: 23-11-2026 a las 19:47:13
-- Versión del servidor: 11.4.14-MariaDB
-- Versión de PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `if0_43508428_agendapersonal`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contactos`
--

CREATE TABLE `contactos` (
  `id_contacto` int(10) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `ciudad` varchar(100) NOT NULL,
  `id_usuario` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `contactos`
--

INSERT INTO `contactos` (`id_contacto`, `nombre`, `telefono`, `correo`, `ciudad`, `id_usuario`) VALUES
(12, 'Mateo Villavicencio', '098 765 1924', 'mateo.villavicencio@ejemplo.com', 'Guayaquil', 11),
(13, 'Valentina Paredes', '96 308 5417', 'valentina.paredes@ejemplo.com', 'Cuenca', 11),
(14, 'Andrés Salazar', '099 824 6730', 'andres.salazar@ejemplo.com', 'Ambato', 11),
(15, 'Camila Espinoza', '097 519 2048', 'camila.espinoza@ejemplo.com', 'Manta', 11),
(16, 'Camila Espinoza', '097 519 2048', 'camila.espinoza@ejemplo.com', 'Manta', 11),
(17, 'Sebastián Cordero', '098 147 9652', 'sebastian.cordero@ejemplo.com', 'Loja', 11),
(18, 'Jostin Paucar', '0995005821', 'fahemif243@dvdee.com', 'Nabón', 11);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(10) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `correo`, `password`, `fecha_registro`) VALUES
(7, 'Juan Alberto Perez Perez', 'figaka7154@kranove.com', '$2y$10$24iVf32nrX1PCd4V5G80buCpIxDla0JPggHkLamXJCkf.vjwyvmVm', '2026-11-22 23:51:54'),
(8, 'Adrian Palta', 'xijos10589@forims.com', '$2y$10$DXAwgS7DwNzQNCTX/MkPPeezSBuvhk/EoXdqWFr7rRmEmdL2tWqfC', '2026-11-22 23:56:23'),
(9, 'Juan Coronel', 'hetat97225@kranove.com', '$2y$10$ZDlIWNOAcqlJQuS0w9J8teSNPPMHYwACqJsRa/bdB3Ypnz2SDnnI6', '2026-11-23 00:10:11'),
(10, 'Mayra Morocho', 'freddysonso@hotmail.com', '$2y$10$lu88mpcHLDpoe5rAms4PveBfFqDmT/JS6gBBh66tDoMMcgAQlcX/a', '2026-11-23 00:27:08'),
(11, 'Johnny Paucar', 'johnnylalo181@gmail.com', '$2y$10$gPbXeM3PJTvE3j2YYiUIFuWNOUy.Naufk00152mgCvNqeTl78ZNPG', '2026-11-23 00:35:30');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `contactos`
--
ALTER TABLE `contactos`
  ADD PRIMARY KEY (`id_contacto`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `contactos`
--
ALTER TABLE `contactos`
  MODIFY `id_contacto` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `contactos`
--
ALTER TABLE `contactos`
  ADD CONSTRAINT `fk_contactos_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
