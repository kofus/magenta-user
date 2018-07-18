<?php
namespace Kofus\System\Form\Fieldset\Page;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Filter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PriorityFieldset extends Fieldset implements InputFilterProviderInterface
{
	public function init()
	{
		$el = new Element\Number('priority', array('label' => 'Priorität'));
		$el->setOption('help-block', 'Element werden nach Priorität aufsteigend sortiert');
		$this->add($el);
	}

	public function getInputFilterSpecification()
	{
		return array(
		    'priority' => array(
		        'required' => false,
		    ),
		);
	}
	
	
}

