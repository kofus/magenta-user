<?php
namespace Kofus\System\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;



class DoctrineEvents implements EventManagerAwareInterface
{
    protected $events;
    
    public function setEventManager(EventManagerInterface $events)
    {
    	$events->setIdentifiers(array('DOCTRINE', get_called_class()));
    	$this->events = $events;
    	return $this;
    }
    
    public function getEventManager()
    {
    	if (null === $this->events)
    		$this->setEventManager(new EventManager());
    	 
    	return $this->events;
    }
    
    public function __call($name, $params)
    {
        if (defined('Doctrine\ORM\Events::' . $name)) {
            $this->getEventManager()->trigger($name, $this, $params);
        } else {
            throw new \Exception('Undefined doctrine event: ' . $name);
        }
    }
    
}