<?php 
namespace Kofus\System\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kofus\System\Node;
use Kofus\System\Entity\TagVocabularyEntity;


/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE") 
 * @ORM\Table(name="kofus_tag_relations")
 *
 */
class TagRelationEntity implements Node\NodeInterface
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
        return 'TAGR';
    }
    
    public function getNodeId()
    {
        return $this->getNodeType() . $this->getId();
    }
    
	/**
	 * @ORM\Column()
	 */
	protected $foreignId;
	
	public function setForeignId($value)
	{
		$this->foreignId = $value; return $this;
	}
	
	public function getForeignId()
	{
		return $this->foreignId;
	}
	
	/**
	 * @ORM\ManyToOne(targetEntity="Kofus\System\Entity\TagEntity")
	 */
	protected $tag;
	
	public function setTag(TagEntity $entity)
	{
	    $this->tag = $entity; return $this;
	}
	
	public function getTag()
	{
	    return $this->tag;
	}
	
	
	public function __toString()
	{
	    return $this->getNodeId();
	}
	
	
	
	
	
	
}