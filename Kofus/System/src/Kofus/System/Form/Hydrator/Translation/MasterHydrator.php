<?php
namespace Kofus\System\Form\Hydrator\Translation;

use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MasterHydrator implements HydratorInterface, ServiceLocatorAwareInterface
{
	public function extract($object)
	{
	    return array(
	        'msg_id' => $object->getMsgId(),
	        'value' => $object->getValue(),
	        'locale' => $object->getLocale(),
	        'text_domain' => $object->getTextDomain()
	    );
	}

	public function hydrate(array $data, $object)
	{
	    $object->setValue($data['value']);
		return $object;
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
	
	protected function nodes()
	{
	    return $this->getServiceLocator()->get('KofusNodeService');
	}
	
}