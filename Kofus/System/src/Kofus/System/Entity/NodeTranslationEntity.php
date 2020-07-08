<?php 
namespace Kofus\System\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="kofus_system_node_translations", uniqueConstraints={@ORM\UniqueConstraint(name="u", columns={"msgId", "locale", "textDomain"})})
 *
 */
class NodeTranslationEntity
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
    protected $msgId;
    
    public function getMsgId()
    {
        return $this->msgId;
    }
    
    public function setMsgId($value)
    {
    	$this->msgId = $value; return $this;
    }
    
    /**
     * @ORM\Column(length=5)
     */
    protected $locale;
    
    public function getLocale()
    {
    	return $this->locale;
    }
    
    public function setLocale($value)
    {
    	$this->locale = $value; return $this;
    }
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $value;
    
    public function getValue()
    {
    	return $this->value;
    }
    
    public function setValue($value)
    {
    	$this->value = $value; return $this;
    }
    
    /**
     * @ORM\Column(nullable=true)
     */
    protected $textDomain;
    
    public function getTextDomain()
    {
        return $this->textDomain;
    }
    
    public function setTextDomain($value)
    {
        $this->textDomain = $value; return $this;
    }
}