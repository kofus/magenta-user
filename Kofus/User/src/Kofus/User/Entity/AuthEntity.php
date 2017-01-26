<?php
 
namespace Kofus\User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kofus\System\Node\NodeInterface;
use Kofus\User\Entity\AccountEntity;


/**
 * @ORM\Entity
 * @ORM\Table(name="kofus_user_auth", indexes={@ORM\Index(name="kofus_user_auth_identitytype", columns={"identity", "type"})})
 *
 */
class AuthEntity implements NodeInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    protected $id;
    
    public function getId()
    {
        return $this->id;
    }
    
    
    /**
     * Set the encrypted password
     * @ORM\Column()
     */
    protected $credential;
    
    public function setCredential($value)
    {
    	$this->credential = $value; return $this;
    }
    
    public function getCredential()
    {
    	return $this->credential;
    }

    /**
     * @ORM\Column(nullable=true)
     */
    protected $identity;
    
    public function setIdentity($value)
    {
        $this->identity = $value; return $this;
    }
    
    public function getIdentity()
    {
        return $this->identity;
    }
    
    
    /**
     * @ORM\Column(length=15)
     */
    protected $encryption = 'password';
    
    const ENCRYPT_MD5 = 'md5';
    const ENCRYPT_PLAINTEXT = 'plaintext';
    const ENCRYPT_PASSWORD = 'password';
    const ENCRYPT_DRUPAL = 'drupal';
    
    public static $ENCRYPTIONS = array(
    	self::ENCRYPT_PASSWORD => 'PASSWORD',
        self::ENCRYPT_MD5 => 'MD5',
        self::ENCRYPT_DRUPAL => 'Drupal',
        self::ENCRYPT_PLAINTEXT => 'No encryption (plain text)'
    );
    
    public function setEncryption($value)
    {
    	$this->encryption = $value; return $this;
    }
    
    public function getEncryption()
    {
    	return $this->encryption;
    }
    
    /**
     * @ORM\Column(length=15)
     */
    protected $type = self::TYPE_WEB_LOGIN;
    
    const TYPE_WEB_LOGIN = 'login';
    
    public static $TYPES = array(
    	self::TYPE_WEB_LOGIN => 'Web Login'
    );
    
    public function setType($value)
    {
    	$this->type = $value; return $this;
    }
    
    public function getType($pretty=false)
    {
        if ($pretty)
    	   return self::$TYPES[$this->type];
        return $this->type;
    }
    
    /**
     * @ORM\ManyToOne(targetEntity="Kofus\User\Entity\AccountEntity")
     */
    protected $account;
    
    public function setAccount(AccountEntity $entity)
    {
        $this->account = $entity; return $this;
    }
    
    public function getAccount()
    {
        return $this->account;
    }
    
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    
    protected $expiration;
    
    public function setExpiration(\DateTime $datetime=null)
    {
        $this->expiration = $datetime; return $this;
    }
    
    public function getExpiration()
    {
        return $this->expiration;
    }
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $enabled;
    
    public function isEnabled($bool=null)
    {
        if ($bool === null)
            return $this->enabled;
        $this->enabled = (bool) $bool;
        return $this;
    }
    

    public function getNodeType()
    {
    	return 'AUTH';
    }
    
    public function getNodeId()
    {
        return $this->getNodeType() . $this->getId();
    }
    
    public function __toString()
    {
    	return $this->getType() .':'. $this->getIdentity() . ' (AUTH' . $this->getId() . ')';
    }
    
    
}
