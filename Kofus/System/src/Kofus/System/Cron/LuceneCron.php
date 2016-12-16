<?php 

namespace Kofus\System\Cron;
use Kofus\System\Cron\AbstractCron;


class LuceneCron extends AbstractCron
{
    public function run()
    {
        $spec = $this->getSpecification();
        $search = $this->getServiceLocator()->get('KofusSearchService');
        $search->reindex($spec['node_type'], array('de_DE'));
        return 'completed';
    }
    
   
    protected function em()
    {
        return $this->getServiceLocator()->get('Doctrine/ORM/EntityManager');
    }
    
    protected function nodes()
    {
        return $this->getServiceLocator()->get('KofusNodeService');
    }
    
    protected function mailer()
    {
        return $this->getServiceLocator()->get('KofusMailerService');
    }
}