<?php

namespace Kofus\System\View\Helper;
use Zend\View\Helper\AbstractHelper;

class BodyTagHelper extends AbstractHelper
{
	protected $css = array();
	protected $html = array();
	
    public function __invoke()
    {
    	return $this;
    }
    
    public function addCss($className)
    {
    	$this->css[] = $className; return $this;
    }
    
    public function appendHtml($html, $position='beforeClose')
    {
        $this->html[$position][] = $html;
    }
    
    public function getClassNames()
    {
    	return $this->css;
    }
    
    public function openTag()
    {
    	$s = '<body';
    	if ($this->css) {
    		$css = implode(' ', $this->css);
    		$s .= ' class="' . $this->view->escapeHtmlAttr($css) . '"';
    	}
    	 
    	return $s . '>';
    }
    
    public function closeTag()
    {
        $html = '';
        if (isset($this->html['beforeClose']))
            $html .= implode("\n", $this->html['beforeClose']);    
        
        $html .= "\n</body>";
        
    	return $html;
    }
    
    public function __toString()
    {
    	return $this->openTag();
    }
}