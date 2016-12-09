<?php

namespace Kofus\Archive\Sqlite\Table;

use Kofus\Archive\Sqlite\Result\EventResult;
use Kofus\System\Node\NodeInterface;

class Events extends AbstractTable
{
	protected function init()
	{
		$this->getDb()->query('
		    CREATE TABLE events (
                "id" INTEGER PRIMARY KEY,
    		    "identifier",
    		    "event",
    		    "nodes",
		        "nodes_txt",
    		    "remote_addr",
    		    "timestamp" INTEGER
        );')->execute();
	}
	
	public function add($identifier, $event, $nodes=array())
	{
	    $txt = array(); $nodeIds = array();
	    foreach ($nodes as $node) {
	        if (! $node instanceof NodeInterface)
	            continue;
	        $txt[] = (string) $node;
	        $nodeIds[] = $node->getNodeId();
	    }
	    if ($nodeIds) {
	        $nodeIds = '|' . implode('|', $nodeIds) . '|';
	    } else {
	        $nodeIds = null;
	    } 
	    
		$record = array(
		    'identifier' => $identifier,
		    'event' => $event,
		    'nodes' => $nodeIds,
		    'nodes_txt' => implode(', ', $txt),
		    'remote_addr' => $_SERVER['REMOTE_ADDR'],
		    'timestamp' => time() 
		);
	
		$this->insert('events', $record);
	}
	
	
	public function getEvents()
	{
		$records = $this->getDb()->query('
		    SELECT *
		    FROM events
		    ORDER BY timestamp DESC;
		')->execute();
		
		$results = array();
		foreach ($records as $record)
		    $results[] = new EventResult($record);
		
		return $results;
	}	
	
	public function getEvent($id)
	{
		$records = $this->getDb()->query('
		    SELECT *
		    FROM events
		    WHERE id = '. $this->pl()->quoteValue($id).'
		')->execute();
		 
		$results = array();
		foreach ($records as $record)
			return new EventResult($record);
	}
	
	
	
}