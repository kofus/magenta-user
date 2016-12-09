<?php
namespace Kofus\Archive\Sqlite\Result;



class SessionResult
{
    protected $record;
    
    public function __construct(array $record)
    {
        $this->record = $record;
    }

    
    public function getRelatedNodeIds()
    {
        return explode('|', trim($this->record['nodes'], '|'));
    }
    
    
    public function getId()
    {
        return $this->record['id'];
    }
    
    public function getName()
    {
        return $this->record['name'];
    }
    
    public function getRemoteAddr()
    {
        return $this->record['remote_addr'];
    }
    
    public function getHttpUserAgent()
    {
        return $this->record['http_user_agent'];
    }
    
    public function getTimestampModified()
    {
        return \DateTime::createFromFormat('U', $this->record['modified']);
        
    }
    
    public function getTimestampCreated()
    {
    	return \DateTime::createFromFormat('U', $this->record['created']);
    
    }
    
    
    
}


