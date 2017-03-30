<?php

namespace Kofus\System\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class FormBuilderPlugin extends AbstractPlugin
{
    public function __invoke()
    {
        $service = new \Kofus\System\Service\FormBuilderService();
        $service->setServiceLocator($this->getController()->getServiceLocator());
        return $service;
    }
    
}