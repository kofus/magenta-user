<?php

namespace Kofus\System\Form\Element\Immutable;

use Traversable;
use Zend\InputFilter\InputProviderInterface;
use Zend\Form\Element\Hidden as DefaultElement;

class Hidden extends DefaultElement
{
	
    public function setValue($value)
    {
    	return $this;
    }
    
    public function setImmutableValue($value)
    {
        parent::setValue($value);
    }
    
    
}
