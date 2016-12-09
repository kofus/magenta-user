<?php

namespace Kofus\User\Form\Fieldset\Acl;

use Zend\Form\Fieldset;
use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;
use Kofus\User\Entity\AuthEntity;

class TestFieldset extends Fieldset implements InputFilterProviderInterface
{
	public function init()
	{
	    $roles = array();
	    foreach ($this->getAcl()->getRoles() as $role => $parents)
	        $roles[$role] = $role;
		$el = new Element\Select('role', array('label' => 'Role'));
		$el->setValueOptions($roles);
		$this->add($el);
		
		$resources = array();
		foreach ($this->getAcl()->getResources() as $resource => $parents)
		    $resources[$resource] = $resource;
		$el = new Element\Select('resource', array('label' => 'Resource'));
		$el->setValueOptions($resources);
		$this->add($el);
		
		$el = new Element\Text('privilege', array('label' => 'Privilege'));
		$this->add($el);
		
		$el = new Element\Submit('submit', array('label' => 'Test Privilege'));
		$this->add($el);
	}
	
	public function setAcl($acl)
	{
	    $this->acl = $acl; return $this;
	}
	
	public function getAcl()
	{
	    return $this->acl;
	}

	public function getInputFilterSpecification()
	{
	    $stringTrim = new \Zend\Filter\StringTrim();
		return array(
			//'type' => array('required' => true)
		);
	}
}
