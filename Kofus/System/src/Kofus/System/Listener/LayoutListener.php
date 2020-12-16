<?php
namespace Kofus\System\Listener;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\AbstractListenerAggregate;

class LayoutListener extends AbstractListenerAggregate implements ListenerAggregateInterface
{
    protected $e;
    
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'initModuleLayout'));
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, array($this, 'deployAssets'));
        $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, array($this, 'compressHtml'));
    }
    
    public function deployAssets(MvcEvent $e)
    {
    	$this->deployLinks($e);
    }
    
    protected function getPublicFileContent($uri)
    {
        $publicDir = (defined('KOFUS_PUBLIC_DIR') ? KOFUS_PUBLIC_DIR : 'public');
        
        $filename = null;
        if (file_exists($publicDir . '/' . $uri)) 
            $filename = $publicDir . '/' . $uri;
        
        // File in module public path?
        if (! $filename && strpos($uri, '/cache/public/') === 0) {
            $uri = str_replace('/cache/public/', '', $uri);
            
            $paths = $this->e->getApplication()->getServiceManager()->get('KofusConfigService')->get('public_paths');
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
        $headLink = $viewHelperManager->get('headLink');
        $headScript = $viewHelperManager->get('headScript');
        $assets = $e->getApplication()->getServiceManager()->get('KofusConfigService')->get('assets');
        $layout = $viewHelperManager->get('layout')->getLayout();
        
        $this->e = $e;
        
        // No assets for this layout enabled?
        if (! isset($assets['enabled'][$layout])) 
            return;
        
        $sassUris = array();
        $compressCss = isset($assets['compress_css']);
        $compressJs = isset($assets['compress_js']);
        $contentJs = '';
        $contentCss = '';
        
   	    foreach ($assets['enabled'][$layout] as $asset) {
    		$assetFound = false;
    		
    		// Add css files
    		if (isset($assets['available'][$asset]['files']['css'])) {
    			foreach ($assets['available'][$asset]['files']['css'] as $uri) {
    				if (isset($assets['available'][$asset]['base_uri']))
    					$uri = $assets['available'][$asset]['base_uri'] . '/' . $uri;
    				if ($compressCss)
    			        $contentCss .= $this->getPublicFileContent($uri);
    			   
    			    if (! $compressCss)
    				    $headLink()->appendStylesheet($uri);
    				$assetFound = true;
    			}
    			if ($compressCss && $contentCss) {
    			    $compressCssFilename = 'public/cache/css/' . $layout . '.css';
    			    if (! is_dir(dirname($compressCssFilename)))
    			        mkdir(dirname($compressCssFilename));
    			    file_put_contents($compressCssFilename, $contentCss);
    			    $headLink()->appendStylesheet('/cache/css/' . $layout . '.css');
    			}
    		}
    		
    		// Add and compile sass files
    		if (isset($assets['available'][$asset]['files']['sass'])) {
    			foreach ($assets['available'][$asset]['files']['sass'] as $uri) {
    				if (isset($assets['available'][$asset]['base_uri']))
    					$uri = $assets['available'][$asset]['base_uri'] . '/' . $uri;
    				$sassUris[] = $uri;
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
    				    if (! $compressJs) {
    					   $headScript()->appendFile($uri);
    				    } else {
    				        $contentJs .= "\n" . $this->getPublicFileContent($uri);
    				    }
    				}
    				$assetFound = true;
    			}
    		}
    		if ($compressJs && $contentJs) {
    		    $compressJsFilename = 'public/cache/js/' . md5($layout) . '.js';
    		    if (! is_dir(dirname($compressJsFilename)))
    		        mkdir(dirname($compressJsFilename));
    		    file_put_contents($compressJsFilename, $contentJs);
    		    $headScript()->appendFile('/cache/js/' . md5($layout) . '.js');
    		}
    		
    		// Inline scripts
    		if (isset($assets['available'][$asset]['files']['js-inlines'])) {
    			foreach ($assets['available'][$asset]['files']['js-inlines'] as $script) {
   					$headScript()->appendScript($script);
    				$assetFound = true;
    			}
    		}	
    		
    		if ($sassUris)
    		    $this->createSass($sassUris);
    
    		if (! $assetFound) throw new \Exception('No definition found for asset "' . $asset . '"');
    	}
    }
    
    protected function createSass(array $uris)
    {
        $viewHelperManager = $this->e->getApplication()->getServiceManager()->get('viewHelperManager');
        $headLink = $viewHelperManager->get('headLink');
        
        $content = '';
        foreach ($uris as $uri)
            $content .= $this->getPublicFileContent($uri);
        
        // Prepare output file
        $hash = md5(implode('|', $uris));
        $filename = 'public/cache/sass/' . $hash . '.css';
        if (! file_exists(dirname($filename)))
        	mkdir(dirname($filename), 0777, true);
        
        // Compile
        $scssc = new \Leafo\ScssPhp\Compiler();
        $s = $scssc->compile($content);
        
        file_put_contents($filename, $s);
        $headLink()->appendStylesheet('/cache/sass/' . $hash . '.css');        
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
    

    public function compressHtml($e)
    {        
        $config = $e->getApplication()->getServiceManager()->get('KofusConfig');
    	if ($config->get('assets.compress_html')) {
    		$body = $e->getApplication()->getResponse()->getContent();
    		if (strpos($body, '<!DOCTYPE html') === 0) {
    			$body = preg_replace('/\s+/', ' ', $body);
    			$e->getApplication()->getResponse()->setContent($body);
    		}
    	}
    }
    
    
}