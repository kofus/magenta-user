<?php
namespace Kofus\Media\Entity;
use Doctrine\ORM\Mapping as ORM;
use Kofus\System\Node;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\Table(name="kofus_media_files")
 * 
 */
class FileEntity implements Node\NodeInterface, Node\EnableableNodeInterface
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
	protected $filename;
	
	public function setFilename($value)
	{
		$this->filename = $value; return $this;
	}
	
	public function getFilename()
	{
		return $this->filename;
	}
	
	/**
	 * @ORM\Column()
	 */
	protected $mimeType;
	
	public function setMimeType($value)
	{
		$this->mimeType = $value; return $this;
	}
	
	public function getMimeType()
	{
		return $this->mimeType;
	}
	

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $filesize;
	
	public function setFilesize($value)
	{
		$this->filesize = $value; return $this;
	}
	
	public function getFilesize()
	{
		return $this->filesize;
	}
	
	/**
	 * @ORM\Column(nullable=true)
	 */
	protected $hash;
	
	public function setHash($value)
	{
		$this->hash = $value; return $this;
	}
	
	public function getHash()
	{
		return $this->hash;
	}
		
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $enabled = true;
	
	public function isEnabled($bool = null)
	{
		if ($bool !== null) {
		    $this->enabled = (bool) $bool;
		    return $this;
		}
		return $this->enabled;
	}
	
	public static function determineType(array $infoArray)
	{
		$mimeType = strtolower($infoArray['type']);
		$extension = strtolower(pathinfo($infoArray['name'], PATHINFO_EXTENSION));
		
		if ('pdf' == $extension || strpos($mimeType, 'pdf'))
			return 'pdf';
		if (false !== strpos($mimeType, 'image'))
			return 'image';
		return 'file';
	}
	
	
	public function getNodeType()
	{
		return 'F';
	}
	
	public function __toString()
	{
		return $this->getNodeId();
	}
	
	public function getNodeId()
	{
		return $this->getNodeType() . $this->getId();
	}
	
	public function getPath()
	{
		return 'data/media/files/' . $this->getFilename();
	}
	
	
}

