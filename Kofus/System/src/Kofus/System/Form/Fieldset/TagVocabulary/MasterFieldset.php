<?php
namespace Kofus\System\Form\Fieldset\TagVocabulary;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;



class MasterFieldset extends Fieldset implements InputFilterProviderInterface
{
	public function init()
	{
		$el = new Element\Text('title', array('label' => 'Title'));
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
		);
	}
	
	
}

