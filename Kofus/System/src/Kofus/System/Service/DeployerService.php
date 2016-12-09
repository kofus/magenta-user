<?php

namespace Kofus\System\Service;

class DeployerService
{
	
	protected static function filter($filename)
	{
		$filename = strtolower($filename);
		$filename = preg_replace('/[^a-z0-9\.\-\_]/', '', $filename);
		$filename = preg_replace('/\.+/', '.', $filename);
		return $filename;
	}
	
    public static function getServerConfigFilename()
    {
        $prefix = 'config/environment/server/';
        $hostname = self::filter(gethostname());
        $filename = $prefix . $hostname . '.php';
        if (file_exists($filename))
        	return $filename;
    }
    
    public static function getDomainConfigFilename()
    {
    	$prefix = 'config/environment/domain/';
    	$domain = self::filter($_SERVER['HTTP_HOST']);
    	$domain = preg_replace('/^www\./', '', $domain);
    	$filename = $prefix . $domain . '.php';
    	if (file_exists($filename))
    		return $filename;
    }
    
    
    public static function getModules(array $modules=array())
    {
    	$filenames = array(
    		self::getServerConfigFilename(),
    		self::getDomainConfigFilename()
    	);	
    	
    	foreach ($filenames as $filename) {
    		if (! $filename) continue;
    		$config = require $filename;
    		if (isset($config['modules']))
    			$modules = array_merge($modules, $config['modules']);
    	}

        return $modules;
    }
    
    public static function getConfigGlobPaths(array $paths=array())
    {
    	if (self::getServerConfigFilename())
    		$paths[] = self::getServerConfigFilename();
    	if (self::getDomainConfigFilename())
    		$paths[] = self::getDomainConfigFilename();
        return $paths;
    }
    /*
    public static function showMaintenancePage($bool, $whitelist=array())
    {
        if ($bool && ! in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
            print '
        		<html>
        		<head>
        		</head>
        		<body style="text-align: center">
                    <p><br></p>
                    <h1>'.$_SERVER['SERVER_NAME'].'</h1>
        			<p>Aufgrund von Wartungsarbeiten ist diese Website kurzzeitig leider nicht erreichbar.</p>
        			<p>Wir bitten um Entschuldigung.</p>
                    <p>(Ihre IP: '.$_SERVER['REMOTE_ADDR'].')</p>
        		</body>
        		</html>
    	   '; die();            
        }
    } */
}