<?php

namespace Kofus\Mailer\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kofus\System\Node\NodeInterface;
use Kofus\Mailer\Entity\NewsgroupEntity;



/**
 * @ORM\Entity
 * @ORM\Table(name="kofus_mailer_subscriptions")
 *
 */
class SubscriptionEntity implements NodeInterface
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
     * @ORM\Column()
     */
    protected $emailAddress;
    
    public function setEmailAddress($value)
    {
    	$this->emailAddress = $value; return $this;
    }
    
    public function getEmailAddress()
    {
    	return $this->emailAddress;
    }
    
    /**
     * @ORM\Column(nullable=true)
     */
    protected $name;
    
    public function setName($value)
    {
    	$this->name = $value; return $this;
    }
    
    public function getName()
    {
        if ($this->name)
    	   return $this->name;
        return trim($this->getTitle() . ' ' . $this->getFirstName() . ' ' . $this->getLastName());
    }
    
    /**
     * @ORM\Column(nullable=true)
     */
    protected $firstName;
    
    public function setFirstName($value)
    {
        $this->firstName = $value; return $this;
    }
    
    public function getFirstName()
    {
        return $this->firstName;
    }
    
    /**
     * @ORM\Column(nullable=true)
     */
    protected $lastName;
    
    public function setLastName($value)
    {
        $this->lastName = $value; return $this;
    }
    
    public function getLastName()
    {
        return $this->lastName;
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
     * @ORM\Column(nullable=true, length=1)
     */
    protected $gender;
    
    public function setGender($value)
    {
    	$this->gender = $value; return $this;
    }
    
    public function getGender()
    {
    	return $this->gender;
    }
    
    /**
     * @ORM\ManyToOne(targetEntity="Kofus\Mailer\Entity\NewsgroupEntity")
     */
    protected $newsgroup;
    
    public function setNewsgroup(NewsgroupEntity $entity)
    {
    	$this->newsgroup = $entity; return $this;
    }
    
    public function getNewsgroup()
    {
    	return $this->newsgroup;
    }
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $timestampCreated;
    
    public function setTimestampCreated(\DateTime $timestamp)
    {
        $this->timestampCreated = $timestamp; return $this;
    }
    
    public function getTimestampCreated()
    {
        return $this->timestampCreated;
    }
    
    
    
	public function getNodeType()
	{
		return 'SUBSCR';
	}
	
	public function getNodeId()
	{
	    return $this->getNodeType() . $this->getId();
	}
	
	public function __toString()
	{
		return $this->getNodeId();	
	}
}

