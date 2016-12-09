<?php

namespace Kofus\System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class GenericController extends AbstractActionController
{
    /**
	/* Deliver file from lib directory and copy it to cache folder
	 * for future request 
	 */
    public function dumplibfileAction()
    {
        $uri = $this->getRequest()->getUri()->getPath(); 
        $service = new \Kofus\System\Service\LibService();
        $filename = $service->getFilenameByUri($uri);
        $type = \Kofus\System\Service\LibService::getMimeType($filename);
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