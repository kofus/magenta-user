<?php
namespace Kofus\System\View\Helper\Optimizer;


abstract class AbstractHelper extends \Zend\View\Helper\AbstractHelper 
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
    	    $service = new \Kofus\System\Service\LibService();
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
    
    
    
}