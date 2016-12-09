<?php

namespace Kofus\Archive\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class HttpController extends AbstractActionController
{
    public function listAction()
    {
        $this->archive()->uriStack()->push();
        $results = $this->archive()->http($this->params('namespace'))->getRequests();
        $paginator = $this->paginator($results);
    	return new ViewModel(array(
    		'paginator' => $paginator,
    	    'namespace' => $this->params('namespace')
    	));
    }
    
    public function viewAction()
    {
        return new ViewModel(array(
        	'http' => $this->archive()->http($this->params('namespace'))->getHttp($this->params('id'))
        ));
    }
    
    public function bodytextAction()
    {
        $mail = $this->archive()->mails()->getMail($this->params('id')); 
        return $this->getResponse()->setContent($mail->getBodyText());
    }
    
}
