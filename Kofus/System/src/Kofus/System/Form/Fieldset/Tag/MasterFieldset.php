<?php
namespace Kofus\System\Form\Fieldset\Tag;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;



class MasterFieldset extends Fieldset implements InputFilterProviderInterface, ServiceLocatorAwareInterface
{
	public function init()
	{
		$el = new Element\Text('title', array('label' => 'Title'));
		$this->add($el);
		
		$valueOptions = array();
		foreach ($this->nodes()->getRepository('TAGV')->findAll() as $node)
		    $valueOptions[$node->getNodeId()] = $node->getTitle();
		$el = new Element\Select('vocabulary', array('label' => 'Vokabular'));
		$el->setValueOptions($valueOptions);
		$this->add($el);
		
	}

	public function getInputFilterSpecification()
	{
		return array(
		    'title' => array(
		        'required' => true,
		        'filters' => array(
		            array('name' => 'stringtrim')
		        )
		    ),
		    'vocabulary' => array(
		        'required' => true
		    )
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
	
	protected function nodes()
	{
	    return $this->getServiceLocator()->get('KofusNodeService');
	}
	
	
	
}

