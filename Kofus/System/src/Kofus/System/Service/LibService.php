<?php

namespace Kofus\System\Service;


class LibService 
{
	public function getFilenameByUri($uri)
	{
	    $filename = preg_replace('([^\w\s\d\-_~,;:\[\]\(\).\/])', '', $uri);
	    $filename = preg_replace('([\.]{2,})', '', $filename);
        $filename = str_replace($this->getUriPathPrefix(), '', $filename);
        	    
	    foreach ($this->getLibPaths() as $libPath) {
	    	$testFilename = $libPath . $filename;
	    	if (is_file($testFilename))
	    	    return $testFilename;
	    }
	}
	
	public function getUriByFilename($filename)
	{
	    $libPath = KOFUS_MODULE_SYSTEM_PATH . '/lib/';
	    if (strpos($filename, $libPath) !== false)
	       return str_replace($libPath, '/cache/lib', $filename);
	    
	    $publicPath = realpath('public');
	    if (strpos($filename, $publicPath) !== false)
	    	return str_replace($publicPath, '', $filename);
	     
	}
	
	public function getLibPaths()
	{
	    return array(
	        'public', 
	        KOFUS_MODULE_SYSTEM_PATH  . '/lib/'
	    );
	}
	
	public function getUriPathPrefix()
	{
	    return '/cache/lib';
	}
	
	public static function getMimeType($filename)
	{
	    if (preg_match('/\.css$/i', $filename)) {
	    	$type = 'text/css';
	    } elseif (preg_match('/\.js$/i', $filename)) {
	    	$type = 'text/javascript';
	    } elseif (preg_match('/\.svg$/i', $filename)) {
	    	$type = 'image/svg+xml';
	    } else {
	        //$finfo = finfo_open(FILEINFO_MIME_TYPE, $this->config()->get('executables.magic', '/usr/share/misc/magic'));
	        $finfo = finfo_open(FILEINFO_MIME_TYPE);
	        if (! $finfo)
	        	throw new \Exception('finfo not available');
	        $type = finfo_file($finfo, $filename);
	        finfo_close($finfo);
	    }
	    return $type; 
	}
	
	public function createCacheFile($uri)
	{
	    $target = 'public/' . $uri;
	    if (! is_dir(dirname($target))) {
	        if (! mkdir(dirname($target), 0777, true))
	            throw new \Exception('Could not create ' . $target);
	    } 
	    $filename = $this->getFilenameByUri($uri);
	    $success = copy($filename, $target);
	    if (! $success)
	        throw new \Exception('Could not copy ' . $filename . ' to ' . $target);
	}
	
	
}