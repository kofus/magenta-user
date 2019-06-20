<?php

namespace Kofus\System\Search\Document;
use ZendSearch\Lucene\Document\Field;
use ZendSearch\Lucene\Document;
use Kofus\System\Entity\TagEntity;

class TagDocument extends Document 
{
	public function populateNode(TagEntity $entity)
	{
		$this->addField(
				Field::text('node_id', $entity->getNodeId())
		);
		
		$segments = array($entity->getTitle());
		$parent = $entity->getParent();
		while ($parent) {
		    $segments[] = $parent->getTitle();
		    $parent = $parent->getParent();
		}
		
		
		
		$this->addField(
				Field::text('label', implode(' > ', $segments) . ' [' . $entity->getVocabulary()->getTitle() . ']')
		);
		$this->addField(
				Field::text('node_type', $entity->getNodeType())
		);
	}
}