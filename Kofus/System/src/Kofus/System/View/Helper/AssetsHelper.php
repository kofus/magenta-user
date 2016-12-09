<?php

namespace Kofus\System\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class AssetsHelper extends AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $sm;
    protected $available = array();
    protected $enabled = array();
    
    
    public function __invoke($layout='default')
    {
    	$this->layout = $layout;
        if (! $this->available && ! isset($this->enabled[$layout])) {
            $config = $this->getServiceLocator()->getServiceLocator()->get('Config');
            if (isset($config['assets']['available']))
                $this->available = $config['assets']['available'];
            if (isset($config['assets']['enabled'][$layout]))
                $this->enabled[$layout] = $config['assets']['enabled'][$layout];
        }
        return $this;
    }
    
    public function enable($asset)
    {
        if (! isset($this->available[$asset]))
            throw new \Exception('Asset "'.$asset.'" is not available. Available assets are: ' . implode(', ', array_keys($this->available)));
        
        if (! in_array($asset, $this->enabled[$this->layout]))
            $this->enabled[$this->layout][] = $asset;
        return $this;
    }
    
    public function getServiceLocator()
    {
        return $this->sm;
    }
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->sm = $serviceLocator;
        return $this;
    }
    
    public function getDependencies($asset)
    {
        $dependencies = array();
        if (isset($this->available[$asset]['dependencies']))
            $dependencies = $this->available[$asset]['dependencies'];
        $output = array();
        foreach ($dependencies as $dependency) {
            $output[] = $dependency;
            $output = array_merge($output, $this->getDependencies($dependency)); 
        }
        return $output;
        
    }
    protected function resolveDependencies()
    {
        foreach ($this->enabled[$this->layout] as $index => $asset) {
            foreach ($this->getDependencies($asset) as $dependency) {
                if (! in_array($dependency, $this->enabled[$this->layout]))
                    array_unshift($this->enabled[$this->layout], $dependency);             
            }
        }
    }
    
    public function deploy()
    {
        if (! isset($this->enabled[$this->layout])) return;
        
    	$this->appendLinks();
    	$this->appendInlines();
    }
    
    public function appendLinks()
    {
        if (! isset($this->enabled[$this->layout])) return;
        foreach ($this->enabled[$this->layout] as $asset) {
        	$assetFound = false;
            if (isset($this->available[$asset]['files']['css'])) {
                foreach ($this->available[$asset]['files']['css'] as $filename) {
                    if (isset($this->available[$asset]['base_uri']))
                        $filename = $this->available[$asset]['base_uri'] . '/' . $filename;
                    $this->view->headLink()->appendStylesheet($filename);
                    $assetFound = true;
                }
            }
            if (isset($this->available[$asset]['files']['js'])) {
            	foreach ($this->available[$asset]['files']['js'] as $filename) {
            		if (isset($this->available[$asset]['base_uri']))
            			$filename = $this->available[$asset]['base_uri'] . '/' . $filename;
            		
            		if ('html5' == $asset) {
                		$this->view->headScript()->appendFile($filename, 'text/javascript', array('conditional' => 'lt IE 9',));
            		} else {
            		    $this->view->headScript()->appendFile($filename);
            		}
            		$assetFound = true;
            	}
            }
            
            if (! $assetFound) throw new \Exception('No definition found for asset "' . $asset . '"');
        }
    }
    
    public function appendInlines()
    {
        if (! isset($this->enabled[$this->layout])) return;
    	foreach ($this->enabled[$this->layout] as $asset) {
    		if (isset($this->available[$asset]['files']['js-inlines'])) {
    			foreach ($this->available[$asset]['files']['js-inlines'] as $script) {
   					$this->view->headScript()->appendScript($script);
    			}
    		}
    	}
    }
}