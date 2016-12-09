<?php
namespace Kofus\Archive\Sqlite\Result;


class SoapResult
{
    protected $record;
    
    public function __construct(array $record)
    {
        $this->record = $record;
    }

    public function getTimestamp()
    {
        $dateTime = \DateTime::createFromFormat('U', $this->record['timestamp']);
        return $dateTime;
    }
    
    public function getRelatedNodeIds()
    {
        return explode('|', trim($this->record['nodes'], '|'));
    }
    
    public function getRequest()
    {
        return $this->record['request_body'];
    }
    
    public function getResponse()
    {
        return $this->record['response_body'];
    }
    
    public function getRequestHeaders()
    {
    	return $this->record['request_headers'];
    }
    
    public function getResponseHeaders()
    {
    	return $this->record['response_headers'];
    }
    
    
    
    public function getId()
    {
        return $this->record['id'];
    }
}


