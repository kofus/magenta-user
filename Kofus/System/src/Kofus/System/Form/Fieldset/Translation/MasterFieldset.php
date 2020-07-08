<?php
namespace Kofus\System\Form\Fieldset\Translation;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Kofus\System\Form\Element\NodeSelect;



class MasterFieldset extends Fieldset implements InputFilterProviderInterface, ServiceLocatorAwareInterface
{
	public function init()
	{
		$el = new Element\Text('msg_id', array('label' => 'MSG-ID'));
		$el->setAttribute('readonly', 'readonly');
		$this->add($el);
		
		$el = new Element\Textarea('value', array('label' => 'Übersetzung'));
		$this->add($el);
		
		$el = new Element\Text('locale', array('label' => 'Locale'));
		$el->setAttribute('readonly', 'readonly');
		$this->add($el);
		
		$el = new Element\Text('text_domain', array('label' => 'Kontext'));
		$el->setAttribute('readonly', 'readonly');
		$this->add($el);
		
	}

	public function getInputFilterSpecification()
	{
		return array(
		    'value' => array(
		        'required' => true,
		        'filters' => array(
		            array('name' => 'stringtrim'),
		            array('name' => 'tonull')
		        )
		    ),
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

