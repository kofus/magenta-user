<?php
 
namespace Kofus\User\Entity;


use Doctrine\ORM\Mapping as ORM;
use Kofus\System\Node;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE") 
 * @ORM\Table(name="kofus_user_roles")
 */
class RoleEntity implements Node\NodeInterface, RoleInterface
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
    
    public function setId($value)
    {
        $this->id = $value; return $this;
    }
    
    /**
     * @ORM\Column()
     */
    protected $name;
    
    public function setName($value)
    {
        $this->name = $value; return $this;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * @ORM\ManyToOne(targetEntity="Kofus\User\Entity\RoleEntity", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent;
    
    public function setParent(RoleEntity $entity=null)
    {
        $this->parent = $entity; return $this;
    }
    
    /**
     * @return Kofus\User\Entity\RoleEntity
     */
    public function getParent()
    {
        return $this->parent;
    }
    
    /**
     * @ORM\OneToMany(targetEntity="Kofus\User\Entity\RoleEntity", mappedBy="parent")
     * @var array
     */
    protected $children=array();
    
    public function getChildren()
    {
        return $this->children;
    }
    
    /**
     * @ORM\OneToMany(targetEntity="Kofus\User\Entity\AccountEntity", mappedBy="role")
     * @var array
     */
    protected $userAccounts=array();
    
    public function getUserAccounts()
    {
        return $this->userAccounts;
    }
    
    /**
     * @ORM\Column(type="json_array")
     */
    protected $variables = array();
    
    public function setVariable($key, $value)
    {
        $this->variables[$key] = $value; return $this;
    }
    
    public function getVariable($key, $defaultValue=null)
    {
        if (isset($this->variables[$key]))
            return $this->variables[$key];
        return $defaultValue;
    }
    
    public function getNodeType()
    {
    	return 'UR';
    }
    
    public function __toString()
    {
    	return $this->getName() . ' (' . $this->getNodeId() . ')';
    }
    
    public function getNodeId()
    {
        return $this->getNodeType() . $this->getId();
    }
    
    public function getRoleId()
    {
        return $this->getNodeId();
    }
    
    
    
}
