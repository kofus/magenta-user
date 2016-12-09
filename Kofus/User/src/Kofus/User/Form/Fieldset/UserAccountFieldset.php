<?php

namespace Kofus\User\Form\Fieldset;

use Zend\Form\Fieldset;
use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;

class UserAccountFieldset extends Fieldset implements InputFilterProviderInterface
{
	public function init()
	{
		$el = new Element\Text('name', array('label' => 'Display Name'));
		$this->add($el);
		
		$el = new Element\Select('status', array('label' => 'Status'));
		$el->setValueOptions(\Kofus\User\Entity\AccountEntity::$STATUS);
		$this->add($el);
		
	}

	public function getInputFilterSpecification()
	{
	    $stringTrim = new \Zend\Filter\StringTrim();
		return array(
			'name' => array('required' => true, 'filters' => array($stringTrim)),
			'status' => array('required' => true)
		);
	}
}
