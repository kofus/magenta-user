<?php
namespace Kofus\System\View\Helper\Optimizer;
use Kofus\System\View\Helper\Optimizer\AbstractHelper;

class Sass extends AbstractHelper
{
    public function compile()
    {
    	$scss = '';
    	$hash = array();
        foreach ($this->getFilenames() as $index => $filename) {
        	$scss .= file_get_contents($filename) . "\n";
        	$hash[] = $filename;
                
                $this->view->headLink()->offsetUnset($index);
        }
        
        $hashId = md5(implode('|', $hash));
        $this->makeFile('public/cache/sass/' . $hashId . '.css', $this->compileFile($scss));
        $this->view->headLink()->appendStylesheet('/cache/sass/' . $hashId . '.css');
        
    }
    
    protected function getUrls()
    {
    	if (! $this->urls) {
    		foreach ($this->view->headLink() as $index => $link) {
    			if ($link->rel == 'stylesheet' && $link->href !== false) {
    			    if (preg_match('/\.scss$/', $link->href))
    				    $this->urls[$index] = $link->href;
    			}
    		}
    	}
    	return $this->urls;
    }
    
    protected function compileFile($content)
    {
        $scssc = new \scssc();
        $scssc->setFormatter('scss_formatter_compressed');
        $s = $scssc->compile($content);
        
        return $this->getBranding() . $s; 
    }

    /*
    protected function getHashFilename($filename)
    {
        return 'public' . $this->getHashUrl($filename);
    } */
    
    protected function getHashUrl($filename)
    {
   		$s = $filename . ' ' . $this->getLastChange();
        return '/cache/sass/' . md5($s) . '.css';
        
    }
}