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
		
		
		$label = implode(' > ', $segments);
		if ($entity->getVocabulary())
		    $label .= ' [' . $entity->getVocabulary()->getTitle() . ']';
		
		$this->addField(
				Field::text('label',  $label)
		);
		$this->addField(
				Field::text('node_type', $entity->getNodeType())
		);
	}
}