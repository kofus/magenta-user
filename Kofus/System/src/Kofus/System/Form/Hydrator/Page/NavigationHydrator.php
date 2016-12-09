<?php
namespace Kofus\System\Form\Hydrator\Page;

use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class NavigationHydrator implements HydratorInterface, ServiceLocatorAwareInterface
{
	public function extract($object)
	{
	    $data = array(
	    	'nav_label' => $object->getNavLabel(),
	        'uri_segment' => $object->getUriSegment(),
	        'nav_visible' => $object->isNavVisible(),
	        'priority' => $object->getPriority()
	    );
	    
		if ($object->getParent()) {
			$data['position'] = $object->getParent()->getNodeId();
		} else {
			$data['position'] = $object->getNavbar();
		}
		
		return $data;
	}

	public function hydrate(array $data, $object)
	{
	    $object->setNavLabel($data['nav_label']);
	    $object->isNavVisible($data['nav_visible']);
	    $object->setPriority($data['priority']);
	    
	    $config = $this->getServiceLocator()->get('KofusConfig');
	    $navbars = $config->get('navbars');
	    
	    // Position
	    if (isset($navbars[$data['position']])) {
	        $object->setNavbar($data['position']);
	        $object->setParent(null);
	    } else {
	        $nodes = $this->getServiceLocator()->get('KofusNodeService');
	        $node = $nodes->getNode($data['position'], array('PG'));
	        $object->setNavbar(null);
	        $object->setParent($node);
	    }
	    
	    // Uri segment
	    $uriSegment = $data['uri_segment'];
	    if (! $uriSegment) {
	        
	        if ($data['nav_label']) {
	           $uriSegment = $data['nav_label'];    
	        } elseif ($object->getTitle()) {
	            $uriSegment = $object->getTitle();
	        } elseif ($object->getNodeId()) {
	            $uriSegment = $object->getNodeId();
	        } else {
	            $uriSegment = \Zend\Math\Rand::getString(8, 'abcdefghijklmnopqrstuvwxyz');
	        }
	    }
	    $filter = new \Kofus\System\Filter\UrlSegment();
	    $uriSegment = $filter->filter($uriSegment);
	    $object->setUriSegment($uriSegment);
	    
	    
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
		
	
	
}