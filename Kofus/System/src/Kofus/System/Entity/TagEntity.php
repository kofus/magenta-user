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
class TagEntity implements Node\NodeInterface, Node\SortableNodeInterface, Node\TranslatableNodeInterface
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
        return 'T';
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
	
	public function getTitle($recursive=false, $separator=' » ')
	{
	    if ($recursive) {
	        $title = array($this->getTitle());
	        
	        $parent = $this->getParent();
	        
	        while($parent) {
	            $title[] = $parent->getTitle();
	            $parent = $parent->getParent();
	        }
	        
	        return implode($separator, array_reverse($title));
	    }
	    
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
	 * @ORM\ManyToOne(targetEntity="Kofus\System\Entity\TagEntity", inversedBy="children")
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
	 */
	protected $parent;
	
	public function setParent(TagEntity $entity=null)
	{
	    $this->parent = $entity; return $this;
	}
	
	/**
	 * @return Opac\Entity\TagEntity
	 */
	public function getParent()
	{
	    return $this->parent;
	}
	
	/**
	 * @ORM\OneToMany(targetEntity="Kofus\System\Entity\TagEntity", mappedBy="parent")
	 * @var array
	 */
	protected $children=array();
	
	public function getChildren()
	{
	    return $this->children;
	}
	
	/**
	 * @ORM\Column(nullable=true)
	 */
	protected $uriSegment;
	
	public function getUriSegment()
	{
	    return $this->uriSegment;
	}
	
	public function setUriSegment($value)
	{
	    $this->uriSegment = $value; return $this;
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
	
	public function getTranslatableMethods()
	{
	    return array(
	        'getUriSegment' => 'uriSegment',
	        'getTitle' => 'title'
	    );
	}
	
	
	
	
	
	
}