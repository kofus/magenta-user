<?php

namespace Kofus\Media\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Imagick;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;

class PdfController extends AbstractActionController
{
    public function listAction()
    {
        $this->archive()->uriStack()->push();
        $entities = $this->nodes()->getRepository('PDF')->findAll();
        return new ViewModel(array(
            'entities' => $entities
        ));
    }
    
    public function dumpAction()
    {
        $uri = $this->getRequest()->getRequestUri();
        $link = $this->em()->getRepository('Kofus\System\Entity\LinkEntity')->findOneBy(array('uri' => $uri));
        if (! $link) {
            return $this->getResponse()->setStatusCode(Response::STATUS_CODE_404)
                ->setReasonPhrase('Not Found')
                ->setContent('Not Found');
        }
            
        $pdf = $this->nodes()->getNode($link->getLinkedNodeId());
        if (! $pdf instanceof \Kofus\Media\Entity\PdfEntity)
            throw new \Exception('Node '.$pdf->getNodeId().' must implement PdfEntity');
        
        $cacheFilename = 'public/' . $link->getUri();
        if (! is_dir(dirname($cacheFilename))) {
            if (! mkdir(dirname($cacheFilename), 0777, true))
                throw new \Exception('Could not create directory ' . dirname($cacheFilename));
        }
        copy($pdf->getPath(), $cacheFilename);
        
        $response = $this->getResponse()
            ->setStatusCode(Response::STATUS_CODE_200)
            ->setContent(file_get_contents($cacheFilename));
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/pdf');
        return $response;
    }
    	
}