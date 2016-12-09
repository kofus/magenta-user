<?php

namespace Kofus\System\Db\Sqlite\File;
use Kofus\System\Db\Sqlite\File\AbstractFile;

class Batches extends AbstractFile
{
	protected function init()
	{
		$this->getDb()->query('CREATE TABLE batches (
            "id" INTEGER PRIMARY KEY,            
		    "classname",
			"current_index",
			"last_index",
			"enabled" INTEGER,
			"timestamp_created" INTEGER,
			"timestamp_completed" INTEGER,
			"elapsed" INTEGER
        );')->execute();
	}
	
	
	
}