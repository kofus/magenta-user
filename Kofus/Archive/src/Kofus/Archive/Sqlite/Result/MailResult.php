<?php
namespace Kofus\Archive\Sqlite\Result;

use Kofus\Archive\Sqlite\Hydrator\MailHydrator;
use Zend\Mail\Message;

class MailResult
{
    protected $record;
    
    public function __construct(array $record)
    {
        $this->record = $record;
    }
    
    public function getObject()
    {
        $hydrator = new MailHydrator();

        $mail = new Message();
        $mail = $hydrator->hydrate($this->record, $mail);
        return $mail;
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
    
    public function getBodyText()
    {
        return $this->record['body_text'];
    }
    
    public function getId()
    {
        return $this->record['id'];
    }
}

