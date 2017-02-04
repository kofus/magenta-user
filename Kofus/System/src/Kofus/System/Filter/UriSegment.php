<?php

namespace Kofus\System\Filter;
use Zend\Filter\FilterInterface;

class UriSegment implements FilterInterface
{
	public function filter($value) {
	    $uri = mb_strtolower(trim($value), 'utf8');
	    $uml = array('/ä/', '/ü/', '/ö/', '/ß/', '/\//');
	    $ers = array('ae', 'ue', 'oe', 'ss', ' ');
	    $uri = preg_replace($uml, $ers, $uri);
	    $uri = preg_replace('/\s+/', '-', $uri);
	    $uri = preg_replace('/\-+/', '-', $uri);
	    $uri = preg_replace('/[^a-z0-9\-\_]/', '', $uri);
	    $uri = preg_replace('/\-+/', '-', $uri);
	    return $uri;
	}
}