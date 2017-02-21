<?php

namespace Kofus\System\Db\Sqlite\File;
use Kofus\System\Db\Sqlite\File\AbstractFile;

class SettingsDb extends AbstractFile
{
	protected function init()
	{
		$this->getDb()->query('CREATE TABLE settings (
            "key" PRIMARY KEY,
		    "value" TEXT
        );')->execute();
	}
	
	protected function getRecord($key)
	{
	    $records = $this->getDb()->query('SELECT * FROM settings WHERE key = '.$this->pl()->quoteValue($key))->execute();
	    return $records->current();
	}
	
	public function setValue($key, $value)
	{
	    if ($value === null)
	        return $this->unsetValue($key);
	    
	    $record = $this->getRecord($key);
	    if ($record) {
	        $record['value'] = $value;
	        $this->update('settings', array('value' => $value), 'key = ' . $this->pl()->quoteValue($key));
	    } else {
	        $this->insert('settings', array('key' => $key, 'value' => $value));
	    }
	}
	
	public function unsetValue($key)
	{
        $this->getDb()->query('DELETE FROM settings WHERE key = ' . $this->pl()->quoteValue($key))
            ->execute();
	}
	
	public function getValue($key, $default=null)
	{
	    $records = $this->getDb()->query('
		    SELECT *
		    FROM settings
		    WHERE key = '.$this->pl()->quoteValue($key).'
		')->execute();
	    $record = $records->current();
	    if ($record)
	        return $record['value'];
	    return $default;
	}
}