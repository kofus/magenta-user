<?php

namespace Kofus\User\Form\Fieldset\Auth;

use Zend\Form\Fieldset;
use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;
use Kofus\User\Entity\AuthEntity;

class MasterFieldset extends Fieldset implements InputFilterProviderInterface
{
	public function init()
	{
		$el = new Element\Select('type', array('label' => 'Authentication Method'));
		$el->setValueOptions(AuthEntity::$TYPES);
		$this->add($el);
		
		$el = new Element\Text('identity', array('label' => 'Identity'));
		$this->add($el);
		
		$el = new Element\Password('password', array('label' => 'Password'));
		$this->add($el);
		
		$el = new Element\Password('password2', array('label' => 'Repeat Password'));
		$this->add($el);
		
		
		
	}

	public function getInputFilterSpecification()
	{
	    $stringTrim = new \Zend\Filter\StringTrim();
		return array(
			'type' => array('required' => true)
		);
	}
}
