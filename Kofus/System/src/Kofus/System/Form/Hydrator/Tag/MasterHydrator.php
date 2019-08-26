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
	    if (! $vocabularyId && isset($_GET['vocabulary']))
	        $vocabularyId = $_GET['vocabulary'];
	    
	    
		$data['title'] = $object->getTitle();
		$data['vocabulary'] = $vocabularyId;
		
		$parentId = null;
		if ($object->getParent())
		    $parentId = $object->getParent()->getNodeId();
		$data['parent'] = $parentId;
		
		$data['uri_segment'] = $object->getUriSegment();
		
		
		return $data;
	}

	public function hydrate(array $data, $object)
	{
	    $object->setTitle($data['title']);
	    
	    $vocabulary = null;
	    if ($data['vocabulary'])
	        $vocabulary = $this->nodes()->getNode($data['vocabulary'], 'TV');
	    $object->setVocabulary($vocabulary);
	    
	    $parent = null;
	    if ($data['parent'])
	        $parent = $this->nodes()->getNode($data['parent'], 'T');
	    $object->setParent($parent);
	    
	    $filter = new \Kofus\System\Filter\UriSegment();
	    
	    $uriSegment = null;
	    if ($data['uri_segment'])
	        $uriSegment = $data['uri_segment'];
	        
	    if (! $uriSegment)
	        $uriSegment = $object->getTitle();
	    $object->setUriSegment($filter->filter($uriSegment));
	    
        
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