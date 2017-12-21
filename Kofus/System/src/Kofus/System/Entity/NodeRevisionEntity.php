<?php 
namespace Kofus\System\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kofus\User\Entity\AccountEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="kofus_system_node_revisions")
 *
 */
class NodeRevisionEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    protected $id;
    
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $number;
    
    public function setNumber($value)
    {
        $this->number = (int) $value; return $this;
    }
    
    public function getNumber()
    {
        return $this->number;
    }
    
    
	/**
	 * @ORM\Column()
	 */
	protected $nodeId;
	
	public function setNodeId($value)
	{
		$this->nodeId = $value; return $this;
	}
	
	public function getNodeId()
	{
		return $this->nodeId;
	}
	
	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $timestamp;
	
	public function setTimestamp(\DateTime $datetime=null)
	{
	    $this->timestamp = $datetime; return $this;
	}
	
	public function getTimestamp()
	{
	    return $this->timestamp;
	}
	
	/**
	 * @ORM\Column()
	 */
	protected $field;
	
	public function setField($value)
	{
	    $this->field = $value; return $this;
	}
	
	public function getField()
	{
	    return $this->field;
	}
	
	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	protected $value;
	
	public function setValue($value)
	{
	    $this->value = $value; return $this;
	}
	
	public function getValue()
	{
	    return $this->value;
	}
	
	/**
	 * @ORM\ManyToOne(targetEntity="Kofus\User\Entity\AccountEntity")
	 */
	protected $userAccount;
	
	public function setUserAccount(AccountEntity $entity=null)
	{
	    $this->userAccount = $entity; return $this;
	}
	
	public function getUserAccount()
	{
	    return $this->userAccount;
	}
	
}