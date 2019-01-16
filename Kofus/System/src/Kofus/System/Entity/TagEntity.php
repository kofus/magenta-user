<?php 
namespace Kofus\System\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kofus\System\Node;
use Kofus\System\Entity\TagVocabularyEntity;


/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE") 
 * @ORM\Table(name="kofus_tags")
 *
 */
class TagEntity implements Node\NodeInterface, Node\SortableNodeInterface
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
    
    public function getNodeType()
    {
        return 'TAG';
    }
    
    public function getNodeId()
    {
        return $this->getNodeType() . $this->getId();
    }
    
	/**
	 * @ORM\Column()
	 */
	protected $title;
	
	public function setTitle($value)
	{
		$this->title = $value; return $this;
	}
	
	public function getTitle()
	{
		return $this->title;
	}
	
	/**
	 * @ORM\ManyToOne(targetEntity="Kofus\System\Entity\TagVocabularyEntity")
	 */
	protected $vocabulary;
	
	public function setVocabulary(TagVocabularyEntity $entity)
	{
	    $this->vocabulary = $entity; return $this;
	}
	
	public function getVocabulary()
	{
	    return $this->vocabulary;
	}
	
	
	/**
	 * @ORM\ManyToMany(targetEntity="Kofus\System\Entity\TagEntity", mappedBy="parents")
	 */
	protected $children = array();
	
	public function setChildren($entities)
	{
	    $this->children = $entities; return $this;
	}
	
	public function getChildren()
	{
	    return $this->children;
	}
	
	public function hasChildren()
	{
	    return (count($this->children) > 0);
	}
	
	/**
	 * @ORM\ManyToMany(targetEntity="Kofus\System\Entity\TagEntity", inversedBy="children")
	 * @ORM\JoinTable(name="kofus_tags_hierarchy",
	 *      joinColumns={@ORM\JoinColumn(name="parent_id", referencedColumnName="id")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="child_id", referencedColumnName="id")}
	 *      )
	 */
	protected $parents = array();
	
	public function setParents($entities)
	{
	    $this->parents = $entities; return $this;
	}
	
	public function getParents()
	{
	    return $this->parents;
	}
	
	public function hasParents()
	{
	    return (count($this->parents) > 0);
	}
	
	/**
	 * @ORM\Column(type="bigint", nullable=true)
	 */
	protected $priority;
	
	public function setPriority($value)
	{
	    $this->priority = (int) $value; return $this;
	}
	
	public function getPriority()
	{
	    return $this->priority;
	}
	
	public function __toString()
	{
	    return $this->getTitle();
	}
	
	
	
	
	
	
}