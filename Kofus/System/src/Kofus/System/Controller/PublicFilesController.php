<?php

namespace Kofus\System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class PublicFilesController extends AbstractActionController
{
    /**
	/* Deliver file from lib directory and copy it to cache folder
	 * for future request 
	 */
    public function dumpAction()
    {
        $uri = $this->getRequest()->getUri()->getPath(); 
        $service = $this->getServiceLocator()->get('KofusPublicFilesService');
        $filename = $service->getFilenameByUri($uri);
        $type = $service->getMimeType($filename);
        $service->createCacheFile($uri);
        
        //$view = new ViewModel();
        //$view->setTerminal(true);
        
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $contentType = new \Zend\Http\Header\ContentType($type);
        $headers->addHeader($contentType);
        $response->setHeaders($headers);
        $response->setContent(file_get_contents($filename));
        
        return $response;
    }
}