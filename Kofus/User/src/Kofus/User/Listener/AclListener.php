<?php

namespace Kofus\User\Listener;

use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Kofus\System\Exception;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\RouteMatch;

class AclListener implements ListenerAggregateInterface
{
    protected $listeners = array();
    
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'onRoute'), 100);
    }
    
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener))
                unset($this->listeners[$index]);
        }        
    }
    
    protected function validate($resource, $action)
    {
        if (! $this->userService->isAllowed($resource, $action)) {
            $this->triggerPermissionDenied($resource, $action);
            return false;
        }
        return true;
        
    }
    
    protected function triggerPermissionDenied($resource, $action)
    {
        // Error: Permission denied
        $routeParams = array(
        		'controller' => 'Kofus\System\Controller\Error',
        		'action' => 'permissiondenied',
        		'matched_role' => $this->userService->getRole(),
        		'matched_resource' => $resource,
        		'matched_action' => $action
        );
        if ($this->r->getParam('language'))
        	$routeParams['language'] = $this->r->getParam('language');
        if ($this->r->getParam('locale'))
        	$routeParams['locale'] = $this->r->getParam('locale');
        $this->r = new RouteMatch($routeParams);
        $this->r->setMatchedRouteName('error');
        $this->e->setRouteMatch($this->r);
    }
    
    public function onRoute(EventInterface $e)
    {
        // Init
        $this->e = $e;
        $sm = $this->e->getApplication()->getServiceManager();
        $config = $sm->get('KofusConfig');
        $this->userService = $sm->get('KofusUserService');
        $this->r = $this->e->getRouteMatch();
        
        // Handling for special routes
        switch ($this->r->getMatchedRouteName()) {
        	case 'register':
            case 'forgotpassword':
        	case 'login':
                if (! $this->userService->getAuth())
                	return;
                	
            case 'logout':
                if ($this->userService->getAuth())
                    return;
                
            case 'kofus_system':
                if ('Kofus\System\Controller\Node' == $this->r->getParam('controller')) {
                    $action = $this->r->getParam('action');
                    $resource = \Zend\Filter\StaticFilter::execute($this->r->getParam('id'), 'Alpha');
                    return $this->validate($resource, $action);
                }
                if ('Kofus\System\Controller\Relation' == $this->r->getParam('controller')) {
                	$action = $this->r->getParam('action');
                	if ('edit' == $action) {
                		$nodes = $sm->get('KofusNodeService');
                		$relation = $nodes->getRelation($this->r->getParam('id'));
                		$this->validate($relation->getType(), 'edit');
                		$this->validate($_GET['edit'], 'edit');
                		return;
                	}
                	if ('delete' == $action) {
                	    $nodes = $sm->get('KofusNodeService');
                	    $relation = $nodes->getRelation($this->r->getParam('id'));
                	    $this->validate($relation->getType(), 'delete');
                	    if (isset($_GET['delete']))
                	       $this->validate($_GET['delete'], 'delete');
                	    return;
                	}
                	if ('add' == $action) {
                		$nodes = $sm->get('KofusNodeService');
                		$relType = \Zend\Filter\StaticFilter::execute($this->r->getParam('id'), 'Alpha');
                		$relType .= '_' . $_GET['add'];
                		$this->validate($relType, 'add');
                		$this->validate($_GET['add'], 'add');
                		return;
                	}
                }
        }
        
        // Determine "resource" and "action"
        $resource = $this->r->getParam('controller'); 
        $action = $this->r->getParam('action');

        
        // Is allowed? => done
        if ($this->userService->isAllowed($resource, $action))
            return;
        
        // Not allowed and not logged in? => redirect to login page
        if (! $this->userService->getAuth()) {
        	if (! in_array($this->r->getMatchedRouteName(), array('kofus_media_image', 'kofus_media_pdf'))) {
	        	$routeParams = array(
	            	'controller' => 'Application\Controller\Auth',
	            	'action' => 'login',
	        	);
	        	if ($this->r->getParam('language'))
	        		$routeParams['language'] = $this->r->getParam('language');
	        	if ($this->r->getParam('locale'))
	        		$routeParams['locale'] = $this->r->getParam('locale');
	        	 
	            $this->r = new RouteMatch($routeParams);
	            $this->r->setMatchedRouteName('login');
	            $this->e->setRouteMatch($this->r);
	            
	            return;
        	}
        }
        
        return $this->triggerPermissionDenied($resource, $action);
   }    
}