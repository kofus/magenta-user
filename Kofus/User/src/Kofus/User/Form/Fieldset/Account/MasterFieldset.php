<?php

namespace Kofus\User\Form\Fieldset\Account;

use Zend\Form\Fieldset;
use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MasterFieldset extends Fieldset implements InputFilterProviderInterface, ServiceLocatorAwareInterface
{
	public function init()
	{
		$el = new Element\Text('name', array('label' => 'Display Name'));
		$this->add($el);
		
		$el = new Element\Select('status', array('label' => 'Status'));
		$el->setValueOptions(\Kofus\User\Entity\AccountEntity::$STATUS);
		$this->add($el);
		
		$roles = array_keys($this->getServiceLocator()->get('KofusConfigService')->get('user.acl.roles'));
		$el = new Element\Select('role', array('label' => 'Role'));
		$el->setValueOptions(array_combine($roles, $roles));
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
	
	protected $sm;
	
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
		$this->sm = $serviceLocator;
	}
	
	public function getServiceLocator()
	{
		return $this->sm;
	}
	
}
