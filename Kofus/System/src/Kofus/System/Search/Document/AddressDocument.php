<?php

namespace Kofus\System\Search\Document;
use Kofus\System\Entity\AddressEntity;
use ZendSearch\Lucene\Document\Field;
use ZendSearch\Lucene\Document;
use Kofus\System\Filter\SearchTerm;

class AddressDocument extends Document
{
	public function populateNode(AddressEntity $node)
	{
		$this->addField(Field::text('node_id', $node->getNodeId()));
		$this->addField(Field::text('node_type', $node->getNodeType()));
		$this->addField(Field::text('label', $node->render()));
		
		$this->addField(Field::text('address_recipient', $node->getRecipient()));
		$this->addField(Field::text('address_additional1', $node->getAdditional1()));
		$this->addField(Field::text('address_additional2', $node->getAdditional2()));
		$this->addField(Field::text('address_post_code', $node->getPostCode()));
		$this->addField(Field::text('address_city', $node->getCity()));
		$this->addField(Field::text('address_street', $node->getStreet()));
		$this->addField(Field::text('address_foreign_node_id', $node->getForeignNodeId()));
		
		
		
	}
}