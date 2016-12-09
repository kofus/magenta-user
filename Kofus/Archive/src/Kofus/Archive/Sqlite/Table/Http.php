<?php

namespace Kofus\Archive\Sqlite\Table;

use Kofus\Archive\Sqlite\Result\HttpResult;

class Http extends AbstractTable
{
	protected function init()
	{
		$this->getDb()->query('CREATE TABLE http (
            "id" INTEGER PRIMARY KEY,
		    "method",
		    "uri",
		    "status_code",
		    "request_headers",
		    "request_body", 
		    "response_headers",
		    "response_body",
		    "nodes",
		    "timestamp" INTEGER		    
        );')->execute();
	}
	
	public function add(\Zend\Http\Client $client, array $relatedNodeIds=array())
	{
	    $requestBody = $client->getRequest()->getContent();
	    $requestHeaders = $client->getRequest()->getHeaders()->toString();
	    $responseBody = $client->getResponse()->getBody();
	    $responseHeaders = $client->getResponse()->getHeaders()->toString();
	    
		$record = array(
                'uri' => $client->getUri(),
		        'method' => $client->getMethod(),
				'status_code' => $client->getResponse()->getStatusCode(),
				'request_headers' => $requestHeaders,
		        'request_body' => $requestBody,
		        'response_headers' => $responseHeaders,
		        'response_body' => $responseBody,
				'timestamp' => time(),
		);
		
		if ($relatedNodeIds)
			$record['nodes'] = '|' . implode('|', $relatedNodeIds) . '|';
		
	
		$this->insert('http', $record);
	}
	
	public function getRequests()
	{
		$records = $this->getDb()->query('
		    SELECT *
		    FROM http
		    ORDER BY timestamp DESC;
		')->execute();
		
		$results = array();
		foreach ($records as $record)
		    $results[] = new HttpResult($record);
		
		return $results;
	}	
	
	public function getHttp($id)
	{
		$id = (int) $id;
		$records = $this->getDb()->query('
		    SELECT *
		    FROM http
		    WHERE id = '.$id.'
		')->execute();
		 
		$results = array();
		foreach ($records as $record)
			return new HttpResult($record);
	}
	
	
	
}