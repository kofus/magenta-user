<?php
namespace Kofus\Archive\Service;

use Zend\Session\Container;

class UriStackService
{
    
    protected static $containerName = 'Kofus_Archive_Service_BrowserService';
    
    protected static function getStack()
    {
    	$container = new Container(self::$containerName);
    	if (isset($container->stack))
    		return $container->stack;
    	return array();
    }
    
    protected static function setStack(array $stack)
    {
    	$container = new Container(self::$containerName);
    	$container->stack = $stack;
    }
    
    
    public function push($uri=null)
    {
    	if (null === $uri)
    		$uri = $_SERVER['REQUEST_URI'];
    
    	$stack = self::getStack();
    	array_unshift($stack, $uri);
    	$stack = array_unique($stack);
    	self::setStack($stack);
    }
    
    public static function reset()
    {
    	self::setStack(array());
    }
    
    public static function first($default='/')
    {
    	$stack = self::getStack();
    	if ($stack) {
    	    foreach ($stack as $uri) {
    	        if ($uri == $_SERVER['REQUEST_URI']) continue;
    	        return $uri;
    	    }
    	}
    	return $default;
    }
    
    public function pop($default='/')
    {
    	$stack = self::getStack();
    	
    	if ($stack) {
    		$uri = array_shift($stack);
    		self::setStack($stack);
    		return $uri;
    	}
    	return $default;

    	/*
    	while ($stack) {
    		$uri = array_shift($stack);
            if ($uri != $_SERVER['REQUEST_URI']) {
                self::setStack($stack);
                return $uri;
            }
    	} */
    }
    
    public static function dump()
    {
    	return self::getStack();
    }
    
}