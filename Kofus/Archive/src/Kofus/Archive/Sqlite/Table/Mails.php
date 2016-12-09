<?php

namespace Kofus\Archive\Sqlite\Table;

use Kofus\Archive\Sqlite\Hydrator\MailHydrator;
use Kofus\Archive\Sqlite\Result\MailResult;


class Mails extends AbstractTable
{
	protected function init()
	{
		$this->getDb()->query('CREATE TABLE mails (
		    "id" INTEGER PRIMARY KEY, 
		    "encoding", 
		    "headers",
		    "sender",
		    "subject",
		    "body",
		    "body_text",
		    "nodes",
		    "timestamp" INTEGER
        );')->execute();
	}
	
	
	public function add(\Zend\Mail\Message $mail, array $relatedNodeIds=array())
	{
	    $hydrator = new MailHydrator();
	    
	    $record = $hydrator->extract($mail);
	    $record['timestamp'] = time();
        
        if ($relatedNodeIds)
        	$record['nodes'] = '|' . implode('|', $relatedNodeIds) . '|';
		
        $this->insert('mails', $record);		
	}
	
    public function getMails()
	{
		$records = $this->getDb()->query('
		    SELECT * 
		    FROM mails 
		    ORDER BY timestamp DESC
		')->execute();
		
		$results = array();
		foreach ($records as $record)
		    $results[] = new MailResult($record); 
		
		return $results;
	}
	
	public function getMail($id)
	{
	    $id = (int) $id;
	    $records = $this->getDb()->query('
		    SELECT *
		    FROM mails
		    WHERE id = '.$id.'
		')->execute();
	    
	    $results = array();
	    foreach ($records as $record)
	    	return new MailResult($record);
	}
	
}