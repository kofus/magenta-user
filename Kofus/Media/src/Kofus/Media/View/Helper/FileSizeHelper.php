<?php

namespace Kofus\Media\View\Helper;
use Zend\View\Helper\AbstractHelper;

class FileSizeHelper extends AbstractHelper
{
    public function __invoke($bytes, $precision=2)
    {
    	if (! $bytes) return '-';
    	
    	$units = array('B', 'KB', 'MB', 'GB', 'TB');
    	
    	$bytes = max($bytes, 0);
    	$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    	$pow = min($pow, count($units) - 1);
    	
    	$bytes /= pow(1024, $pow);
    	
    	return round($bytes, $precision) . ' ' . $units[$pow];
    }
    
}