<?php 
namespace Kofus\System\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kofus\System\Node;
use Kofus\Calendar\Entity\CalendarEntity;


/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE") 
 * @ORM\Table(name="kofus_tag_vocabularies")
 *
 */
class TagVocabularyEntity implements Node\NodeInterface
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
        return 'TV';
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
	
	public function __toString()
	{
	    return $this->getTitle() . ' (' . $this->getNodeId() . ')';
	}
}