<?php
namespace Kofus\System\Form\Fieldset\Relation;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Filter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RelationFieldset extends Fieldset implements InputFilterProviderInterface
{
    
    protected $specs = array();
    
    public function setSpecifications(array $specs)
    {
        $this->specs = $specs; return $this;
    }
    
    public function getSpecifications()
    {
        return $this->specs;
    }
    
	public function init()
	{
	    $fieldsetLabel = null; 
	    if (isset($this->specs['fieldset']['label']))
	        $fieldsetLabel = $this->specs['fieldset']['label'];
        $this->setLabel($fieldsetLabel);
	    
	    if (isset($this->specs['fields']['label']['label'])) {
	    	$label = $this->specs['fields']['label']['label'];
	    
    	    if (isset($this->specs['fields']['label']['value_options'])) {
                $el = new Element\Select('label', array('label' => $label));
                $el->setValueOptions($this->specs['fields']['label']['value_options']);
    	    } else {
    	        $el = new Element\Text('label', array('label' => $label));
    	    }
	    } else {
	        $el = new Element\Hidden('label');
	    }
	    if (isset($_GET['label'])) {
	        $el->setValue($_GET['label']);
	    }
		$this->add($el);
		
		$el = new Element\Number('weight', array('label' => 'Priority'));
		$this->add($el);
		
	}

	public function getInputFilterSpecification()
	{
	    $trim = new Filter\StringTrim();
	    $null = new Filter\ToNull();
	    $digits = new Filter\Digits();
		return array(
		    'label' => array(
		        'required' => false,
		        'filters' => array($trim, $null)
		    ),
		    'weight' => array(
		        'required' => false,
		    		'filters' => array($digits)
		    ),
		);
	}
	
	
}

