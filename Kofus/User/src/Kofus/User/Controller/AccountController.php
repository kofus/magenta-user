<?php
namespace Kofus\User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AccountController extends AbstractActionController
{
    public function listAction()
    {
        $this->archive()->uriStack()->push();
        
        $entities = $this->nodes()->getRepository('U')->findby(array(), array('timestampCreated' => 'DESC'));
        $paginator = $this->paginator($entities);
        return new ViewModel(array(
        	'paginator' => $paginator
        ));
    }
    
    public function loginasAction()
    {
        
    }
}
