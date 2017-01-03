<?php

namespace Kofus\System\View\Helper;
use Zend\View\Helper\AbstractHelper;

class ImplodeValidPiecesHelper extends AbstractHelper
{
    public function __invoke($glue, array $pieces)
    {
        $_pieces = array();
        foreach ($pieces as $piece) {
            if ($piece) $_pieces[] = $piece;
        }
        return implode($glue, $_pieces);
            
    }
    

}