<?php
namespace Kofus\System\Paginator;

use Zend\Paginator\Paginator as ZendPaginator;

class Paginator extends ZendPaginator
{
    protected $id;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setId($value)
    {
        $this->id = $value; return $this;
    }
    
    /**
     * Format: alias => column_name
     * @var array
     */
    protected $sortColumns;
    
    public function setSortColumns(array $columns)
    {
    	$this->sortColumns = $columns; return $this;
    }
    
    public function getSortColumns()
    {
    	return $this->sortColumns;
    }
    
    /**
     * Format: alias => ASC|DESC
     * @var array
     */
    protected $sortDirections;
    
    public function setSortDirections(array $columns)
    {
    	$this->sortDirections = $columns; return $this;
    }
    
    public function getSortDirections()
    {
    	return $this->sortDirections;
    }
    
    protected function _createPages($scrollingStyle = null)
    {
        $pages = parent::_createPages($scrollingStyle);
        $pages->paginatorId = $this->getId();
        return $pages;
    }    
}
