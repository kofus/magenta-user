<?php

namespace Kofus\System\Node;
use Kofus\User\Entity\AccountEntity as UserAccountEntity;

interface NodeUserCreatedInterface
{
	public function getUserCreated();
	
	public function setUserCreated(UserAccountEntity $entity);

}