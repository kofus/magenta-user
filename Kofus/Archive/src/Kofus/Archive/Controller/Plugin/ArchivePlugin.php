<?php

namespace Kofus\Archive\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class ArchivePlugin extends AbstractPlugin
{
    protected $service;
    
    public function __invoke()
    {
    	if (! $this->service)
    		$this->service = $this->getController()->getServiceLocator()->get('KofusArchive');
    	return $this->service;
    }
    
}