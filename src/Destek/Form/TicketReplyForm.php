<?php

namespace Destek\Form;

class TicketReplyForm extends \EasyBib_Form
{
    public function init()
    {
        $this->setMethod('post');

        $message        = new \Zend_Form_Element_Textarea('message');
        $submit         = new \Zend_Form_Element_Button('submit');
        $cancel         = new \Zend_Form_Element_Button('cancel');

        $message->setLabel('Mesaj:')
            ->setAttrib('placeholder', 'Mesaj giriniz!')
            ->setAttrib('class', 'form-control')
            ->setAttrib('rows', '5')
            ->setRequired(true)
            ->setDescription('')
            ->setErrorMessages(array('required' => 'Bu alan gereklidir!'));

        $submit->setLabel('Yanıtla')
            ->setAttrib('class', 'btn btn-lg btn-primary btn-block')
            ->setAttrib('type', 'submit');

        $cancel->setLabel('İptal')
            ->setAttrib('class', 'btn btn-md btn-default btn-block')
            ->setAttrib('type', 'reset');

        //$hash->setIgnore(true);

        // add elements
        $this->addElements(array(
            $message, $submit, $cancel
        ));

        // add display group
        $this->addDisplayGroup(
            array('message', 'submit', 'cancel'),
            'ticker_replies'
        );

        \EasyBib_Form_Decorator::setFormDecorator($this, \EasyBib_Form_Decorator::BOOTSTRAP_MINIMAL, 'submit', 'cancel');
    }
}