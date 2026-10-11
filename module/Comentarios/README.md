# Módulo Comentarios — Zend / Laminas

Módulo reutilizable de comentarios para Zend Framework 3 y Laminas MVC.

## Funcionalidades
- Crear comentarios
- Visualizar comentarios aprobados
- Administrar (editar / eliminar / aprobar)

## Instalación rápida
1. Copiar la carpeta `Comentarios` a `module/`
2. Registrar `'Comentarios'` en `config/modules.config.php`
3. Agregar el namespace `"Comentarios\\": "module/Comentarios/src/"` en el `autoload.psr-4` del `composer.json`
4. Ejecutar `composer dump-autoload -o`
5. Importar `data/schema.sql` en la base de datos
6. Configurar la conexión a BD en `config/autoload/global.php`

## Rutas
| Ruta | Descripción |
|------|-------------|
| `/comentarios` | Listado público |
| `/comentarios/crear` | Formulario |
| `/admin/comentarios` | Panel admin |

## Dependencias
- `laminas/laminas-mvc`
- `laminas/laminas-db`
- `laminas/laminas-form`
- `laminas/laminas-hydrator`

## Compatibilidad
- ✅ Zend Framework 3 (PHP 7.x) — usar `Zend\` en lugar de `Laminas\`
- ✅ Laminas MVC (PHP 8.1+) — versión probada

## Licencia
MIT