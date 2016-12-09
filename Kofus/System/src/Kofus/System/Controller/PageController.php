<?php

namespace Kofus\System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class PageController extends AbstractActionController
{
    public function listAction()
    {
        $this->archive()->uriStack()->push();
        $navbars = $this->config()->get('navbars');     
        $view = new ViewModel(array(
        	'navbars' => $navbars
        ));
        
        return $view;
    }
    
    public function viewAction()
    {
        $this->archive()->uriStack()->push();
        $entity = $this->nodes()->getNode($this->params('id'), 'PG');
        
        $view = new ViewModel(array(
        	'entity' => $entity,
        ));
        
        $relations = array();
        foreach ($this->config()->get('relations.enabled') as $_relation) {
            $relation = explode('_', $_relation);
            if ($relation[0] == 'PG') {
                $relations[$relation[1]] = $this->nodes()->getRelations($entity, $relation[1]);
            }
        }
        
        $view->relations = $relations;
        return $view;
    }
}