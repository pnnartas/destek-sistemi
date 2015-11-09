<?php

namespace Destek\Form;

class LoginForm extends \EasyBib_Form
{

    public function init()
    {
        $this->setMethod('post');

        $email    = new \Zend_Form_Element_Text('email');
        $password = new \Zend_Form_Element_Password('password');
        $submit   = new \Zend_Form_Element_Button('submit');

        $email->setLabel('Email:')
            ->setAttrib('class', 'form-control')
            ->setRequired(true)
            ->addValidator('emailAddress')
            ->setErrorMessages(array('emailAddress' => 'Geçerli bir email adresi giriniz.'));

        $password->setLabel('Şifre:')
            ->setRequired(true)
            ->setAttrib('class', 'form-control')
            ->setValidators(array(
                array('validator' => 'StringLength', 'options' => array(6, 20))
            ))
            ->setErrorMessages(array('StringLength' => 'Şifre en az 6 karakter ve en fazla 20 karakter olabilir'));

        $submit->setLabel('Giriş')
            ->setAttrib('class', 'btn btn-lg btn-info btn-block')
            ->setAttrib('type', 'submit');

        $this->addElements(array(
            $email, $password, $submit
        ));

        $this->addDisplayGroup(
            array('email', 'password', 'submit'),
            'login'
        );

        \EasyBib_Form_Decorator::setFormDecorator($this, \EasyBib_Form_Decorator::BOOTSTRAP_MINIMAL, 'submit', 'cancel');

    }
}