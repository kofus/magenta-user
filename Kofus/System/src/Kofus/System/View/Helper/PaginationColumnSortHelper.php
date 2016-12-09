<?php

namespace Kofus\System\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class PaginationColumnSortHelper extends AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $sm;

    protected function getRouteMatch()
    {
    	return $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch();    	
    }
    
    protected function buildUrl(array $attribs)
    {
    	$routeMatch = $this->getRouteMatch();
    	$params = $routeMatch->getParams();
    	if (isset($params['__CONTROLLER__']))
    		$params['controller'] = $params['__CONTROLLER__'];
    	
    	return $this->getView()->url($routeMatch->getMatchedRouteName(), $params, array('query' => array(
    			'pagination' => $attribs
    	)));    	
    }
    
    
    public function __invoke($name, $label=null, $paginator=null)
    {
    	// Init defaults
    	if (! $paginator)
    		$paginator = $this->getView()->paginator;
    	if (! $label)
    		$label = $name;
    	
     	$sortDirections = $paginator->getSortDirections();
    	if (isset($sortDirections[$name])) {
    		if ('ASC' == $sortDirections[$name]) {
    			$s = '<a href="' . $this->buildUrl(array('sort' => array($name => 'DESC'), 'paginator_id' => $paginator->getId())) . '">';
    			$s .= $this->getView()->translate($label);
    			$s .= ' <span class="glyphicon glyphicon-sort"></span>';
    			$s .= '</a>';
    			
    		} else {
    			$s = '<a href="' . $this->buildUrl(array('sort' => array($name => 'ASC'), 'paginator_id' => $paginator->getId())) . '">';
    			$s .= $this->getView()->translate($label);
    			$s .= ' <span class="glyphicon glyphicon-sort"></span>';
    			$s .= '</a>';    			
    		}
    		
    	} else {
    		$s = '<a href="' . $this->buildUrl(array('sort' => array($name => 'ASC'), 'paginator_id' => $paginator->getId())) . '">';   		
    		$s .= $this->getView()->translate($label);
    		$s .= '</a>';
    	}
    	 

    	
    	return $s;
    }
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
    	$this->sm = $serviceLocator;
    }
    
    public function getServiceLocator()
    {
    	return $this->sm->getServiceLocator();
    }
}


