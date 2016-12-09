<?php
namespace Kofus\System\Search;
use Kofus\System\Node\NodeInterface;

interface NodeDocumentInterface
{
	public function populateNode(NodeInterface $node, $locale);
}