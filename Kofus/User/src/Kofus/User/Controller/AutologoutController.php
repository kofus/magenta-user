<?php
namespace Kofus\User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;



class AutologoutController extends AbstractActionController
{
    public function indexAction()
    {
        $params = $this->user()->getAutologoutParam();
        return new JsonModel($params);
    }
    
    public function logoutAction()
    {
        $account = $this->user()->getAccount();
        if ($account) {
            $this->user()->logout();
            $this->flashMessenger()->addSuccessMessage('Ihre Sitzung wurde aufgrund längerer Inaktivität beendet. Bitte melden Sie sich erneut an.');
        }
        return $this->redirect()->toUrl($this->archive()->uriStack()->pop());
            
    }
    
    public function heartbeatAction()
    {
        return new JsonModel(array(
            'heartbeat' => $this->user()->triggerAutologoutHeartbeat()
        ));
    }
    
 
    
    
    
}