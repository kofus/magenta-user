<?php

namespace Kofus\System\Service;

use Kofus\System\Service\AbstractService;

class ConfigService extends AbstractService
{
	protected $config;
	
	public function getConfig()
	{
		if (! $this->config)
			$this->config = $this->getServiceLocator()->get('Config');
		return $this->config;
	}
	
	public function get($path, $default=null)
	{
		$keys = explode('.', $path);
		$config = $this->getConfig();
	
		foreach ($keys as $key) {
			if (! isset($config[$key])) 
				return $default;
			$config = $config[$key];
		}
		return $config;
	}
	
	public function has($path)
	{
		$keys = explode('.', $path);
		$config = $this->getConfig();
	
		foreach ($keys as $key) {
			if (! isset($config[$key]))
				return false;
			$config = $config[$key];
		}
		return true;
	}
	
	
	
	
}