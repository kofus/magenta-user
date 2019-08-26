<?php

namespace Kofus\System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class TagController extends AbstractActionController
{
    public function listAction()
    {
        $this->archive()->uriStack()->push();
        $qb = $this->nodes()->createQueryBuilder('T');
        $qb->orderBy('n.title', 'ASC');
        return new ViewModel(array(
            'paginator' => $this->paginator($qb)
        ));
    }

}