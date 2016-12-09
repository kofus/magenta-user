<?php
namespace Kofus\System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Kofus\System\Db\Sqlite\File\CronScheduler;
use Kofus\System\Cron\CronInterface;

class CronSchedulerController extends AbstractActionController
{
    public function listAction()
    {
        $tasks = $this->config()->get('cron.tasks');
        
        $scheduler = CronScheduler::open('data/system/cron.db');     
        
        
        return new ViewModel(array(
        	'tasks' => $tasks,
            'scheduler' => $scheduler
        ));   
    }
    
}
