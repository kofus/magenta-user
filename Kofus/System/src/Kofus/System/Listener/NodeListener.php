<?php

namespace Kofus\System\Listener;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\AbstractListenerAggregate;

use Zend\EventManager\Event;

use Kofus\System\Node\NodeCreatedInterface;
use Kofus\System\Node\NodeModifiedInterface;



class NodeListener extends AbstractListenerAggregate implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();
    	$this->listeners[] = $sharedEvents->attach('DOCTRINE', 'preUpdate', array($this, 'setTimestamps'));
    	$this->listeners[] = $sharedEvents->attach('DOCTRINE', 'postPersist', array($this, 'setTimestamps'));
    	//$this->listeners[] = $sharedEvents->attach('DOCTRINE', 'preRemove', array($this, 'deleteNodeDocument'));
    }
    
    public function setTimestamps(Event $event)
    {
        $node = $event->getParam(0)->getEntity();
        if ($node instanceof NodeModifiedInterface)
        	$node->setTimestampModified(new \DateTime());
        if ($node instanceof NodeCreatedInterface && ! $node->getTimestampCreated())
        	$node->setTimestampCreated(new \DateTime());        
    }
    
}