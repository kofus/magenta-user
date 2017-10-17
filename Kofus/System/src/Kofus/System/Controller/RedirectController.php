<?php
namespace Kofus\System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;


class RedirectController extends AbstractActionController
{
    public function indexAction()
    {
        if (! $this->params('url'))
            throw new \Exception('Parameter "url" must be provided for redirect');
        return $this->redirect()->toUrl($this->params('url'));
    }
}
