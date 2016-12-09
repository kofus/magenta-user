<?php

namespace Kofus\System\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;


class EmPlugin extends AbstractPlugin
{
    protected $em;
    
    public function __invoke()
    {
        if (! $this->em) 
            $this->em = $this->getController()->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        return $this->em;
	}

}