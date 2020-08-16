<?php
namespace Kofus\System\Listener;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class I18nListener extends AbstractListenerAggregate implements ListenerAggregateInterface, ServiceLocatorAwareInterface
{
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'initLocale'), 1000);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_BOOTSTRAP, array($this, 'initRedirects'));
        $this->listeners[] = $sharedEvents->attach('translator', 'missingTranslation', array($this, 'handleMissingTranslation'));
    }
    
    public function handleMissingTranslation($e)
    {
        $config = $this->getServiceLocator()->get('KofusConfigService');
        
        $routeMatch = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch();
        if (! $routeMatch) return;
        $routeName = $routeMatch->getMatchedRouteName();
        if (in_array($routeName, $config->get('translator.event_manager_ignore_routes', array()))) return;
        
        $params = $e->getParams();
        $translationService = $this->getServiceLocator()->get('KofusTranslationService');
        if ('node' == $params['text_domain']) return;
        if (! $params['message']) return;
        if ($config->get('locales.default') == $params['locale']) return;
        
        $translationService->addTranslation($params['message'], null, $params['locale'], $params['text_domain'], array('route' => $routeName));
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
    	$locales = $config->get('locales.enabled', array('de_DE'));
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
            foreach ($locales as $_locale) {
                if (strpos($_locale, $language) === 0) {
                    $locale = $_locale; 
                    break;
                }
            }
        }
        
        // 3. Locale in session?
        if (! $locale) {
        	if (isset($session->locale))
        		$locale = $session->locale;
        }
        
        // 4. Browser?
        if (! $locale && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
	        $httpLocale = \Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
	        foreach ($locales as $_locale) {
	            if (strpos($_locale, $httpLocale) === 0) {
	                $locale = $_locale; break;
	            }
	        }
        }
        
        // 5. Default
        if (! in_array($locale, $locales)) $locale = $config->get('locales.default', 'de_DE');
        
        // Validate and store locale
        if ($locale) {
            if (! in_array($locale, $locales))
                throw new \Exception('Invalid locale: ' . $locale);
            $service = $e->getApplication()->getServiceManager()->get('KofusLocale');
            $service->setLocale($locale);
            
            $session->locale = $locale;
        }
    }
    
    protected $sm;
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->sm = $serviceLocator;
    }
    
    public function getServiceLocator()
    {
        return $this->sm;
    }
    
}