<?php

namespace Kofus\System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class TagVocabularyController extends AbstractActionController
{
    public function indexAction()
    {
        return $this->redirect()->toRoute('kofus_system', array('controller' => 'tag-vocabulary', 'action' => 'list'));
    }
    public function listAction()
    {
        $this->archive()->uriStack()->push();
        $qb = $this->nodes()->createQueryBuilder('TV');
        return new ViewModel(array(
            'paginator' => $this->paginator($qb)
        ));
    }
    
    
}