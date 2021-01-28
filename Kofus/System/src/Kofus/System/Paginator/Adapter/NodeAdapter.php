<?php
namespace Kofus\System\Paginator\Adapter;
use Zend\Paginator\Adapter\AdapterInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\CountWalker;
use Doctrine\ORM\Query\ResultSetMapping;

class NodeAdapter implements AdapterInterface
{
    protected $qb;
    
    public function __construct($qb)
    {
        $this->qb = $qb;
    }
    
    public function getItems($offset, $itemCountPerPage)
    {
        $qb = clone $this->qb;
        $qb->setFirstResult($offset);
        $qb->setMaxResults($itemCountPerPage);
        
        return $qb->getQuery()->getResult();
    }
    
    protected $count;
    
    public function count()
    {
        if (null === $this->count) {
            $countQuery = $this->qb->getQuery();
            $countQuery->setHint(CountWalker::HINT_DISTINCT, true);
            
            $platform = $countQuery->getEntityManager()->getConnection()->getDatabasePlatform(); // law of demeter win
            
            $rsm = new ResultSetMapping();
            $rsm->addScalarResult($platform->getSQLResultCasing('dctrn_count'), 'count');
            
            $countQuery->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Doctrine\ORM\Tools\Pagination\CountOutputWalker');
            $countQuery->setResultSetMapping($rsm);
            $countQuery->setFirstResult(null)->setMaxResults(null);
            try {
                $data =  $countQuery->getScalarResult();
                $data = array_map('current', $data);
                $this->count = array_sum($data);
            } catch(\Exception $e) {
                $this->count = 0;
            }
        }
            
        return $this->count;
    }
    
}