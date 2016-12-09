<?php
namespace Kofus\System\Service;


use Kofus\System\Service\AbstractService;
use Kofus\System\Db\Sqlite\File\Batches;
use Kofus\System\Batch\BatchInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;


class BatchService extends AbstractService
{
	public function add(BatchInterface $batch)
	{
		if ($batch instanceof ServiceLocatorAwareInterface)
			$batch->setServiceLocator($this->getServiceLocator());
		$total = count($batch->getItems());
		
		$db = $this->getDb();
		$record = array(
				"classname" => get_class($batch),
				"current_index" => 0,
				"last_index" => $total,
				"enabled" => 1,
				"timestamp_created" => time(),
				"elapsed" => 0				
		);
		$db->insert('batches', $record);
	}
	
	public function getBatches()
	{
		$db = $this->getDb();
		$records = $db->query('SELECT * FROM batches; ')->execute();
		$batches = array();
		foreach ($records as $record) {
			$batch = new $record['classname']();
			$batch->setMetaParams($record);
			if ($batch instanceof ServiceLocatorAwareInterface)
				$batch->setServiceLocator($this->getServiceLocator());
			$batches[] = $batch;
		}
		return $batches;
	}
	
	public function getBatch($id)
	{
		$db = $this->getDb();
		$records = $db->query('SELECT * FROM batches WHERE id = ' . $db->pl()->quoteValue($id))->execute();
		foreach ($records as $record) {
			$batch = new $record['classname']();
			$batch->setMetaParams($record);
			if ($batch instanceof ServiceLocatorAwareInterface)
				$batch->setServiceLocator($this->getServiceLocator());
				
			break;
		}		
		return $batch;
	}
	
	public function reset($batch)
	{
		$record = $batch->getMetaParams();
		$record['current_index'] = 0;
		$record['elapsed'] = 0;
		$db = $this->getDb();
		$db->update('batches', $record, 'id = ' . $db->pl()->quoteValue($record['id']));
		$batch->setMetaParams($record);
		return $batch;
		
	}
	
	public function run($batch)
	{
		$currentIndex = $batch->getMetaParam('current_index');
		if ($currentIndex == 0)
			$batch->beforeBatch();
		$index = 0;
		$processCounter = 0;
		$batch->beforeProcess();
		
		$time = time();
		foreach ($batch->getItems() as $item) {
			$index += 1;			
			if ($index <= $currentIndex) continue;
			$batch->process($item);
			$processCounter += 1;
			if ($processCounter == $batch->getBatchSize()) break;
		}

		
		$record = $batch->getMetaParams();
		$record['current_index'] = $index;
		$record['elapsed'] += time() - $time;
		
		$batch->afterProcess();		
		
		$db = $this->getDb();
		$db->update('batches', $record, 'id = ' . $db->pl()->quoteValue($record['id']));
		$batch->setMetaParams($record);
		return $batch;
	}
	
	
	protected function getDb()
	{
		return Batches::open('data/system/batches.db');
	}

}