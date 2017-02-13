<?php
namespace Kofus\System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Kofus\System\Db\Sqlite\File\CronScheduler;
use Kofus\System\Cron\CronInterface;

class CronController extends AbstractActionController
{
    const MINUTE = 0;
    const HOUR = 1;
    const DAY = 2;
    const WEEK = 3;
    const MONTH = 4;
    
    public function triggerAction()
    {
    	// ACL
    	if ($this->getRequest() instanceof \Zend\Http\Request) {
        	$passphrase = $this->config()->get('cron.passphrase');
        	if ($passphrase && $passphrase != $this->params('passphrase'))
        		return $this->getResponse()->setStatusCode(404)->setContent('Permission denied.');
    	}
    	
    	$now = new \DateTime();
    
		foreach ($this->config()->get('cron.tasks', array()) as $taskId => $task) {
		    if ($this->isTaskInSlot($task, $now))
    			$this->run($taskId);
		}
		

    	exit();
    }

    protected function isTaskInSlot($task, \DateTime $timestamp=null)
    {
        if (! $timestamp) $timestamp = new \DateTime();
        
        $criteria = array(
        	'i' => $task[self::MINUTE],
            'H' => $task[self::HOUR],
            'd' => $task[self::DAY],
            'w' => $task[self::WEEK],
            'm' => $task[self::MONTH]
        );
        
        foreach ($criteria as $format => $value) {
            if ($value !== null) {
            	if (is_array($value)) {
            		if (! in_array((int) $timestamp->format($format), $value))
            			return false;
            	} elseif ($value != (int) $timestamp->format($format)) {
            	   return false;
            	}
            }
        }
        
        return true;
    }
    
    
    
    protected function run($taskId)
    {
        $task = $this->config()->get('cron.tasks.' . $taskId);
        if (! $task) return;
        
        
        print $taskId . ': ';
        $instance = $this->getServiceLocator()->get($task[5]);
        if (! $instance instanceof CronInterface)
            throw new \Exception('Service ' . $task[5] . ' must implement CronInterface');
        $instance->setSpecification($task[6]);
        //$instance->setStoreParams($scheduler->getStoreParams($taskId));
        //$scheduler->setStatus($taskId, 'running');
        
        $status = $instance->run();
        
        //$scheduler->setStatus($taskId, $status);
        //$scheduler->setStoreParams($taskId, $instance->getStoreParams());
        print $status . '<br>';        
    }
    

    
}
