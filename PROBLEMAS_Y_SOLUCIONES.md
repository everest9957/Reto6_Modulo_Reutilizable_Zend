Este archivo va en la **raíz** del proyecto:

```markdown
# 🐛 Problemas encontrados y soluciones — Reto 6

Documentación completa de los obstáculos enfrentados al portar el módulo
`Comentarios` desde Zend Framework 3 a Laminas MVC con PHP 8.4.

---

## Índice

1. [Zend Framework incompatible con PHP 8.4](#1-zend-framework-incompatible-con-php-84)
2. [Esqueleto Laminas restringe PHP ~8.1–8.3](#2-esqueleto-laminas-restringe-php-8183)
3. [Error post-create: remove-package-artifacts.php](#3-error-post-create-remove-package-artifactsphp)
4. [Configuración `service_manager` vs `dependencies`](#4-configuración-service_manager-vs-dependencies)
5. [composer.json con 4 barras en PSR-4](#5-composerjson-con-4-barras-en-psr-4)
6. [BOM UTF-8 en archivos PHP](#6-bom-utf-8-en-archivos-php)
7. [use Interop\Container\ContainerInterface](#7-use-interopcontainercontainerinterface)
8. [Module.php con AutoloaderProviderInterface](#8-modulephp-con-autoloaderproviderinterface)
9. [Referencias Zend\ residuales](#9-referencias-zend-residuales)
10. [Caché de configuración de Laminas](#10-caché-de-configuración-de-laminas)
11. [dependencies no se fusiona con service_manager](#11-dependencies-no-se-fusiona-con-service_manager)
12. [Acentos corruptos en la BD](#12-acentos-corruptos-en-la-bd)
13. [Formulario descuadrado con Bootstrap](#13-formulario-descuadrado-con-bootstrap)

---

## 1. Zend Framework incompatible con PHP 8.4

**Síntoma:**
Cannot use zendframework/skeleton-application's latest version 3.1.3
as it requires php ^5.6 || ^7.0 which is not satisfied by your platform.

text

**Causa:** Zend Framework está descontinuado. Sus dependencias internas
(`doctrine/instantiator 1.0.4`) no soportan PHP 8.x.

**Solución:** Migrar a **Laminas**, el sucesor oficial mantenido por la
comunidad. Los namespaces `Zend\` pasan a `Laminas\` y la arquitectura
es idéntica.

**Captura:** `15_1_laminas_instalado.png`

---

## 2. Esqueleto Laminas restringe PHP ~8.1–8.3

**Síntoma:**
Cannot use laminas/laminas-mvc-skeleton's latest version 2.4.0
as it requires php ~8.1.0 || ~8.2.0 || ~8.3.0 which is not satisfied
by your platform.

text

**Causa:** El esqueleto declara explícitamente no soportar PHP 8.4
(probablemente por falta de pruebas en esa versión).

**Solución:** Instalar con `--ignore-platform-reqs`:

```powershell
composer create-project -s dev laminas/laminas-mvc-skeleton laminas-skeleton-test --ignore-platform-reqs
También se debe usar el mismo flag al instalar paquetes adicionales.

Captura: 15_10c_composer_require_form_db.png

3. Error post-create: remove-package-artifacts.php
Síntoma:

text
Script php -r 'if (file_exists("bin/remove-package-artifacts.php")) include ...'
handling the post-create-project-cmd event returned with error code 1
Causa: El esqueleto referencia un script que ya no existe en la
versión actual, pero el instalador no lo ha actualizado.

Solución: Ignorar. No afecta al funcionamiento del proyecto. El
esqueleto queda instalado y operativo.

4. Configuración service_manager vs dependencies
Síntoma: El error

text
Unable to resolve service "Comentarios\Model\ComentarioTable" to a factory
aparecía aunque el module.config.php tenía registrada la factory.

