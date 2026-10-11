<?php
namespace Comentarios\Form;

use Zend\Form\Form;

class ComentarioForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('comentario');

        $this->add([
            'name' => 'post_id',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name' => 'autor',
            'type' => 'Text',
            'options' => ['label' => 'Nombre'],
            'attributes' => ['class' => 'form-control'],
        ]);

        $this->add([
            'name' => 'email',
            'type' => 'Email',
            'options' => ['label' => 'Email'],
            'attributes' => ['class' => 'form-control'],
        ]);

        $this->add([
            'name' => 'contenido',
            'type' => 'Textarea',
            'options' => ['label' => 'Comentario'],
            'attributes' => ['class' => 'form-control', 'rows' => 4],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => [
                'value' => 'Enviar comentario',
                'id'    => 'submitbutton',
                'class' => 'btn btn-primary',
            ],
        ]);
    }
}
