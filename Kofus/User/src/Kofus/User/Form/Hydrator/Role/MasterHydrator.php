<?php
namespace Kofus\User\Form\Hydrator\Role;

use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MasterHydrator implements HydratorInterface, ServiceLocatorAwareInterface
{
    public function extract($object)
    {
        $parentId = null;
        if ($object->getParent()) $parentId = $object->getParent()->getNodeId();
        
        return array(
            'name' => $object->getName(),
            'parent' => $parentId
        );
    }

    public function hydrate(array $data, $object)
    {
        $object->setName($data['name']);
        
        $parent = null;
        if ($data['parent']) $parent = $this->nodes()->getNode($data['parent'], 'UR');
        $object->setParent($parent);
        
        return $object;
    }
    
    protected function nodes()
    {
        return $this->getServiceLocator()->get('KofusNodeService');
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