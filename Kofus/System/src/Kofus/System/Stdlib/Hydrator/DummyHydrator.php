<?php
namespace Kofus\System\Stdlib\Hydrator;

use Zend\Stdlib\Hydrator\HydratorInterface;

class DummyHydrator implements HydratorInterface
{
    public function extract($object)
    {
        return array();
    }
    
    public function hydrate(array $data, $object)
    {
        return $object;
    }
    
}