<?php

namespace Kofus\System\Listener;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\EventManager\Event;
use Kofus\System\Node\NodeInterface;



class LuceneListener extends AbstractListenerAggregate implements ListenerAggregateInterface, ServiceLocatorAwareInterface
{
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();
    	$this->listeners[] = $sharedEvents->attach('DOCTRINE', 'preUpdate', array($this, 'updateNodeDocument'));
    	$this->listeners[] = $sharedEvents->attach('DOCTRINE', 'postPersist', array($this, 'updateNodeDocument'));
    	$this->listeners[] = $sharedEvents->attach('DOCTRINE', 'preRemove', array($this, 'deleteNodeDocument'));
    }
    
    public function updateNodeDocument(Event $event)
    {
        $node = $event->getParam(0)->getEntity();
        if ($node instanceof NodeInterface) {
            $config = $this->getServiceLocator()->get('KofusConfig');
            if ($config->get('nodes.available.'.$node->getNodeType().'.search_documents'))
                $this->getServiceLocator()->get('KofusSearchService')->updateNode($node);
        }
    }
    
    public function deleteNodeDocument(Event $event)
    {
        $node = $event->getParam(0)->getEntity();
        if ($node instanceof NodeInterface) {
            $config = $this->getServiceLocator()->get('KofusConfig');
            if ($config->get('nodes.available.'.$node->getNodeType().'.search_documents'))
                $this->getServiceLocator()->get('KofusSearchService')->deleteNode($node);
        }
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