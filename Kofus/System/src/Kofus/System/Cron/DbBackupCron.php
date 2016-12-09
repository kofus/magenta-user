<?php
namespace Kofus\System\Cron;
use Kofus\System\Cron\AbstractJob;

class DbBackupCron extends AbstractJob
{
    public function run()
    {
        $db = $this->getServiceLocator()->get('KofusDatabase');
        $db->dump();
    	return 'completed';
    }
    

}