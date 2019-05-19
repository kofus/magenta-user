<?php
namespace Kofus\System;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\MvcEvent;
use Kofus\System\Form\Element\NodeSelect;
use Zend\ModuleManager\ModuleManager;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use Zend\View\Helper\PaginationControl;
define('KOFUS_MODULE_SYSTEM_PATH', __DIR__);

class Module implements AutoloaderProviderInterface
{

    public function onBootstrap(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $this->e = $e;
        
        $this->bootstrapPhpSettings($e);
        $this->bootstrapSession($e);
        $this->bootstrapDoctrineEvents($e);
        $this->bootstrapExceptionLogging($e);
        
        
        // View helpers overwrite
        $pm = $sm->get('ViewHelperManager')
            ->get('Navigation')
            ->getPluginManager();
        $pm->setInvokableClass('dropdownMenu', '\Kofus\System\View\Helper\Navigation\DropdownMenu');
        PaginationControl::setDefaultScrollingStyle('sliding');
        PaginationControl::setDefaultViewPartial('kofus/paginator/bootstrap.phtml');
        
        $urlHelper = $sm->get('ViewHelperManager')->get('Url');
        NodeSelect::setDefaultAjaxUrl($urlHelper('kofus_system', array(
            'controller' => 'node',
            'action' => 'select',
            'id' => '{node_type}'
        )));
        NodeSelect::setDefaultServiceLocator($sm);
        
        \Zend\Navigation\Page\Mvc::setDefaultRouter($e->getRouter());
        
        define('REQUEST_TIME', time());
        define('ROOT_DIR', realpath(__DIR__ . '/../../../../..'));
    }
    
    protected function bootstrapExceptionLogging($e)
    {
        $sm = $e->getApplication()->getServiceManager();
        if ($sm->has('logger')) {
            $eventManager        = $e->getApplication()->getEventManager();
            $eventManager->attach('dispatch.error', function($event){
                $exception = $event->getResult()->exception;
                if ($exception) {
                    $sm = $event->getApplication()->getServiceManager();
                    $service = $sm->get('logger');
                    $service->crit((string) $exception);
                }
            });
        }
            
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php'
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/', __NAMESPACE__)
                )
            )
        );
    }

    public function isConsole()
    {
        return strpos(php_sapi_name(), 'cli') !== false;
    }

    public function getConfig()
    {
        $config = array();
        foreach (glob(__DIR__ . '/config/*.config.php') as $filename)
            $config = array_merge_recursive($config, include $filename);
        
        if (! $this->isConsole()) {
            $config['listeners'][] = 'KofusPublicFilesListener';
            $config['listeners'][] = 'KofusNodeListener';
            $config['listeners'][] = 'KofusLayoutListener';
            $config['listeners'][] = 'KofusI18nListener';
        } else {
            $config['listeners'][] = 'KofusNodeListener';
        }
        return $config;
    }
    
    public function bootstrapPhpSettings(MvcEvent $e)
    {
        $config = $e->getApplication()->getServiceManager()->get('config');
        if (isset($config['php_settings'])) {
            foreach ($config['php_settings'] as $key => $value) {
                ini_set($key, $value);
            }
        }
    }
    

    /**
     * Listen to events from Doctrine event manager and then
     * trigger corresponding events in ZF2 framework
     * 
     * @param MvcEvent $e            
     */
    protected function bootstrapDoctrineEvents(MvcEvent $e)
    {
        // Doctrine listener => event manager
        $events = new \Kofus\System\EventManager\DoctrineEvents();
        $em = $e->getApplication()
            ->getServiceManager()
            ->get('Doctrine\ORM\EntityManager');
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

    /**
     * Assembles console help texts as provided in console router config (param "help_text")
     * 
     * @return array
     */
    public function getConsoleUsage(Console $console)
    {
        $usage = array();
        $config = $this->getConfig();
        if (isset($config['console']['router']['routes'])) {
            foreach ($config['console']['router']['routes'] as $route) {
                if (isset($route['options']['help_text']))
                    $usage[$route['options']['route']] = $route['options']['help_text'];
            }
        }
        return $usage;
    }

    public function bootstrapSession($e)
    {
        if ($this->isConsole()) return;
        
        $serviceManager = $e->getApplication()->getServiceManager();
        $config = $serviceManager->get('Config');
        if (! isset($config['session'])) return;
            
        $session = $e->getApplication()
            ->getServiceManager()
            ->get('Zend\Session\SessionManager');
        $session->start();
        
        $container = new Container('initialized');
        if (! isset($container->init)) {
            $request = $serviceManager->get('Request');
            
            $container->init = 1;
            $container->remoteAddr = $request->getServer()->get('REMOTE_ADDR');
            $container->httpUserAgent = $request->getServer()->get('HTTP_USER_AGENT');
            
            
            $sessionConfig = $config['session'];
            if (isset($sessionConfig['validators'])) {
                $chain = $session->getValidatorChain();
                
                foreach ($sessionConfig['validators'] as $validator) {
                    switch ($validator) {
                        case 'Zend\Session\Validator\HttpUserAgent':
                            $validator = new $validator($container->httpUserAgent);
                            break;
                        case 'Zend\Session\Validator\RemoteAddr':
                            $validator = new $validator($container->remoteAddr);
                            break;
                        default:
                            $validator = new $validator();
                    }
                    
                    $chain->attach('session.validate', array(
                        $validator,
                        'isValid'
                    ));
                }
            }
        }
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Zend\Session\SessionManager' => function ($sm) {
                    $config = $sm->get('config');
                    if (isset($config['session'])) {
                        $session = $config['session'];
                        
                        $sessionConfig = null;
                        if (isset($session['config'])) {
                            $class = isset($session['config']['class']) ? $session['config']['class'] : 'Zend\Session\Config\SessionConfig';
                            $options = isset($session['config']['options']) ? $session['config']['options'] : array();
                            $sessionConfig = new $class();
                            $sessionConfig->setOptions($options);
                        }
                        
                        $sessionStorage = null;
                        if (isset($session['storage'])) {
                            $class = $session['storage'];
                            $sessionStorage = new $class();
                        }
                        
                        $sessionSaveHandler = null;
                        if (isset($session['save_handler'])) {
                            // class should be fetched from service manager since it will require constructor arguments
                            $sessionSaveHandler = $sm->get($session['save_handler']);
                            if ($sessionSaveHandler && $sessionSaveHandler instanceof \Zend\Cache\Storage\StorageInterface)
                                $sessionSaveHandler = new \Zend\Session\SaveHandler\Cache($sessionSaveHandler);
                        }
                        
                        $sessionManager = new SessionManager($sessionConfig, $sessionStorage, $sessionSaveHandler);
                    } else {
                        $sessionManager = new SessionManager();
                    }
                    Container::setDefaultManager($sessionManager);
                    return $sessionManager;
                }
            )
        );
    }
}