Causa: El módulo original (Zend Framework 2) usaba la clave
service_manager. En Laminas moderno, la clave recomendada es
dependencies. Al migrar hay que cambiar la clave, no solo el
namespace.

Solución: Editar module/Comentarios/config/module.config.php:

php
// Zend Framework 2
'service_manager' => [
    'factories' => [...],
],

// Laminas (a partir de ZF 3)
'dependencies' => [
    'factories' => [...],
],
Captura: 15_5_module_config_laminas.png

5. composer.json con 4 barras en PSR-4
Síntoma: El autoloader no encontraba las clases del módulo:

text
Class Comentarios\Controller\IndexController does not comply with psr-4
autoloading standard (rule: Comentarios\\ => ./module/Comentarios/src). Skipping.
Y en vendor/composer/autoload_psr4.php aparecía:

php
'Comentarios\\\\' => array($baseDir . '/module/Comentarios/src'),
Causa: El composer.json contenía "Comentarios\\\\" (cuatro barras)
en lugar de "Comentarios\\" (dos barras = una barra real en JSON).
Esto ocurría porque en algún momento se editó el archivo con un
reemplazo automático que duplicó las barras.

Solución: Reescribir la sección autoload del composer.json con
exactamente dos barras:

json
"autoload": {
    "psr-4": {
        "Application\\": "module/Application/src/",
        "Comentarios\\": "module/Comentarios/src/"
    }
}
Y regenerar:

bash
composer dump-autoload -o
Capturas: 15_10q_composer_json_corregido.png, 15_10r_composer_validate.png

6. BOM UTF-8 en archivos PHP
Síntoma: Mensajes de Composer:

text
Class Comentarios\Controller\AdminController located in ./module/Comentarios/src/Controller/AdminController.php
does not comply with psr-4 autoloading standard (rule: Comentarios\\ => ./module/Comentarios/src). Skipping.
Causa: Los archivos .php fueron guardados con BOM UTF-8
(bytes EF BB BF al inicio). El BOM aparece antes de <?php,
impidiendo que PHP interprete el archivo como código.

Esto ocurre al usar Set-Content -Encoding UTF8 en Windows PowerShell
(la versión 5.x añade BOM por defecto).

Solución: Eliminar el BOM con un script PowerShell:

powershell
Get-ChildItem -Path module\Comentarios -Recurse -Filter *.php | ForEach-Object {
    $content = [System.IO.File]::ReadAllText($_.FullName)
    if ($content.Length -gt 0 -and $content[0] -eq [char]0xFEFF) {
        $content = $content.Substring(1)
    }
    $utf8NoBom = New-Object System.Text.UTF8Encoding($false)
    [System.IO.File]::WriteAllText($_.FullName, $content, $utf8NoBom)
}
Capturas: 15_10t_bom_detectado.png, 15_10u_bom_eliminado.png, 15_10v_sin_bom.png

7. use Interop\Container\ContainerInterface
Síntoma: El error del ServiceManager:

text
Unable to resolve service "Comentarios\Model\ComentarioTable" to a factory;
are you certain you provided it during configuration?
A pesar de que:

La clase existe ✅

La factory está registrada en dependencies.factories ✅

El archivo no tiene BOM ✅

Causa: Los factories usaban Interop\Container\ContainerInterface
(namespace antiguo del paquete container-interop, predecesor de
PSR-11). En Laminas el namespace correcto es
Psr\Container\ContainerInterface. Como el __invoke no cumplía
el contrato de Laminas\ServiceManager\Factory\FactoryInterface, el
ServiceManager rechazaba invocarlo silenciosamente.

Solución: Reemplazar en los tres factories
(ComentarioTableFactory.php, IndexControllerFactory.php,
AdminControllerFactory.php):

php
// Antes
use Interop\Container\ContainerInterface;

// Después
use Psr\Container\ContainerInterface;

Verificar con:

