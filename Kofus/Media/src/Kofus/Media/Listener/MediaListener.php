<?php
namespace Kofus\Media\Listener;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\AbstractListenerAggregate;

class MediaListener extends AbstractListenerAggregate implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'rejectMirrors'), 100);
    }
    
    public function rejectMirrors(MvcEvent $e)
    {
        $config = $e->getApplication()->getServiceManager()->get('KofusConfig');
        $mirrors = $config->get('media.mirrors', array());
        if ($mirrors && $e->getRequest() instanceof \Zend\Http\Request) {
            $hostname = 'http://' . $_SERVER['HTTP_HOST'];
            if (in_array($hostname, $mirrors)) {
                
                // This is a mirror server!
                $route = $e->getRouteMatch()->getMatchedRouteName();
                if (strpos($route, 'kofus_media') !== 0) {
                    $response = $e->getApplication()->getResponse();
                    $response->setStatusCode(404);
                    $response->setContent('Error: File not available on mirror server');
                    $response->send();
                    exit();
                }
            }
        }
    }
    
}