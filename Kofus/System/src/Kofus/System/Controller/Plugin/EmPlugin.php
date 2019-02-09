<?php

namespace Kofus\System\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;


class EmPlugin extends AbstractPlugin
{
    protected $em;
    
    public function __invoke($namespace='orm_default')
    {
        return $this->getController()->getServiceLocator()->get('doctrine.entitymanager.' . $namespace);
	}

}