<?php
namespace Kofus\System\View\Helper\Optimizer;
use Kofus\System\View\Helper\Optimizer\AbstractHelper;
use Zend\Uri\UriFactory;

class Css extends AbstractHelper
{
    
    public function compress()
    {
        if ($this->isDebug() || ! is_file($this->getHashFilename())) {
            $content = '';
            foreach ($this->getFilenames() as $filename) {
                $content .= $this->getContent($filename) . "\n";
            }
            //$content = $this->compressContent($content);
            //$content = $this->getBranding() . $content;
            $this->makeFile($this->getHashFilename(), $content);
        }
        
        $this->removeHeadLinks();
        $this->view->headLink()->appendStylesheet($this->getHashUrl());
    }
    
    
    protected function compressContent($content)
    {
        // Strip comments
        $content = preg_replace('!/\*.*?\*/!s', '', $content);
        $content = preg_replace('/\n\s*\n/', "\n", $content);
        
        // Strip whitespaces
        $content = preg_replace('/\s+/', ' ', $content);
        
        return $content;
    }
    
    protected function getContent($filename)
    {
        $s = '';
        $content = file_get_contents($filename); 
        $subs = array();

        // Replace URLs
        $count = preg_match_all('/url\((.+?)\)/', $content, $matches);
        for ($i = 0; $i < $count; $i += 1) {
            $match1 = trim($matches[1][$i], ' "\'');
            if (strpos($match1, '..') === 0 || strpos($match1, '/') !== 0) {
            	$baseDir = dirname($this->toUrl($filename));
            	$url = $baseDir . '/' . $match1;
            	$url = preg_replace('/\/+/', '/', $url);
            	$subs[$matches[0][$i]] = "url('".$url."')";
            }
        }
        
        // Replace imports
        $count = preg_match_all('/\@import (.+?);/', $content, $matches);
        if ($count) {
            for ($i = 0; $i < $count; $i += 1) {
            	$match1 = trim($matches[1][$i], ' "\'');
        		$baseDir = dirname($this->toUrl($filename));
        		$url = $baseDir . '/' . $match1;
        		$url = preg_replace('/\/+/', '/', $url);
        		$includeFilename = $this->toFilename($url);
        		$subs[$matches[0][$i]] = $this->getContent($includeFilename);
            }
        }
        
        // Execute replacements
        foreach ($subs as $search => $replace) 
            $content = str_replace($search, $replace, $content);
        
        /*
        if (strpos($filename, 'imageflow')) {
            print htmlentities($content);
            die();
        } */
        	
        return $content;
    }
    
    protected function toFilename($url)
    {
        $service = new \Kofus\System\Service\LibService();
        return $service->getFilenameByUri($url);
    }
    
    protected function toUrl($filename)
    {
        $service = new \Kofus\System\Service\LibService();
        return $service->getUriByFilename($filename);
    }
    
    protected function getUrls()
    {
        if (! $this->urls) {
            foreach ($this->view->headLink() as $index => $link) {
            	if ($link->rel == 'stylesheet' && $link->href !== false) {
            	    $uri = UriFactory::factory($link->href); 
            	    if (! $uri->isAbsolute())
                		$this->urls[$index] = $link->href;
            	}
            }
        }
        return $this->urls;
    }
    
    
    
    protected function getHashUrl()
    {
        if (! $this->hashUrl) {
        	$s = implode(' ', $this->getFilenames()) . ' ' . $this->getLastChange();
        	$this->hashUrl = '/cache/css/' . md5($s) . '.css';
        }
        return $this->hashUrl;
        
    }
    
    protected function getHashFilename()
    {
        return 'public' . $this->getHashUrl();
    }
    
    protected function removeHeadLinks()
    {
        foreach ($this->getUrls() as $index => $link) {
       		$this->view->headLink()->offsetUnset($index);
        }
    }
    
}