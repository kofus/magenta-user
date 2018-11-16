<?php
namespace Kofus\User\Form\Fieldset\Auth;

use Zend\Form\Fieldset;
use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;
use Kofus\User\Entity\AuthEntity;

class PassphraseFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function init()
    {
        $el = new Element\Text('account', array('label' => 'User'));
        $el->setAttribute('readonly', 'readonly');
        $this->add($el);
        
        $el = new Element\Text('identity', array(
            'label' => 'Passphrase'
        ));
        $el->setAttribute('autocomplete', 'off');
        $this->add($el);
        
        $el = new Element\Checkbox('enabled', array('label' => 'enabled?'));
        $this->add($el);
    }

    public function getInputFilterSpecification()
    {
        return array(
            'identity' => array(
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'Zend\Filter\StringTrim'
                    )
                )
            ),
        )
        ;
    }
}
