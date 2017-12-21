<?php

namespace Kofus\Archive;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use Kofus\Archive\Db\TableGateway\Sessions as TableGateway;
use Zend\Session\SessionManager;
use Zend\Session\SaveHandler\DbTableGateway;
use Zend\Session\SaveHandler\DbTableGatewayOptions;
use Kofus\Archive\Sqlite\Table\Sessions;
use Zend\Http\Request as HttpRequest;

class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
		    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                ),
            ),
        );
    }
    
    public function onBootstrap(MvcEvent $e)
    {
    } 

  

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    
}
