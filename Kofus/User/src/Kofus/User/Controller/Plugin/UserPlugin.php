<?php

namespace Kofus\User\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;


class UserPlugin extends AbstractPlugin
{
    public function __invoke()
    {
        return $this->getController()->getServiceLocator()->get('KofusUserService');
	}

}