<?php

namespace Kofus\Archive\Sqlite\Table;

use Kofus\Archive\Sqlite\Result\SoapResult;
use Zend\Http\Headers;

class Soap extends AbstractTable
{
	protected function init()
	{
		$this->getDb()->query('CREATE TABLE soap (
            "id" INTEGER PRIMARY KEY,
		    "request_headers",
		    "request_body", 
		    "response_headers",
		    "response_body",
		    "nodes",
		    "timestamp" INTEGER		    
        );')->execute();
	}
	
	public function add(\SoapClient $client, array $relatedNodeIds=array())
	{
	    $requestBody = $client->__getLastRequest();
	    $requestHeaders = $client->__getLastRequestHeaders();
	    $responseBody = $client->__getLastResponse();
	    $responseHeaders = $client->__getLastResponseHeaders();
	    
		$record = array(
				'request_headers' => $requestHeaders,
		        'request_body' => $requestBody,
		        'response_headers' => $responseHeaders,
		        'response_body' => $responseBody,
				'timestamp' => time(),
		);
		
		if ($relatedNodeIds)
			$record['nodes'] = '|' . implode('|', $relatedNodeIds) . '|';
		
	
		$this->insert('soap', $record);
	}
	
	public function getRequests()
	{
		$records = $this->getDb()->query('
		    SELECT *
		    FROM soap
		    ORDER BY timestamp DESC;
		')->execute();
		
		$results = array();
		foreach ($records as $record)
		    $results[] = new SoapResult($record);
		
		return $results;
	}	
	
	public function getSoap($id)
	{
		$id = (int) $id;
		$records = $this->getDb()->query('
		    SELECT *
		    FROM soap
		    WHERE id = '.$id.'
		')->execute();
		 
		$results = array();
		foreach ($records as $record)
			return new SoapResult($record);
	}
	
	
	
}