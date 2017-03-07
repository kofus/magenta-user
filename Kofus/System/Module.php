<?php

namespace Kofus\System;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\MvcEvent;
use Kofus\System\Form\Element\NodeSelect;
use Zend\ModuleManager\ModuleManager;


use Zend\View\Helper\PaginationControl;

define('KOFUS_MODULE_SYSTEM_PATH', __DIR__);

class Module implements AutoloaderProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
    	$eventManager = $e->getApplication()->getEventManager();
        $sm = $e->getApplication()->getServiceManager();
        $this->e = $e;
        
        $this->bootstrapDoctrineEvents($e);
        
         // View helpers overwrite
        $pm = $sm->get('ViewHelperManager')->get('Navigation')->getPluginManager();
        $pm->setInvokableClass('dropdownMenu', '\Kofus\System\View\Helper\Navigation\DropdownMenu');
        PaginationControl::setDefaultScrollingStyle('sliding');
        PaginationControl::setDefaultViewPartial('kofus/paginator/bootstrap.phtml');        
        
        $urlHelper = $sm->get('ViewHelperManager')->get('Url');
        NodeSelect::setDefaultAjaxUrl($urlHelper('kofus_system', array('controller' => 'node', 'action' => 'select', 'id' => '{node_type}')));
        NodeSelect::setDefaultServiceLocator($sm);
        
        \Zend\Navigation\Page\Mvc::setDefaultRouter($e->getRouter());

        define('REQUEST_TIME', time());
    }
    
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
    
    public function isConsole()
    {
        return isset($_SERVER['argc']) && ($_SERVER['argc'] > 0);
    }
    
    public function getConfig()
    {
    	$config = array();
    	foreach (glob(__DIR__ . '/config/*.config.php') as $filename)
    		$config = array_merge_recursive($config, include $filename);

    	
    	
    	$config['listeners'] = array(
    		'KofusErrorListener'
    	);
    	if (! $this->isConsole()) {
    	    $config['listeners'][] = 'KofusPublicFilesListener';
    	    $config['listeners'][] = 'KofusNodeListener';
    	    $config['listeners'][] = 'KofusLayoutListener';
    	    $config['listeners'][] = 'KofusI18nListener';
    	}
    	return $config;
    }
    
    /**
     * Listen to events from Doctrine event manager and then
     * trigger corresponding events in ZF2 framework
     * @param MvcEvent $e
     */
    protected function bootstrapDoctrineEvents(MvcEvent $e)
    {
    	// Doctrine listener => event manager
    	$events = new \Kofus\System\EventManager\DoctrineEvents();
    	$em = $e->getApplication()->getServiceManager()->get('Doctrine\ORM\EntityManager');
    	$em->getEventManager()->addEventListener(array(
    			\Doctrine\ORM\Events::onClassMetadataNotFound,
    			\Doctrine\ORM\Events::onClear,
    			\Doctrine\ORM\Events::onFlush,
    			\Doctrine\ORM\Events::postFlush,
    			\Doctrine\ORM\Events::postLoad,
    			\Doctrine\ORM\Events::postPersist,
    			\Doctrine\ORM\Events::postRemove,
    			\Doctrine\ORM\Events::postUpdate,
    			\Doctrine\ORM\Events::preFlush,
    			\Doctrine\ORM\Events::prePersist,
    			\Doctrine\ORM\Events::preRemove,
    			\Doctrine\ORM\Events::preUpdate
    	), $events);
    }
    
    
    
}
