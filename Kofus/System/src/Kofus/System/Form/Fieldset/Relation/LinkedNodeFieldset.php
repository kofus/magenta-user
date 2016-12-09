<?php
namespace Kofus\System\Form\Fieldset\Relation;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Filter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LinkedNodeFieldset extends Fieldset implements InputFilterProviderInterface
{
    
    protected $nodeLabel = 'Element';
    
    public function setNodeLabel($value)
    {
        $this->nodeLabel = $value; return $this;
    }
    
    public function getNodeLabel()
    {
        return $this->nodeLabel;
    }
    
    protected $nodes = array();
    
    public function setNodes($nodes)
    {
        $this->nodes = $nodes; return $this;
    }
    
    public function getNodes()
    {
        return $this->nodes;
    }
    
	public function init()
	{
	    $valueOptions = array();
	    foreach ($this->getNodes() as $node)
	        $valueOptions[$node->getNodeId()] = (string) $node;
	    
        $el = new Element\Select('linked_node_id', array('label' => $this->getNodeLabel()));
        $el->setValueOptions($valueOptions);
        $this->add($el);
	}

	public function getInputFilterSpecification()
	{
	    $digits = new Filter\Digits();
		return array(
		    'linked_node_id' => array(
		        'required' => true,
		    ),
		);
	}
	
	
}

