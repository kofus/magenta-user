<?php
namespace Kofus\System\View\Helper\Optimizer;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


abstract class AbstractHelper extends \Zend\View\Helper\AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $urls = array();
    protected $filenames = array();
    protected $hashUrl;
    
    protected $debug = false;
    
    public function isDebug($bool=null)
    {
    	if ($bool !== null)
    		$this->debug = (bool) $bool;
    	return $this->debug;
    }
    
    protected function getFilenames()
    {
    	if (! $this->filenames) {
    	    $service = $this->getServiceLocator()->get('KofusPublicFilesService');

    		foreach ($this->getUrls() as $index => $url) {
    		    $filename = $service->getFilenameByUri($url);
    			if (! $filename)
    				throw new \Exception('Could not map url '.$url.' to a valid filename');
    			$this->filenames[$index] = $filename;
    		}
    	}
    	return $this->filenames;
    }
    
    protected function getLastChange()
    {
    	$mtime = 0;
    	foreach ($this->getFilenames() as $filename)
    		$mtime = max($mtime, filemtime($filename));
    	return $mtime;
    }
    
    protected function makeFile($path, $content)
    {
        if (! is_dir(dirname($path))) {
            if (! mkdir(dirname($path), 0777, true)) 
                throw new \Exception('Could not create directory ' . dirname($path));
        }
        
        if (false === file_put_contents($path, $content))
            throw new \Exception('Could not create file ' . $path);
    }
    
    protected function getBranding()
    {
        return '/* KOFUS Optimizer ' . date('Y-m-d H:i:s') . ' */ ';        
    }
    
    protected $sm;
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
    	$this->sm = $serviceLocator;
    }
    
    public function getServiceLocator()
    {
    	return $this->sm;
    }    
    
    
    
}