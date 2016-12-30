<?php

namespace Kofus\System\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class NodeNavigationHelper extends AbstractHelper
{
    protected $nodeType;
    protected $node;
    
    public function __invoke($nodeOrType, $action='list', $label=null)
    {
        if ($nodeOrType instanceof \Kofus\System\Node\NodeInterface) {
            $this->node = $nodeOrType;
            $this->nodeType = $nodeOrType->getNodeType();
        } else {
    	   $this->nodeType = $nodeOrType;
        }
    	$this->action = $action;
    	$this->label = $label;
    	return $this;
    }
    
    public function render()
    {
    	$config = $this->getView()->config()->get('nodes.available.' . $this->nodeType);
    	if (! $config) return;
    	if (! isset($config['navigation'][$this->action]))
    		return;
    	 
    	$pages = $config['navigation'][$this->action]; 
    	
    	$nav = new \Zend\Navigation\Navigation($pages);
    	
    	if ($this->node) {
        	foreach ($nav as $page) {
        	    if ($page->get('params')) {
        	        $params = $page->get('params');
        	        foreach ($params as $key => $value) {
        	            if ($value == '{node_id}') $params[$key] = $this->node->getNodeId();
        	            if ($value == '{node_type}') $params[$key] = $this->node->getNodeType();
        	        }
        	        $page->set('params', $params);
        	    }
        	}
    	}
    	
    	$s = '<nav class="navbar navbar-default">';
    	
    	if ($this->label === null && isset($config['label_pl']))
    	    $this->label = $config['label_pl'];
    	
    	if ($this->label) {
    		$s .= '<div class="navbar-brand">';
    		$s .= $this->getView()->escapeHtml($this->getView()->translate($this->label));
    		$s .= '</div>';
    	}
    	$s .= '<div class="container-fluid">';
    	$s .= $this->getView()->navigation()
    		->menu($nav)
    		->setUlClass('nav navbar-nav navbar-right')
    		->setPartial('kofus/layout/admin/navbar.phtml')
    		
    	;
    	$s .= '</div>';
    	$s .= '</nav>';
    		
    	
    	return $s;
    }
}


