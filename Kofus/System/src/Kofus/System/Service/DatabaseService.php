<?php

namespace Kofus\System\Service;

use Kofus\System\Service\AsbtractService;

class DatabaseService extends AbstractService
{
    public function dump()
    {
        // Fetch database config
        $config = $this->getServiceLocator()->get('Config');
        $db = $config['doctrine']['connection']['orm_default'];
        $dbParams = $db['params'];
        
        // Determine database driver type
        if (strpos(strtolower($db['driverClass']), 'mysql') !== false) {
        	$type = 'mysql';
        } elseif (strpos(strtolower($db['driverClass']), 'pgsql')) {
        	$type = 'psql';
    	} elseif (strpos(strtolower($db['driverClass']), 'sqlite')) {
    		$type = 'sqlite';
        		 
        } else {
        	throw new \Exception('Database driver? ' . $db['driverClass']);
        }
        
        // Fetch dump via console
        switch ($type) {
        	case 'mysql':
        		$handle = popen('mysqldump --user='.escapeshellarg($dbParams['user']).' --password='.escapeshellarg($dbParams['password']).' --host='.escapeshellarg($dbParams['host']).' --port='. ((int) $dbParams['port']) . ' '. escapeshellarg($dbParams['dbname']), 'r');
        		$s = stream_get_contents($handle);
        		fclose($handle);
        		break;
        
        	case 'psql':
        		putenv('PGPASSWORD=' . $dbParams['password']);
        		putenv('PGUSER=' . $dbParams['user']);
        		$handle = popen('pg_dump --inserts --format plain -h '.escapeshellarg($dbParams['host']).' --port='.(int)$dbParams['port']. ' '. escapeshellarg($dbParams['dbname']), 'r');
        		putenv('PGPASSWORD');
        		putenv('PGUSER');
        		$s = stream_get_contents($handle);
        		fclose($handle);
        		break;
        		
        	case 'sqlite':
        	    $s = file_get_contents($dbParams['path']);
        	    
        }
        return $s;
    }
    
    protected function createFilename()
    {
        $config = $this->getServiceLocator()->get('Config');
        $db = $config['doctrine']['connection']['orm_default'];
        if (strpos($db['driverClass'], 'Sqlite')) {
            $filename = 'db-' . date('Y-m-d-His') . '.db';
        } else {
            $filename = $db['params']['dbname'] . '-' . date('Y-m-d-His') . '.sql';
        }
        return $filename;
    }
    
    public function save($filename=null)
    {
        if (! $filename) 
            $filename = 'data/backups/' . $this->createFilename();
        $s = $this->dump();
        
        if (! is_dir(dirname($filename)))
        	mkdir(dirname($filename), 0777, true);
        
        file_put_contents($filename, $s);
    }
    
}