<?php
namespace Kofus\System\Form\Fieldset\Content;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Filter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MasterFieldset extends Fieldset implements InputFilterProviderInterface
{
	public function init()
	{
		$el = new Element\Text('title', array('label' => 'Title'));
		$this->add($el);
		
		$el = new Element\Textarea('content', array('label' => 'Inhalt'));
		$el->setAttribute('class', 'ckeditor');
		$this->add($el);
		
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
		        'required' => false,
		        'filters' => array($trim, $null)
		    ),
		    'content' => array(
		        'required' => true,
		        'filters' => array($trim, $null)
		    ),
		);
	}
	
	
}

