<?php

namespace Kofus\System\Search\Document;
use ZendSearch\Lucene\Document\Field;
use ZendSearch\Lucene\Document;

class CountryDocument extends Document
{
	public function populate($id, $label)
	{
		$this->addField(Field::text('node_id', $id));
		$this->addField(Field::text('node_type', 'COUNTRY'));
		$this->addField(Field::text('label', $label));
	}
}