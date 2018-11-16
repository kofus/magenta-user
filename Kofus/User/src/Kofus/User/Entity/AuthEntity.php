<?php
 
namespace Kofus\User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kofus\System\Node\NodeInterface;
use Kofus\User\Entity\AccountEntity;


/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE") 
 * @ORM\Table(name="kofus_user_auth")
 *
 */
class AuthEntity implements NodeInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    protected $id;
    
    public function getId()
    {
        return $this->id;
    }
    

    /**
     * @ORM\Column(nullable=true)
     */
    protected $identity;
    
    public function setIdentity($value)
    {
        $this->identity = $value; return $this;
    }
    
    public function getIdentity()
    {
        return $this->identity;
    }
    
    
    
    
    /**
     * @ORM\ManyToOne(targetEntity="Kofus\User\Entity\AccountEntity")
     */
    protected $account;
    
    public function setAccount(AccountEntity $entity)
    {
        $this->account = $entity; return $this;
    }
    
    public function getAccount()
    {
        return $this->account;
    }
    
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    
    protected $expiration;
    
    public function setExpiration(\DateTime $datetime=null)
    {
        $this->expiration = $datetime; return $this;
    }
    
    public function getExpiration()
    {
        return $this->expiration;
    }
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $enabled;
    
    public function isEnabled($bool=null)
    {
        if ($bool === null)
            return $this->enabled;
        $this->enabled = (bool) $bool;
        return $this;
    }
    

    public function getNodeType()
    {
    	return 'AUTH';
    }
    
    public function getNodeId()
    {
        return $this->getNodeType() . $this->getId();
    }
    
    public function __toString()
    {
    	return $this->getIdentity() . ' (' . $this->getNodeId() . ')';
    }
    
    
}
