<?php

namespace Kofus\Media\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class MediaHelper extends AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $sm;
    
    public function __invoke()
    {
        return $this->getServiceLocator()->get('KofusMediaService');
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


