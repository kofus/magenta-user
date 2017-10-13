<?php
namespace Kofus\System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;


class RedirectController extends AbstractActionController
{
    public function indexAction()
    {
        return $this->redirect()->toUrl($this->params('url'));
    }
}
