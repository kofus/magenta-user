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
    
    protected function getPageArray($tag)
    {
        $page = array(
            'label' => $tag->getTitle(),
            'uri' => '/de/kofus/system/node/edit/' . $tag->getNodeId(),
            'node-id' => $tag->getNodeId()
        );
        foreach ($tag->getChildren() as $child) {
            $page['pages'][] = $this->getPageArray($child);
        }
        return $page;
    }
    
    public function viewAction()
    {
        $this->archive()->uriStack()->push();
        $vocab = $this->nodes()->getNode($this->params('id'), 'TV');
        
        $tags = $this->nodes()->getRepository('T')->findBy(array('vocabulary' => $vocab));
        

        $navArray = array();
        foreach ($tags as $tag) {
            $navArray[] = $this->getPageArray($tag);
  
        }
        
        $navContainer = new \Zend\Navigation\Navigation($navArray);
        
        return new ViewModel(array(
            'vocabulary' => $vocab,
            'navContainer' => $navContainer
        ));
    }
    
    
}