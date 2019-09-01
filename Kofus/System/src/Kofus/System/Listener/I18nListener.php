<?php
namespace Kofus\System\Listener;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\AbstractListenerAggregate;

class I18nListener extends AbstractListenerAggregate implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'initLocale'), 1000);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_BOOTSTRAP, array($this, 'initRedirects'));
        
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
	        $locale = \Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
	        if (! in_array($locale, $locales))
	        	$locale = $config->get('locales.default', 'de_DE');
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
    
   
    
}