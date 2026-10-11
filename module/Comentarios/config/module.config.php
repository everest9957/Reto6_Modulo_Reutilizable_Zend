<?php
namespace Comentarios;

use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'comentarios' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/comentarios[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'comentarios-admin' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/admin/comentarios[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],

    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Controller\IndexControllerFactory::class,
            Controller\AdminController::class => Controller\AdminControllerFactory::class,
        ],
    ],

    'service_manager' => [
        'factories' => [
            Model\ComentarioTable::class => Model\ComentarioTableFactory::class,
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
