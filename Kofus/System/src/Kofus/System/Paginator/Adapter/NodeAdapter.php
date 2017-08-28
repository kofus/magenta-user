<?php
namespace Kofus\System\Paginator\Adapter;
use Zend\Paginator\Adapter\AdapterInterface;

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
    
    public function count()
    {
        $sql = $this->qb->getQuery()->getSql();
        return $this->qb->getEntityManager()->getConnection()->exec($sql); 
    }
    
}