<?php
namespace Kofus\Archive\Listener;
use Zend\EventManager\Event;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class EventListener extends AbstractListenerAggregate implements ListenerAggregateInterface, ServiceLocatorAwareInterface
{
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();
        $this->listeners[] = $sharedEvents->attach('KOFUS_USER', '*', array($this, 'handleUserEvent'));
        $this->listeners[] = $sharedEvents->attach('KOFUS_MAILER', 'send', array($this, 'handleMailEvent'));
        
    }
    
    public function handleUserEvent(Event $e)
    {
        $archive = $this->getServiceLocator()->get('KofusArchive');
        $archive->events()->add(
            'KOFUS_USER', 
            $e->getName(),
            $e->getParams()
        );
    }
    
    public function handleMailEvent(Event $e)
    {
        $msg = $e->getParam(0);
    	$archive = $this->getServiceLocator()->get('KofusArchive');
    	$archive->mails()->add($msg);
    }
    
    
    protected $sm;
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
    	$this->sm = $serviceLocator;
    }
    
    public function getServiceLocator()
    {
    	return $this->sm;
    }
    
    
    
}