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
    
	public function filter($value) {
	    foreach ($this->specialChars as $specialChar)
	        $value = str_replace($specialChar, $this->escapeChar . $specialChar, $value);
	    
	    return $value;
	}
}