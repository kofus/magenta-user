<?php
namespace Kofus\System\Listener;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\Http\Request as HttpRequest;

class PublicFilesListener extends AbstractListenerAggregate implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();
        $this->listeners[] = $events->attach(MvcEvent::EVENT_BOOTSTRAP, array($this, 'provideModulePublicFiles'));
    }
    
    public function provideModulePublicFiles(MvcEvent $e)
    {
        $request = $e->getRequest();
        if (! $request instanceof HttpRequest)
            return;
        
        $uri = $request->getUri();
        $requestPath = urldecode($uri->getPath()); 
        
        if (strpos($requestPath, '/cache/public/') === 0) {
        
            // Validate request path
            if (preg_match('/\.\.+/', $requestPath) || preg_match('/[^a-z0-9\-\_\/\.]/i', $requestPath)) {
            	$response = $e->getResponse();
            	$response->setStatusCode('400');
            	$response->setContent('Error: Bad request');
            	$response->send();
            	exit();
            }

            $filePaths = $e->getApplication()->getServiceManager()->get('KofusConfig')->get('public_paths');
            foreach ($filePaths as $filePath) {
                $sourceFilename = realpath($filePath . '/' . str_replace('/cache/public/', '', $requestPath));
                if (! $sourceFilename) continue;
                
                $targetFilename = 'public' . $requestPath;

                if (! file_exists(dirname($targetFilename)))
                    mkdir(dirname($targetFilename), 0777, true);
                copy($sourceFilename, $targetFilename);
                
                $response = $e->getResponse();
                $headers = $response->getHeaders();
                $contentType = new \Zend\Http\Header\ContentType($this->getMimeType($targetFilename));
                $headers->addHeader($contentType);
                $response->setHeaders($headers);
                $response->setContent(file_get_contents($targetFilename));
                $response->send();
                exit();
            }
        }
    }
    
    public static function getMimeType($filename)
    {
    	if (preg_match('/\.css$/i', $filename)) {
    		$type = 'text/css';
    	} elseif (preg_match('/\.js$/i', $filename)) {
    		$type = 'text/javascript';
    	} elseif (preg_match('/\.svg$/i', $filename)) {
    		$type = 'image/svg+xml';
    	} else {
    		//$finfo = finfo_open(FILEINFO_MIME_TYPE, $this->config()->get('executables.magic', '/usr/share/misc/magic'));
    		$finfo = finfo_open(FILEINFO_MIME_TYPE);
    		if (! $finfo)
    			throw new \Exception('finfo not available');
    		$type = finfo_file($finfo, $filename);
    		finfo_close($finfo);
    	}
    	return $type;
    }
  
    
}