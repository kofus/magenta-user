<?php
namespace Kofus\System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class ErrorController extends AbstractActionController
{
    public function indexAction()
    {
    }
    
    public function permissiondeniedAction()
    {
        $this->getResponse()->setStatusCode(403);
        return new ViewModel(array(
            'displayExceptions' => $this->config()->get('view_manager.display_exceptions'),
            'routeParams' => $this->params()->fromRoute()        	
        ));
    }
    
    
    
}
