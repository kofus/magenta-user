<?php
namespace Kofus\System\Form\Hydrator\Page;

use Zend\Stdlib\Hydrator\HydratorInterface;

class PriorityHydrator implements HydratorInterface
{
	public function extract($object)
	{
		$data['priority'] = $object->getPriority();
		return $data;
	}

	public function hydrate(array $data, $object)
	{
	    $object->setPriority($data['priority']);
        
		return $object;
	}
}