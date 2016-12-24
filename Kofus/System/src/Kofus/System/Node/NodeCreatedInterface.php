<?php

namespace Kofus\System\Node;

interface NodeCreatedInterface
{
	public function getTimestampCreated();
	
	public function setTimestampCreated(\DateTime $datetime);

}