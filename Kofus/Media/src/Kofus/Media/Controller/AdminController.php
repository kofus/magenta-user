<?php

namespace Kofus\Media\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Imagick;
use Zend\Http\Response;

class AdminController extends AbstractActionController
{
    public function clearimagecacheAction()
    {
        $this->media()->clearcache();
    }
    	
}