<?php

namespace Kofus\System\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\Session\Container;



class SessionHelper extends AbstractHelper
{
    protected $sm;
    protected $config;
    
    public function __invoke()
    {
    	return $this;
    }
    
    public function counter($key, $autoIncrement=true)
    {
        $container = new Container('Kofus\System\View\Helper\SessionHelper');
        $keys = array();
        if (isset($container->keys))
            $keys = $container->keys;
        if (! isset($keys[$key]))
            $keys[$key] = 0;
        $counter = $keys[$key];
        
        $keys[$key] = $counter + 1;
        $container->keys = $keys;
        return $counter;
    }
    
}


