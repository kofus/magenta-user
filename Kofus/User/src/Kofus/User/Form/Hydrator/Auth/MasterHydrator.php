<?php
namespace Kofus\User\Form\Hydrator\Auth;

use Zend\Stdlib\Hydrator\HydratorInterface;

class MasterHydrator implements HydratorInterface
{

    public function extract($object)
    {
        return array(
            'type' => $object->getType(),
        );
    }

    public function hydrate(array $data, $object)
    {
        $object->setType($data['type']);
        return $object;
    }
}