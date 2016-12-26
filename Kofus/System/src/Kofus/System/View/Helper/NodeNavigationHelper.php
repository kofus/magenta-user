<?php

namespace Kofus\System\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class NodeNavigationHelper extends AbstractHelper
{
    protected $nodeType;
    
    public function __invoke($nodeType, $action='list')
    {
    	$this->nodeType = $nodeType;
    	$this->action = $action;
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
    	
    	$s = '<nav class="navbar navbar-default">';
    	if (isset($config['label_pl'])) {
    		$s .= '<div class="navbar-brand">';
    		$s .= $this->getView()->escapeHtml($this->getView()->translate($config['label_pl']));
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


