<?php

namespace Kofus\System\View\Helper;
use Zend\View\Helper\AbstractHelper;

class ShortenStringHelper extends AbstractHelper
{
    public function __invoke($s, $length=15)
    {
        $s = trim($s);
        if (strlen($s) > $length)
            return substr($s, 0, $length - 3) . '...';
    	return $s;
    }
    

}