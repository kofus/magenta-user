<?php

namespace Kofus\Archive\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class UriStackController extends AbstractActionController
{
    public function goBackAction()
    {
  		$uri = $this->archive()->uriStack()->pop();
  		if (md5($uri) == $this->params('namespace'))
  			$uri = $this->archive()->uriStack()->pop();
  		
  		return $this->redirect()->toUrl($uri);
    }

    
    
}
