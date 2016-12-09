<?php
namespace Kofus\User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Storage\Session as Storage;

class AuthController extends AbstractActionController
{
     public function listAction()
     {
         $this->archive()->uriStack()->push();
         $account = $this->nodes()->getNode($this->params('id', 'U'));
         
         $entities = $this->nodes()->getRepository('AUTH')->findBy(array('account' => $account));
         
         return new ViewModel(array(
         	'entities' => $entities,
             'account' => $account
         ));
     }
     
     public function executeAction()
     {
         $auth = $this->nodes()->getNode($this->params('id', 'AUTH'));
         $storage = new Storage();
         $storage->write($auth->getNodeId());
         return $this->redirect()->toRoute('index');
     }
}
