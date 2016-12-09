<?php
namespace Kofus\Archive\Sqlite\Result;



class EventResult
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
    
    public function getIdentifier()
    {
        return $this->record['identifier'];
    }
    
    public function getEvent()
    {
        return $this->record['event'];
    }
    
    public function getNodesText()
    {
        return $this->record['nodes_txt'];
    }
    
    public function getTimestamp()
    {
    	return \DateTime::createFromFormat('U', $this->record['timestamp']);
    }
    
    
    
}


