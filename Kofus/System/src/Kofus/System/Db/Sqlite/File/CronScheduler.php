<?php

namespace Kofus\System\Db\Sqlite\File;
use Kofus\System\Db\Sqlite\File\AbstractFile;

class CronScheduler extends AbstractFile
{
	protected function init()
	{
		$this->getDb()->query('CREATE TABLE cron_jobs (
            "id" PRIMARY KEY,
		    "status",
		    "store_params",
		    "timestamp" INTEGER		    
        );')->execute();
	}
	
	public function getPausedJob()
	{
	    $records = $this->getDb()->query("
		    SELECT *
		    FROM cron_jobs
		    WHERE status = 'paused'
		")->execute();	
	    return $records->current();    
	}
	
	public function setStatus($id, $status)
	{
	    $record = $this->getRecord($id);
	    if ($record) {
	        $record['status'] = $status;
	        $record['timestamp'] = time();
	        $this->update('cron_jobs', $record, 'id = ' . $this->pl()->quoteValue($id));
	    } else {
	        $record = array(
	        	'id' => $id,
	            'status' => $status,
	            'timestamp' => time()
	        );
	        $this->insert('cron_jobs', $record);
	    }    
	}
	
	public function setStoreParams($id, array $params)
	{
		$record = $this->getRecord($id);
		if ($record) {
			$record['store_params'] = serialize($params);
			$record['timestamp'] = time();
			$this->update('cron_jobs', $record, 'id = ' . $this->pl()->quoteValue($id));
		} else {
			$record = array(
					'id' => $id,
					'store_params' => serialize($params),
					'timestamp' => time()
			);
			$this->insert('cron_jobs', $record);
		}
	}	
	
	public function getStoreParams($id)
	{
	    $record = $this->getRecord($id);
	    if ($record && $record['store_params'])
	        return unserialize($record['store_params']);
	    return array();
	}
	
	public function getLastTimestamp($id)
	{
	    $record = $this->getRecord($id);
	    if ($record && $record['timestamp'])
	    	return \DateTime::createFromFormat('U', $record['timestamp']);
	}
	
	public function getRecord($id)
	{
	    $records = $this->getDb()->query('
		    SELECT *
		    FROM cron_jobs
		    WHERE id = '.$this->pl()->quoteValue($id).'
		')->execute();
	    return $records->current();    
	}
	
	public function getRecords()
	{
	    $records = $this->getDb()->query('
		    SELECT *
		    FROM cron_jobs
		')->execute();
	    
	    return $records;  
	}
	
	
	
	
	
}