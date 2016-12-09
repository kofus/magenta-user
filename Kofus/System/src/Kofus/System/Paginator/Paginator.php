<?php
namespace Kofus\System\Paginator;

use Zend\Paginator\Paginator as ZendPaginator;

class Paginator extends ZendPaginator
{
    protected $hash;
    
    public function getHash()
    {
        return $this->hash;
    }
    
    public function setHash($value)
    {
        $this->hash = $value; return $this;
    }
    
    protected function _createPages($scrollingStyle = null)
    {
        $pages = parent::_createPages($scrollingStyle);
        $pages->hash = $this->getHash();
        return $pages;
    }    
}
