<?php

namespace Kofus\System\Listener;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\AbstractListenerAggregate;

use Zend\EventManager\Event;

use Kofus\System\Node\NodeCreatedInterface;
use Kofus\System\Node\NodeModifiedInterface;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Kofus\System\Node\RevisableNodeInterface;
use Kofus\System\Node\NodeUserCreatedInterface;




class NodeListener extends AbstractListenerAggregate implements ListenerAggregateInterface, ServiceLocatorAwareInterface
{
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();
    	$this->listeners[] = $sharedEvents->attach('DOCTRINE', 'preUpdate', array($this, 'setTimestamps'));
    	$this->listeners[] = $sharedEvents->attach('DOCTRINE', 'prePersist', array($this, 'setTimestamps'));
    	$this->listeners[] = $sharedEvents->attach('DOCTRINE', 'preUpdate', array($this, 'addNodeRevision'));
    	$this->listeners[] = $sharedEvents->attach('DOCTRINE', 'preUpdate', array($this, 'setUserAccount'));
    	$this->listeners[] = $sharedEvents->attach('DOCTRINE', 'prePersist', array($this, 'setUserAccount'));
    	
    }
    
    protected function getCurrentDateTime()
    {
        return \DateTime::createFromFormat('U', REQUEST_TIME);
    }
    
    /**
     * Deploy timestamps for NodeCreatedInterface and NodeModifiedInterface
     * @param Event $event
     */
    public function setTimestamps(Event $event)
    {
        $node = $event->getParam(0)->getEntity();
        if ($node instanceof NodeCreatedInterface && ! $node->getTimestampCreated())
        	$node->setTimestampCreated($this->getCurrentDateTime());
    	if ($node instanceof NodeModifiedInterface) {
    	    if ($node instanceof NodeCreatedInterface) {
        	    if (! $node->getTimestampCreated() || $node->getTimestampCreated()->format('Y-m-d H:i:s') != $this->getCurrentDateTime()->format('Y-m-d H:i:s'))
            	    $node->setTimestampModified($this->getCurrentDateTime());
            } else {
                $node->setTimestampModified($this->getCurrentDateTime());
        	}
    	}
        	
    }

    /**
     * Deploy user account for NodeUserCreatedInterface
     * @param Event $event
     */
    public function setUserAccount(Event $event)
    {
        $node = $event->getParam(0)->getEntity();
        if ($node instanceof NodeUserCreatedInterface && ! $node->getUserCreated()) {
            $account = $this->getServiceLocator()->get('KofusUserService')->getAccount();
            $node->setUserCreated($account);
        }
    }
    
    public function addNodeRevision(Event $event)
    {
        $node = $event->getParam(0)->getEntity();
        if ($node instanceof RevisableNodeInterface) {
            
            // Has the node just been created within this request?
            if ($node instanceof NodeCreatedInterface) {
                $created = $node->getTimestampCreated();
                $now = $this->getCurrentDateTime();
                if ($created->format('Y-m-d H:i:s') == $now->format('Y-m-d H:i:s')) {
                    return;
                }
            }
            
            
            $nodeService = $this->getServiceLocator()->get('KofusNodeService');
            $number = $nodeService->getRevisionNumber($node);
            $number += 1;
            
            foreach ($event->getParam(0)->getEntityChangeSet() as $field => $changes) {
                
                if (! $node->getFieldName($field)) continue;
                if (is_string($changes[1])) {
                    if (trim(strip_tags($changes[0])) == trim(strip_tags($changes[1])))
                        continue;
                }
                
                $newValue = $changes[1];
                $oldValue = $changes[0];
                
                $nodeService->addRevision($node, $field, $oldValue, $newValue, $number);
            }
        }
    }
    
    protected function em()
    {
        return $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
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