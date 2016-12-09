<?php
namespace Kofus\User\Form\Hydrator;

use Zend\Stdlib\Hydrator\HydratorInterface;

class UserAccountHydrator implements HydratorInterface
{

    public function extract($object)
    {
        return array(
            'name' => $object->getName(),
            'status' => $object->getStatus()
        );
    }

    public function hydrate(array $data, $object)
    {
        $object->setName($data['name']);
        $object->setStatus($data['status']);
        return $object;
    }
}