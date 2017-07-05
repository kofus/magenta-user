<?php

namespace Kofus\System\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\Session\Container;
use ZendSearch\Lucene\Analysis\Analyzer;



class SearchResultHelper extends AbstractHelper
{
    protected $text;
    protected $keywords;
    protected $prefix = '<b>';
    protected $postfix = '</b>';
    
    
    public function __invoke($text, $keywords)
    {
        $this->text = $text;
        $this->keywords = $keywords;
    	return $this;
    }
    
    public function render()
    {
        $text = $this->highlight($this->text, $this->keywords);
        $text = $this->abridge($text, $this->keywords);
        return $text;
    }
    
    public function __toString()
    {
        return $this->render();
    }
    
    
    protected function highlight($text, $words)
    {
        $index = strpos(strtolower($text), strtolower($words));
        
        while ($index !== false) {
            $text = substr_replace($text, $this->prefix, $index, 0);
            $text = substr_replace($text, $this->postfix, $index + strlen($this->prefix) + strlen($words), 0);
            $index = strpos(strtolower($text), strtolower($words), $index + strlen($this->prefix) + strlen($words) + strlen($this->postfix));
        }
        return $text;
    }
    
    protected function abridge($text, $q)
    {
        $indexes = array();
        $words = explode(' ', $text);
        
        foreach ($words as $index => $word) {
            if (strpos($word, $this->prefix) !== false) {
                $indexes = array_merge(range($index+10, $index-10), $indexes);
            }
        }
        
        $s = '';
        foreach ($words as $index => $word) {
            if (in_array($index, $indexes))
                $s .= $word . ' ';
        }
        
        return $s;
        
    }
    
    

    
    
    
}


