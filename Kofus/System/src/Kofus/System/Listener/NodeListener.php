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
use Kofus\System\Entity\NodeRevisionEntity;



class NodeListener extends AbstractListenerAggregate implements ListenerAggregateInterface, ServiceLocatorAwareInterface
{
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();
    	$this->listeners[] = $sharedEvents->attach('DOCTRINE', 'preUpdate', array($this, 'setTimestamps'));
    	$this->listeners[] = $sharedEvents->attach('DOCTRINE', 'prePersist', array($this, 'setTimestamps'));
    	
    	$this->listeners[] = $sharedEvents->attach('DOCTRINE', 'preUpdate', array($this, 'addNodeRevision'));
    	$this->listeners[] = $sharedEvents->attach('DOCTRINE', 'postPersist', array($this, 'addFirstNodeRevision'));
    }
    
    public function setTimestamps(Event $event)
    {
        $node = $event->getParam(0)->getEntity();
        if ($node instanceof NodeModifiedInterface)
        	$node->setTimestampModified(new \DateTime());
        if ($node instanceof NodeCreatedInterface && ! $node->getTimestampCreated())
        	$node->setTimestampCreated(new \DateTime());        
    }
    
    public function addFirstNodeRevision(Event $event)
    {
        $node = $event->getParam(0)->getEntity();
        if ($node instanceof RevisableNodeInterface) {
            $fields = $this->em()->getClassMetadata(get_class($node))->getFieldNames();
            $now = new \DateTime();
            
            foreach ($fields as $field) {
                if ('id' == $field) continue;
                $qb = $this->em()->getConnection()->createQueryBuilder();
                $qb->insert('kofus_system_node_revisions')
                    ->values(array(
                        'timestamp' => '?',
                        'field' => '?',
                        'value' => '?',
                        'nodeId' => '?'
                    ))
                    ->setParameter(0, $now->format('Y-m-d H:i:s'))
                    ->setParameter(1, $field)
                    ->setParameter(2, $node->getFieldValue($field))
                    ->setParameter(3, $node->getNodeId());
                $qb->execute();
            }
        }
    }
    
    public function addNodeRevision(Event $event)
    {
        $node = $event->getParam(0)->getEntity();
        if ($node instanceof RevisableNodeInterface) {
            $now = new \DateTime();
            
            foreach ($event->getParam(0)->getEntityChangeSet() as $field => $changes) {
                $qb = $this->em()->getConnection()->createQueryBuilder();
                $qb->insert('kofus_system_node_revisions')
                    ->values(array(
                        'timestamp' => '?',
                        'field' => '?',
                        'value' => '?',
                        'nodeId' => '?'
                    ))
                    ->setParameter(0, $now->format('Y-m-d H:i:s'))
                    ->setParameter(1, $field)
                    ->setParameter(2, $changes[1])
                    ->setParameter(3, $node->getNodeId());
                $qb->execute();
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