<?php 
namespace Kofus\Media\Service;
use Kofus\System\Service\AbstractService;
use Zend\Math\Rand;

class MediaService extends AbstractService
{
    
    protected function removeDir($dirPath)
    {
        foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dirPath, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $path) {
        	$path->isDir() && !$path->isLink() ? rmdir($path->getPathname()) : unlink($path->getPathname());
        }
    }
    
    public function clearCache(\Kofus\System\Node\NodeInterface $node=null)
    {
        if ($node === null) {
            $this->removeDir('public/cache/media');
            return; 
        }
        
        if ($node instanceof \Kofus\Media\Entity\FileEntity) {
            
            $links = $this->em()->getRepository('Kofus\System\Entity\LinkEntity')->findBy(array(
                'linkedNodeId' => $node->getNodeId()
            ));
            foreach ($links as $link) {
                $linkUri = \Zend\Uri\UriFactory::factory($link->getUri(), 'http');
                $filename = 'public/' . trim($linkUri->getPath(), '/');
                if (strpos($filename, 'public/cache/media/') !== 0)
                    return;
                if (file_exists($filename))
                    unlink($filename);
            }
        }
    } 
    
    public function getImageLink(\Kofus\Media\Entity\ImageEntity $image, $display='thumb', array $options=array())
    {
        $link = $this->em()->getRepository('Kofus\System\Entity\LinkEntity')->findOneBy(array('linkedNodeId' => $image->getNodeId(), 'context' => $display));
        if (! $link) {
            $config = $this->config()->get('media.image.displays.available.' . $display);
            
            $path = $image->getPath();
            if (! is_readable($path) && isset($config['error_image']))
                $path = $config['error_image'];
            
            $imagick = new \Imagick($path);
            $imagick = $this->process($image, $display);
            $extension = strtolower($imagick->getImageFormat());
            
            $r = Rand::getString(16, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXZY0123456789');
            $uri = '/cache/media/image/' . $display . '/' . $r . '.' . $extension;
            
            $mirrors = $this->config()->get('media.mirrors', array());
            if ($mirrors) {
                $key = array_rand($mirrors);
                $uri = trim($mirrors[$key], '/') . $uri;
            }
            
            $link = new \Kofus\System\Entity\LinkEntity();
            $link->setLinkedNodeId($image->getNodeId())
                ->setContext($display)
                ->setUri($uri);
            
            $this->em()->persist($link);
            $this->em()->flush();
        }
        
        return $link;
    }
    
    public function getPdfLink(\Kofus\Media\Entity\PdfEntity $pdf, array $options=array())
    {
    	$link = $this->em()->getRepository('Kofus\System\Entity\LinkEntity')->findOneBy(array('linkedNodeId' => $pdf->getNodeId(), 'context' => 'pdf'));
    	if (! $link) {
    	    if ($pdf->getUriSegment()) {
    	        $uriSegment = $pdf->getUriSegment();
    	    } elseif ($pdf->getTitle()) {
    	        $filter = new \Kofus\System\Filter\UriSegment();
    	        $uriSegment = $filter->filter($pdf->getTitle());
    	    } else {
                $uriSegment = Rand::getString(16, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXZY0123456789');
    	    }
    	    
    	    $uri = '/cache/media/pdf/' . $uriSegment . '.pdf';
    
    		$link = new \Kofus\System\Entity\LinkEntity();
    		$link->setLinkedNodeId($pdf->getNodeId())
        		->setContext('pdf')
        		->setUri($uri);
    
    		$this->em()->persist($link);
    		$this->em()->flush();
    	}
    
    	return $link;
    }
    
    
    public function process(\Kofus\Media\Entity\ImageEntity $node, $display)
    {
        $config = $this->config()->get('media.image.displays.available.' . $display);
        if (! $config)
            throw new \Exception('No media specifications found for ' . $node->getNodeType() . ' / ' . $display);

        $path = $node->getPath();
        if (! is_readable($path) && isset($config['error_image']))
            $path = $config['error_image'];
        
        if ($node instanceof \Kofus\Media\Entity\PdfEntity)
            $path .= '[0]';
        $imagick = new \Imagick($path);
        
        $pluginManager = new \Zend\Filter\FilterPluginManager();
        $filenames = scandir(__DIR__ . '/../Imagick/Filter');
        foreach ($filenames as $filename) {
            if (in_array($filename, array('.', '..')))
                continue;
            $classname = 'Kofus\Media\Imagick\Filter\\' . str_replace('.php', '', $filename);
            $filtername = str_replace('.php', '', $filename);
            $pluginManager->setInvokableClass($filtername, $classname);
        }
        
        $filterChain = new \Zend\Filter\FilterChain();
        $filterChain->setPluginManager($pluginManager);
        $filterChain->setOptions($config);
        $imagick = $filterChain->filter($imagick);
        
        return $imagick;
    }
    
    
    
}
