<?php
namespace Kofus\System\Db\Sqlite\File;

abstract class AbstractFile
{
	protected $db;
	protected $filename;
	
	protected function init()
	{
		// To be overwritten
	}
	
	/**
	 * Creates new database file; existing file will be overwritten
	 * @param unknown $filename
	 * @return unknown
	 */
	public static function create($filename)
	{
		$classname = get_called_class();
		$instance = new $classname($filename);
		$instance->init();
		return $instance;
	}
	
	/**
	 * Opens existing database file or creates new one if it does not exist yet
	 * @param unknown $filename
	 * @return unknown
	 */
	public static function open($filename)
	{
		$classname = get_called_class();
		$instance = new $classname($filename);
		return $instance;
	}
	
	/**
	 * @return \Zend\Db\Adapter\Adapter
	 */
	public function getDb()
	{
		if (! $this->db) {
		    $filename = $this->getFilename();
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
	
	public function query($q)
	{
		return $this->getDb()->query($q);
	}
	
	public function insert($table, $record)
	{
		$keys = array();
		$values = array();
		foreach ($record as $key => $value) {
			$keys[] = $this->pl()->quoteIdentifier($key);
			$values[] = $this->pl()->quoteValue($value);
		}
		
		return $this->getDb()->query('INSERT INTO ' . $table . ' (' . implode(', ', $keys) . ') VALUES (' . implode(', ', $values) . ');')->execute();
	}
	
	public function update($table, $record, $where)
	{
		$clause = array();
		foreach ($record as $key => $value)
			$clause[] = $this->pl()->quoteIdentifier($key) . ' = ' . $this->pl()->quoteValue($value);
	
		return $this->getDb()->query('UPDATE ' . $table . ' SET ' . implode(', ', $clause) . ' WHERE ' . $where)->execute();
	}	
	
	protected function __construct($filename)
	{
		$this->filename = $filename;
	}
	
	public function getFilename()
	{
		return $this->filename;
	}
	
	
	
	
	
	
	
}