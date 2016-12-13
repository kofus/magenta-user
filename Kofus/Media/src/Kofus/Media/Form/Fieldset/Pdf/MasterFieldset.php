<?php
namespace Kofus\Media\Form\Fieldset\Pdf;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
use Zend\Filter;
use Zend\Validator;

class MasterFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function init()
    {
        $el = new Element\Text('title', array(
            'label' => 'Title'
        ));
        $this->add($el);
        
        // $el = new Element\Checkbox('enabled', array('label' => 'enabled?'));
        // $this->add($el);
    }

    public function getInputFilterSpecification()
    {
        return array(
            'title' => array(
                'required' => false,
                'filters' => array(
                    array(
                        'name' => 'Zend\Filter\ToNull'
                    ),
                    array(
                        'name' => 'Zend\Filter\StringTrim'
                    )
                )
            )
            
        // 'enabled' => array('required' => false)
                );
    }
}
