<?php

namespace Kofus\System\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class NavigationHelper extends AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $sm;
    
    public function __invoke($navbar)
    {
        $service = $this->getServiceLocator()->getServiceLocator()->get('KofusNavigationService');
        $service->setNavbar($navbar);
        $service->loadConfig();
        return $service;
    }
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
    	$this->sm = $serviceLocator;
    }
    
    public function getServiceLocator()
    {
    	return $this->sm;
    }
}


