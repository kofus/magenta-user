<?php

namespace Kofus\System\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Kofus\System\View\Helper\Optimizer;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class OptimizerHelper extends AbstractHelper implements ServiceLocatorAwareInterface
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
        $helper->setServiceLocator($this->getServiceLocator());
        return $helper;
    }
    
    public function scripts()
    {
        $helper = new Optimizer\Scripts();
        $helper->setView($this->getView());
        $helper->isDebug($this->isDebug());
        $helper->setServiceLocator($this->getServiceLocator());
        return $helper;
    }
    
    public function sass()
    {
        $helper = new Optimizer\Sass();
        $helper->setView($this->getView());
        $helper->isDebug($this->isDebug());
        $helper->setServiceLocator($this->getServiceLocator());
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