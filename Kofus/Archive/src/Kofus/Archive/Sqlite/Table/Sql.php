<?php

namespace Kofus\Archive\Sqlite\Table;
use Doctrine\DBAL\Logging\SQLLogger as SqlLoggerInterface;

class Sql extends AbstractTable implements SqlLoggerInterface
{
	protected function init()
	{
		$this->getDb()->query('CREATE TABLE sql_logs (
            "id" INTEGER PRIMARY KEY,
		    "method",
		    "uri", 
		    "sql",
		    "params",
		    "types",
			"backtrace",
			"execution_time" INTEGER,
		    "timestamp" INTEGER		    
        );')->execute();
	}
	
	public function getRequests()
	{
		$_records = $this->getDb()->query('
		    SELECT id, method, uri, timestamp, COUNT(id) AS num
		    FROM sql_logs
			GROUP BY method, uri, timestamp			
		    ORDER BY timestamp DESC;
	
		')->execute();
		$records = array();
		foreach ($_records as $record)
			$records[] = $record;
	
		return $records;
	}
	
	public function getRecordById($id)
	{
		$_records = $this->getDb()->query('
		    SELECT *
		    FROM sql_logs
			WHERE id = '.$this->pl()->quoteValue($id).';
		')->execute();
		$records = array();
		foreach ($_records as $record)
			return $record;

	}
		
	
	public function getSql($method, $uri, $timestamp)
	{
		$_records = $this->getDb()->query('
		    SELECT *
		    FROM sql_logs
			WHERE method = '.$this->pl()->quoteValue($method).'
				AND uri = '.$this->pl()->quoteValue($uri).'
				AND timestamp = '.$this->pl()->quoteValue($timestamp).'
		    ORDER BY timestamp DESC;
		
		')->execute();
		$records = array();
		foreach ($_records as $record)
			$records[] = $record;
		
		return $records;		
	}
	
	public function startQuery($sql, array $params = null, array $types = null)
	{
		if (strpos($_SERVER['REQUEST_URI'], '/de/system/') === 0)
			return;
		
		if (! $params) $params = array();
		if (! $types) $types = array();
		$backtrace = array();
		foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $entry) {
			if (! isset($entry['file'])) continue;
			if (strpos($entry['file'], '/vendor/doctrine/') !== false) continue;
			$backtrace[] = $entry;		
		}
		
		$record = array(
			'method' => $_SERVER['REQUEST_METHOD'],
			'uri' => $_SERVER['REQUEST_URI'],
			'sql' => $sql,
			'params' => serialize($params),
			'types' => serialize($params),
			'timestamp' => REQUEST_TIME,
			'backtrace' => serialize($backtrace)
		);
		$this->insert('sql_logs', $record);
	}
	
	public function stopQuery()
	{
		
	}	
	

	
	
	
}