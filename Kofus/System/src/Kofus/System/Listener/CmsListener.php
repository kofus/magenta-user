<?php
namespace Kofus\System\Listener;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\AbstractListenerAggregate;

class CmsListener extends AbstractListenerAggregate implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'initLocale'), 1000);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'initLayout'));
        $this->listeners[] = $events->attach(MvcEvent::EVENT_BOOTSTRAP, array($this, 'initRedirects'));
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, array($this, 'addAssets'));
    }
    
    public function initRedirects(MvcEvent $e)
    {
        $redirects = $e->getApplication()->getServiceManager()->get('KofusConfig')->get('redirects');
        if ($redirects) {
            $requestUri = $e->getRequest()->getRequestUri();
            if (isset($redirects[$requestUri])) {
                $response = $e->getResponse();
                $headers = $response->getHeaders();
                $headers->addHeaderLine('Location', $redirects[$requestUri]);
                $response->send();
                exit();
            }
        }
    }
    
    public function initLocale(MvcEvent $e)
    {
    	$config = $e->getApplication()->getServiceManager()->get('KofusConfig');
    	$locales = $config->get('locales.enabled', array('de_DE', 'en_US'));
    	$locale = null;
    	
    	// Forced by config?
    	if (count($locales) == 1)
    		$locale = $locales[0];
    	
    	$session = new \Zend\Session\Container('Locale');
    	
    	// 1. Has locale explicitly been specified in route, i.e. url?
    	if (! $locale && $e->getRouteMatch()) {
        	$locale = $e->getRouteMatch()->getParam('locale');
    	}
        
        // 2. Has language explicitly been specified in route, i.e. url? => set locale by language
        if (! $locale && $e->getRouteMatch()) {
            $language = $e->getRouteMatch()->getParam('language');
            if ($language == 'de') {
                $locale = 'de_DE';
            } elseif ($language == 'en') {
                $locale = 'en_US';
            }
        }
        
        // 3. Locale in session?
        if (! $locale) {
        	if (isset($session->locale))
        		$locale = $session->locale;
        }
        
        // 4. Browser?
        if (! $locale && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
	        $locale = \Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
	        if (! in_array($locale, $locales))
	        	$locale = 'en_US';
        }        
        
        // Validate and store locale
        if ($locale) {
            if (! in_array($locale, $locales))
                throw new \Exception('Invalid locale: ' . $locale);
            $service = $e->getApplication()->getServiceManager()->get('KofusLocale');
            $service->setLocale($locale);
            
            $session->locale = $locale;
        }
    }
    
    public function ____compressHtml()
    {
        return; // it does not work yet!
        if ($this->getConfig('optimizer.' . $this->getLayout() . '.html.compress')) {
            $body = $this->e->getApplication()->getResponse()->getContent();
            if (strpos($body, '<!DOCTYPE html') === 0) {
            	$body = preg_replace('/\s+/', ' ', $body);
            	$this->e->getApplication()->getResponse()->setContent($body);
            }
        }
    }
    
    public function addAssets(MvcEvent $e) 
    {
        $viewHelperManager = $e->getApplication()->getServiceManager()->get('viewHelperManager');
        $layout = $viewHelperManager->get('layout')->getLayout();
        
        $assets = $viewHelperManager->get('assets');
        $assets($layout)->deploy();
        
        $config = $e->getApplication()->getServiceManager()->get('KofusConfig');
        
        if ($config->get('optimizer.' . $layout)) {
            $optimizer = $viewHelperManager->get('optimizer');
            //$optimizer->isDebug($this->getConfig('optimizer.debug', false));
            
            if ($config->get('optimizer.'.$layout.'.styles.sass'))
            	$optimizer->sass()->compile();
            
            if ($config->get('optimizer.'.$layout.'.styles.compress'))
            	$optimizer->css()->compress();
            
            if ($config->get('optimizer.'.$layout.'.scripts.compress'))
            	$optimizer->scripts()->compress();
        }
        
    }
    
    public function ____addCron()
    {
        $interval = $this->getConfig('cron.dynamic.interval');  
        if ($interval) {
            $dm = $this->e->getApplication()->getServiceManager()->get('DataManager');
            $lastCron = $dm->getSetting('last_cron', 0);
            if ($lastCron + $interval < time()) { 
                $viewHelperManager = $this->e->getApplication()->getServiceManager()->get('viewHelperManager');
                $placeholder = $viewHelperManager->get('placeholder');
                $placeholder->getContainer('cron')->set('<img src="/cron/'.time().'.gif" width="1" height="1" />');
            }
        }
    }
    
    public function ____addHeadMeta(MvcEvent $e)
    {
        return;
        $entity = $e->getRouteMatch()->getParam('node');
        $viewHelperManager = $e->getApplication()->getServiceManager()->get('viewHelperManager');
        $headMetaHelper = $viewHelperManager->get('headMeta');
        
        if ($entity && $entity->getInfoId() == 'PG') {
        	if ($entity->getMetaKeywords())
        		$headMetaHelper->appendName('keywords', $entity->getMetaKeywords());
        	if ($entity->getMetaDescription())
        		$headMetaHelper->appendName('description', $entity->getMetaDescription());
        }
    }
    
    public function ____addHeadTitle(MvcEvent $e)
    {
        $node = $e->getRouteMatch()->getParam('node');
        $viewHelperManager = $e->getApplication()->getServiceManager()->get('viewHelperManager');
        
        $headTitleHelper = $viewHelperManager->get('headTitle');
        $headTitleHelper->setSeparator(' - ');
        if ($node && $node->getNodeType() == 'PG') {
        	$title = $node->getTitle();
        	$headTitleHelper->append($title);
        }
        $config = $e->getApplication()->getServiceManager()->get('KofusConfig');
        if ($config->has('project_title'))
            $headTitleHelper->append($config->get('project_title'));
    }
    
    public function initLayout($e)
    {
        $result = $e->getResult();
         
        // ajax requests
        if ($result instanceof \Zend\View\Model\JsonModel) {
        	$result->setTerminal(true);
        	 
        } elseif ($result instanceof \Zend\View\Model\ViewModel) {
        	$result->setTerminal($e->getRequest()->isXmlHttpRequest());
        }
        
        if ($result && $result->terminate()) return;
         
        // admin layout
        $controller = $e->getTarget();
        $controllerClass = get_class($controller);
        
        $config = $e->getApplication()->getServiceManager()->get('KofusConfig');
        foreach ($config->get('view_manager.module_layouts', array()) as $namespace => $layout) {
        	if (strpos($controllerClass, $namespace) === 0) {
        		$controller->layout($layout);
        		break;
        	}
        }
        
    }
    
    
}