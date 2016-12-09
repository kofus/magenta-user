<?php

namespace Kofus\WebService\PayPalPlus\Form\Fieldset\Experience;

use Zend\Form\Fieldset;
use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;

class MasterFieldset extends Fieldset implements InputFilterProviderInterface
{
	public function init()
	{
	    $this->setName('master');
	    
	    $el = new Element\Text('id', array('label' => 'ID'));
	    $el->setAttribute('disabled', 'disabled');
	    $this->add($el);
	    
		$el = new Element\Text('name', array('label' => 'Name'));
		$el->setOption('help-block', 'Name of the web experience profile. Unique among only the profiles for a given merchant.');
		$this->add($el);
		
	}

	public function getInputFilterSpecification()
	{
	    $trim = new \Zend\Filter\StringTrim();
	    $null = new \Zend\Filter\ToNull();
	    $uri = new \Zend\Validator\Uri(array('allowRelative' => false));
	    
		return array(
			'name' => array('required' => true, 'filters' => array($trim, $null)),
		);
	}
}
