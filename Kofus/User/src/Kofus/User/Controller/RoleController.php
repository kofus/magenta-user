<?php
namespace Kofus\User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class RoleController extends AbstractActionController
{
    public function listAction()
    {
        $this->archive()->uriStack()->push();
        
        $entities = $this->nodes()->getRepository('UR')->findAll();
        
        return new ViewModel(array(
        	'entities' => $entities
        ));
    }
    
}
