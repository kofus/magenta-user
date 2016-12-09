<?php
namespace Kofus\System\I18n;

class IntlDateFormatter extends \IntlDateFormatter
{
    public function getPattern()
    {
        print parent::getPattern(); die();
    }
    
}