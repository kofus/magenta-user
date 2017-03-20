<?php

namespace Kofus\System\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\Filter\FilterChain;

class FilterHelper extends AbstractHelper
{
    protected $filterChains = array();
    
    public function __invoke($value, $filters)
    {
        if (! is_array($filters))
            $filters = array($filters);
        
        $hash = md5(implode($filters));
        if (! isset($this->filterChains[$hash])) {
        	$this->filterChains[$hash] = new FilterChain();
        	foreach ($filters as $filterName)
        		$this->filterChains[$hash]->attachByName($filterName);
        }
        return $this->filterChains[$hash]->filter($value);
        
    }
}