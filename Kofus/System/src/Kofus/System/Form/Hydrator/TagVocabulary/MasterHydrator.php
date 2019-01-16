<?php
namespace Kofus\System\Form\Hydrator\TagVocabulary;

use Zend\Stdlib\Hydrator\HydratorInterface;

class MasterHydrator implements HydratorInterface
{
	public function extract($object)
	{
		$data['title'] = $object->getTitle();
		return $data;
	}

	public function hydrate(array $data, $object)
	{
	    $object->setTitle($data['title']);
        
		return $object;
	}
}