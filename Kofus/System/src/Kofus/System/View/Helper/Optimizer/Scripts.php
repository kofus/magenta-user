<?php
namespace Kofus\System\View\Helper\Optimizer;
use Kofus\System\View\Helper\Optimizer\AbstractHelper;
use Zend\Uri\UriFactory;


class Scripts extends AbstractHelper
{
    public function compress()
    {
        if ($this->isDebug() || ! is_file($this->getHashFilename())) {
            $content = '';
            foreach ($this->getFilenames() as $filename) {
                if (strpos($filename, 'assets/ckeditor') === false)
                    $content .= $this->getContent($filename);
            
            }
            $content = $this->compressContent($content);
            $content = $this->getBranding() . $content;
            
        	$this->makeFile($this->getHashFilename(), $content);
        }
        $this->removeHeadScripts();
        $this->view->headScript()->appendFile($this->getHashUrl());
        
    }
    
    public function getUrls()
    {
        if (! $this->urls) {
            foreach ($this->view->headScript() as $index => $script) {
            	if ($script->type == 'text/javascript' && isset($script->attributes['src'])) {
            	    $uri = UriFactory::factory($script->attributes['src']);
            	    if (! $uri->isAbsolute())
            	        $this->urls[$index] = $script->attributes['src'];
            	}
            }
        }
        return $this->urls;
    }
    
    protected function getHashUrl()
    {
    	if (! $this->hashUrl) {
    		$s = implode(' ', $this->getFilenames()) . ' ' . $this->getLastChange();
    		$this->hashUrl = '/cache/scripts/' . md5($s) . '.js';
    	}
    	return $this->hashUrl;
    }
    
    protected function getHashFilename()
    {
    	return 'public' . $this->getHashUrl();
    }
    
    protected function removeHeadScripts()
    {
    	foreach ($this->getUrls() as $index => $link) {
    	    if (strpos($link, 'assets/ckeditor') === false)
        		$this->view->headScript()->offsetUnset($index);
    	}
    }
    
    protected function compressContent($content)
    {
    	// Strip comments
    	$content = preg_replace('/\/\/ .+?\n/', '', $content);
    	$content = preg_replace('!/\*.*?\*/!s', '', $content);
    	$content = preg_replace('/\n\s*\n/', "\n", $content);
    
    	// Strip whitespaces
    	//$content = preg_replace('/\s+/', ' ', $content);
    
    	return $content;
    }
    
    
    protected function getContent($filename)
    {
   		return file_get_contents($filename) . ' ';
    }
    
    public function mergeScripts()
    {
    	$scripts = array();
    	foreach ($this->view->headScript() as $index => $script) {
    		if ($script->type == 'text/javascript' && isset($script->attributes['src']))
    			$scripts[$index] = $script;
    	}
    
    	$hashes = array();
    	foreach ($scripts as $index => $script) {
    		$this->view->headScript()->offsetUnset($index);
    		$hashes[] = md5(serialize($script));
    	}
    	$hash = md5(implode(' ', $hashes));
    	$this->view->headScript()->appendFile('/cache/js/' . $hash . '.js');
    	$mergedFilename = 'public/cache/js/' . $hash . '.js';
    
    	if (! is_dir(dirname($mergedFilename)))
    		mkdir(dirname($mergedFilename), 0777, true);
    
    
    	// Collect filenames and last timestamp
    	$filenames = array();
    	$lastModified = 0;
    	foreach ($scripts as $script) {
    		$found = false;
    		print $script->attributes['src'] . '<br>';
    		 
    		foreach ($this->assetPaths as $assetPath) {
    			$jsFilename = $assetPath . '/' . $script->attributes['src'];
    			if (file_exists($jsFilename)) {
    				$filenames[] = $jsFilename;
    				$found = true;
    				$lastModified = max(filemtime($jsFilename), $lastModified);
    				break;
    			}
    		}
    		if (! $found)
    			throw new \Exception('Javascript file not found: ' . $script->attributes['src']);
    	}
    	die();
    
    	// Merge
    	if ($this->debug || ! file_exists($mergedFilename) || filemtime($mergedFilename) < $lastModified) {
    		$f = fopen($mergedFilename, 'w');
    		foreach ($filenames as $filename)
    			fwrite($f, file_get_contents($filename));
    		fclose($f);
    	}
    }
        
    
    
}