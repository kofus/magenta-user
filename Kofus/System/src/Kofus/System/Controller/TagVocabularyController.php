<?php

namespace Kofus\System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class TagVocabularyController extends AbstractActionController
{
    public function listAction()
    {
        $this->archive()->uriStack()->push();
        $qb = $this->nodes()->createQueryBuilder('TAGV');
        return new ViewModel(array(
            'paginator' => $this->paginator($qb)
        ));
    }
    
    
}