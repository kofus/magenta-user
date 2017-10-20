<?php
namespace Kofus\Media\Imagick;

use Zend\Filter\AbstractFilter as ZendAbstractFilter;

abstract class AbstractFilter extends ZendAbstractFilter
{
    public function __construct($options)
    {
        $this->setOptions($options);
    }
    
}