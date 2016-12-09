<?php

namespace Kofus\Media\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;


class MediaPlugin extends AbstractPlugin
{
    protected $plugin;
    
    public function __invoke()
    {
        if (! $this->plugin) 
            $this->plugin = $this->getController()->getServiceLocator()->get('KofusMediaService');
        return $this->plugin;
	}

}