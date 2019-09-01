<?php
namespace Kofus\System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\View\Model\ViewModel;
use Kofus\System\Node\TranslatableNodeInterface;



class RelationController extends AbstractActionController
{
    public function addAction()
    {
        $nodeType = $this->params()->fromQuery('add');
        $nodeTypeConfig = $this->nodes()->getConfig($nodeType);
        $relatedNode = $this->nodes()->getNode($this->params('id'));
        $spec = $this->config()->get('relations.available.' . $relatedNode->getNodeType() . '_' . $nodeType, array());
        
        // Entity
        $entityClass = $nodeTypeConfig['entity'];
        $entity = new $entityClass();
        if ($entity instanceof ServiceLocatorAwareInterface)
        	$entity->setServiceLocator($this->getServiceLocator());
        
        // Forms
        $fieldsetRelation = new \Kofus\System\Form\Fieldset\Relation\RelationFieldset('relation');
        $fieldsetRelation->setSpecifications($spec);
        
        $formCreate = $this->formBuilder()
            ->setEntity($entity)
            ->setContext('add')
            ->setLabelSize('col-sm-3')->setFieldSize('sm-9')
            ->addFieldset($fieldsetRelation)
            ->buildForm()
            ->add(new \Zend\Form\Element\Submit('submit', array('label' => 'Save')));

        $fieldsetRelation2 = new \Kofus\System\Form\Fieldset\Relation\RelationFieldset('relation');
        $fieldsetRelation2->setSpecifications($spec);
        $fieldsetLinkedNode = new \Kofus\System\Form\Fieldset\Relation\LinkedNodeFieldset('linked_node');
        $fieldsetLinkedNode->setNodeLabel($nodeTypeConfig['label']);
        $fieldsetLinkedNode->setNodes($this->em()->getRepository($nodeTypeConfig['entity'])->findAll());
        $formLink = $this->formBuilder()
            ->reset()
            ->setLabelSize('col-sm-3')->setFieldSize('sm-9')
            ->addFieldset($fieldsetLinkedNode)
            ->addFieldset($fieldsetRelation2)
            ->buildForm()
            ->add(new \Zend\Form\Element\Submit('submit', array('label' => 'Save')));
        
        
        $formCreate->bind($entity);
        
        if ($this->getRequest()->isPost()) {
            
            if ($this->getRequest()->getPost('linked_node')) {
                $formLink->setData($this->getRequest()->getPost());
                if ($formLink->isValid()) {
                    
                    // Create relation
                    $relation = new \Kofus\System\Entity\RelationEntity();
                    $relation->setNode1Id($formLink->get('linked_node')->get('linked_node_id')->getValue())
                        ->setNode2Id($relatedNode->getNodeId())
                        ->setLabel($formLink->get('relation')->get('label')->getValue())
                        ->setWeight($formLink->get('relation')->get('weight')->getValue());
                    $this->em()->persist($relation);
                    $this->em()->flush();
                    
                    $this->flashmessenger()->addSuccessMessage('Added relation');
                    return $this->redirect()->toUrl($this->archive()->uriStack()->pop());
                }
                
            } else {
                $data = array_merge_recursive($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());
            	$formCreate->setData($data);
            	
            	if ($formCreate->isValid()) {
            	     
            		$this->em()->persist($entity);
            		$this->em()->flush();
            		$this->links()->rebuildLinks($entity);
            		
            		// Create relation
            		$relation = new \Kofus\System\Entity\RelationEntity();
            		$relation->setNode1Id($entity->getNodeId())
                        ->setNode2Id($relatedNode->getNodeId())
                        ->setLabel($formCreate->get('relation')->get('label')->getValue())
                        ->setWeight($formCreate->get('relation')->get('weight')->getValue());
            		$this->em()->persist($relation);
            		$this->em()->flush();
            		
            		$this->flashmessenger()->addSuccessMessage('Added relation');
            		return $this->redirect()->toUrl($this->archive()->uriStack()->pop());
            	} else {
            		//print_r($formCreate->getMessages()); die();
            	}
            }
        }
        
        $actions = array('create', 'link');
        if (isset($spec['actions']))
            $actions = $spec['actions'];
        
        return new ViewModel(array(
                'actions' => $actions,
        		'formCreate' => $formCreate->prepare(),
                'formLink' => $formLink->prepare(),
        		'nodeTypeConfig' => $nodeTypeConfig,
        		'formTemplate' => 'kofus/system/relation/form/panes.phtml',
                'relatedNode' => $relatedNode
        ));
        
    }
    public function editAction()
    {
    	$locales = $this->config()->get('locales.available');
    	$translator = $this->getServiceLocator()->get('translator');
    
    	// Entity
    	$relation = $this->nodes()->getRelation($this->params('id'), $this->params()->fromQuery('edit'));
    	$entity = $relation->getNode();
    	$nodeTypeConfig = $this->nodes()->getConfig($entity->getNodeType());
    
    	// Form
    	$fieldsetRelation = new \Kofus\System\Form\Fieldset\Relation\RelationFieldset('relation');
    	$fieldsetRelation->setSpecifications($this->config()->get('relations.available.' . $relation->getType(), array()));
    	 
    	//$fieldsetRelation->setSpecification($this->config()->get('data_items.available.'. . ));
    	$serviceForm = $this->getServiceLocator()->get('KofusFormService');
    	$fb = $serviceForm
        	->setEntity($entity)
        	->setContext('edit')
        	->setLabelSize('col-sm-3')->setFieldSize('sm-9')

        	->addFieldset($fieldsetRelation);
  
    	foreach ($this->config()->get('locales.available') as $locale) {
    	    if ($locale != $this->config->get('locales.default', 'de_DE'))
    	        $fb->addTranslationFieldset($locale);
    	}
    	    
    	$form = $fb->buildForm();
        $form->add(new \Zend\Form\Element\Submit('submit', array('label' => 'Save')));
    
    	$form->bind($entity);
    	 
    	if ($this->getRequest()->isPost()) {
    	    $data = array_merge_recursive($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());
    	    	
    		$form->setData($data);
    		//$this->uploader()->handleUpload($form, $entity);
    		if ($form->isValid()) {
    
    			// Save entity
    			$this->em()->persist($entity);
    			$this->em()->flush();
    			$this->links()->rebuildLinks($entity);
    			$this->media()->clearCache($entity);
    			
    			// Save translations
    			$translations = $this->getServiceLocator()->get('KofusTranslationService');
    			if ($entity instanceof TranslatableNodeInterface) {
    				foreach ($form as $fieldset) {
    					if (! $fieldset instanceof \Zend\Form\FieldsetInterface)
    						continue;
    					$locale = substr($fieldset->getName(), 0, 5);
    					if (in_array($locale, $locales)) {
    						foreach ($entity->getTranslatableMethods() as $method => $attribute) {
    							if (! $fieldset->has($attribute)) continue;
    							$value = $fieldset->get($attribute)->getValue();
    							$translations->addNodeTranslation($entity, $method, $value, $locale);
    						}
    					}
    				}
    			}
    			 
    			 
    			// Save relation data
    			$relation->setLabel($form->get('relation')->get('label')->getValue());
    			$relation->setWeight($form->get('relation')->get('weight')->getValue());
    			$this->em()->persist($relation);
    			$this->em()->flush();
    			 
    			$this->flashmessenger()->addSuccessMessage(sprintf($translator->translate('Updated %s'), $translator->translate($nodeTypeConfig['label']) . ' ' . $entity->getNodeId()));
    			return $this->redirect()->toUrl($this->archive()->uriStack()->pop());
    		}
    	} else {
    			
    		// Load translations
    		if ($entity instanceof TranslatableNodeInterface) {
    			foreach ($form as $fieldset) {
    				if (! $fieldset instanceof \Zend\Form\FieldsetInterface)
    					continue;
    				$locale = substr($fieldset->getName(), 0, 5);
    				if (in_array($locale, $locales)) {
    					foreach ($entity->getTranslatableMethods() as $method => $attribute) {
    						if (! $fieldset->has($attribute)) continue;
    						$msgId = $entity->getNodeId() . ':' . $method;
    						$msg = $this->em()->getRepository('Kofus\System\Entity\NodeTranslationEntity')->findOneBy(array('msgId' => $msgId, 'locale' => $locale));
    						if ($msg) {
    							$fieldset->get($attribute)->setValue($msg->getValue());
    						} else {
    							$fieldset->get($attribute)->setValue($entity->$method());
    						}
    					}
    				}
    			}
    		}
    		
    		// Load relation data
    		$form->get('relation')->get('label')->setValue($relation->getLabel());
    		$form->get('relation')->get('weight')->setValue($relation->getWeight());
    	}
    	 
    	 
    	 
    	return new ViewModel(array(
    			'form' => $form->prepare(),
    			'nodeTypeConfig' => $nodeTypeConfig,
    			'formTemplate' => 'kofus/system/node/form/panes.phtml',
    			'entity' => $entity,
    	        'relation' => $relation,
    			'locales' => $locales
    	));
    }
    
