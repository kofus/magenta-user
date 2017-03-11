<?php

namespace Kofus\System\View\Helper;
use Zend\View\Helper\AbstractHelper;

class DurationHelper extends AbstractHelper
{
    public function __invoke($value)
    {
        $hour = floor($value / 60);
        $minutes = $value % 60;
        
        return $hour . ':' . $minutes;
    }
}