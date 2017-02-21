<?php
namespace Kofus\System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\View\Model\ViewModel;
use Kofus\System\Node\LinkedNodeInterface;
use Kofus\System\Node\TranslatableNodeInterface;
use Zend\View\Model\JsonModel;


class NodeController extends AbstractActionController
{
    public function addAction()
    {
        $nodeType = $this->params('id');
        $nodeTypeConfig = $this->nodes()->getConfig($nodeType);
        
        // Entity
        $entityClass = $nodeTypeConfig['entity'];
        $entity = new $entityClass();
        if ($entity instanceof ServiceLocatorAwareInterface)
        	$entity->setServiceLocator($this->getServiceLocator());
        
        // Form
        $form = $this->formBuilder()
            ->setEntity($entity)
            ->setContext('add')
            ->setLabelSize('col-sm-3')->setFieldSize('sm-9')
            ->buildForm()
            ->add(new \Zend\Form\Element\Submit('submit', array('label' => 'Save')));
        
        if ($this->getRequest()->isPost()) {
            $data = array_merge_recursive($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());
        	$form->setData($data);
        	//$this->uploader()->handleUpload($form, $entity);
        	
        	if ($form->isValid()) {
        		$this->em()->persist($entity);
        		$this->em()->flush();
        		$this->links()->rebuildLinks($entity);
        		
        	    $translator = $this->getServiceLocator()->get('translator');
        		$this->flashmessenger()->addSuccessMessage(sprintf($translator->translate('Added %s'), $translator->translate($nodeTypeConfig['label']) . ' ' . $entity->getNodeId()));
        		
        		$filter = new \Kofus\System\Filter\SubstitutionFilter();
        		$filter->setParams(array('node_id' => $entity->getNodeId()));
        		$nodeTypeConfig = $filter->filter($nodeTypeConfig);
        		
        		$redirectRoute = null;
        		if (isset($nodeTypeConfig['form']['add']['redirect']['route'])) {
        			$redirectRoute = $nodeTypeConfig['form']['add']['redirect']['route'];
        		} elseif (isset($nodeTypeConfig['form']['default']['redirect']['route'])) {
        			$redirectRoute = $nodeTypeConfig['form']['default']['redirect']['route'];
        		}
        		
        		if ($redirectRoute) {
        			return call_user_func_array(array($this->redirect(), 'toRoute'), $redirectRoute);	
        		} else {
        			return $this->redirect()->toUrl($this->archive()->uriStack()->pop());
        		}
        	}
        }
        
        return new ViewModel(array(
            'form' => $form->prepare(), 
            'nodeTypeConfig' => $nodeTypeConfig,
            'formTemplate' => 'kofus/system/node/form/panes.phtml'
        ));
    }
    
    public function editAction()
    {
        $locales = $this->config()->get('locales.available', array('de_DE'));
        $translator = $this->getServiceLocator()->get('translator');
        
        
    	// Entity
    	$entity = $this->nodes()->getNode($this->params('id'));
    	if (! $entity instanceof TranslatableNodeInterface)
    	    $locales = array('de_DE');
    	$nodeTypeConfig = $this->nodes()->getConfig($entity->getNodeType());
    	 
    	// Form
    	$fb = $this->formBuilder()
        	->setEntity($entity)
        	->setContext('edit')
        	->setLabelSize('col-sm-3')->setFieldSize('sm-9');
    	if (in_array('en_US', $locales))
    	    $fb->addTranslationFieldset('en_US');
    	$form = $fb->buildForm()
        	->add(new \Zend\Form\Element\Submit('submit', array('label' => 'Save')));

    	$form->bind($entity);
    	
    	if ($this->getRequest()->isPost()) {
    	    $data = array_merge_recursive($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());
    		$form->setData($data);
    		//$this->uploader()->handleUpload($form, $entity);
    		if ($form->isValid()) {
    		    
    		    // Save entity
    			$this->em()->persist($entity);
    			$this->em()->flush();
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
    			
    			$this->links()->rebuildLinks($entity);
    			
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
    	}
    	
    	
    	
    	return new ViewModel(array(
    			'form' => $form->prepare(),
    			'nodeTypeConfig' => $nodeTypeConfig,
    			'formTemplate' => 'kofus/system/node/form/panes.phtml',
    	       'entity' => $entity,
    	       'locales' => $locales
    	));
    	 
    	 
    }
    
    
    public function deleteAction()
    {
        // Init        
        $entity = $this->nodes()->getNode($this->params('id'));
        $nodeTypeConfig = $this->nodes()->getConfig($entity->getNodeId());
        $session = new \Zend\Session\Container('NodeController_delete');
        
        if ($this->params()->fromQuery('confirm') && $this->params()->fromQuery('confirm') == $session->token) {

            // Custom factories for deletion?
            $actions = $this->config()->get('nodes.available.' . $entity->getNodeType() . '.actions.delete.factories', array());
            if ($actions) {
                foreach ($actions as $action)
                    $action($this->getServiceLocator(), $entity);
                                    
            } else {
            	// Delete cache
            	$this->media()->clearCache($entity);
            	
                // Delete translations
                $this->translations()->deleteNodeTranslations($entity);
                
                // Delete relations
                $this->nodes()->deleteRelations($entity);
                
                // Delete cms links
                $this->links()->deleteNodeLinks($entity);
                
                // Delete node itself
            	$this->nodes()->deleteNode($entity);
            	
            }
        	
        	$this->flashmessenger()->addSuccessMessage('Node has been deleted');
        	return $this->redirect()->toRoute('admin');
        }
        $session->token = \Zend\Math\Rand::getString(8, 'abcdefghijklmnopqrstuvwxyz0123456789');
        
        return new ViewModel(array(
            'entity' => $entity,
            'nodeTypeConfig' => $nodeTypeConfig,
            'token' => $session->token
        ));
    }
    
    public function rebuildlinksAction()
    {
        foreach ($this->config()->get('nodes.enabled') as $nodeType) {
            if (in_array($nodeType, array('LANGUAGE', 'COUNTRY'))) continue;
            $nodes = $this->nodes()->getNodes($nodeType);
            foreach ($nodes as $node) {
                if (! $node instanceof LinkedNodeInterface)
                    continue;
                //print $node . '<br>';
                $this->links()->rebuildLinks($node);
            }
        }
    }
    
    public function selectAction()
    {
    	$nodeType = $this->params('id');
    	$q = $_GET['q'];
    	
    	$filterAlnum = new \Zend\I18n\Filter\Alnum();
    	
    	if (strlen($filterAlnum->filter($q)) > 2) {
            $filterLucene = new \Kofus\System\Filter\LuceneQueryValue();
    	    $value = $filterLucene->filter($q);
    	    $query = "'$value*' AND node_type: '$nodeType'";
    	    $hits = $this->lucene()->getIndex()->find($query);
    	} else {
    	    $hits = array();
    	}
    	
    	// Assemble result array
    	$results = array();
    	foreach ($hits as $hit) {
    		$results[] = array(
    				'id' => $hit->node_id,
    				'text' => $hit->label
    				);
    	}
    	
    	// Output json
    	return new JsonModel(array(
    			'count' => count($results),
    			'results' => $results,
    	));
    	 
    }
    
    

}
