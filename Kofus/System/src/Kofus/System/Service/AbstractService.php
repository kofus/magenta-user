<?php

namespace Kofus\System\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


abstract class AbstractService implements ServiceLocatorAwareInterface
{
    protected $sm;
	
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
		$this->sm = $serviceLocator;
	}
	
	public function getServiceLocator()
	{
		return $this->sm;
	}
	
	protected function em()
	{
		return $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
	}
	
	protected function nodes()
	{
		return $this->getServiceLocator()->get('KofusNodeService');
	}
	
	
	protected function config()
	{
	    return $this->getServiceLocator()->get('KofusConfig');
	}
	
	protected function search()
	{
		return $this->getServiceLocator()->get('KofusSearchService');
	}
	
}