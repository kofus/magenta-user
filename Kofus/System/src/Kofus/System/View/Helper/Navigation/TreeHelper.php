<?php

namespace Kofus\System\View\Helper\Navigation;
use Zend\View\Helper\AbstractHelper;



class TreeHelper extends AbstractHelper
{
	protected $collapseLevel = 1;
	protected $glyphiconCollapsed = 'glyphicon glyphicon-menu-down';
	protected $glyphiconExpanded = 'glyphicon glyphicon-menu-right';
	protected $glyphiconLeaf = 'glyphicon';
	
	public function setCollapseLevel($value)
	{
	    $this->collapseLevel = $value; return $this;
	}
	
	public function getCollapseLevel()
	{
	    return $this->collapseLevel;
	}
	
    public function __invoke($container)
    {
        $this->container = $container;
    	return $this;;
    }
    
    public function __toString()
    {
        $html = '<div class="tree">';
        $html .= $this->renderPages($this->container->getPages());
        $html .= '</div>';
        return $html;
    }
    
    protected function renderPages($pages, $level=0)
    {
        if (is_array($pages))
            $pages = new \Zend\Navigation\Navigation($pages);
    	$html = '<ul>';
    	$alpha = new \Zend\I18n\Filter\Alpha();
    	foreach ($pages as $page) {
    		$children = $page->getPages();
    		$isCollapsed = ($level > $this->collapseLevel);
    		$hasChildren = (bool) count($children);
    		
    		$html .= '<li';
    		if ($isCollapsed) $html .= ' style="display: none"';
    		if ($page->get('enabled') === false)
    			$html .= ' class="not-published"';
    		$html .= '>';
    		
    		$html .= '<span>';
    		
    		// glyphicon
    		if (! $hasChildren) {
    			$glyphicon = $this->glyphiconLeaf;
    		} elseif ($isCollapsed) {
    			$glyphicon = $this->glyphiconCollapsed; 
    		} else {
    			$glyphicon = $this->glyphiconExpanded; 
    		}
    		
    		$html .= '<i class="' . $glyphicon . '"></i> ';
    		$html .= $page->getLabel();
    		$html .= '</span> ';
    		
    		$resource = \Zend\Filter\StaticFilter::execute($page->get('node-id'), 'Alpha');
    		
    		if ($page->get('route')) {
    			$params = array();
    			if ($page->get('controller'))
    				$params['controller'] = $page->get('controller');
    			if ($page->get('action'))
    				$params['action'] = $page->get('action');
    			if ($page->get('node-id'))
    				$params['id'] = $page->get('node-id');
    			$href = $this->getView()->url($page->get('route'), $params, true);
    			$html .= '<a href="'.$href.'">&nbsp;<i class="glyphicon glyphicon-arrow-right"></i></a>';
    			 
    		} elseif ($page->get('uri')) {
    			$href = $page->get('uri');
    			$html .= '<a href="'.$href.'">&nbsp;<i class="glyphicon glyphicon-arrow-right"></i></a>';
    			     			
    		}
    		
    		if ($hasChildren)
    			$html .= $this->renderPages($children, $level + 1);
    		
    		
    		$html .= '</li>';
    	}
    	$html .= '</ul>';
    	return $html;
    }
}