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
use Kofus\System\Node\NodeInterface;



class NodeListener extends AbstractListenerAggregate implements ListenerAggregateInterface, ServiceLocatorAwareInterface
{
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();
    	$this->listeners[] = $sharedEvents->attach('DOCTRINE', 'preUpdate', array($this, 'setTimestamps'));
    	$this->listeners[] = $sharedEvents->attach('DOCTRINE', 'prePersist', array($this, 'setTimestamps'));
    	
    	$this->listeners[] = $sharedEvents->attach('DOCTRINE', 'postUpdate', array($this, 'updateNodeDocument'));
    	$this->listeners[] = $sharedEvents->attach('DOCTRINE', 'postPersist', array($this, 'updateNodeDocument'));
    	
    	$this->listeners[] = $sharedEvents->attach('DOCTRINE', 'preRemove', array($this, 'deleteNodeDocument'));
    	 
    }
    
    public function setTimestamps(Event $event)
    {
        $node = $event->getParam(0)->getEntity();
        if ($node instanceof NodeModifiedInterface)
        	$node->setTimestampModified(new \DateTime());
        if ($node instanceof NodeCreatedInterface && ! $node->getTimestampCreated())
        	$node->setTimestampCreated(new \DateTime());        
    }
    
    public function updateNodeDocument(Event $event)
    {
    	$node = $event->getParam(0)->getEntity();
    	if ($node instanceof NodeInterface) {
    	    print 'update node';
    		$config = $this->getServiceLocator()->get('KofusConfig');
    		if ($config->get('nodes.available.'.$node->getNodeType().'.search_documents'))
    			$this->getServiceLocator()->get('KofusLuceneService')->updateNode($node);
    	}
    }
    
    public function deleteNodeDocument(Event $event)
    {
    	$node = $event->getParam(0)->getEntity();
    	if ($node instanceof NodeInterface) {
    		$config = $this->getServiceLocator()->get('KofusConfig');
    		if ($config->get('nodes.available.'.$node->getNodeType().'.search_documents'))
    			$this->getServiceLocator()->get('KofusLuceneService')->deleteNode($node);
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