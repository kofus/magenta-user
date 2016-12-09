<?php
namespace Kofus\Archive\Service;
use Kofus\System\Service\AbstractService;
use Kofus\Archive\Sqlite\Table;


class ArchiveService extends AbstractService
{
    public function uriStack()
    {
        return new UriStackService();
    }
    
    public function mails($namespace='mails')
    {
        return Table\Mails::getInstance($namespace);
    }
    
    public function http($namespace='http')
    {
    	return Table\Http::getInstance($namespace);
    }
    
    public function soap($namespace='soap')
    {
    	return Table\Soap::getInstance($namespace);
    }
    
    
    public function events($namespace='events')
    {
        return Table\Events::getInstance($namespace);
    }
    
    public function sql($namespace='doctrine')
    {
    	return Table\Sql::getInstance($namespace);
    }
      
    public function lucene($namespace='lucene')
    {
    	return Table\Lucene::getInstance($namespace);
    }      
    
    public function sessions($namespace='sessions')
    {
        return Table\Sessions::getInstance($namespace);
    }
    
    
}