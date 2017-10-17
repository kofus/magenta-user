<?php
namespace Kofus\System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Form\Element\Submit;
use Zend\Form\Form;


class ConsoleController extends AbstractActionController
{
    public function rebuildLuceneIndexAction()
    {
        print $this->lucene()->reindex($this->params('node_type'));
        
            
    }
    
}
