<?php

namespace Kofus\Archive\Sqlite\Table;

use Kofus\Archive\Sqlite\Result\SessionResult;

class Sessions extends AbstractTable
{
	protected function init()
	{
		$this->getDb()->query('CREATE TABLE sessions (
            "id" VARCHAR(32) PRIMARY KEY,
		    "name" VARCHAR(32),
		    "data" TEXT,
		    "modified" INTEGER,
		    "lifetime" INTEGER,
		    "nodes",
		    "http_user_agent",
		    "remote_addr",
		    "created" INTEGER
        );')->execute();
	}
	
	public function getSessions()
	{
		$records = $this->getDb()->query('
		    SELECT *
		    FROM sessions
		    ORDER BY modified DESC;
		')->execute();
		
		$results = array();
		foreach ($records as $record)
		    $results[] = new SessionResult($record);
		
		return $results;
	}	
	
	public function updateSession($sessionId, array $record)
	{
	    $keys = array();
	    $values = array();
	    foreach ($record as $key => $value) {
	    	$keys[] = $this->pl()->quoteIdentifier($key);
	    	$values[] = $this->pl()->quoteValue($value);
	    }
	    
	    $sql = 'UPDATE sessions SET ';
	    $pairs = array();
	    for ($i = 0; $i < count($keys); $i += 1) 
	        $pairs[] = $keys[$i] . ' = ' . $values[$i];
	    $sql .= implode(', ', $pairs);
	    $sql .= ' WHERE id = ' . $this->pl()->quoteValue($sessionId) . '; ';
	    
	    $this->getDb()->query($sql)->execute();
	     
	}
	
	public function getSession($id)
	{
		$records = $this->getDb()->query('
		    SELECT *
		    FROM sessions
		    WHERE id = '. $this->pl()->quoteValue($id).'
		')->execute();
		 
		$results = array();
		foreach ($records as $record)
			return new SessionResult($record);
	}
	
	
	
}