    /**
     * Repair operations:
     * - delete relation with a missing (already deleted) node
     */
    public function repairAction()
    {
        $alpha = new \Zend\I18n\Filter\Alpha();
        $relations = $this->em()->getRepository('Kofus\System\Entity\RelationEntity')->findAll();
        foreach ($relations as $relation) {
            $nodeIds = array($relation->getNode1Id(), $relation->getNode2Id());
            foreach ($nodeIds as $nodeId) {
                $nodeType = $alpha->filter($nodeId);
                if (! $this->nodes()->isNodeTypeEnabled($nodeType))
                    continue;
                $node = $this->nodes()->getNode($nodeId);
                if (! $node) {
                    print $relation->getId() . ': ' . $relation->getNode1Id() . '_' . $relation->getNode2Id() . '<br>';                    
                    $this->em()->remove($relation);
                    continue;
                }
            }
        }
        $this->em()->flush();
        die('done');
    }
    
    public function deleteAction()
    {
        $nodeType = $this->params()->fromQuery('delete');
        $relation = $this->nodes()->getRelation($this->params('id'), $nodeType);
        $view = new ViewModel();
        $node = null;
        
        
        if ($nodeType) {
            $node = $relation->getNode();
            $view->node = $node;
            $view->nodeTypeConfig = $this->nodes()->getConfig($nodeType);
        }
        
        // Init
        $session = new \Zend\Session\Container('RelationController_delete');
        
        if ($this->params()->fromQuery('confirm') && $this->params()->fromQuery('confirm') == $session->token) {
            $node = null;
            $this->nodes()->deleteRelation($relation, $node);
            
        	$this->flashmessenger()->addSuccessMessage('Node has been deleted');
        	return $this->redirect()->toUrl($this->archive()->uriStack()->pop());
        }
        $session->token = \Zend\Math\Rand::getString(8, 'abcdefghijklmnopqrstuvwxyz0123456789');
        
        return new ViewModel(array(
        		'token' => $session->token,
            'relation' => $relation,
            'nodeType' => $nodeType
        ));
        
    }
    
    

}
