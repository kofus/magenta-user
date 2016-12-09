<?php

namespace Kofus\System\Filter;
use Zend\Filter\FilterInterface;

class SubstitutionFilter implements FilterInterface
{
    
	public function filter($value) 
	{
	    if (is_array($value))
	    	return $this->filterArray($value);
		return $this->filterString($value);
	}
	
	protected function filterArrayCallback(&$value, $key)
	{
			$value = $this->filterString($value);
	}
	
	protected function filterArray(array $array)
	{
		array_walk_recursive($array, array($this, 'filterArrayCallback'));
		return $array;
	}
	
	protected function filterString($s)
	{
		foreach ($this->getParams() as $key => $value)
			$s = str_replace('{' . $key . '}', $value, $s);
		return $s;
	}
	
	
	protected $params = array();
	
	public function setParams(array $params)
	{
		$this->params = $params; return $this;
	}
	
	public function getParams()
	{
		return $this->params; 
	}
	
	public function setParam($key, $value)
	{
		$this->params[$key] = $value; return $this;
	}
	
	public function getParam($key)
	{
		if (isset($this->params[$key]))
			return $this->params[$key];
	}
}