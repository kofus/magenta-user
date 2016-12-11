<?php 
namespace Kofus\System\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kofus\System\Node\NodeInterface;
use Kofus\System\Node\TranslatableNodeInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="kofus_system_node_relations")
 *
 */
class RelationEntity
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
     * @ORM\Column(length=15)
     */
    protected $node1Id;
    
    public function setNode1Id($value)
    {
        $this->node1Id = $value;
        return $this;
    }
    
    public function getNode1Id()
    {
        return $this->node1Id;
    }
    
    /**
     * @ORM\Column(length=15)
     */
    
    protected $node2Id;
    
    public function setNode2Id($value)
    {
    	$this->node2Id = $value;
    	return $this;
    }
    
    public function getNode2Id()
    {
    	return $this->node2Id;
    }
    
    /**
     * @ORM\Column(nullable=true)
     */
    protected $label;
    
    public function setLabel($value)
    {
    	$this->label = $value;
    	return $this;
    }
    
    public function getLabel()
    {
    	return $this->label;
    }
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $weight;
    
    public function setWeight($value)
    {
    	$this->weight = $value;
    	return $this;
    }
    
    public function getWeight()
    {
    	return $this->weight;
    }
    
    
    protected $node;
    
    public function setNode(NodeInterface $node)
    {
        $this->node = $node; return $this;
    }
    
    public function getNode()
    {
        return $this->node;
    }
    
    public function getType()
    {
        $alpha = new \Zend\I18n\Filter\Alpha();
        $type1 = $alpha->filter($this->getNode1Id());
        $type2 = $alpha->filter($this->getNode2Id());
        
        if (true || $this->getNode() && $this->getNode()->getNodeType() == $type2) {
            $type = $type2 . '_' . $type1;
        } else {
            $type = $type1 . '_' . $type2;
        }
        return $type;
    }
    
    
    
    
    
	
	
	
	
	
	
	
}