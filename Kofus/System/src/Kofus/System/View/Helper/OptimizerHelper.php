<?php

namespace Kofus\System\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Kofus\System\View\Helper\Optimizer;

class OptimizerHelper extends AbstractHelper
{
    protected $debug = false;
    
    public function __invoke()
    {
    	return $this;
    }
    
    public function isDebug($bool=null)
    {
        if ($bool !== null) 
            $this->debug = (bool) $bool;
        return $this->debug;
    }
    
    public function css()
    {
        $helper = new Optimizer\Css();
        $helper->setView($this->getView());
        $helper->isDebug($this->isDebug());
        return $helper;
    }
    
    public function scripts()
    {
        $helper = new Optimizer\Scripts();
        $helper->setView($this->getView());
        $helper->isDebug($this->isDebug());
        return $helper;
    }
    
    public function sass()
    {
        $helper = new Optimizer\Sass();
        $helper->setView($this->getView());
        $helper->isDebug($this->isDebug());
        return $helper;
    }
    
    public function clearCache()
    {
        $paths = array(
        	'public/cache/css',
            'public/cache/scripts',
            'public/cache/sass',
            'public/cache/js'
        );
        
        foreach ($paths as $path) {
            $files = glob($path . '/*');
            foreach ($files as $file) {
                if (is_file($file))
                    unlink($file);
            }
            if (is_dir($path)) rmdir($path);
        }
    }
}