<?php
 
namespace Kofus\User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kofus\System\Node\NodeInterface;
use Kofus\User\Entity\AccountEntity;


/**
 * @ORM\Entity
 *
 */
class AuthPassphraseEntity extends AuthEntity
{

    public function getNodeType()
    {
    	return 'AUTHPASS';
    }
    
    public function getNodeId()
    {
        return $this->getNodeType() . $this->getId();
    }
    
    public function __toString()
    {
    	return $this->getType() .':'. $this->getIdentity() . ' (' . $this->getNodeId() . ')';
    }
    
    
}
