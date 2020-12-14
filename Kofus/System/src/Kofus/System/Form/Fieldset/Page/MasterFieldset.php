<?php
namespace Kofus\System\Form\Fieldset\Page;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Filter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MasterFieldset extends Fieldset implements InputFilterProviderInterface, ServiceLocatorAwareInterface
{
	public function init()
	{
	    $this->setLabel('Page');
	    
		$el = new Element\Text('title', array('label' => 'Title'));
		$this->add($el);
		
		$el = new Element\Textarea('content', array('label' => 'Content'));
		$el->setAttribute('class', 'ckeditor');
		$this->add($el);
		
		$templates = $this->config()->get('nodes.available.PG.templates', array());
		if ($templates) {
            $el = new Element\Select('template', array('label' => 'Template'));
            $el->setValueOptions($templates);
            $this->add($el);
		}
		
		$el = new Element\Checkbox('enabled', array('label' => 'enabled?'));
		//$el->setOption('help-block', 'FF');
		$this->add($el);
	}

	public function getInputFilterSpecification()
	{
	    $trim = new Filter\StringTrim();
	    $null = new Filter\ToNull();
		return array(
		    'title' => array(
		        'required' => true,
		        'filters' => array($trim, $null)
		    ),
		    'content' => array(
		        'required' => false,
		        'filters' => array($trim, $null)
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
	
	protected function config()
	{
	    return $this->getServiceLocator()->get('KofusConfig');
	}
	
	
	
}

