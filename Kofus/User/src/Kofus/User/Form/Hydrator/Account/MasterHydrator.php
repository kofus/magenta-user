<?php
namespace Kofus\User\Form\Hydrator\Account;

use Zend\Stdlib\Hydrator\HydratorInterface;

class MasterHydrator implements HydratorInterface
{

    public function extract($object)
    {
        return array(
            'name' => $object->getName(),
            'status' => $object->getStatus(),
            'role' => $object->getRole()
        );
    }

    public function hydrate(array $data, $object)
    {
        $object->setName($data['name']);
        $object->setStatus($data['status']);
        $object->setRole($data['role']);
        return $object;
    }
}