<?php
namespace Kofus\Archive\Sqlite\Table;

abstract class AbstractTable
{
	protected $id;
	protected $db;
	
	protected static $path = 'data/archive/';
	
	
	protected function init()
	{
		// To be overwritten
	}
	
	public static function create($id)
	{
		$classname = get_called_class();
		$instance = new $classname($id);
		$instance->init();
		return $instance;
	}
	
	public static function getInstance($id)
	{
		$classname = get_called_class();
		$instance = new $classname($id);
		return $instance;
	}
	
	/**
	 * @return \Zend\Db\Adapter\Adapter
	 */
	public function getDb()
	{
		if (! $this->db) {
		    $splinters = explode('\\', get_called_class());
		    $classname = array_pop($splinters);
		    $classname = strtolower($classname);
			$filename =  self::$path . $classname . '/' . $this->id . '.db';
			$triggerInit = false;
			if (! is_dir(dirname($filename)))
				mkdir(dirname($filename), 0777, true);
			chmod(dirname($filename), 0777);

			if (! is_file($filename)) {
				$handle = fopen($filename, 'w');
				if ($handle) {
					fclose($handle);
				} else {
					throw new \Exception('Could not create file ' . $filename);
				}
				$triggerInit = true;
			}
			$this->db = new \Zend\Db\Adapter\Adapter(array(
					'driver' 	=> 'Pdo_Sqlite',
					'database' 	=> $filename
			));
			
			if ($triggerInit) $this->init();
		}
		return $this->db;
	}
	
	public function pl()
	{
		return $this->getDb()->getPlatform();
	}
	
	protected function insert($table, $record)
	{
		$keys = array();
		$values = array();
		foreach ($record as $key => $value) {
			$keys[] = $this->pl()->quoteIdentifier($key);
			$values[] = $this->pl()->quoteValue($value);
		}
		
		return $this->getDb()->query('INSERT INTO ' . $table . ' (' . implode(', ', $keys) . ') VALUES (' . implode(', ', $values) . ');')->execute();
	}
	
	protected function __construct($id)
	{
	    $id = \Zend\Filter\StaticFilter::execute($id, 'Alnum');
	    $id = strtolower($id);
		$this->id = $id;
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	
	
	
	
	
	
}