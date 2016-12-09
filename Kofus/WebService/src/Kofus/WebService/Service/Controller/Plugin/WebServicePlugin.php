<?php

namespace Kofus\WebService\Service\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class WebServicePlugin extends AbstractPlugin
{
    protected $service;
    
    public function __invoke()
    {
    	if (! $this->service)
    		$this->service = $this->getController()->getServiceLocator()->get('KofusWebService');
    	return $this->service;
    }
    
}