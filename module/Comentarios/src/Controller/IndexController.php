<?php
namespace Comentarios\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Comentarios\Entity\Comentario;
use Comentarios\Model\ComentarioTable;
use Comentarios\Form\ComentarioForm;

class IndexController extends AbstractActionController
{
    private $table;

    public function __construct(ComentarioTable $table)
    {
        $this->table = $table;
    }

    public function indexAction()
    {
        $postId = $this->params()->fromQuery('post_id');
        return new ViewModel([
            'comentarios' => $this->table->fetchAprobados($postId),
        ]);
    }

    public function crearAction()
    {
        $form = new ComentarioForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $comentario = new Comentario();
            $form->setInputFilter($comentario->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $comentario->exchangeArray($form->getData());
                $this->table->saveComentario($comentario);
                return $this->redirect()->toRoute('comentarios');
            }
        }

        return new ViewModel(['form' => $form]);
    }
}
