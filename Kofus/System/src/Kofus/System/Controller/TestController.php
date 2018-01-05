<?php
namespace Kofus\System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;



class TestController extends AbstractActionController
{
    public function exceptionAction()
    {
        throw new \Exception('Test Exception');
    }
    
}