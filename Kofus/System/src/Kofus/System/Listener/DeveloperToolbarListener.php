<?php
namespace Kofus\System\Listener;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\AbstractListenerAggregate;

class DeveloperToolbarListener extends AbstractListenerAggregate implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();
        $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, array($this, 'renderToolbar'));
    }
    
    public function renderToolbar(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $userService = $sm->get('KofusUserService');
        
        
        $html = '<div class="navbar navbar-inverse navbar-fixed-bottom">';
        $html .= '<ul class="navbar-nav">';
        $html .= '<li>' . $userService->getAccount() . '</li>';
        $html .= '<li>' . $userService->getAuth() . '</li>';
        $html .= '</ul>';
        $html .= '</div>';
        
        $body = $e->getApplication()->getResponse()->getContent();
        $body .= $html;
        $e->getApplication()->getResponse()->setContent($body);
        
    }
    
    
    
    
}