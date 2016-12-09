<?php
namespace Kofus\System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;


class BatchController extends AbstractActionController
{
    public function listAction()
    {
    	$service = $this->getServiceLocator()->get('KofusBatchService');
		return new ViewModel(array(
			'batches' => $service->getBatches()
		));
    }
    
    public function reloadAction()
    {
    	$service = $this->getServiceLocator()->get('KofusBatchService');
    	$classnames = $this->config()->get('batches');
    	foreach ($classnames as $classname) {
    		$batch = new $classname();
    		$service->add($batch);
    	}
    	return $this->redirect()->toRoute('kofus_system', array('controller' => 'batch', 'action' => 'list'));
    }
    
    public function resetAction()
    {
    	$service = $this->getServiceLocator()->get('KofusBatchService');
    	$batchId = $this->params('id');
    	$batch = $service->getBatch($batchId);
    	$service->reset($batch);
    	return $this->redirect()->toRoute('kofus_system', array('controller' => 'batch', 'action' => 'list'));
    }
    
    public function runAction()
    {
    	ini_set('max_execution_time', 0);
    	$service = $this->getServiceLocator()->get('KofusBatchService');
    	$batchId = $this->params('id');
    	$batch = $service->getBatch($batchId);
    	$service->run($batch);
    	
    	if (isset($_GET['loop']) && $batch->getMetaParam('current_index') < $batch->getMetaParam('last_index')) {
    		return new ViewModel(array(
    				'batch' => $batch
    		));
    	} else {
    		return $this->redirect()->toRoute('kofus_system', array('controller' => 'batch', 'action' => 'list'));
    	}
    }
    
}
