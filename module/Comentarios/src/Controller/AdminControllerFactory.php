<?php
namespace Comentarios\Controller;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Comentarios\Model\ComentarioTable;

class AdminControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new AdminController($container->get(ComentarioTable::class));
    }
}
