<?php

namespace Kofus\Archive\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class EventController extends AbstractActionController
{
    public function listAction()
    {
        $namespace = 'events';
        $this->archive()->uriStack()->push();
        $results = $this->archive()->events($namespace)->getEvents();
        $paginator = $this->paginator($results);
    	return new ViewModel(array(
    		'paginator' => $paginator
    	));
    }
    
    public function viewAction()
    {
        return new ViewModel(array(
        	'http' => $this->archive()->http($this->params('namespace'))->getHttp($this->params('id'))
        ));
    }
    
    
}
