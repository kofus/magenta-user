<?php
namespace Kofus\System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Form\Element\Submit;
use Zend\Form\Form;


class SearchController extends AbstractActionController
{
    public function indexAction()
    {
    	// Query node types
    	$nodeTypes = array();
     	foreach ($this->config()->get('nodes.enabled') as $nodeType) {
     		$nodeTypeConfig = $this->config()->get('nodes.available.' . $nodeType);
     		if (isset($nodeTypeConfig['search_documents'])) {
	     		$nodeTypes[$nodeType]['config'] = $nodeTypeConfig;
     			foreach ($this->config()->get('locales.enabled') as $locale) {
	     			$hits = $this->search()->getIndex($locale)->find('node_type: ' . $nodeType);
     				$nodeTypes[$nodeType][$locale] = count($hits);
     			}
     		}
     	}   
        
     	// Build form
     	$form = new Form();
     	foreach ($nodeTypes as $nodeType => $delta) {
     		$el = new \Zend\Form\Element\Checkbox($nodeType);
     		$form->add($el);
     	}
     	$submitDelete = new Submit('delete', array('label' => 'Delete'));
     	$submitDelete->setValue('delete');
     	$form->add($submitDelete);
     	
     	$submitIndex = new Submit('index', array('label' => 'Index'));
     	$submitIndex->setValue('index');
     	$form->add($submitIndex);
     	
     	// Handle submit
     	if ($this->getRequest()->isPost()) {
     		$form->setData($this->getRequest()->getPost());
     		if ($form->isValid()) {
     			if ($this->getRequest()->getPost('delete') == 'delete') {
	     			foreach ($nodeTypes as $nodeType => $delta) {
	     				if ($form->get($nodeType)->getValue())
	     					$this->search()->deleteNodeType($nodeType);
	     			}
     			}
     			if ($this->getRequest()->getPost('index') == 'index') {
     				foreach ($nodeTypes as $nodeType => $delta) {
     					if ($form->get($nodeType)->getValue()) 
     						$this->search()->reindex($nodeType);
     				}
     			}
     			return $this->redirect()->toRoute('kofus_system', array('controller' => 'search', 'action' => 'index'));
     		}
     	}
     		
     	
     	
     	return new ViewModel(array(
     		'nodeTypes' => $nodeTypes,
     		'form' => $form
     	));
        
        
            
            
    }
    
}
