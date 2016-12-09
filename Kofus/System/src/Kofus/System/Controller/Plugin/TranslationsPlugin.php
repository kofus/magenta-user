<?php

namespace Kofus\System\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;


class TranslationsPlugin extends AbstractPlugin
{
    protected $plugin;
    
    public function __invoke()
    {
        if (! $this->plugin) 
            $this->plugin = $this->getController()->getServiceLocator()->get('KofusTranslationService');
        return $this->plugin;
	}

}