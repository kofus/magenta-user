<?php
namespace Kofus\System\Cron;
use Kofus\System\Cron\AbstractCron;

class DbBackupCron extends AbstractCron
{
    public function run()
    {
        $db = $this->getServiceLocator()->get('KofusDatabase');
        $db->save();
    	return 'completed';
    }
    

}