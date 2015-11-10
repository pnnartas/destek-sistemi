<?php

namespace Destek\Form;

class CategoryForm extends \EasyBib_Form
{

    public function init()
    {
        $this->setMethod('post');

        $name    = new \Zend_Form_Element_Text('name');
        $submit   = new \Zend_Form_Element_Button('submit');
        $cancel   = new \Zend_Form_Element_Button('cancel');

        $name->setLabel('Kategori Adı:')
            ->setAttrib('placeholder', 'Kategori adı giriniz!')
            ->setAttrib('class', 'form-control')
            ->setRequired(true)
            ->setDescription('')
            ->setErrorMessages(array('required' => 'Kategori adı zorunludur!'));


        $submit->setLabel('Kaydet')
            ->setAttrib('class', 'btn btn-lg btn-primary btn-block')
            ->setAttrib('type', 'submit');
        $cancel->setLabel('İptal')
            ->setAttrib('class', 'btn btn-md btn-default btn-block')
            ->setAttrib('type', 'reset');

        $elementsArray = array(
            $name, $submit, $cancel
        );
        // add elements
        $this->addElements($elementsArray);


        // add display group
        $this->addDisplayGroup(
            array('categoryName', 'submit', 'cancel'),
            'users'
        );

        \EasyBib_Form_Decorator::setFormDecorator($this, \EasyBib_Form_Decorator::BOOTSTRAP_MINIMAL, 'submit', 'cancel');
    }
}