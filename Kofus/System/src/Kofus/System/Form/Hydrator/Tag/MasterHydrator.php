<?php
namespace Kofus\System\Form\Hydrator\Tag;

use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MasterHydrator implements HydratorInterface, ServiceLocatorAwareInterface
{
	public function extract($object)
	{
	    $vocabularyId = null;
	    if ($object->getVocabulary())
	        $vocabularyId = $object->getVocabulary()->getNodeId();
	    
	    
		$data['title'] = $object->getTitle();
		$data['vocabulary'] = $vocabularyId;
		return $data;
	}

	public function hydrate(array $data, $object)
	{
	    $object->setTitle($data['title']);
	    
	    $vocabulary = null;
	    if ($data['vocabulary'])
	        $vocabulary = $this->nodes()->getNode($data['vocabulary'], 'TAGV');
	    $object->setVocabulary($vocabulary);
	    
        
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