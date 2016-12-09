<?php

namespace Kofus\Archive\Sqlite\Table;


class Lucene extends AbstractTable 
{
	protected function init()
	{
		$this->getDb()->query('CREATE TABLE lucene_queries (
            "id" INTEGER PRIMARY KEY,
		    "request_method",
		    "request_uri", 
			"request_time" INTEGER,
			"request_hash",				
		    "query",
			"results",
			"backtrace"
	    
        );')->execute();
		
		$this->getDb()->query('CREATE TABLE lucene_documents (
            "id" INTEGER PRIMARY KEY,
		    "request_method",
		    "request_uri", 
			"request_time" INTEGER,
			"request_hash",	
		    "operation",
			"document",
			"backtrace"

        );')->execute();		
	}
	
	public function getRequests()
	{
		$_records = $this->getDb()->query('
		    SELECT request_hash, request_method, request_uri, request_time, COUNT(id) AS num
		    FROM lucene_queries
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
		    FROM lucene_logs
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
	
	public function addQuery($query, $resultCount=null)
	{
		$record = array(
			'request_method' => $_SERVER['REQUEST_METHOD'],
			'request_uri' => $_SERVER['REQUEST_URI'],
			'request_time' => REQUEST_TIME,
			'request_hash' => $this->buildRequestHash(),
			'query' => $query,
			'result_count' => $resultCount,
			'backtrace' => $this->buildBacktrace()
		);
		$this->insert('lucene_queries', $record);
	}
	
	protected function buildRequestHash()
	{
		return md5($_SERVER['REQUEST_METHOD'] . $_SERVER['REQUEST_URI'] . REQUEST_TIME . $_SERVER['REMOTE_ADDR']);
	}
	
	protected function buildBacktrace()
	{
		$backtrace = array();
		foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $entry) {
			if (! isset($entry['file'])) continue;
			//if (strpos($entry['file'], '/vendor/doctrine/') !== false) continue;
			$backtrace[] = $entry;
		}
		return serialize($backtrace);		
	}
	
	public function addDocumentOperation($document, $operation='add')
	{
		$record = array(
				'request_method' => $_SERVER['REQUEST_METHOD'],
				'request_uri' => $_SERVER['REQUEST_URI'],
				'request_time' => REQUEST_TIME,
				'request_hash' => $this->buildRequestHash(),
				'operation' => $operation,
				'document' => $document,
				'backtrace' => $this->buildBacktrace()
		);
		$this->insert('lucene_documents', $record);
	}	

	
	
	
}