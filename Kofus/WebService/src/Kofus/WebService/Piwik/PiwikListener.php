<?php
namespace Kofus\WebService\Piwik;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\Mvc\MvcEvent;


class PiwikListener extends AbstractListenerAggregate implements ListenerAggregateInterface
{
	public function attach(EventManagerInterface $events)
	{
		$sharedEvents = $events->getSharedManager();
		$this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, array($this, 'trackPageView'));
	}
	
	public function trackPageView(MvcEvent $e)
	{
	    $vm = $e->getApplication()->getServiceManager()->get('viewHelperManager');
	    
	    // View Helpers
	    $headScript = $vm->get('headScript');
	    $bodyTag = $vm->get('bodyTag');
	    
	    // Config values
	    $config = $e->getApplication()->getServiceManager()->get('KofusConfig');
	    $siteId = $config->get('webservice.piwik.site_id');
	    $url = $config->get('webservice.piwik.url');
	    $domains = $config->get('webservice.piwik.domains');
	    
	    if ($siteId && $url && $domains) {

	        // Integrate noscript html tag
	        $bodyTag->appendHtml('<noscript><p><img src="//'.$url.'/piwik.php?idsite=1" style="border:0;" alt="" /></p></noscript>');
	        
	        // Integrate js code
    	    $headScript->appendScript("
                  var _paq = _paq || [];
                  _paq.push(['setDomains', ".json_encode($domains)."]);
                  _paq.push(['trackPageView']);
                  _paq.push(['enableLinkTracking']);
                  (function() {
                    var u='//".$url."/';
                    _paq.push(['setTrackerUrl', u+'piwik.php']);
                    _paq.push(['setSiteId', '".$siteId."']);
                    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
                    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
                  })();
            ");
	    }	        
	}

}
