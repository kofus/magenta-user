<?php 
namespace Kofus\System\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kofus\System\Node;


/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE") 
 * @ORM\Table(name="kofus_system_contents")
 *
 */
class ContentEntity implements Node\NodeInterface, Node\NodeModifiedInterface, Node\ParameterInterface
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
        return 'C';
    }
    
    public function getNodeId()
    {
        return $this->getNodeType() . $this->getId();
    }
    
    
	/**
	 * @ORM\Column(nullable=true)
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
	 * @ORM\Column(type="text", nullable=true)
	 */
	protected $content;
	
	public function setContent($value)
	{
		$this->content = $value; return $this;
	}
	
	public function getContent()
	{
		return $this->content;
	}
	
	
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $enabled = true;
	
	public function isEnabled($boolean=null)
	{
		if ($boolean !== null)
		    $this->enabled = (bool) $boolean;
		return $this->enabled;
	}
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $navVisible = false;
	
	public function isNavVisible($boolean=null)
	{
		if ($boolean !== null)
			$this->navVisible = (bool) $boolean;
		return $this->navVisible;
	}
	
	
	/**
	 * @ORM\ManyToOne(targetEntity="Kofus\System\Entity\ContentEntity")
	 */
	protected $parent;
	
	public function setParent(ContentEntity $entity=null)
	{
		$this->parent = $entity; return $this;
	}
	
	public function getParent()
	{
		return $this->parent;
	}
	
	/**
	 * @ORM\Column(nullable=true)
	 */
	protected $navLabel;
	
	public function getNavLabel()
	{
	    if ($this->navLabel)
	        return $this->navLabel;
	    return $this->getTitle();
	}
	
	public function setNavLabel($value)
	{
	    if (! $value)
	        $value = $this->getTitle();
	    $this->navLabel = $value; return $this;
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
	 * @ORM\Column(length=50, nullable=true)
	 */
	protected $navbar;
	
	public function getNavbar()
	{
		return $this->navbar;
	}
	
	public function setNavbar($value)
	{
		$this->navbar = $value; return $this;
	}
	
	/**
	 * @ORM\Column(type="integer")
	 */
	protected $priority = 0;
	
	public function getPriority()
	{
		return $this->priority;
	}
	
	public function setPriority($value)
	{
		$this->priority = (int) $value; return $this;
	}
	
	
	public function __toString()
	{
		return $this->getTitle() . ' (' . $this->getNodeId() . ')';
	}
	
	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	protected $timestampModified;
	
	public function setTimestampModified(\DateTime $datetime)
	{
		$this->timestampModified = $datetime; return $this;
	}
	
	public function getTimestampModified()
	{
		return $this->timestampModified;
	}
	
	/**
	 * @ORM\Column(type="json_array")
	 */
	protected $params = array();
	
	public function setParam($key, $value)
	{
	    $this->params[$key] = $value; return $this;
	}
	
	public function getParam($key)
	{
	    if (isset($this->params[$key]))
	        return $this->params[$key];
	}
	
	
	
}