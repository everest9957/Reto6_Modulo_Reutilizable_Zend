# 🗡️ Reto 6 — Módulo Reutilizable de Comentarios en Zend/Laminas

![Estado](https://img.shields.io/badge/estado-completado-success)
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
- [Problemas encontrados](#-problemas-encontrados)
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
- 🎨 **Vistas independientes** con estilos inline.
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
├── capturas/                          # Evidencias del proceso (48 imágenes)
├── module/
│   └── Comentarios/                   # 🎯 Módulo reutilizable
│       ├── config/
│       │   └── module.config.php
│       ├── data/
│       │   └── schema.sql
│       ├── src/
│       │   ├── Controller/
│       │   ├── Entity/
│       │   ├── Form/
│       │   ├── Model/
│       │   └── Module.php
│       ├── view/
│       │   └── comentarios/
│       ├── composer.json
│       └── README.md
│
├── LICENSE
├── README.md
└── PROBLEMAS_Y_SOLUCIONES.md
```

---

## 🚀 Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/everest9957/Reto6_Modulo_Reutilizable_Zend.git
cd Reto6_Modulo_Reutilizable_Zend
```

### 2. Crear un proyecto Laminas MVC

```bash
composer create-project -s dev laminas/laminas-mvc-skeleton laminas-skeleton-test --ignore-platform-reqs
cd laminas-skeleton-test
```

![Instalación de Laminas MVC](capturas/15_1_laminas_instalado.png)

### 3. Copiar el módulo al esqueleto

```powershell
Copy-Item -Path "..\module\Comentarios" -Destination "module\Comentarios" -Recurse
```

![Módulo copiado al esqueleto](capturas/15_3_modulo_copiado_laminas.png)

### 4. Adaptar namespaces Zend → Laminas

```powershell
Get-ChildItem -Path .\module\Comentarios -Recurse -Filter *.php | ForEach-Object {
    (Get-Content $_.FullName -Raw) -replace 'Zend\\', 'Laminas\' |
        Set-Content $_.FullName -NoNewline
}
```

![Reemplazo de namespaces](capturas/15_4_reemplazo_zend_laminas.png)

### 5. Instalar dependencias adicionales

```bash
composer require --ignore-platform-reqs \
    laminas/laminas-form \
    laminas/laminas-db \
    laminas/laminas-mvc-form \
    laminas/laminas-mvc-i18n \
    laminas/laminas-navigation
```

Cuando el asistente pregunte en qué archivo inyectar el módulo, elegir `1` (`config/modules.config.php`).

![Instalación de paquetes Laminas](capturas/15_10c_composer_require_form_db.png)

### 6. Registrar el módulo

Editar `config/modules.config.php` y agregar `'Comentarios'` al final:

```php
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
```

![Registro del módulo](capturas/15_6_modules_config_laminas.png)

### 7. Configurar el autoload PSR-4

Editar `composer.json` y añadir:

```json
"autoload": {
    "psr-4": {
        "Application\\": "module/Application/src/",
        "Comentarios\\": "module/Comentarios/src/"
    }
}
```

Regenerar:

```bash
composer dump-autoload -o
```

![Autoload PSR-4](capturas/15_7_composer_autoload_laminas.png)

### 8. Configurar la conexión a la base de datos

Editar `config/autoload/global.php`:

```php
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
```

![Configuración de BD](capturas/15_8_db_config_global_laminas.png)

### 9. Crear e importar la base de datos

Asegúrate de tener MySQL arrancado en XAMPP.

![XAMPP con MySQL corriendo](capturas/15_9b_xampp_mysql_running.png)

Importar el esquema:

```bash
mysql -u root < module/Comentarios/data/schema.sql
```

![Base de datos creada](capturas/15_9_base_datos_creada.png)

### 10. Arrancar el servidor de desarrollo

```bash
php -S localhost:8888 -t public
```

![Servidor corriendo](capturas/15_2a_servidor_corriendo.png)

Abrir en el navegador: [http://localhost:8888](http://localhost:8888)

![Pantalla de bienvenida de Laminas](capturas/15_2_laminas_welcome.png)

---

## 🎮 Uso

| Ruta | Descripción |
|------|-------------|
| `/comentarios` | Listado público de comentarios aprobados |
| `/comentarios/crear` | Formulario de nuevo comentario |
| `/admin/comentarios` | Panel de administración |
| `/admin/comentarios/aprobar/{id}` | Aprobar un comentario |
| `/admin/comentarios/editar/{id}` | Editar un comentario |
| `/admin/comentarios/eliminar/{id}` | Eliminar un comentario |

### Listado público

![Listado público de comentarios](capturas/15_11a_listado_publico_funcionand.png)

### Formulario de creación

![Formulario de crear comentario](capturas/15_11b_crear_funcionando.png)

### Panel de administración

![Panel de administración](capturas/15_vista_admin_listado.png)

---

## 🗄️ Base de datos

### Esquema

```sql
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
```

![Esquema SQL](capturas/17_schema_sql.png)

---

## 🐛 Problemas encontrados

Durante el desarrollo se enfrentaron múltiples incidencias relacionadas
con la migración de **Zend Framework → Laminas** y la compatibilidad
con **PHP 8.4**.

La documentación completa con **13 problemas** y sus soluciones está en
[`PROBLEMAS_Y_SOLUCIONES.md`](PROBLEMAS_Y_SOLUCIONES.md).

**Resumen:**

| # | Problema | Solución |
|---|----------|----------|
| 1 | Zend incompatible con PHP 8.4 | Migrar a Laminas |
| 2 | Esqueleto Laminas restringe PHP ~8.1-8.3 | Usar `--ignore-platform-reqs` |
| 3 | Error post-create del esqueleto | Ignorar (no crítico) |
| 4 | `service_manager` vs `dependencies` | Cambiar la clave |
| 5 | `composer.json` con 4 barras en PSR-4 | Corregir a 2 barras |
| 6 | BOM UTF-8 en archivos PHP | Eliminar BOM con PowerShell |
| 7 | `Interop\Container` en factories | Reemplazar por `Psr\Container` |
| 8 | `Module.php` con `AutoloaderProviderInterface` | Simplificar el Module |
| 9 | Referencias `Zend\` residuales | Reemplazar por `Laminas\` |
| 10 | Caché de config de Laminas | Limpiar `data/cache/` |
| 11 | `dependencies` no se fusiona con `service_manager` | Registrar en ambas |
| 12 | Acentos corruptos en la BD | Actualizar con script PHP + PDO |
| 13 | Formulario descuadrado con Bootstrap | Estilos inline |

---

## 📸 Capturas del proceso

Todas las capturas están en la carpeta [`capturas/`](capturas/).

### Bloque 1 — Estructura del módulo

![Estructura inicial](capturas/01_estructura_carpetas_inicial.png)

![Composer del módulo](capturas/02_composer_json_modulo.png)

![Module.php](capturas/03_module_php.png)

![module.config.php inicial](capturas/04_module_config_inicial.png)

![Entidad Comentario](capturas/05_entidad_comentario.png)

![ComentarioTable](capturas/06_comentario_table.png)

![ComentarioTableFactory](capturas/07_comentario_table_factory.png)

![IndexController](capturas/08_index_controller.png)

![AdminController](capturas/09_admin_controller.png)

![Controller factories](capturas/10_controladores_factory.png)

![module.config.php actualizado](capturas/11_module_config_actualizado.png)

![Formulario ComentarioForm](capturas/12_formulario_comentario.png)

![Vista listado público](capturas/13_vista_listado_publico.png)

![Vista crear comentario](capturas/14_vista_crear_comentario.png)

![Vista admin listado](capturas/15_vista_admin_listado.png)

![Vista admin editar](capturas/16_vista_admin_editar.png)

![Schema SQL](capturas/17_schema_sql.png)

### Bloque 2 — Entorno Laminas

![Laminas instalado](capturas/15_1_laminas_instalado.png)

![Pantalla de bienvenida](capturas/15_2_laminas_welcome.png)

![Servidor corriendo](capturas/15_2a_servidor_corriendo.png)

![Módulo copiado](capturas/15_3_modulo_copiado_laminas.png)

![Reemplazo Zend → Laminas](capturas/15_4_reemplazo_zend_laminas.png)

![module.config adaptado](capturas/15_5_module_config_laminas.png)

![modules.config con Comentarios](capturas/15_6_modules_config_laminas.png)

![Autoload PSR-4](capturas/15_7_composer_autoload_laminas.png)

![Configuración BD](capturas/15_8_db_config_global_laminas.png)

![Base de datos creada](capturas/15_9_base_datos_creada.png)

![XAMPP con MySQL corriendo](capturas/15_9b_xampp_mysql_running.png)

![Test conexión BD](capturas/15_6c_test_conexion_bd.png)

### Bloque 3 — Errores y soluciones

![Instalación de paquetes Laminas](capturas/15_10c_composer_require_form_db.png)

![modules.config completo](capturas/15_10d_modules_config_completo.png)

![composer.json corregido](capturas/15_10q_composer_json_corregido.png)

![composer validate](capturas/15_10r_composer_validate.png)

![BOM detectado](capturas/15_10t_bom_detectado.png)

![BOM eliminado](capturas/15_10u_bom_eliminado.png)

![Verificación sin BOM](capturas/15_10v_sin_bom.png)

![dump-autoload](capturas/15_10l_dump_autoload.png)

![dump-autoload OK](capturas/15_10w_dump_autoload_ok.png)

![Module.php corregido](capturas/15_10y_module_php_corregido.png)

![Verificación sin Zend](capturas/15_10z_sin_zend.png)

### Bloque 4 — Módulo funcionando

![Listado público funcionando](capturas/15_11a_listado_publico_funcionand.png)

![Formulario crear funcionando](capturas/15_11b_crear_funcionando.png)

---

## 💡 Lecciones aprendidas

1. **Zend Framework → Laminas** no es solo renombrar namespaces.
   Algunos patrones (`AutoloaderProviderInterface`, `StandardAutoloader`)
   deben eliminarse, no traducirse.

2. **`service_manager` vs `dependencies`**: en Laminas moderno usar
   `dependencies`, y en caso de problemas registrar en ambas.

3. **Caché de Laminas** es agresiva: tras cada cambio de configuración, hay que:
   - Eliminar `data/cache/`
   - Ejecutar `composer dump-autoload -o`
   - Reiniciar el servidor PHP
   - Recargar el navegador con `Ctrl + Shift + R`

4. **BOM UTF-8 en PowerShell**: el flag `-Encoding UTF8` añade BOM en
   versiones antiguas de Windows PowerShell.

5. **Acentos y PowerShell**: nunca pasar SQL con acentos a `mysql.exe`
   desde la consola. Usar scripts PHP con PDO.

6. **Diagnóstico estructurado**: usar `ReflectionClass` para inspeccionar
   el estado interno del ServiceManager.

---

## 📚 Recursos

- [Laminas MVC Documentation](https://docs.laminas.dev/laminas-mvc/)
- [Laminas Service Manager](https://docs.laminas.dev/laminas-servicemanager/)
- [PSR-4 Autoloading Standard](https://www.php-fig.org/psr/psr-4/)
- [PSR-11 Container Interface](https://www.php-fig.org/psr/psr-11/)
- [Zend Framework → Laminas Migration Guide](https://docs.laminas.dev/migration/)

---

## 👤 Autor

**everest9957**
Curso *Código Samurái — Desarrollo Web con Frameworks 6*

[GitHub](https://github.com/everest9957)

---

## 📄 Licencia

Este proyecto está bajo la licencia **MIT**. Consulta el archivo
[`LICENSE`](LICENSE) para más detalles.