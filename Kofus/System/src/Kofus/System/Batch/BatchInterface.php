<?php
namespace Kofus\System\Batch;


interface BatchInterface
{
	public function getItems();
	
	public function process($item);
	
	public function setMetaParams(array $params);
	
	public function getMetaParams();
}