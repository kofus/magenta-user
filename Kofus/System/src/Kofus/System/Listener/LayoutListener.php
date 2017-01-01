<?php
namespace Kofus\System\Listener;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\AbstractListenerAggregate;

class LayoutListener extends AbstractListenerAggregate implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'initModuleLayout'));
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, array($this, 'deployAssets'));
    }
    
    public function deployAssets(MvcEvent $e)
    {
    	$this->deployLinks($e);
    }
    
    protected function getPublicFileContent(MvcEvent $e, $uri)
    {
        $filename = null;
        if (file_exists('public/' . $uri)) 
            $filename = 'public/' . $uri;
        if (! $filename) {
            $paths = $e->getApplication()->getServiceManager()->get('KofusConfigService')->get('public_paths');
            foreach ($paths as $path) {
                if (file_exists($path . '/' . $uri))
                    $filename = $path . '/' . $uri;
            }
        }
        
        if (! $filename)
            throw new \Exception('Uri could not be mapped to a public file: ' . $uri);
        
        
        return file_get_contents($filename);

        
    }
    
    protected function deployLinks(MvcEvent $e)
    {
        // Init
        $viewHelperManager = $e->getApplication()->getServiceManager()->get('viewHelperManager');
        $layout = $viewHelperManager->get('layout')->getLayout();
        $headLink = $viewHelperManager->get('headLink');
        $headScript = $viewHelperManager->get('headScript');
        $assets = $e->getApplication()->getServiceManager()->get('KofusConfigService')->get('assets');
        
        // No assets for this layout enabled?
        if (! isset($assets['enabled'][$layout])) 
            return;

   	    foreach ($assets['enabled'][$layout] as $asset) {
    		$assetFound = false;
    		
    		// Add css files
    		if (isset($assets['available'][$asset]['files']['css'])) {
    			foreach ($assets['available'][$asset]['files']['css'] as $uri) {
    				if (isset($assets['available'][$asset]['base_uri']))
    					$uri = $assets['available'][$asset]['base_uri'] . '/' . $uri;
    				$headLink()->appendStylesheet($uri);
    				$assetFound = true;
    			}
    		}
    		
    		// Add and compile sass files
    		if (isset($assets['available'][$asset]['files']['sass'])) {
    			foreach ($assets['available'][$asset]['files']['sass'] as $uri) {
    				if (isset($assets['available'][$asset]['base_uri']))
    					$uri = $assets['available'][$asset]['base_uri'] . '/' . $uri;
    				$scssc = new \scssc();
    				$scssc->setFormatter('scss_formatter_compressed');
    				$s = $scssc->compile($this->getPublicFileContent($e, $uri));
    				$filename = 'public/cache/sass/' . md5($uri) . '.css';
    				if (! file_exists(dirname($filename)))
    				    mkdir(dirname($filename), 0777, true);
    				file_put_contents($filename, $s);
    				$headLink()->appendStylesheet('/cache/sass/' . md5($uri) . '.css');
    				$assetFound = true;
    			}
    		}
    		
    		// Add js files
    		if (isset($assets['available'][$asset]['files']['js'])) {
    			foreach ($assets['available'][$asset]['files']['js'] as $uri) {
    				if (isset($assets['available'][$asset]['base_uri']))
    					$uri = $assets['available'][$asset]['base_uri'] . '/' . $uri;
    
    				if ('html5' == $asset) {
    					$headScript()->appendFile($uri, 'text/javascript', array('conditional' => 'lt IE 9',));
    				} else {
    					$headScript()->appendFile($uri);
    				}
    				$assetFound = true;
    			}
    		}
    
    		if (! $assetFound) throw new \Exception('No definition found for asset "' . $asset . '"');
    	}
    }
   
    
    public function initModuleLayout($e)
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
    
    
}