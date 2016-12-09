<?php 
namespace Kofus\System\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kofus\System\Node\NodeInterface;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE") 
 * @ORM\Table(name="kofus_system_addresses")
 *
 */
class AddressEntity implements NodeInterface
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
        return 'ADDR';
    }
    
    public function getNodeId()
    {
        return $this->getNodeType() . $this->getId();
    }
    
    /**
     * @ORM\Column(nullable=true, length=15)
     */
    protected $foreignNodeId;
    
    public function setForeignNodeId($value)
    {
    	$this->foreignNodeId = $value; return $this;
    }
    
    public function getForeignNodeId()
    {
    	return $this->foreignNodeId;
    }
    
    /**
     * @ORM\Column(nullable=true, length=15)
     */
    protected $type;
    
    public function setType($value)
    {
    	$this->type = $value; return $this;
    }
    
    public function getType()
    {
    	return $this->type;
    }
    
    
    
	/**
	 * @ORM\Column(nullable=true)
	 */
	protected $street;
	
	public function setStreet($value)
	{
		$this->street = $value; return $this;
	}
	
	public function getStreet()
	{
		return $this->street;
	}
	
	/**
	 * @ORM\Column(nullable=true)
	 */
	protected $additional1;
	
	public function setAdditional1($value)
	{
		$this->additional1 = $value; return $this;
	}
	
	public function getAdditional1()
	{
		return $this->additional1;
	}
	
	/**
	 * @ORM\Column(nullable=true)
	 */
	protected $additional2;
	
	public function setAdditional2($value)
	{
		$this->additional2 = $value; return $this;
	}
	
	public function getAdditional2()
	{
		return $this->additional2;
	}
	
	
	/**
	 * @ORM\Column(nullable=true)
	 */
	protected $postCode;
	
	public function setPostCode($value)
	{
		$this->postCode = $value; return $this;
	}
	
	public function getPostCode()
	{
		return $this->postCode;
	}
	
	/**
	 * @ORM\Column(nullable=true)
	 */
	protected $city;
	
	public function setCity($value)
	{
		$this->city = $value; return $this;
	}
	
	public function getCity()
	{
		return $this->city;
	}
	
	/**
	 * @ORM\Column(nullable=true, length=2)
	 */
	protected $country = 'DE';
	
	public function setCountry($value)
	{
		$this->country = $value; return $this;
	}
	
	public function getCountry($pretty=false)
	{
	    if ($pretty && $this->country) {
	        return \Locale::getDisplayRegion('-' . $this->country);
	    }
		return $this->country;
	}
	
	/**
	 * @ORM\Column(nullable=true)
	 */
	protected $recipient;
	
	public function setRecipient($value)
	{
		$this->recipient = $value; return $this;
	}
	
	public function getRecipient()
	{
		return $this->recipient;
	}
	
	public function render($separator=', ')
	{
	    $s = array();
	    if ($this->getRecipient())
	    	$s[] = $this->getRecipient();
	    if ($this->getAdditional1())
	    	$s[] = $this->getAdditional1();
	    if ($this->getStreet())
	    	$s[] = $this->getStreet();
	    $s[] = $this->getPostCode() . ' ' . $this->getCity();
	    if ($this->getCountry() != 'DE')
	       $s[] = $this->getCountry(true);
	    return implode($separator, $s); 
	}
	
	public function hash()
	{
	    $values = array(
	    	$this->getAdditional1(),
	        $this->getAdditional2(),
	        $this->getCity(),
	        $this->getCountry(),
	        $this->getPostCode(),
	        $this->getRecipient(),
	        $this->getStreet()
	    );
	    $hash = implode('|', $values);
	    return md5($hash);
	}
	
	public function __toString()
	{
		return $this->render(', ');
	}
	
	
}