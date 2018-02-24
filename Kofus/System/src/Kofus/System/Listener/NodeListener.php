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
    }
    
    public function setTimestamps(Event $event)
    {
        $node = $event->getParam(0)->getEntity();
        if ($node instanceof NodeModifiedInterface)
        	$node->setTimestampModified(new \DateTime());
        if ($node instanceof NodeCreatedInterface && ! $node->getTimestampCreated())
        	$node->setTimestampCreated(new \DateTime());        
    }
    
    public function addNodeRevision(Event $event)
    {
        $node = $event->getParam(0)->getEntity();
        if ($node instanceof RevisableNodeInterface) {
            $now = \DateTime::createFromFormat('U', REQUEST_TIME);
            $nodeService = $this->getServiceLocator()->get('KofusNodeService');
            $number = $nodeService->getRevisionNumber($node);
            $number += 1;
            
            foreach ($event->getParam(0)->getEntityChangeSet() as $field => $changes) {
                
                if (! $node->getFieldName($field)) continue;
                if (is_string($changes[1])) {
                    if (trim(strip_tags($changes[0])) == trim(strip_tags($changes[1])))
                        continue;
                }
                
                $value = $changes[0];
                if (is_array($value))
                    $value = implode('; ', $value);
                
                $qb = $this->em()->getConnection()->createQueryBuilder();
                $qb->insert('kofus_system_node_revisions')
                ->values(array(
                    'timestamp' => '?',
                    'field' => '?',
                    'value' => '?',
                    'nodeId' => '?',
                    'number' => '?'
                ))
                ->setParameter(0, $now->format('Y-m-d H:i:s'))
                ->setParameter(1, $field)
                ->setParameter(2, $value)
                ->setParameter(3, $node->getNodeId())
                ->setParameter(4, $number)
                ;
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