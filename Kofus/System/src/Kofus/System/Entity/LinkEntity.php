<?php 
namespace Kofus\System\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="kofus_system_node_links", uniqueConstraints={@ORM\UniqueConstraint(name="uri", columns={"uri"})})
 *
 */
class LinkEntity
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
        return 'LINK';
    }
    
    public function getNodeId()
    {
        return $this->getNodeType() . $this->getId();
    }
    
    
	/**
	 * @ORM\Column()
	 */
	protected $uri;
	
	public function setUri($value)
	{
		$this->uri = $value; return $this;
	}
	
	public function getUri()
	{
		return $this->uri;
	}
	
	/**
	 * @ORM\Column(length=15)
	 */
	protected $linkedNodeId;
	
	public function setLinkedNodeId($value)
	{
	    $this->linkedNodeId = $value; return $this;
	}
	
	public function getLinkedNodeId()
	{
	    return $this->linkedNodeId;
	}
	
	/**
	 * @ORM\Column(length=5, nullable=true)
	 */
	protected $locale;
	
	public function setLocale($value)
	{
	    $this->locale = $value; return $this;
	}
	
	public function getLocale()
	{
	    return $this->locale; 
	}
	
	/**
	 * @ORM\Column(nullable=true)
	 */
	protected $context;
	
	public function setContext($value)
	{
		$this->context = $value; return $this;
	}
	
	public function getContext()
	{
		return $this->context;
	}
	
	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	protected $expires;
	
	public function setExpirationDate(\DateTime $datetime=null)
	{
	    $this->expires = $datetime; return $this;
	}
	
	public function getExpirationDate()
	{
	    return $this->expires;
	}
	
	
	public function __toString()
	{
		return $this->getUri();
	}
	
	
	
	
	
	
	
}