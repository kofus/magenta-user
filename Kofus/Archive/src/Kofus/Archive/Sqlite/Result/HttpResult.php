<?php
namespace Kofus\Archive\Sqlite\Result;

use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Http\Headers;

class HttpResult
{
    protected $record;
    
    public function __construct(array $record)
    {
        $this->record = $record;
    }

    /**
     * @return \Zend\Http\Request
     */
    public function getRequest()
    {
        $headers = Headers::fromString($this->record['request_headers']);
        
        $request = new Request();
        $request->setUri($this->record['uri']);
        $request->setMethod($this->record['method']);
        $request->setContent($this->record['request_body']);
        $request->setHeaders($headers);
        
        return $request;
    }
    
    public function getResponse()
    {
        $headers = Headers::fromString($this->record['response_headers']);
        
        $response = new Response();
        $response->setHeaders($headers);
        $response->setContent($this->record['response_body']);
        
        return $response;
    }
    
    public function getStatusCode()
    {
        return $this->record['status_code'];
    }
    
    public function getMethod()
    {
        return $this->record['method'];
    }
    
    public function getUri()
    {
        return $this->record['uri'];
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
    
    
    public function getId()
    {
        return $this->record['id'];
    }
}


