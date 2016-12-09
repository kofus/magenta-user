<?php
namespace Kofus\Archive\Db\TableGateway;
use Zend\Db\TableGateway\TableGateway;

class Sessions extends TableGateway
{
    /**
     * Insert
     *
     * @param  array $set
     * @return int
     */
    public function insert($set)
    {
        $set = array_merge($set, $this->defaultValues);
        return parent::insert($set);
    }
    
    protected $defaultValues = array();
    
    public function setDefaultValues(array $values)
    {
        $this->defaultValues = $values;
        return $this;
    }
    
    
}