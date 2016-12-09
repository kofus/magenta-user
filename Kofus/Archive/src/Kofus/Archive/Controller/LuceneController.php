<?php

namespace Kofus\Archive\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class LuceneController extends AbstractActionController
{
    public function listAction()
    {
    	$namespace = $this->params('namespace', 'default');
        $this->archive()->uriStack()->push();
        $results = $this->archive()->lucene($namespace)->getRequests();
        $paginator = $this->paginator($results);
    	return new ViewModel(array(
    		'paginator' => $paginator,
    		'namespace' => $namespace
    	));
    }
    
    public function viewAction()
    {
    	$table = $this->archive()->sql($this->params('namespace'));
    	$record = $table->getRecordById($this->params('id'));
    	$records = $table->getSql($record['method'], $record['uri'], $record['timestamp']);
    	
        return new ViewModel(array(
        	'records' => $records,
        		'record' => $record
        ));
    }

    
}
