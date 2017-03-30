<?php

namespace Kofus\System\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class FormPlugin extends AbstractPlugin
{
    public function __invoke()
    {
        $service = new \Kofus\System\Service\FormService();
        $service->setServiceLocator($this->getController()->getServiceLocator());
        return $service;
    }
    
}