powershell
Get-ChildItem module\Comentarios -Recurse -Filter *.php | Select-String "Interop"
# No debe devolver nada
8. Module.php con AutoloaderProviderInterface
Síntoma: La configuración del módulo no se registraba correctamente
en el ServiceManager, aunque el archivo module.config.php era
correcto y el módulo aparecía en modules.config.php.

Causa: El Module.php implementaba AutoloaderProviderInterface y
devolvía getAutoloaderConfig() con StandardAutoloader. Este patrón
(Zend Framework 2) interfiere con el autoload PSR-4 de Composer y
rompe la fusión de configuración en el ServiceManager.

Solución: Simplificar el Module.php eliminando todo lo relativo a
autoload (Composer ya se encarga):

php
<?php
namespace Comentarios;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
Captura: 15_10y_module_php_corregido.png

9. Referencias Zend\ residuales
Síntoma: Errores de "Class not found" para clases Zend\... al
ejecutar el módulo en Laminas.

Causa: El reemplazo automático Zend\ → Laminas\ no alcanzó
todos los archivos. En particular, Module.php seguía usando
Zend\ModuleManager\Feature\....

Solución: Verificar y limpiar todas las referencias residuales:

powershell
Get-ChildItem module\Comentarios -Recurse -Filter *.php | Select-String "Zend"
# No debe devolver nada
Si devuelve algo, reemplazar con:

powershell
Get-ChildItem module\Comentarios -Recurse -Filter *.php | ForEach-Object {
    $content = Get-Content $_.FullName -Raw
    $content = $content -replace 'Zend\\', 'Laminas\'
    $content = $content -replace 'Zend/', 'Laminas/'
    [System.IO.File]::WriteAllText($_.FullName, $content, (New-Object System.Text.UTF8Encoding($false)))
}
Captura: 15_10z_sin_zend.png

10. Caché de configuración de Laminas
Síntoma: Tras corregir module.config.php o Module.php, el
navegador seguía mostrando el mismo error. Los cambios no tenían
efecto.

Causa: Laminas cachea la configuración fusionada en
data/cache/module-config-cache.*.php y el mapeo de módulos en
data/cache/module-class-map-*. El servidor PHP en memoria seguía
leyendo la config vieja.

Solución: Rutina obligatoria tras cada cambio de configuración:

powershell
# 1. Limpiar caché
Remove-Item -Path "data\cache\*" -Recurse -Force -ErrorAction SilentlyContinue

# 2. Regenerar autoload
composer dump-autoload -o

# 3. Reiniciar el servidor (Ctrl + C y volver a arrancar)
php -S localhost:8888 -t public

# 4. En el navegador: Ctrl + Shift + R (recarga forzada)
Captura: 15_10w_dump_autoload_ok.png

11. dependencies no se fusiona con service_manager
Síntoma: La configuración fusionada sí mostraba
dependencies.factories.Comentarios\Model\ComentarioTable, pero el
ServiceManager interno (verificado con ReflectionClass) solo
tenía 52 factories y ninguna de Comentarios.

Esto se confirmó con un diagnóstico detallado:

text
5. Config fusionada:
   dependencies.factories:
     - Comentarios\Model\ComentarioTable         ← ✅

6. Factories internas del SM (reflexión):
   Total: 52
   (ninguna de Comentarios)                       ← ❌
Causa: En esta versión/configuración de Laminas, la clave
dependencies no se fusiona automáticamente con service_manager a
nivel del ServiceManager raíz cuando se carga desde módulos. Las
factories quedan solo en la config, no en la lista interna del SM.

Solución: Registrar las factories en ambas claves:

php
'service_manager' => [
    'factories' => [
        Model\ComentarioTable::class => Model\ComentarioTableFactory::class,
    ],
],
'dependencies' => [
    'factories' => [
        Model\ComentarioTable::class => Model\ComentarioTableFactory::class,
    ],
],
12. Acentos corruptos en la BD
Síntoma: En el navegador aparecía Ana L?pez, art?culo,
Mar?a P?rez.

