AGENDA PERSONAL

1. Descripción del proyecto

Agenda Personal es una aplicación web desarrollada para facilitar la administración y organización de contactos personales.

El sistema permite a los usuarios registrarse, iniciar sesión y gestionar sus contactos de manera individual, garantizando que cada usuario pueda administrar únicamente su propia información.

Entre sus principales funcionalidades se encuentran:

- Registro de nuevos usuarios.
- Inicio y cierre de sesión.
- Agregar nuevos contactos.
- Consultar contactos registrados.
- Editar información de los contactos.
- Eliminar contactos.
- Almacenamiento de información en una base de datos MySQL.

2. Tecnologías utilizadas

Para el desarrollo de la aplicación se utilizaron las siguientes tecnologías:

- **PHP:** Lenguaje de programación utilizado para desarrollar la lógica del sistema y gestionar las operaciones del servidor.
- **MySQL:** Sistema gestor de bases de datos utilizado para almacenar la información de usuarios y contactos.
- **HTML5:** Lenguaje utilizado para estructurar las páginas web.
- **CSS3:** Utilizado para diseñar y dar formato a la interfaz gráfica.
- **XAMPP:** Entorno de servidor local utilizado para ejecutar Apache, PHP y MySQL.
- **Git y GitHub:** Herramientas utilizadas para el control de versiones y almacenamiento del código fuente.

3. Requisitos para su ejecución

Para instalar y ejecutar correctamente el sistema se requiere:

- Sistema operativo Windows.
- XAMPP instalado.
- Servidor Apache habilitado.
- Motor de base de datos MySQL en ejecución.
- Navegador web (Google Chrome, Microsoft Edge o Firefox).
- Git (opcional, para clonar el repositorio).

4. Pasos para instalar la aplicación

Paso 1. Instalar XAMPP

Descargar e instalar XAMPP en el equipo.

Iniciar los servicios Apache y MySQL desde el panel de control de XAMPP.

Paso 2. Descargar el proyecto

Clonar el repositorio de GitHub utilizando el siguiente comando:

```bash
git clone https://github.com/johnnylalo181-ux/agenda_personal.git
```

También se puede descargar el proyecto en formato ZIP desde GitHub.

Paso 3. Ubicar el proyecto

Copiar la carpeta del proyecto dentro del directorio:

```text
C:\xampp\htdocs\
```

La ubicación final debe ser:

```text
C:\xampp\htdocs\agenda_personal
```

Paso 4. Crear la base de datos

1. Abrir el navegador web.
2. Ingresar a phpMyAdmin:

```text
http://localhost/phpmyadmin
```

3. Crear una base de datos con el nombre:

```text
agenda_personal
```

4. Crear las tablas necesarias para el funcionamiento del sistema: usuarios y contactos, incluyendo su relación mediante el identificador del usuario.
Paso 5. Configurar la conexión a la base de datos

Crear el archivo `conexion.php` dentro de la carpeta `config`.

Configurar los datos de conexión de acuerdo con las credenciales de MySQL del equipo donde se instalará la aplicación.

**Nota:** El archivo de conexión contiene información local de acceso a la base de datos y no se incluye en el repositorio público.

Paso 6. Ejecutar la aplicación

Abrir el navegador e ingresar a la siguiente dirección:

```text
http://localhost/agenda_personal/login.php
```

Desde esta dirección se podrá acceder al sistema, registrar un usuario e iniciar sesión para gestionar los contactos personales.

5. Autor

Proyecto desarrollado como práctica de desarrollo de aplicaciones web, utilizando PHP y MySQL.