<?php

namespace Kofus\System\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class ConfigHelper extends AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $sm;
    protected $config;
    
    public function __invoke()
    {
    	if (! $this->config)
    		$this->config = $this->getServiceLocator()->get('KofusConfig');
    	return $this->config;
    }
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
    	$this->sm = $serviceLocator;
    }
    
    public function getServiceLocator()
    {
    	return $this->sm->getServiceLocator();
    }
}


