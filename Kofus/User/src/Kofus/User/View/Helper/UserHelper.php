<?php

namespace Kofus\User\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class UserHelper extends AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $sl;
    
    public function __invoke()
    {
        return $this->getServiceLocator()->getServiceLocator()->get('KofusUserService');
    }
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
    	$this->sl = $serviceLocator;
    }
    
    public function getServiceLocator()
    {
    	return $this->sl;
    }
    
}