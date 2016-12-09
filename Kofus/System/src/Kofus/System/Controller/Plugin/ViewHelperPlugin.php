<?php

namespace Kofus\System\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;


class ViewHelperPlugin extends AbstractPlugin
{
    protected $manager;
    
    protected function getManager()
    {
        if (! $this->manager) 
            $this->manager = $this->getController()->getServiceLocator()->get('ViewHelperManager');
        return $this->manager;
	}
	
	public function __call($method, $args)
	{
	    $helper = $this->getManager()->get($method);
	    return call_user_func_array(array($helper, '__invoke'), $args);
	}
	
	

}