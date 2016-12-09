<?php 
namespace Kofus\System\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="kofus_system_node_translations")
 *
 */
class NodeTranslationEntity 
{
    /**
     * @ORM\Id
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
     * @ORM\Id
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
     * @ORM\Column(type="text")
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
    
    
    
    
	
}