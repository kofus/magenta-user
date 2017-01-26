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
    
    public function getServiceConfig()
    {
    	return array(
    			'factories' => array(
    					'Zend\Session\SessionManager' => function ($sm) {
    						
    											
    						return $this->bootstrapSession($sm);
    						
    					},
    			),
    	);
    }    
    
    protected function bootstrapSession($sm)
    {
        $config = $sm->get('Config');
        
        if (isset($config['kofus_sessions']) && 'disabled' == $config['kofus_sessions'])
            return;
        
    	// Init sqlite database for sessions
    	$db = Sessions::getInstance('sessions');
    	$tableGateway = new TableGateway('sessions', $db->getDb());
    	$options = new DbTableGatewayOptions();
    	$saveHandler = new DbTableGateway($tableGateway, $options);
    		
    	// Create session manager
    	$sessionManager = new SessionManager();
    	$sessionManager->setSaveHandler($saveHandler);
    	$sessionManager->start();
    		
    	Container::setDefaultManager($sessionManager);
    		
    	$container = new Container('init');
    		
    	if (!isset($container->init)) {
    	
    		$request = $sm->get('Request');
    	
    		$sessionManager->regenerateId(true);
    		$container->init          = 1;
    		$container->remoteAddr    = $request->getServer()->get('REMOTE_ADDR');
    		$container->httpUserAgent = $request->getServer()->get('HTTP_USER_AGENT');
    	

    		$sessionConfig = $config['session'];
    		if (isset($sessionConfig['validators'])) {
    			$chain   = $sessionManager->getValidatorChain();
    				
    			foreach ($sessionConfig['validators'] as $validator) {
    				switch ($validator) {
    					case 'Zend\Session\Validator\HttpUserAgent':
    						$validator = new $validator($container->httpUserAgent);
    						break;
    					case 'Zend\Session\Validator\RemoteAddr':
    						$validator  = new $validator($container->remoteAddr);
    						break;
    					default:
    						$validator = new $validator();
    				}
    				$chain->attach('session.validate', array($validator, 'isValid'));
    			}
    		}
    	
    		$tableGateway->setDefaultValues(array(
    				'remote_addr' => $container->remoteAddr,
    				'http_user_agent' => $container->httpUserAgent,
    				'created' => time()
    		));    
    	} 
    	return $sessionManager;
    }
    
   
    
    public function onBootstrap(MvcEvent $e)
    {
    	$sm = $e->getApplication()->getServiceManager();
    	
    	if ($e->getRequest() instanceof HttpRequest)
    	   $this->bootstrapSession($sm);
    } 

  

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    
}
