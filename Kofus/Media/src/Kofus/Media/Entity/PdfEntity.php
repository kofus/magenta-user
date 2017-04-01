<?php
namespace Kofus\Media\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class PdfEntity extends ImageEntity 
{
	public function getNodeType()
	{
		return 'PDF';
	}
	
	/**
	 * @return \Imagick
	 */
	public function getImagick()
	{
	    return new \Imagick($this->getPath() . '[0]');
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
	 * @ORM\Column(nullable=true)
	 */
	protected $uriSegment;
	
	public function setUriSegment($value)
	{
		$this->uriSegment = $value; return $this;
	}
	
	public function getUriSegment()
	{
		return $this->uriSegment;
	}
	
}

