<?php

namespace Kofus\System\Filter;
use Zend\Filter\FilterInterface;

class LuceneQueryValue implements FilterInterface
{
    protected $specialChars = array(
        '+', '-', 
        '&', '|', 
        '!', 
        '(', ')', '[', ']', '{', '}',
        '^', '"', "'", '~', '*', '?', ':', '\\'
    );
    protected $escapeChar = '/';
    
    protected $removeChars = array(
        '(', ')'
    );
    
	public function filter($value) 
	{
	    foreach ($this->removeChars as $removeChar)
	        $value = str_replace($removeChar, '', $value);
	    foreach ($this->specialChars as $specialChar)
	        $value = str_replace($specialChar, $this->escapeChar . $specialChar, $value);
	    
        $value = trim($value);
	    
	    return $value;
	}
}