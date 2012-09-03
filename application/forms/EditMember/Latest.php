<?php

/**
 * Form definition for table member.
 *
 * @package Zodekentest
 * @author Zodeken
 * @version $Id$
 *
 */
class Application_Form_EditMember_Latest extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');

        $this->addElement(
            $this->createElement('hidden', 'id')
                
        );

        $this->addElement(
            $this->createElement('text', 'email')
                ->setLabel('Email')
                ->setAttrib("maxlength", 150)
                ->setRequired(true)
                ->addValidator(new Zend_Validate_StringLength(array("max" => 150)))
                ->addValidator(new Zend_Validate_EmailAddress())
                ->addFilter(new Zend_Filter_StringTrim())
        );

        $this->addElement(
            $this->createElement('radio', 'gender')
                ->setLabel('Gender')
                ->setMultiOptions(array('Male' => 'Male','Female' => 'Female'))
                ->setSeparator(" ")
                ->addValidator(new Zend_Validate_InArray(array('haystack' => array('Male' => 'Male','Female' => 'Female'))))
        );

        $this->addElement(
            $this->createElement('text', 'date_registered')
                ->setLabel('Date Registered')
                ->setValue(date("Y-m-d H:i:s"))
                ->addFilter(new Zend_Filter_StringTrim())
        );

        $this->addElement(
            $this->createElement('button', 'submit')
                ->setLabel('Save')
                ->setAttrib('type', 'submit')
        );

        parent::init();
    }
}