<?php

namespace Kofus\System\Form\Element;

use Zend\ModuleManager\Feature\ViewHelperProviderInterface;

class Html extends \Zend\Form\Element
{
    public function setHtml($value)
    {
        $this->html = $value; return $this;
    }
	
    public function getHtml()
    {
        return $this->html;
    }
    
    
}