Diagnóstico:

sql
SELECT autor, HEX(autor) FROM comentarios;
-- Ana L?pez → 416E61204C3F70657A
--              ^^^^^^ 3F = '?' literal, no un carácter UTF-8
Causa: Dos problemas combinados:

Los datos se insertaron sin charset UTF-8 en la conexión.
MySQL reemplazó los caracteres no representables por ?.

PowerShell corrompe los acentos al pasar SQL a mysql.exe.
Los convierte a ? (byte 3F) antes de enviarlos.

Solución: Usar un script PHP con PDO para actualizar los datos.
PHP lee los acentos del archivo .php en UTF-8 directamente, sin pasar
por la consola:

php
$pdo = new PDO(
    'mysql:dbname=zend_comentarios;host=127.0.0.1;charset=utf8mb4',
    'root',
    '',
    [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"]
);

$data = [
    [1, 'Ana López',   'ana@example.com',    'Excelente artículo, muy claro.'],
    [2, 'Carlos Ruiz', 'carlos@example.com', 'Me ayudó mucho, gracias.'],
    [3, 'María Pérez', 'maria@example.com',  'Tengo una duda sobre el paso 3.'],
];

$stmt = $pdo->prepare("UPDATE comentarios SET autor = ?, email = ?, contenido = ? WHERE id = ?");
foreach ($data as $row) {
    $stmt->execute([$row[1], $row[2], $row[3], $row[0]]);
}
Verificar el resultado con HEX(autor) — debe contener C3B3 (ó
correcta) en lugar de 3F (?):

text
Ana López → 416E61204CC3B370657A
                    ^^^^^ ó correcta
También se actualizó config/autoload/global.php para forzar el
charset en cada conexión:

php
'driver_options' => [
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
],
13. Formulario descuadrado con Bootstrap
Síntoma: Los campos del formulario aparecían desalineados, sin
estructura visual clara. Los botones y etiquetas no respetaban el
layout.

Causa: Las vistas usaban clases Bootstrap 5 (form-control,
btn, card) pero el layout del esqueleto Laminas incluye una versión
más antigua o directamente no incluye Bootstrap 5.

Solución: Reescribir las vistas con HTML limpio y estilos inline
que no dependen de ninguna versión de Bootstrap:

php
<div style="margin-bottom:15px;">
    <label style="display:block; font-weight:bold; margin-bottom:5px;">Nombre</label>
    <?= $this->formElement($form->get('autor')) ?>
    <?= $this->formElementErrors($form->get('autor')) ?>
</div>

<style>
    input[type="text"], input[type="email"], textarea {
        width: 100%; max-width: 600px; padding: 8px;
        border: 1px solid #ccc; border-radius: 4px;
    }
    button[type="submit"] {
        background: #005a8c; color: white; padding: 10px 20px;
        border: none; border-radius: 4px; cursor: pointer;
    }
</style>
📚 Lecciones aprendidas
Zend Framework → Laminas no es solo renombrar namespaces.
Algunos patrones (AutoloaderProviderInterface, StandardAutoloader)
deben eliminarse, no traducirse.

service_manager vs dependencies: en Laminas moderno usar
dependencies, y en caso de problemas registrar en ambas.

Caché de Laminas es agresiva. Siempre limpiar data/cache/ y
reiniciar el servidor tras cambios de configuración.

BOM UTF-8 en PowerShell: usar
[System.IO.File]::WriteAllText($path, $content, (New-Object System.Text.UTF8Encoding($false)))
para garantizar que no se agregue BOM.

Acentos y PowerShell: nunca pasar acentos a mysql.exe desde
la consola. Usar scripts PHP con PDO.

Diagnóstico estructurado: usar ReflectionClass sobre el
ServiceManager para inspeccionar su estado interno y compararlo con
la config fusionada.