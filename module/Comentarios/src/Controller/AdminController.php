<?php
namespace Comentarios\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Comentarios\Model\ComentarioTable;

class AdminController extends AbstractActionController
{
    private $table;

    public function __construct(ComentarioTable $table)
    {
        $this->table = $table;
    }

    public function indexAction()
    {
        return new ViewModel([
            'comentarios' => $this->table->fetchAll(),
        ]);
    }

    public function editarAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('comentarios-admin');
        }

        try {
            $comentario = $this->table->getComentario($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('comentarios-admin');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $comentario->exchangeArray($request->getPost());
            $this->table->saveComentario($comentario);
            return $this->redirect()->toRoute('comentarios-admin');
        }

        return new ViewModel(['comentario' => $comentario]);
    }

    public function eliminarAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if ($id) {
            $this->table->deleteComentario($id);
        }
        return $this->redirect()->toRoute('comentarios-admin');
    }

    public function aprobarAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if ($id) {
            $comentario = $this->table->getComentario($id);
            $comentario->estado = 'aprobado';
            $this->table->saveComentario($comentario);
        }
        return $this->redirect()->toRoute('comentarios-admin');
    }
}
