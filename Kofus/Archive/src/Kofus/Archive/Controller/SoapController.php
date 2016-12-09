<?php

namespace Kofus\Archive\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class SoapController extends AbstractActionController
{
    public function listAction()
    {
        $this->archive()->uriStack()->push();
        $results = $this->archive()->soap($this->params('namespace'))->getRequests();
        $paginator = $this->paginator($results);
    	return new ViewModel(array(
    		'paginator' => $paginator,
    	    'namespace' => $this->params('namespace')
    	));
    }
    
    public function viewAction()
    {
        return new ViewModel(array(
        	'soap' => $this->archive()->soap($this->params('namespace'))->getSoap($this->params('id'))
        ));
    }
    
}
