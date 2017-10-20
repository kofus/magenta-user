<?php

namespace Kofus\Media\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Imagick;
use Zend\Http\Response;

class ImageController extends AbstractActionController
{
    public function processAction()
    {
        $uri = $this->getRequest()->getRequestUri();
        $link = $this->em()->getRepository('Kofus\System\Entity\LinkEntity')->findOneBy(array('uri' => $uri));
        if (! $link) {
            $fullUri = 'http://' . $_SERVER['HTTP_HOST'] . $uri;
            $link = $this->em()->getRepository('Kofus\System\Entity\LinkEntity')->findOneBy(array('uri' => $fullUri));
        }
        
        if (! $link) {
            return $this->getResponse()->setStatusCode(Response::STATUS_CODE_404)
                ->setReasonPhrase('Not Found')
                ->setContent('Not Found');
        }
            
        $image = $this->nodes()->getNode($link->getLinkedNodeId());
        $imagick = $this->media()->process($image, $link->getContext());
        
        // Save?
        $spec = $this->config()->get('media.image.displays.available.' . $link->getContext());
        $useCache = true;
        if (array_key_exists('cache', $spec))
            $useCache = $spec['cache'];
        
        if ($useCache) {
            $cacheFilename = 'public/' . $uri;
            if (! is_dir(dirname($cacheFilename))) {
                if (! mkdir(dirname($cacheFilename), 0777, true))
                    throw new \Exception('Could not create directory ' . dirname($cacheFilename));
            }
            $imagick->writeImage($cacheFilename);
        }
        
        $response = $this->getResponse()
            ->setStatusCode(Response::STATUS_CODE_200)
            ->setContent($imagick);
        $response->getHeaders()->addHeaderLine('Content-Type', 'image/' . strtolower($imagick->getImageFormat()));
        return $response;
    }
    	
}