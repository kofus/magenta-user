<?php

namespace Kofus\System\Service;

use Kofus\System\Service\AbstractService;
use Kofus\System\Db\Sqlite\File\SettingsDb;

class SettingsService extends AbstractService
{
    
    public function setSystemValue($key, $value)
    {
        $db = SettingsDb::open('data/system/settings.db');
        $db->setValue($key, $value);
    }
    
    public function getSystemValue($key, $default=null)
    {
        $db = SettingsDb::open('data/system/settings.db');
        return $db->getValue($key, $default);
    }
	
	
}