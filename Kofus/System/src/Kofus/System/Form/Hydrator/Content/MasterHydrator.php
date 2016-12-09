<?php
namespace Kofus\System\Form\Hydrator\Content;

use Zend\Stdlib\Hydrator\HydratorInterface;

class MasterHydrator implements HydratorInterface
{
	public function extract($object)
	{
		$data['title'] = $object->getTitle();
		$data['content'] = $object->getContent();
		$data['enabled'] = $object->isEnabled();
		return $data;
	}

	public function hydrate(array $data, $object)
	{
	    $object->setTitle($data['title']);
	    $object->setContent($data['content']);
	    $object->isEnabled($data['enabled']);
        
		return $object;
	}
}