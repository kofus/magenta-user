<?php
namespace Kofus\System\Cron\Job\Lucene;
use Kofus\System\Cron\AbstractJob;

class ReIndexNodes extends AbstractJob
{
    public function run()
    {
        $search = $this->getServiceLocator()->get('KofusSearchService');
        $search->reindex(array('B'));
    	return 'completed';
    }
    

}