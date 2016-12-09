<?php

namespace Kofus\Archive\View\Helper;
use Zend\View\Helper\AbstractHelper;



class UrlBackHelper extends AbstractHelper 
{
    public function __invoke()
    {
    	return $this->getView()->url('kofus_archive', array('controller' => 'uri-stack', 'action' => 'go-back', 'namespace' => md5($_SERVER['REQUEST_URI'])));
    }
    

}


