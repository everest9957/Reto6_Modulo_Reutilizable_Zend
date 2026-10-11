<?php
namespace Comentarios\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Comentarios\Entity\Comentario;

class ComentarioTableFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $dbAdapter = $container->get(AdapterInterface::class);
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Comentario());

        $tableGateway = new TableGateway('comentarios', $dbAdapter, null, $resultSetPrototype);

        return new ComentarioTable($tableGateway);
    }
}
