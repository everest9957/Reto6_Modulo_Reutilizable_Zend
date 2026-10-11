<?php
namespace Comentarios\Model;

use Zend\Db\TableGateway\TableGatewayInterface;
use Comentarios\Entity\Comentario;

class ComentarioTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function fetchAprobados($postId = null)
    {
        $where = ['estado' => 'aprobado'];
        if ($postId) {
            $where['post_id'] = $postId;
        }
        return $this->tableGateway->select($where);
    }

    public function getComentario($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (!$row) {
            throw new \RuntimeException("Comentario $id no encontrado");
        }
        return $row;
    }

    public function saveComentario(Comentario $comentario)
    {
        $data = [
            'post_id'   => $comentario->post_id,
            'autor'     => $comentario->autor,
            'email'     => $comentario->email,
            'contenido' => $comentario->contenido,
            'estado'    => $comentario->estado ?? 'pendiente',
        ];

        $id = (int) $comentario->id;
        if ($id === 0) {
            $data['fecha_creacion'] = date('Y-m-d H:i:s');
            $this->tableGateway->insert($data);
            return $this->tableGateway->lastInsertValue;
        }

        $this->tableGateway->update($data, ['id' => $id]);
        return $id;
    }

    public function deleteComentario($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
}
