<?php
namespace Kofus\System\Form\Hydrator\Page;

use Zend\Stdlib\Hydrator\HydratorInterface;

class MasterHydrator implements HydratorInterface
{
	public function extract($object)
	{
		$data['title'] = $object->getTitle();
		$data['content'] = $object->getContent();
		$data['template'] = $object->getTemplate();
		$data['enabled'] = $object->isEnabled();
		return $data;
	}

	public function hydrate(array $data, $object)
	{
	    $object->setTitle($data['title']);
	    $object->setContent($data['content']);
	    $object->isEnabled($data['enabled']);
	    
	    if (array_key_exists('template', $data)) {
	        $object->setTemplate($data['template']);
	    }
        
		return $object;
	}
}