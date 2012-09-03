<?php

/**
 * Form definition for table tag.
 *
 * @package Zodekentest
 * @author Zodeken
 * @version $Id$
 *
 */
class Application_Form_EditTag extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');

        $this->addElement(
            $this->createElement('hidden', 'id')
                
        );

        $this->addElement(
            $this->createElement('text', 'name')
                ->setLabel('Name')
                ->setAttrib("maxlength", 250)
                ->setRequired(true)
                ->addValidator(new Zend_Validate_StringLength(array("max" => 250)))
                ->addFilter(new Zend_Filter_StringTrim())
        );

        $this->addElement(
            $this->createElement('text', 'post_count')
                ->setLabel('Post Count')
                ->setRequired(true)
                ->addValidator(new Zend_Validate_Int())
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