<?php

namespace Kofus\System\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Kofus\System\Paginator\Adapter\NodeAdapter;
use Kofus\System\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Session\Container;


class PaginatorPlugin extends AbstractPlugin
{
    protected $plugin;
    
    protected function createId()
    {
    	$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    	array_shift($backtrace);    	
    	array_shift($backtrace);
    	array_shift($backtrace);
    	$callData = array_shift($backtrace);
    	return md5(serialize($callData));
    }
    
    public function __invoke($mixed, array $options=array())
    {
    	// Determine data type for pagination
        $dataType = 'Array';
        if (is_object($mixed)) $dataType = get_class($mixed);
        
        // Determine paginator id
        if (! isset($options['id']))
        	$options['id'] = $this->createId();
        $paginatorId = $options['id'];
 
        // Fetch params
        $params = $this->getController()->params()->fromQuery('pagination', array());   
        $sortDirections = array();     
        
        // Init session
        $session = new Container('Pagination');
        $sessionData = array();
        if (isset($session->$paginatorId))
        	$sessionData = $session->$paginatorId;
        
        switch ($dataType) {
        	case 'Doctrine\ORM\QueryBuilder':
				
        		// Sort submitted?
      	    	if (isset($params['paginator_id']) && $params['paginator_id'] == $paginatorId
        				&& isset($params['sort']) && isset($options['sort_columns'])) {
      	    			foreach ($params['sort'] as $name => $direction) {
      	    				if (isset($options['sort_columns'][$name])) 
      	    					$sortDirections[$name] = $direction;
   	    				
      	    			}
      	    			$sessionData['sortDirections'] = $sortDirections;
      	    			$sessionData['page'] = null;
      	    	} elseif (isset($sessionData['sortDirections'])) {
      	    		$sortDirections = $sessionData['sortDirections'];
      	    	}
      	    	
      	    	foreach ($sortDirections as $name => $direction)
      	    		$mixed->orderBy($options['sort_columns'][$name], $direction);

   	    		$paginator = new Paginator(new NodeAdapter($mixed));
   	    		$paginator->setId($paginatorId);
   	    		$paginator->setSortDirections($sortDirections);
      	    		
      	    	// Pagination?
   	    		if (isset($params['paginator_id']) && $params['paginator_id'] == $paginatorId && isset($params['page'])) {  	
   	    			$paginator->setCurrentPageNumber((int) $params['page']);
   	    			$sessionData['page'] = (int) $params['page'];
      	    	} elseif (isset($sessionData['page'])) {
      	    		$paginator->setCurrentPageNumber($sessionData['page']);
      	    	}
 
        	    break;
        	    
        	case 'Array':
        	    $paginator = new Paginator(new ArrayAdapter($mixed));
        	    $paginator->setId($paginatorId);
        	    
        	    // Pagination?
        	    if (isset($params['paginator_id']) && $params['paginator_id'] == $paginatorId && isset($params['page'])) {
        	    	$paginator->setCurrentPageNumber((int) $params['page']);
        	    	$sessionData['page'] = (int) $params['page'];
        	    } elseif (isset($sessionData['page'])) {
        	    	$paginator->setCurrentPageNumber($sessionData['page']);
        	    }        	    
        	    break;
        	    
        	default:
        	    throw new \Exception('No pagination adapter found for ' . $dataType);
        }
        
		$session->$paginatorId = $sessionData;
        //$session->setExpirationSeconds(60);
        

        return $paginator;
	}

}