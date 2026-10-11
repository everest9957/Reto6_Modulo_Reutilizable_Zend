<?php
namespace Comentarios\Entity;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Comentario implements InputFilterAwareInterface
{
    public $id;
    public $post_id;
    public $autor;
    public $email;
    public $contenido;
    public $fecha_creacion;
    public $estado;

    protected $inputFilter;

    public function exchangeArray(array $data)
    {
        $this->id             = $data['id'] ?? null;
        $this->post_id        = $data['post_id'] ?? null;
        $this->autor          = $data['autor'] ?? null;
        $this->email          = $data['email'] ?? null;
        $this->contenido      = $data['contenido'] ?? null;
        $this->fecha_creacion = $data['fecha_creacion'] ?? null;
        $this->estado         = $data['estado'] ?? 'pendiente';
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("No usado");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name'     => 'post_id',
                'required' => true,
                'filters'  => [['name' => 'Int']],
            ]);

            $inputFilter->add([
                'name'     => 'autor',
                'required' => true,
                'filters'  => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'StringLength', 'options' => ['min' => 2, 'max' => 100]],
                ],
            ]);

            $inputFilter->add([
                'name'     => 'email',
                'required' => true,
                'filters'  => [['name' => 'StringTrim']],
                'validators' => [
                    ['name' => 'EmailAddress'],
                ],
            ]);

            $inputFilter->add([
                'name'     => 'contenido',
                'required' => true,
                'filters'  => [['name' => 'StripTags']],
                'validators' => [
                    ['name' => 'StringLength', 'options' => ['min' => 5]],
                ],
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
