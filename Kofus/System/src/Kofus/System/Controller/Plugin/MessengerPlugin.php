<?php

namespace Kofus\System\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;


class MessengerPlugin extends AbstractPlugin
{
    
    
    
    protected $plugin;
    
    public function __invoke()
    {
        return $this;
	}
	

}