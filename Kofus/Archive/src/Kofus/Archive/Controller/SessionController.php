<?php

namespace Kofus\Archive\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class SessionController extends AbstractActionController
{
    public function listAction()
    {
        $this->archive()->uriStack()->push();
        $results = $this->archive()->sessions()->getSessions();
        $paginator = $this->paginator($results);
    	return new ViewModel(array(
    		'paginator' => $paginator,
    	));
    }
    
    public function deleteAction()
    {
    	unlink('data/archive/sessions/sessions.db');
    	return $this->redirect()->toRoute('kofus_archive', array('controller' => 'session', 'action' => 'list'), true);
    }
    
    
}
