<?php

namespace Kofus\System\View\Helper\Form;
use Zend\View\Helper\AbstractHelper;

class HtmlHelper extends AbstractHelper
{
    protected $el;
    
    public function __invoke($el)
    {
    	$this->el = $el; return $this;
    }
    
    public function __toString()
    {
        return $this->el->getHtml();
    }
    
}