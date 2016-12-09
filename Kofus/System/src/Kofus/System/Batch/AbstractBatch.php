<?php
namespace Kofus\System\Batch;

use Kofus\System\Service\AbstractService;
use Kofus\System\Batch\BatchInterface;

class AbstractBatch extends AbstractService implements BatchInterface
{
	protected $metaParams = array();
	
	public function setMetaParams(array $params)
	{
		$this->metaParams = $params; return $this;
	}
	
	public function getMetaParams()
	{
		return $this->metaParams;
	}
	
	public function getMetaParam($key)
	{
		if (isset($this->metaParams[$key]))
			return $this->metaParams[$key];
	}
	
	public function getItems()
	{
		return array();
	}
	
	public function process($item) {}
	
	public function beforeProcess() {}
	
	public function afterProcess() {}
	
	public function beforeBatch() {}
	public function afterBatch() {}
	
	protected $batchSize = 10;
	
	public function getBatchSize()
	{
		return $this->batchSize;
	}
	
	public function setBatchSize($value)
	{
		$this->batchSize = $value; return $this;
	}
	
}