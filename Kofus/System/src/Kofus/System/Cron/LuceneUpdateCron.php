<?php 

namespace Kofus\System\Cron;
use Kofus\System\Cron\AbstractCron;


class LuceneUpdateCron extends AbstractCron
{
    public function run()
    {
        $spec = $this->getSpecification();
        $lucene = $this->getServiceLocator()->get('KofusLuceneService');
        foreach ($spec['node_types'] as $nodeType)
        	$lucene->updateModifiedNodes($nodeType, 'de_DE');
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