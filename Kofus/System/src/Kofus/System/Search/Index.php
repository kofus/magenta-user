<?php
namespace Kofus\System\Search;

use ZendSearch\Lucene\Index as LuceneIndex;

class Index extends LuceneIndex
{
	public function find($query)
	{
		return parent::find($query);
		
		//$log = \Kofus\Archive\Sqlite\Table\Lucene::getInstance('lucene');
		//$log->addQuery($query);
		//$results = parent::find($query);
	}
}
