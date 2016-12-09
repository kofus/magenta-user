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
    
    public function __invoke($data, $hash=null)
    {
        $class = 'Array';
        if (is_object($data))
            $class = get_class($data);
        
        switch ($class) {
        	case 'Doctrine\ORM\QueryBuilder':
        	    $paginator = new Paginator(new NodeAdapter($data));
        	    if (! $hash)
        	       $hash = md5(serialize($data->getQuery()->getSql()));
        	    $paginator->setHash($hash);
        	    break;
        	    
        	case 'Array':
        	    $paginator = new Paginator(new ArrayAdapter($data));
        	    if (! $hash)
        	       $hash = md5(serialize($data));
        	    $paginator->setHash($hash);
        	    break;
        	    
        	default:
        	    throw new \Exception('No pagination adapter found for ' . $class);
        }
        
        $session = new Container('Pagination');
        //$session->setExpirationSeconds(60);
        
        $params = $this->getController()->params()->fromQuery('pagination', array());
        if (isset($params['hash']) && isset($params['page']) && $params['hash'] == $hash) {
            $paginator->setCurrentPageNumber((int) $params['page']);
            $session->$hash = (int) $params['page'];
            
        } else {
            if (isset($session->$hash))
                $paginator->setCurrentPageNumber($session->$hash);
        }
        
        return $paginator;
	}

}