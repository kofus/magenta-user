<?php
namespace Kofus\System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;



class TestController extends AbstractActionController
{
    public function triggerAction()
    {
        $type = $this->params('id');
        $logger = $this->getServiceLocator()->get('logger');
        
        switch ($type) {
            case 'exception':
                print 'trigger exception';
                throw new \Exception('test exception');
                break;
                
            case 'warning':
                print 'trigger warning';
                trigger_error('test warning', E_USER_WARNING);
                break;
                
            case 'notice':
                print 'trigger notice';
                trigger_error('test notice', E_USER_NOTICE);
                break;
                
            case 'error':
                print 'trigger fatal error';
                trigger_error('fatal error test', E_USER_ERROR);
                break;
                
            case 'deprecated':
                print 'trigger deprecated';
                trigger_error('deprecated test', E_USER_DEPRECATED);
                break;
                
            default:
                print 'trigger what?';
                
        }
        
        exit();
    }
    
}