<?php
 
namespace Kofus\User\Entity;

use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity
 */
class AuthLoginEntity extends AuthEntity
{
   
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
    
    
    public function getNodeType()
    {
    	return 'AUTHLOGIN';
    }
    
}
