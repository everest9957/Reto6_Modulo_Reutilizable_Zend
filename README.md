# 🗡️ Reto 6 — Módulo Reutilizable de Comentarios en Zend/Laminas

![Estado](https://img.shields.io/badge/estado-funcionando-success)
![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php)
![Laminas](https://img.shields.io/badge/Laminas-MVC-blue)
![Licencia](https://img.shields.io/badge/licencia-MIT-green)

Módulo de comentarios **reutilizable** desarrollado como parte del **Reto 6** del curso *Código Samurái — Desarrollo Web con Frameworks 6*.

Originalmente planteado para **Zend Framework**, se ha implementado y validado también en **Laminas MVC**, el sucesor oficial mantenido por la comunidad, dado que Zend Framework está descontinuado y no es compatible con PHP 8.x.

---

## 📋 Tabla de contenidos

- [Descripción](#-descripción)
- [Funcionalidades](#-funcionalidades)
- [Requisitos](#-requisitos)
- [Estructura del proyecto](#-estructura-del-proyecto)
- [Instalación](#-instalación)
- [Uso](#-uso)
- [Base de datos](#-base-de-datos)
- [Problemas encontrados y soluciones](#-problemas-encontrados-y-soluciones)
- [Capturas del proceso](#-capturas-del-proceso)
- [Lecciones aprendidas](#-lecciones-aprendidas)
- [Autor](#-autor)

---

## 📖 Descripción

Este proyecto implementa un **módulo de comentarios** para aplicaciones Zend Framework 3 / Laminas MVC, diseñado con el objetivo de ser **totalmente reutilizable**: puede integrarse en cualquier proyecto Zend/Laminas existente copiando la carpeta del módulo y registrándolo en la configuración de la aplicación.

El módulo cubre el ciclo completo de un sistema de comentarios: **creación, visualización y administración**, incluyendo moderación (aprobar, editar, eliminar).

### Compatibilidad

| Framework | Versión | Estado |
|-----------|---------|--------|
| Zend Framework | 3.x (PHP 7.x) | ✅ Compatible a nivel de código |
| Laminas MVC | 3.8+ (PHP 8.1–8.4) | ✅ Probado y funcionando |

---

## ✨ Funcionalidades

### Públicas
- 📝 **Crear comentarios** asociados a un post del blog.
- 👁️ **Visualizar comentarios aprobados** ordenados cronológicamente.

### Administración
- 🔧 **Panel de administración** con listado completo de comentarios.
- ✅ **Aprobar** comentarios pendientes.
- ✏️ **Editar** autor, email, contenido y estado.
- 🗑️ **Eliminar** comentarios.

### Arquitectura
- 📦 **Estructura modular PSR-4**.
- 🔌 **Registro por configuración** mínima (una línea en `modules.config.php`).
- 🎨 **Vistas independientes** con estilos inline (no dependen del layout).
- 🛡️ **Validación de entrada** mediante `InputFilter`.

---

## ⚙️ Requisitos

- **PHP** 8.1+ (probado con 8.4.25)
- **Composer** 2.6+
- **MySQL** 5.7+ / MariaDB 10.4+ (XAMPP sirve perfectamente)
- **Laminas MVC Skeleton** 2.x
- Extensiones PHP: `pdo_mysql`, `mbstring`, `json`

---

## 📁 Estructura del proyecto
```
Reto6_Modulo_Reutilizable_Zend/
│
├── capturas/ # Evidencias del proceso
│ ├── 01_estructura_carpetas_inicial.png
│ ├── 02_composer_json_modulo.png
│ ├── ... (48 capturas en total)
│ └── 17_schema_sql.png
│
├── module/
│ └── Comentarios/ # 🎯 Módulo reutilizable
│ ├── config/
│ │ └── module.config.php
│ ├── data/
│ │ └── schema.sql
│ ├── src/
│ │ ├── Controller/
│ │ │ ├── IndexController.php
│ │ │ ├── IndexControllerFactory.php
│ │ │ ├── AdminController.php
│ │ │ └── AdminControllerFactory.php
│ │ ├── Entity/
│ │ │ └── Comentario.php
│ │ ├── Form/
│ │ │ └── ComentarioForm.php
│ │ ├── Model/
│ │ │ ├── ComentarioTable.php
│ │ │ └── ComentarioTableFactory.php
│ │ └── Module.php
│ ├── view/
│ │ └── comentarios/
│ │ ├── admin/
│ │ │ ├── editar.phtml
│ │ │ └── index.phtml
│ │ └── index/
│ │ ├── crear.phtml
│ │ └── index.phtml
│ ├── composer.json
│ └── README.md
│
├── PROBLEMAS_Y_SOLUCIONES.md
└── README.md

```text

---

## 🚀 Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/TU_USUARIO/Reto6_Modulo_Reutilizable_Zend.git
cd Reto6_Modulo_Reutilizable_Zend
2. Crear un proyecto Laminas MVC
bash
composer create-project -s dev laminas/laminas-mvc-skeleton laminas-skeleton-test --ignore-platform-reqs
cd laminas-skeleton-test
⚠️ El flag --ignore-platform-reqs es necesario porque el esqueleto declara soporte para PHP 8.1–8.3 y nuestro entorno usa PHP 8.4.

📸 Ver capturas/15_1_laminas_instalado.png

3. Copiar el módulo al esqueleto
powershell
Copy-Item -Path "..\module\Comentarios" -Destination "module\Comentarios" -Recurse
📸 Ver capturas/15_3_modulo_copiado_laminas.png

4. Adaptar namespaces Zend → Laminas
powershell
Get-ChildItem -Path .\module\Comentarios -Recurse -Filter *.php | ForEach-Object {
    (Get-Content $_.FullName -Raw) -replace 'Zend\\', 'Laminas\' |
        Set-Content $_.FullName -NoNewline
}
📸 Ver capturas/15_4_reemplazo_zend_laminas.png

5. Instalar dependencias adicionales
bash
composer require --ignore-platform-reqs \
    laminas/laminas-form \
    laminas/laminas-db \
    laminas/laminas-mvc-form \
    laminas/laminas-mvc-i18n \
    laminas/laminas-navigation
Cuando el asistente pregunte "¿En qué archivo inyectar el módulo?", elegir la opción 1 (config/modules.config.php).

📸 Ver capturas/15_10c_composer_require_form_db.png
📸 Ver capturas/15_10f_composer_require_navigation.png

6. Registrar el módulo
Editar config/modules.config.php:

php
<?php
return [
    'Laminas\Mvc\I18n',
    'Laminas\I18n',
    'Laminas\InputFilter',
    'Laminas\Filter',
    'Laminas\Hydrator',
    'Laminas\Form',
    'Laminas\Db',
    'Laminas\Router',
    'Laminas\Validator',
    'Application',
    'Comentarios',
];
📸 Ver capturas/15_6_modules_config_laminas.png y capturas/15_10d_modules_config_completo.png

7. Configurar el autoload PSR-4
Editar composer.json y añadir:

json
"autoload": {
    "psr-4": {
        "Application\\": "module/Application/src/",
        "Comentarios\\": "module/Comentarios/src/"
    }
}
Regenerar:

bash
composer dump-autoload -o
📸 Ver capturas/15_7_composer_autoload_laminas.png

8. Configurar la conexión a la base de datos
Editar config/autoload/global.php:

php
<?php
return [
    'db' => [
        'driver'   => 'Pdo',
        'dsn'      => 'mysql:dbname=zend_comentarios;host=127.0.0.1;charset=utf8mb4',
        'username' => 'root',
        'password' => '',
        'driver_options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        ],
    ],
];
📸 Ver capturas/15_8_db_config_global_laminas.png

9. Crear e importar la base de datos
Asegúrate de tener MySQL arrancado en XAMPP:

📸 Ver capturas/15_9a_xampp_panel_mysql_stopped.png y capturas/15_9b_xampp_mysql_running.png

Importar el esquema:

bash
mysql -u root < module/Comentarios/data/schema.sql
📸 Ver capturas/15_9_base_datos_creada.png

10. Arrancar el servidor de desarrollo
bash
php -S localhost:8888 -t public
📸 Ver capturas/15_2a_servidor_corriendo.png

Abrir: http://localhost:8888

📸 Ver capturas/15_2_laminas_welcome.png

🎮 Uso
Ruta	Descripción
/comentarios	Listado público de comentarios aprobados
/comentarios/crear	Formulario de nuevo comentario
/admin/comentarios	Panel de administración
/admin/comentarios/aprobar/{id}	Aprobar un comentario
/admin/comentarios/editar/{id}	Editar un comentario
/admin/comentarios/eliminar/{id}	Eliminar un comentario
📸 Ver capturas del módulo funcionando:

capturas/15_11a_listado_publico_funcionand.png

capturas/15_11b_crear_funcionando.png

🗄️ Base de datos
Esquema
sql
CREATE TABLE `comentarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `post_id` INT NOT NULL,
  `autor` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `contenido` TEXT NOT NULL,
  `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `estado` ENUM('pendiente','aprobado','rechazado') DEFAULT 'pendiente',
  INDEX idx_post (post_id),
  INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
📸 Ver capturas/17_schema_sql.png

🐛 Problemas encontrados y soluciones
Durante el desarrollo se enfrentaron múltiples incidencias relacionadas con la migración de Zend Framework → Laminas y la compatibilidad con PHP 8.4. Resumen (documentación completa en PROBLEMAS_Y_SOLUCIONES.md):

#	Problema	Solución	Captura
1	Zend Framework incompatible con PHP 8.4	Migrar a Laminas MVC	15_1_laminas_instalado.png
2	Esqueleto Laminas restringe PHP ~8.1–8.3	Instalar con --ignore-platform-reqs	15_10c_composer_require_form_db.png
3	Error post-create: remove-package-artifacts.php	Ignorar (no afecta)	—
4	service_manager vs dependencies en Laminas	Registrar en la clave correcta	15_5_module_config_laminas.png
5	composer.json con 4 barras (\\\\) en PSR-4	Reescribir con 2 barras (\\)	15_10q_composer_json_corregido.png
6	BOM UTF-8 en archivos PHP del módulo	Limpiar con script PowerShell	15_10t_bom_detectado.png
7	use Interop\Container\ContainerInterface	Reemplazar por Psr\Container\ContainerInterface	—
8	Module.php con AutoloaderProviderInterface	Eliminar, dejar solo ConfigProviderInterface	15_10y_module_php_corregido.png
9	Referencias Zend\ residuales	Reemplazar por Laminas\	15_10z_sin_zend.png
10	Caché de config de Laminas	Limpiar data/cache/ y reiniciar servidor	15_10w_dump_autoload_ok.png
11	Acentos corruptos en BD (byte 3F)	Actualizar con script PHP usando PDO	—
12	Formulario descuadrado (Bootstrap)	Reescribir vistas con estilos inline	—
📸 Capturas del proceso
Todas las capturas están en la carpeta capturas/.

Bloque 1 — Estructura del módulo
Captura	Descripción
https://capturas/01_estructura_carpetas_inicial.png	Estructura de carpetas inicial
https://capturas/02_composer_json_modulo.png	composer.json del módulo
https://capturas/03_module_php.png	Module.php
https://capturas/04_module_config_inicial.png	module.config.php inicial
https://capturas/05_entidad_comentario.png	Entidad Comentario
https://capturas/06_comentario_table.png	ComentarioTable
https://capturas/07_comentario_table_factory.png	ComentarioTableFactory
https://capturas/08_index_controller.png	IndexController
https://capturas/09_admin_controller.png	AdminController
https://capturas/10_controladores_factory.png	Factories de controladores
https://capturas/11_module_config_actualizado.png	module.config.php actualizado
https://capturas/12_formulario_comentario.png	ComentarioForm
https://capturas/13_vista_listado_publico.png	Vista de listado público
https://capturas/14_vista_crear_comentario.png	Vista de crear comentario
https://capturas/15_vista_admin_listado.png	Vista admin - listado
https://capturas/16_vista_admin_editar.png	Vista admin - editar
https://capturas/17_schema_sql.png	schema.sql
Bloque 2 — Entorno Laminas
Captura	Descripción
https://capturas/15_1_laminas_instalado.png	Esqueleto Laminas instalado
https://capturas/15_2_laminas_welcome.png	Pantalla de bienvenida de Laminas
https://capturas/15_2a_servidor_corriendo.png	Servidor PHP en 8888
https://capturas/15_2a_servidor_localhost.png	Servidor en localhost
https://capturas/15_3_modulo_copiado_laminas.png	Módulo copiado al esqueleto
https://capturas/15_4_reemplazo_zend_laminas.png	Reemplazo Zend\ → Laminas\
https://capturas/15_5_module_config_laminas.png	Adaptación a Laminas
https://capturas/15_6_modules_config_laminas.png	Registro del módulo
https://capturas/15_7_composer_autoload_laminas.png	Autoload PSR-4
https://capturas/15_8_db_config_global_laminas.png	Configuración de BD
https://capturas/15_9_base_datos_creada.png	Base de datos creada
https://capturas/15_9a_xampp_panel_mysql_stopped.png	XAMPP con MySQL detenido
https://capturas/15_9b_xampp_mysql_running.png	XAMPP con MySQL corriendo
https://capturas/15_9c_mysql_respondiendo.png	MySQL respondiendo
https://capturas/15_6c_test_conexion_bd.png	Test de conexión PHP → MySQL
Bloque 3 — Errores y soluciones
Captura	Descripción
https://capturas/15_10c_composer_require_form_db.png	Instalación de paquetes Laminas
https://capturas/15_10d_modules_config_completo.png	Lista completa de módulos
https://capturas/15_10f_composer_require_navigation.png	Instalación de navigation
https://capturas/15_10g_composer_show_navigation.png	Verificación de paquetes
https://capturas/15_10q_composer_json_corregido.png	composer.json corregido
https://capturas/15_10r_composer_validate.png	Validación del composer.json
https://capturas/15_10t_bom_detectado.png	Detección de BOM en PHP
https://capturas/15_10u_bom_eliminado.png	Eliminación del BOM
https://capturas/15_10v_sin_bom.png	Verificación sin BOM
https://capturas/15_10l_dump_autoload.png	composer dump-autoload
https://capturas/15_10w_dump_autoload_ok.png	Autoload optimizado correcto
https://capturas/15_10y_module_php_corregido.png	Module.php corregido
https://capturas/15_10z_sin_zend.png	Verificación sin Zend\
Bloque 4 — Módulo funcionando
Captura	Descripción
https://capturas/15_11a_listado_publico_funcionand.png	Listado público funcionando
https://capturas/15_11b_crear_funcionando.png	Formulario de crear funcionando
💡 Lecciones aprendidas
Zend Framework → Laminas no es solo renombrar namespaces. Algunos patrones (AutoloaderProviderInterface, StandardAutoloader) deben eliminarse, no traducirse.

service_manager vs dependencies: registrar en ambas claves garantiza compatibilidad con cualquier versión de Laminas.

Caché de Laminas es agresiva: tras cada cambio en configuración, hay que:

Eliminar data/cache/

Ejecutar composer dump-autoload -o

Reiniciar el servidor PHP

Recargar el navegador con Ctrl + Shift + R

BOM UTF-8 en PowerShell: el flag -Encoding UTF8 añade BOM en versiones antiguas de Windows PowerShell. Usar [System.IO.File]::WriteAllText($path, $content, (New-Object System.Text.UTF8Encoding($false))) para evitar BOM.

Acentos y PowerShell: nunca pasar SQL con acentos a mysql.exe desde la consola — los convierte a ?. Usar scripts PHP con PDO.

Diagnóstico estructurado: cuando algo falla en Laminas, usar ReflectionClass para inspeccionar el estado interno del ServiceManager y comparar con la configuración fusionada.

📚 Recursos
Laminas MVC Documentation

Laminas Service Manager

PSR-4 Autoloading Standard

PSR-11 Container Interface

Zend Framework → Laminas Migration Guide

👤 Autor
Judit Giravent
Curso Código Samurái — Desarrollo Web con Frameworks 6
GitHub

📄 Licencia
Este proyecto está bajo la licencia MIT. Consulta el archivo LICENSE para más detalles.