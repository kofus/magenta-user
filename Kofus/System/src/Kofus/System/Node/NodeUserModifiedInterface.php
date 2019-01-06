<?php

namespace Kofus\System\Node;
use Kofus\User\Entity\AccountEntity as UserAccountEntity;

interface NodeUserModifiedInterface
{
	public function getUserModified();
	
	public function setUserModified(UserAccountEntity $entity);

}